<?php
require_once APP_PATH . '/models/Ajuste.php';
require_once APP_PATH . '/models/Material.php';

class AjusteController extends Controller {

    private Ajuste   $model;
    private Material $matModel;

    public function __construct() {
        $this->requireAdmin();
        $this->model    = new Ajuste();
        $this->matModel = new Material();
    }

    const PER_PAGE = 25;

    public function index(): void {
        $page = max(1, (int) ($_GET['p'] ?? 1));
        $all  = $this->model->allComMaterial();
        $pag  = $this->paginate(count($all), self::PER_PAGE, $page);
        $this->view('ajustes.index', [
            'ajustes' => array_slice($all, $pag['offset'], $pag['perPage']),
            'pag'     => $pag,
        ]);
    }

    public function create(): void {
        $this->view('ajustes.form', [
            'materiais'   => $this->matModel->getStockActual(),
            'csrf'        => $this->csrfField(),
        ]);
    }

    public function store(): void {
        if (!$this->isPost()) { $this->redirect('ajuste'); }
        $this->csrfVerify();

        $matId    = (int)   $this->post('material_id');
        $tipo     = $this->post('tipo');
        $qty      = (float) $this->post('quantidade');
        $motivo   = $this->post('motivo');

        $erros = [];
        if (!$matId)                                $erros[] = 'Seleccione um material.';
        if (!in_array($tipo, ['entrada','saida']))  $erros[] = 'Tipo de ajuste inválido.';
        if ($qty <= 0)                              $erros[] = 'Quantidade deve ser maior que zero.';
        if (empty($motivo))                         $erros[] = 'Motivo é obrigatório.';

        if (!$erros && $tipo === 'saida') {
            $stock = $this->matModel->getStockById($matId);
            if ($qty > $stock) {
                $erros[] = "Stock insuficiente. Disponível: {$stock}.";
            }
        }

        if ($erros) {
            $this->view('ajustes.form', [
                'materiais' => $this->matModel->getStockActual(),
                'erro'      => implode(' ', $erros),
                'csrf'      => $this->csrfField(),
            ]);
            return;
        }

        $this->model->save([
            'material_id' => $matId,
            'tipo'        => $tipo,
            'quantidade'  => $qty,
            'motivo'      => $motivo,
            'usuario_id'  => (int) $_SESSION['usuario']['id'],
        ]);

        $this->flash('success', 'Ajuste de stock registado com sucesso.');
        $this->redirect('ajuste');
    }
}
