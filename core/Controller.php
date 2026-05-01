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

    protected function requireAdmin(): void {
        $this->requireAuth();
        if (($_SESSION['usuario']['perfil'] ?? '') !== 'admin') {
            $this->flash('error', 'Acesso restrito a administradores.');
            $this->redirect('home');
        }
    }

    protected function isAdmin(): bool {
        return ($_SESSION['usuario']['perfil'] ?? '') === 'admin';
    }

    // CSRF ---------------------------------------------------------------

    protected function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function csrfVerify(): void {
        $token = $_POST['_csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->flash('error', 'Pedido inválido. Tente novamente.');
            $this->redirect('home');
        }
    }

    protected function csrfField(): string {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($this->csrfToken()) . '">';
    }

    // Paginação helper ---------------------------------------------------

    protected function paginate(int $total, int $perPage, int $page): array {
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page       = max(1, min($page, $totalPages));
        return [
            'total'      => $total,
            'perPage'    => $perPage,
            'page'       => $page,
            'totalPages' => $totalPages,
            'offset'     => ($page - 1) * $perPage,
        ];
    }
}
