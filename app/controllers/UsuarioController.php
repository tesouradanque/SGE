<?php
require_once APP_PATH . '/models/Usuario.php';

class UsuarioController extends Controller {

    private Usuario $model;
    const PER_PAGE = 25;

    public function __construct() {
        $this->requireAdmin();
        $this->model = new Usuario();
    }

    public function index(): void {
        $page  = max(1, (int) ($_GET['p'] ?? 1));
        $all   = $this->model->all('nome ASC');
        $pag   = $this->paginate(count($all), self::PER_PAGE, $page);
        $this->view('usuarios.index', [
            'usuarios' => array_slice($all, $pag['offset'], $pag['perPage']),
            'pag'      => $pag,
            'csrf'     => $this->csrfField(),
        ]);
    }

    public function create(): void {
        $this->view('usuarios.form', ['usuario' => null, 'action' => 'create', 'csrf' => $this->csrfField()]);
    }

    public function store(): void {
        if (!$this->isPost()) { $this->redirect('usuarios'); }
        $this->csrfVerify();
        $d = $this->camposPost();

        $erros = [];
        if (empty($d['nome']))             $erros[] = 'Nome é obrigatório.';
        if (empty($d['email']))            $erros[] = 'Email é obrigatório.';
        if (empty($d['senha']))            $erros[] = 'Password é obrigatória.';
        if (strlen($d['senha'] ?? '') < 6) $erros[] = 'Password deve ter pelo menos 6 caracteres.';
        if ($d['email'] && $this->model->emailExiste($d['email'])) $erros[] = 'Email já está registado.';

        if ($erros) {
            $this->view('usuarios.form', ['usuario' => $d, 'action' => 'create', 'erro' => implode(' ', $erros), 'csrf' => $this->csrfField()]);
            return;
        }

        $this->model->save($d);
        $this->flash('success', 'Utilizador criado com sucesso.');
        $this->redirect('usuarios');
    }

    public function edit(string $id): void {
        $u = $this->model->find((int)$id);
        if (!$u) { $this->flash('error', 'Utilizador não encontrado.'); $this->redirect('usuarios'); }
        $this->view('usuarios.form', ['usuario' => $u, 'action' => 'edit', 'csrf' => $this->csrfField()]);
    }

    public function update(string $id): void {
        if (!$this->isPost()) { $this->redirect('usuarios'); }
        $this->csrfVerify();
        $d = $this->camposPost();

        $erros = [];
        if (empty($d['nome']))  $erros[] = 'Nome é obrigatório.';
        if (empty($d['email'])) $erros[] = 'Email é obrigatório.';
        if (!empty($d['senha']) && strlen($d['senha']) < 6) $erros[] = 'Password deve ter pelo menos 6 caracteres.';
        if ($d['email'] && $this->model->emailExiste($d['email'], (int)$id)) $erros[] = 'Email já está registado por outro utilizador.';

        // Impedir que o próprio admin se retire o role admin
        if ((int)$id === (int)($_SESSION['usuario']['id'] ?? 0) && $d['perfil'] !== 'admin') {
            $erros[] = 'Não pode alterar o seu próprio perfil de administrador.';
        }

        if ($erros) {
            $u = $this->model->find((int)$id);
            $this->view('usuarios.form', ['usuario' => array_merge($u ?: [], $d, ['id' => $id]), 'action' => 'edit', 'erro' => implode(' ', $erros), 'csrf' => $this->csrfField()]);
            return;
        }

        $this->model->update((int)$id, $d);
        $this->flash('success', 'Utilizador actualizado.');
        $this->redirect('usuarios');
    }

    public function destroy(string $id): void {
        if ((int)$id === (int)($_SESSION['usuario']['id'] ?? 0)) {
            $this->flash('error', 'Não pode eliminar a sua própria conta.');
            $this->redirect('usuarios');
        }
        $this->model->delete((int)$id);
        $this->flash('success', 'Utilizador eliminado.');
        $this->redirect('usuarios');
    }

    private function camposPost(): array {
        return [
            'nome'   => $this->post('nome'),
            'email'  => $this->post('email'),
            'senha'  => $this->post('senha'),
            'perfil' => in_array($this->post('perfil'), ['admin','operador']) ? $this->post('perfil') : 'operador',
            'ativo'  => (int) $this->post('ativo', '1'),
        ];
    }
}
