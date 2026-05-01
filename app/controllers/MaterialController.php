<?php
require_once APP_PATH . '/models/Material.php';

class MaterialController extends Controller {

    private Material $model;

    public function __construct() {
        $this->requireAuth();
        $this->model = new Material();
    }

    public function index(): void {
        $this->view('materiais.index', ['materiais' => $this->model->all('descricao ASC')]);
    }

    public function create(): void {
        $this->view('materiais.form', ['material' => null, 'action' => 'create']);
    }

    public function store(): void {
        if (!$this->isPost()) { $this->redirect('material'); }
        $d = $this->camposPost();
        if (empty($d['codigo']) || empty($d['descricao'])) {
            $this->view('materiais.form', ['material' => $d, 'action' => 'create', 'erro' => 'Código e Descrição são obrigatórios.']);
            return;
        }
        try {
            $this->model->save($d);
            $this->flash('success', 'Material criado com sucesso.');
            $this->redirect('material');
        } catch (\Exception $e) {
            $this->view('materiais.form', ['material' => $d, 'action' => 'create', 'erro' => 'Código já existe.']);
        }
    }

    public function edit(string $id): void {
        $m = $this->model->find((int)$id);
        if (!$m) { $this->flash('error', 'Material não encontrado.'); $this->redirect('material'); }
        $this->view('materiais.form', ['material' => $m, 'action' => 'edit']);
    }

    public function update(string $id): void {
        if (!$this->isPost()) { $this->redirect('material'); }
        $d = $this->camposPost();
        if (empty($d['codigo']) || empty($d['descricao'])) {
            $this->view('materiais.form', ['material' => array_merge($d, ['id' => $id]), 'action' => 'edit', 'erro' => 'Código e Descrição são obrigatórios.']);
            return;
        }
        $this->model->update((int)$id, $d);
        $this->flash('success', 'Material actualizado.');
        $this->redirect('material');
    }

    public function destroy(string $id): void {
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
