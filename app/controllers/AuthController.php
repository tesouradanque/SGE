<?php
require_once APP_PATH . '/models/Usuario.php';

class AuthController extends Controller {

    private Usuario $model;

    public function __construct() {
        $this->model = new Usuario();
    }

    public function login(): void {
        if (!empty($_SESSION['usuario'])) { $this->redirect('home'); }
        $this->view('auth.login', ['erro' => ''], null);
    }

    public function authenticate(): void {
        if (!$this->isPost()) { $this->redirect('auth/login'); }

        $email = $this->post('email');
        $senha = $this->post('senha');
        $user  = $this->model->findByEmail($email);

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['usuario'] = [
                'id'     => $user['id'],
                'nome'   => $user['nome'],
                'email'  => $user['email'],
                'perfil' => $user['perfil'],
            ];
            $this->redirect('home');
        }

        $this->view('auth.login', ['erro' => 'Email ou password incorrectos.'], null);
    }

    public function logout(): void {
        session_destroy();
        $this->redirect('auth/login');
    }
}
