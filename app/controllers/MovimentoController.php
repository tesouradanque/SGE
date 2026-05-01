<?php
require_once APP_PATH . '/models/Material.php';
require_once APP_PATH . '/models/Fatura.php';
require_once APP_PATH . '/models/Requisicao.php';

class MovimentoController extends Controller {

    public function __construct() { $this->requireAuth(); }

    const PER_PAGE = 10;

    public function index(): void {
        $matModel = new Material();
        $fatModel = new Fatura();
        $reqModel = new Requisicao();

        $filtros = $this->getFiltros();
        $page    = max(1, (int) ($_GET['p'] ?? 1));

        $todos = array_merge(
            $fatModel->movimentosEntrada($filtros),
            $reqModel->movimentos($filtros)
        );
        usort($todos, fn($a, $b) => strcmp($a['data'] . $a['referencia'], $b['data'] . $b['referencia']));

        $totalEntradas = array_sum(array_column(array_filter($todos, fn($r) => $r['tipo'] === 'entrada'), 'quantidade'));
        $totalSaidas   = array_sum(array_column(array_filter($todos, fn($r) => $r['tipo'] === 'saida'),   'quantidade'));

        $pag        = $this->paginate(count($todos), self::PER_PAGE, $page);
        $movimentos = array_slice($todos, $pag['offset'], $pag['perPage']);

        $this->view('movimentos.index', [
            'movimentos'    => $movimentos,
            'materiais'     => $matModel->all('descricao ASC'),
            'filtros'       => $filtros,
            'totalEntradas' => $totalEntradas,
            'totalSaidas'   => $totalSaidas,
            'pag'           => $pag,
        ]);
    }

    private function getFiltros(): array {
        $f = [];
        if (!empty($_GET['material_id'])) $f['material_id'] = (int) $_GET['material_id'];
        if (!empty($_GET['tipo']) && in_array($_GET['tipo'], ['entrada','saida'])) $f['tipo'] = $_GET['tipo'];
        if (!empty($_GET['de'])) {
            $d = \DateTime::createFromFormat('d/m/Y', $_GET['de']);
            if ($d) $f['de'] = $d->format('Y-m-d');
        }
        if (!empty($_GET['ate'])) {
            $d = \DateTime::createFromFormat('d/m/Y', $_GET['ate']);
            if ($d) $f['ate'] = $d->format('Y-m-d');
        }
        return $f;
    }
}
