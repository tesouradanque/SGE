<?php
require_once APP_PATH . '/models/Material.php';
require_once APP_PATH . '/models/Fatura.php';
require_once APP_PATH . '/models/Requisicao.php';

class HomeController extends Controller {

    public function __construct() { $this->requireAuth(); }

    public function index(): void {
        $matModel  = new Material();
        $fatModel  = new Fatura();
        $reqModel  = new Requisicao();

        $alertas = $matModel->getLowStock();

        $this->view('home.index', [
            'totalMateriais'   => count($matModel->all()),
            'stockBaixo'       => count($alertas),
            'faturasPendentes' => $fatModel->countPendentes(),
            'requisicoesMes'   => $reqModel->countMes(),
            'materiaisAlerta'  => $alertas,
            'recentesFaturas'  => $fatModel->recentesCom(5),
            'recentesReqs'     => $reqModel->recentesCom(5),
        ]);
    }
}
