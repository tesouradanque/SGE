<?php
require_once APP_PATH . '/models/Funcionario.php';

class FuncionarioController extends Controller {

    private Funcionario $model;
    const PER_PAGE = 10;

    public function __construct() {
        $this->requireAuth();
        $this->model = new Funcionario();
    }

    public function index(): void {
        $search = trim($_GET['q'] ?? '');
        $page   = max(1, (int) ($_GET['p'] ?? 1));
        $all    = $this->model->all('id DESC');
        if ($search !== '') {
            $s   = mb_strtolower($search);
            $all = array_values(array_filter($all, fn($f) =>
                str_contains(mb_strtolower($f['nome']), $s) ||
                str_contains(mb_strtolower($f['cargo'] ?? ''), $s)
            ));
        }
        $pag          = $this->paginate(count($all), self::PER_PAGE, $page);
        $funcionarios = array_slice($all, $pag['offset'], $pag['perPage']);
        $this->view('funcionarios.index', ['funcionarios' => $funcionarios, 'search' => $search, 'pag' => $pag]);
    }

    public function create(): void {
        $this->view('funcionarios.form', ['funcionario' => null, 'action' => 'create', 'csrf' => $this->csrfField()]);
    }

    public function store(): void {
        if (!$this->isPost()) { $this->redirect('funcionario'); }
        $this->csrfVerify();
        $d = $this->camposPost();
        if (empty($d['nome'])) {
            $this->view('funcionarios.form', ['funcionario' => $d, 'action' => 'create', 'erro' => 'Nome obrigatório.', 'csrf' => $this->csrfField()]);
            return;
        }
        $this->model->save($d);
        $this->flash('success', 'Funcionário criado com sucesso.');
        $this->redirect('funcionario');
    }

    public function edit(string $id): void {
        $f = $this->model->find((int)$id);
        if (!$f) { $this->flash('error', 'Funcionário não encontrado.'); $this->redirect('funcionario'); }
        $this->view('funcionarios.form', ['funcionario' => $f, 'action' => 'edit', 'csrf' => $this->csrfField()]);
    }

    public function update(string $id): void {
        if (!$this->isPost()) { $this->redirect('funcionario'); }
        $this->csrfVerify();
        $d = $this->camposPost();
        if (empty($d['nome'])) {
            $this->view('funcionarios.form', ['funcionario' => array_merge($d, ['id' => $id]), 'action' => 'edit', 'erro' => 'Nome obrigatório.', 'csrf' => $this->csrfField()]);
            return;
        }
        $this->model->update((int)$id, $d);
        $this->flash('success', 'Funcionário actualizado.');
        $this->redirect('funcionario');
    }

    public function destroy(string $id): void {
        if (!$this->isAdmin()) {
            $this->flash('error', 'Apenas administradores podem eliminar funcionários.');
            $this->redirect('funcionario');
        }
        try {
            $this->model->delete((int)$id);
            $this->flash('success', 'Funcionário eliminado.');
        } catch (\Exception $e) {
            $this->flash('error', 'Não é possível eliminar: funcionário tem requisições associadas.');
        }
        $this->redirect('funcionario');
    }

    private function camposPost(): array {
        return [
            'nome'     => $this->post('nome'),
            'cargo'    => $this->post('cargo'),
            'telefone' => $this->post('telefone'),
            'email'    => $this->post('email'),
            'ativo'    => (int) $this->post('ativo', '1'),
        ];
    }
}
