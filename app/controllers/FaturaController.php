<?php
require_once APP_PATH . '/models/Fatura.php';
require_once APP_PATH . '/models/Fornecedor.php';
require_once APP_PATH . '/models/Material.php';

class FaturaController extends Controller {

    private Fatura     $model;
    private Fornecedor $fornModel;
    private Material   $matModel;

    const PER_PAGE = 20;

    public function __construct() {
        $this->requireAuth();
        $this->model     = new Fatura();
        $this->fornModel = new Fornecedor();
        $this->matModel  = new Material();
    }

    public function index(): void {
        $filtros = $this->getFiltros();
        $page    = max(1, (int) ($_GET['p'] ?? 1));
        $pag     = $this->paginate($this->model->countAll($filtros), self::PER_PAGE, $page);

        $this->view('faturas.index', [
            'faturas'      => $this->model->allComFornecedorFiltrado($filtros, $pag['perPage'], $pag['offset']),
            'fornecedores' => $this->fornModel->all('nome ASC'),
            'filtros'      => $filtros,
            'pag'          => $pag,
        ]);
    }

    public function create(): void {
        $this->view('faturas.create', [
            'fornecedores' => $this->fornModel->all('nome ASC'),
            'materiais'    => $this->matModel->all('descricao ASC'),
            'erro'         => '',
            'csrf'         => $this->csrfField(),
        ]);
    }

    public function store(): void {
        if (!$this->isPost()) { $this->redirect('faturas'); }
        $this->csrfVerify();

        $nr           = $this->post('nr_fatura');
        $dataRaw      = $this->post('data');
        $dataParsed   = \DateTime::createFromFormat('d/m/Y', $dataRaw);
        $data         = $dataParsed ? $dataParsed->format('Y-m-d') : $dataRaw;
        $fornecedor   = (int) $this->post('fornecedor_id');
        $estado       = $this->post('estado') ?: 'pendente';
        $observacao   = $this->post('observacao');
        $itens        = $_POST['itens'] ?? [];

        $itensValidos = array_filter($itens, function($i) {
            $mid = $i['material_id'] ?? '';
            if ($mid === 'novo') {
                return !empty($i['material_novo_descricao']) && (float)($i['quantidade'] ?? 0) > 0;
            }
            return !empty($mid) && (float)($i['quantidade'] ?? 0) > 0;
        });

        $erros = [];
        if (!$nr)                    $erros[] = 'Nº da fatura é obrigatório.';
        if (!$data)                  $erros[] = 'Data é obrigatória.';
        if (!$fornecedor)            $erros[] = 'Fornecedor é obrigatório.';
        if (empty($itensValidos))    $erros[] = 'Adicione pelo menos um item.';
        if ($nr && $this->model->nrExiste($nr)) $erros[] = "Nº de Fatura «{$nr}» já existe.";

        if ($erros) {
            $this->view('faturas.create', [
                'fornecedores' => $this->fornModel->all('nome ASC'),
                'materiais'    => $this->matModel->all('descricao ASC'),
                'erro'         => implode(' ', $erros),
                'csrf'         => $this->csrfField(),
            ]);
            return;
        }

        $faturaId = $this->model->save([
            'nr_fatura'     => $nr,
            'data'          => $data,
            'fornecedor_id' => $fornecedor,
            'observacao'    => $observacao,
            'estado'        => $estado,
        ]);

        foreach ($itensValidos as $item) {
            if ($item['material_id'] === 'novo') {
                $desc    = trim($item['material_novo_descricao']);
                $codigo  = trim($item['material_novo_codigo'] ?? '') ?: strtoupper(substr($desc, 0, 6));
                $unidade = $item['material_novo_unidade'] ?? 'un';
                $item['material_id'] = $this->matModel->save([
                    'codigo'                => $codigo,
                    'descricao'             => $desc,
                    'unidade'               => $unidade,
                    'preco_unitario_padrao' => (float) $item['preco_unitario'],
                    'stock_minimo'          => 0,
                ]);
            }
            $this->model->saveItem($faturaId, [
                'material_id'    => (int)   $item['material_id'],
                'quantidade'     => (float) $item['quantidade'],
                'preco_unitario' => (float) $item['preco_unitario'],
            ]);
        }

        $this->flash('success', "Fatura {$nr} registada com sucesso.");
        $this->redirect('faturas');
    }

    public function show(string $id): void {
        $fatura = $this->model->findComItens((int)$id);
        if (!$fatura) { $this->flash('error', 'Fatura não encontrada.'); $this->redirect('faturas'); }
        $this->view('faturas.show', ['fatura' => $fatura, 'csrf' => $this->csrfField()]);
    }

    public function estado(string $id): void {
        if (!$this->isPost()) { $this->redirect('faturas'); }
        $this->csrfVerify();
        $estado = $this->post('estado');
        if (!in_array($estado, ['pendente', 'pago'], true)) { $this->redirect("faturas/show/{$id}"); }
        $this->model->updateEstado((int)$id, $estado);
        $this->flash('success', 'Estado actualizado.');
        $this->redirect("faturas/show/{$id}");
    }

    public function destroy(string $id): void {
        if (!$this->isAdmin()) {
            $this->flash('error', 'Apenas administradores podem eliminar faturas.');
            $this->redirect('faturas');
        }
        $this->model->delete((int)$id);
        $this->flash('success', 'Fatura eliminada.');
        $this->redirect('faturas');
    }

    public function exportCsv(): void {
        $filtros = $this->getFiltros();
        $rows    = $this->model->allParaCsv($filtros);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="faturas_' . date('Ymd_His') . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
        fputcsv($out, ['Nº Fatura', 'Fornecedor', 'Data', 'Estado', 'Valor Total (MT)', 'Observação'], ';');
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['nr_fatura'],
                $r['fornecedor'],
                date('d/m/Y', strtotime($r['data'])),
                ucfirst($r['estado']),
                number_format($r['valor_total'], 2, ',', '.'),
                $r['observacao'] ?? '',
            ], ';');
        }
        fclose($out);
        exit;
    }

    private function getFiltros(): array {
        $f = [];
        if (!empty($_GET['estado']))       $f['estado']       = $_GET['estado'];
        if (!empty($_GET['fornecedor_id'])) $f['fornecedor_id'] = (int) $_GET['fornecedor_id'];
        if (!empty($_GET['nr']))           $f['nr']           = $_GET['nr'];
        if (!empty($_GET['de'])) {
            $d = \DateTime::createFromFormat('d/m/Y', $_GET['de']);
            if ($d) $f['de'] = $d->format('Y-m-d');
        }
        if (!empty($_GET['ate'])) {
            $d = \DateTime::createFromFormat('d/m/Y', $_GET['ate']);
            if ($d) $f['ate'] = $d->format('Y-m-d');
        }
        return $f;
    }
}
