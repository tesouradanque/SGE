<?php
require_once APP_PATH . '/models/Fornecedor.php';

class FornecedorController extends Controller {

    private Fornecedor $model;

    public function __construct() {
        $this->requireAuth();
        $this->model = new Fornecedor();
    }

    public function index(): void {
        $this->view('fornecedores.index', ['fornecedores' => $this->model->all('nome ASC')]);
    }

    public function create(): void {
        $this->view('fornecedores.form', ['fornecedor' => null, 'action' => 'create']);
    }

    public function store(): void {
        if (!$this->isPost()) { $this->redirect('fornecedor'); }
        $d = $this->camposPost();
        if (empty($d['nome'])) {
            $this->view('fornecedores.form', ['fornecedor' => $d, 'action' => 'create', 'erro' => 'Nome obrigatório.']);
            return;
        }
        $this->model->save($d);
        $this->flash('success', 'Fornecedor criado com sucesso.');
        $this->redirect('fornecedor');
    }

    public function edit(string $id): void {
        $f = $this->model->find((int)$id);
        if (!$f) { $this->flash('error', 'Fornecedor não encontrado.'); $this->redirect('fornecedor'); }
        $this->view('fornecedores.form', ['fornecedor' => $f, 'action' => 'edit']);
    }

    public function update(string $id): void {
        if (!$this->isPost()) { $this->redirect('fornecedor'); }
        $d = $this->camposPost();
        if (empty($d['nome'])) {
            $this->view('fornecedores.form', ['fornecedor' => array_merge($d, ['id' => $id]), 'action' => 'edit', 'erro' => 'Nome obrigatório.']);
            return;
        }
        $this->model->update((int)$id, $d);
        $this->flash('success', 'Fornecedor actualizado.');
        $this->redirect('fornecedor');
    }

    public function destroy(string $id): void {
        try {
            $this->model->delete((int)$id);
            $this->flash('success', 'Fornecedor eliminado.');
        } catch (\Exception $e) {
            $this->flash('error', 'Não é possível eliminar: fornecedor tem faturas associadas.');
        }
        $this->redirect('fornecedor');
    }

    private function camposPost(): array {
        return [
            'nome'     => $this->post('nome'),
            'nif'      => $this->post('nif'),
            'telefone' => $this->post('telefone'),
            'email'    => $this->post('email'),
            'endereco' => $this->post('endereco'),
        ];
    }
}
