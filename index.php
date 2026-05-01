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

$router = new Router();
$router->dispatch();
