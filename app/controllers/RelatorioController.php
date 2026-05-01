<?php
require_once APP_PATH . '/models/Fatura.php';
require_once APP_PATH . '/models/Requisicao.php';
require_once APP_PATH . '/models/Material.php';

class RelatorioController extends Controller {

    public function __construct() { $this->requireAuth(); }

    public function index(): void {
        $this->view('relatorios.index', [
            'mes' => (int) date('m'),
            'ano' => (int) date('Y'),
        ]);
    }

    public function mensal(): void {
        $mes = (int) ($_GET['mes'] ?? date('m'));
        $ano = (int) ($_GET['ano'] ?? date('Y'));

        $fatModel = new Fatura();
        $reqModel = new Requisicao();
        $matModel = new Material();

        $this->view('relatorios.mensal', [
            'mes'                     => $mes,
            'ano'                     => $ano,
            'nomeMes'                 => $this->nomeMes($mes),
            'faturas'                 => $fatModel->porPeriodo($mes, $ano),
            'requisicoes'             => $reqModel->porPeriodo($mes, $ano),
            'materiaisPorFuncionario' => $reqModel->materiaisPorFuncionario($mes, $ano),
            'totalPorFornecedor'      => $fatModel->totalPorFornecedor($mes, $ano),
            'stockActual'             => $matModel->getStockActual(),
        ]);
    }

    private function nomeMes(int $m): string {
        $nomes = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                  'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        return $nomes[$m] ?? '';
    }
}
