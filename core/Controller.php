<?php
class Controller {

    protected function view(string $view, array $data = [], ?string $layout = 'main'): void {
        extract($data);
        $viewFile = APP_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) { die("View não encontrada: {$viewFile}"); }

        if ($layout === null) {
            require $viewFile;
            return;
        }

        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require $layoutFile;
    }

    protected function redirect(string $path): void {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    protected function json($data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function post(?string $key = null, string $default = '') {
        if ($key === null) return $_POST;
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    protected function flash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function requireAuth(): void {
        if (empty($_SESSION['usuario'])) {
            $this->redirect('auth/login');
        }
    }


}
