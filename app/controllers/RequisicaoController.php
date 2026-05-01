<?php
require_once APP_PATH . '/models/Requisicao.php';
require_once APP_PATH . '/models/Funcionario.php';
require_once APP_PATH . '/models/Material.php';

class RequisicaoController extends Controller {

    private Requisicao  $model;
    private Funcionario $funcModel;
    private Material    $matModel;

    public function __construct() {
        $this->requireAuth();
        $this->model     = new Requisicao();
        $this->funcModel = new Funcionario();
        $this->matModel  = new Material();
    }

    public function index(): void {
        $this->view('requisicoes.index', ['requisicoes' => $this->model->allComFuncionario()]);
    }

    public function create(): void {
        $this->renderCreate();
    }

    public function store(): void {
        if (!$this->isPost()) { $this->redirect('requisicoes'); }

        $nr         = $this->post('nr_requisicao');
        $dataRaw    = $this->post('data');
        $dataParsed = \DateTime::createFromFormat('d/m/Y', $dataRaw);
        $data       = $dataParsed ? $dataParsed->format('Y-m-d') : $dataRaw;
        $funcId     = (int) $this->post('funcionario_id');
        $observacao = $this->post('observacao');
        $itens      = $_POST['itens'] ?? [];

        $itensValidos = array_filter($itens, fn($i) => !empty($i['material_id']) && (float)($i['quantidade'] ?? 0) > 0);

        if (!$nr || !$data || !$funcId || empty($itensValidos)) {
            $this->renderCreate('Preencha todos os campos obrigatórios e adicione pelo menos um item.');
            return;
        }

        if ($this->model->nrExiste($nr)) {
            $this->renderCreate("Nº de Requisição «{$nr}» já existe.");
            return;
        }

        // Preload all stock in one query to avoid N+1
        $stockData = array_column($this->matModel->getStockActual(), null, 'id');

        foreach ($itensValidos as $item) {
            $matId = (int)   $item['material_id'];
            $qty   = (float) $item['quantidade'];
            $stock = (float) ($stockData[$matId]['stock_actual'] ?? 0);
            if ($qty > $stock) {
                $nome = $stockData[$matId]['descricao'] ?? "ID {$matId}";
                $this->renderCreate("Stock insuficiente para «{$nome}». Disponível: {$stock}, Requisitado: {$qty}.");
                return;
            }
        }

        $reqId = $this->model->save([
            'nr_requisicao'  => $nr,
            'data'           => $data,
            'funcionario_id' => $funcId,
            'observacao'     => $observacao,
        ]);

        foreach ($itensValidos as $item) {
            $this->model->saveItem($reqId, [
                'material_id'    => (int)   $item['material_id'],
                'quantidade'     => (float) $item['quantidade'],
                'preco_unitario' => (float) $item['preco_unitario'],
            ]);
        }

        $this->flash('success', "Requisição {$nr} registada com sucesso. Stock abatido automaticamente.");
        $this->redirect('requisicoes');
    }

    public function show(string $id): void {
        $req = $this->model->findComItens((int)$id);
        if (!$req) { $this->flash('error', 'Requisição não encontrada.'); $this->redirect('requisicoes'); }
        $this->view('requisicoes.show', ['requisicao' => $req]);
    }

    public function destroy(string $id): void {
        $this->model->delete((int)$id);
        $this->flash('success', 'Requisição eliminada.');
        $this->redirect('requisicoes');
    }

    private function renderCreate(string $erro = ''): void {
        $materiais = $this->matModel->getStockActual();
        $stockMap  = [];
        foreach ($materiais as $m) {
            $stockMap[(int) $m['id']] = (float) $m['stock_actual'];
        }
        $this->view('requisicoes.create', [
            'funcionarios' => $this->funcModel->ativos(),
            'materiais'    => $materiais,
            'stockMap'     => $stockMap,
            'erro'         => $erro,
        ]);
    }
}
