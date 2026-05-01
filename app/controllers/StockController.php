<?php
require_once APP_PATH . '/models/Material.php';

class StockController extends Controller {

    public function __construct() { $this->requireAuth(); }

    public function index(): void {
        $matModel = new Material();
        $this->view('stock.index', ['materiais' => $matModel->getStockActual()]);
    }
}
