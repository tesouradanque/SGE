<?php
define('BASE_PATH', __DIR__);
define('APP_PATH',  BASE_PATH . '/app');
define('CORE_PATH', BASE_PATH . '/core');

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once CORE_PATH  . '/Model.php';
require_once CORE_PATH  . '/Controller.php';
require_once CORE_PATH  . '/Router.php';

session_start();

// Sessão expira após 60 minutos de inactividade
if (!empty($_SESSION['usuario'])) {
    if (isset($_SESSION['_last_activity']) && (time() - $_SESSION['_last_activity']) > 3600) {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
    $_SESSION['_last_activity'] = time();
}

$router = new Router();
$router->dispatch();
