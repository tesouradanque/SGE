<?php
class Router {

    private $map = [
        'faturas'     => 'FaturaController',
        'requisicoes' => 'RequisicaoController',
        'usuarios'    => 'UsuarioController',
        'movimentos'  => 'MovimentoController',
        'ajuste'      => 'AjusteController',
    ];

    public function dispatch() {
        $url      = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
        $segments = $url !== '' ? explode('/', $url) : [];

        $seg0 = !empty($segments[0]) ? strtolower($segments[0]) : 'home';
        $controllerName = isset($this->map[$seg0])
            ? $this->map[$seg0]
            : ucfirst($seg0) . 'Controller';

        $action = !empty($segments[1]) ? $segments[1] : 'index';
        $params  = array_slice($segments, 2);

        $file = APP_PATH . '/controllers/' . $controllerName . '.php';

        if (!file_exists($file)) { $this->notFound(); return; }
        require_once $file;
        if (!class_exists($controllerName)) { $this->notFound(); return; }

        $controller = new $controllerName();
        if (!method_exists($controller, $action)) { $this->notFound(); return; }

        call_user_func_array([$controller, $action], $params);
    }

    private function notFound() {
        http_response_code(404);
        echo '<!DOCTYPE html><html><head><title>404</title>
              <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
              </head><body class="d-flex align-items-center justify-content-center" style="height:100vh">
              <div class="text-center"><h1 class="display-1 text-muted">404</h1>
              <p class="lead">Página não encontrada</p>
              <a href="' . BASE_URL . '" class="btn btn-primary">Voltar ao início</a>
              </div></body></html>';
    }
}
