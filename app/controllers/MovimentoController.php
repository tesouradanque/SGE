<?php
require_once APP_PATH . '/models/Material.php';
require_once APP_PATH . '/models/Fatura.php';
require_once APP_PATH . '/models/Requisicao.php';

class MovimentoController extends Controller {

    public function __construct() { $this->requireAuth(); }

    public function index(): void {
        $matModel = new Material();
        $fatModel = new Fatura();
        $reqModel = new Requisicao();

        $filtros = $this->getFiltros();

        // Entradas (itens_fatura)
        $entradas = $fatModel->movimentosEntrada($filtros);
        // Saídas (itens_requisicao)
        $saidas   = $reqModel->movimentos($filtros);

        // Unir e ordenar por data DESC
        $todos = array_merge($entradas, $saidas);
        usort($todos, fn($a, $b) => strcmp($b['data'] . $b['referencia'], $a['data'] . $a['referencia']));

        // Totais
        $totalEntradas = array_sum(array_column(array_filter($todos, fn($r) => $r['tipo'] === 'entrada'), 'quantidade'));
        $totalSaidas   = array_sum(array_column(array_filter($todos, fn($r) => $r['tipo'] === 'saida'),   'quantidade'));

        $this->view('movimentos.index', [
            'movimentos'    => $todos,
            'materiais'     => $matModel->all('descricao ASC'),
            'filtros'       => $filtros,
            'totalEntradas' => $totalEntradas,
            'totalSaidas'   => $totalSaidas,
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
