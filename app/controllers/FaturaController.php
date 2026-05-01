<?php
require_once APP_PATH . '/models/Fatura.php';
require_once APP_PATH . '/models/Fornecedor.php';
require_once APP_PATH . '/models/Material.php';

class FaturaController extends Controller {

    private Fatura     $model;
    private Fornecedor $fornModel;
    private Material   $matModel;

    public function __construct() {
        $this->requireAuth();
        $this->model     = new Fatura();
        $this->fornModel = new Fornecedor();
        $this->matModel  = new Material();
    }

    public function index(): void {
        $this->view('faturas.index', ['faturas' => $this->model->allComFornecedor()]);
    }

    public function create(): void {
        $this->view('faturas.create', [
            'fornecedores' => $this->fornModel->all('nome ASC'),
            'materiais'    => $this->matModel->all('descricao ASC'),
            'erro'         => '',
        ]);
    }

    public function store(): void {
        if (!$this->isPost()) { $this->redirect('faturas'); }

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

        if (!$nr || !$data || !$fornecedor || empty($itensValidos)) {
            $this->view('faturas.create', [
                'fornecedores' => $this->fornModel->all('nome ASC'),
                'materiais'    => $this->matModel->all('descricao ASC'),
                'erro'         => 'Preencha todos os campos obrigatórios e adicione pelo menos um item.',
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
        $this->view('faturas.show', ['fatura' => $fatura]);
    }

    public function estado(string $id): void {
        if (!$this->isPost()) { $this->redirect('faturas'); }
        $estado = $this->post('estado');
        if (!in_array($estado, ['pendente', 'pago'], true)) { $this->redirect("faturas/show/{$id}"); }
        $this->model->updateEstado((int)$id, $estado);
        $this->flash('success', 'Estado actualizado.');
        $this->redirect("faturas/show/{$id}");
    }

    public function destroy(string $id): void {
        $this->model->delete((int)$id);
        $this->flash('success', 'Fatura eliminada.');
        $this->redirect('faturas');
    }
}
