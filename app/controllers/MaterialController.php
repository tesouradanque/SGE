<?php
require_once APP_PATH . '/models/Material.php';

class MaterialController extends Controller {

    private Material $model;
    const PER_PAGE = 10;

    public function __construct() {
        $this->requireAuth();
        $this->model = new Material();
    }

    public function index(): void {
        $search = trim($_GET['q'] ?? '');
        $page   = max(1, (int) ($_GET['p'] ?? 1));
        $all    = $this->model->all('id DESC');

        if ($search !== '') {
            $s   = mb_strtolower($search);
            $all = array_filter($all, fn($m) =>
                str_contains(mb_strtolower($m['descricao']), $s) ||
                str_contains(mb_strtolower($m['codigo']), $s)
            );
            $all = array_values($all);
        }

        $total = count($all);
        $pag   = $this->paginate($total, self::PER_PAGE, $page);
        $materiais = array_slice($all, $pag['offset'], $pag['perPage']);

        $this->view('materiais.index', [
            'materiais' => $materiais,
            'search'    => $search,
            'pag'       => $pag,
        ]);
    }

    public function create(): void {
        $this->view('materiais.form', ['material' => null, 'action' => 'create', 'csrf' => $this->csrfField()]);
    }

    public function store(): void {
        if (!$this->isPost()) { $this->redirect('material'); }
        $this->csrfVerify();
        $d = $this->camposPost();
        if (empty($d['codigo']) || empty($d['descricao'])) {
            $this->view('materiais.form', ['material' => $d, 'action' => 'create', 'erro' => 'Código e Descrição são obrigatórios.', 'csrf' => $this->csrfField()]);
            return;
        }
        try {
            $this->model->save($d);
            $this->flash('success', 'Material criado com sucesso.');
            $this->redirect('material');
        } catch (\Exception $e) {
            $this->view('materiais.form', ['material' => $d, 'action' => 'create', 'erro' => 'Código já existe.', 'csrf' => $this->csrfField()]);
        }
    }

    public function edit(string $id): void {
        $m = $this->model->find((int)$id);
        if (!$m) { $this->flash('error', 'Material não encontrado.'); $this->redirect('material'); }
        $this->view('materiais.form', ['material' => $m, 'action' => 'edit', 'csrf' => $this->csrfField()]);
    }

    public function update(string $id): void {
        if (!$this->isPost()) { $this->redirect('material'); }
        $this->csrfVerify();
        $d = $this->camposPost();
        if (empty($d['codigo']) || empty($d['descricao'])) {
            $this->view('materiais.form', ['material' => array_merge($d, ['id' => $id]), 'action' => 'edit', 'erro' => 'Código e Descrição são obrigatórios.', 'csrf' => $this->csrfField()]);
            return;
        }
        $this->model->update((int)$id, $d);
        $this->flash('success', 'Material actualizado.');
        $this->redirect('material');
    }

    public function destroy(string $id): void {
        if (!$this->isAdmin()) {
            $this->flash('error', 'Apenas administradores podem eliminar materiais.');
            $this->redirect('material');
        }
        try {
            $this->model->delete((int)$id);
            $this->flash('success', 'Material eliminado.');
        } catch (\Exception $e) {
            $this->flash('error', 'Não é possível eliminar: material tem movimentos associados.');
        }
        $this->redirect('material');
    }

    private function camposPost(): array {
        return [
            'codigo'                => $this->post('codigo'),
            'descricao'             => $this->post('descricao'),
            'unidade'               => $this->post('unidade') ?: 'un',
            'preco_unitario_padrao' => (float) $this->post('preco_unitario_padrao'),
            'stock_minimo'          => (float) $this->post('stock_minimo'),
        ];
    }
}
