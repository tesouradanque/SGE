<?php
$fmt   = fn($v) => number_format((float)$v, 2, ',', '.');
$titulo = "Relatório Mensal – {$nomeMes} {$ano}";
?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <div class="d-flex align-items-center gap-3">
    <a href="<?= BASE_URL ?>/relatorio" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i></a>
    <h5 class="page-title mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i><?= $titulo ?></h5>
  </div>
  <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
    <i class="fas fa-print me-1"></i>Imprimir
  </button>
</div>

<!-- Navegação de secções -->
<div class="d-flex gap-2 flex-wrap mb-4">
  <a href="#sec-faturas"     class="btn btn-sm btn-outline-primary">Faturas</a>
  <a href="#sec-requisicoes" class="btn btn-sm btn-outline-success">Requisições</a>
  <a href="#sec-por-func"    class="btn btn-sm btn-outline-info">Por Funcionário</a>
  <a href="#sec-fornecedor"  class="btn btn-sm btn-outline-warning">Por Fornecedor</a>
  <a href="#sec-stock"       class="btn btn-sm btn-outline-secondary">Stock Actual</a>
</div>

<!-- 1. Faturas do período -->
<div class="card mb-4" id="sec-faturas">
  <div class="card-header py-2 px-3">
    <strong><i class="fas fa-file-invoice me-2 text-primary"></i>Faturas – <?= $nomeMes ?> <?= $ano ?></strong>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0 table-sm">
      <thead><tr><th>Nº Fatura</th><th>Fornecedor</th><th>Data</th><th class="text-end">Valor Total</th><th>Estado</th></tr></thead>
      <tbody>
      <?php if (empty($faturas)): ?>
        <tr><td colspan="5" class="text-center text-muted py-3">Sem faturas neste período.</td></tr>
      <?php else:
        $totalFat = 0;
        foreach ($faturas as $f):
        $totalFat += $f['valor_total']; ?>
        <tr>
          <td><?= htmlspecialchars($f['nr_fatura']) ?></td>
          <td><?= htmlspecialchars($f['fornecedor_nome']) ?></td>
          <td><?= date('d/m/Y', strtotime($f['data'])) ?></td>
          <td class="text-end"><?= $fmt($f['valor_total']) ?> MT</td>
          <td><span class="badge <?= $f['estado']==='pago' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= ucfirst($f['estado']) ?></span></td>
        </tr>
      <?php endforeach; ?>
        <tr class="table-light fw-bold">
          <td colspan="3" class="text-end">TOTAL</td>
          <td class="text-end text-primary"><?= $fmt($totalFat) ?> MT</td>
          <td></td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 2. Requisições do período -->
<div class="card mb-4" id="sec-requisicoes">
  <div class="card-header py-2 px-3">
    <strong><i class="fas fa-truck-ramp-box me-2 text-success"></i>Requisições – <?= $nomeMes ?> <?= $ano ?></strong>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0 table-sm">
      <thead><tr><th>Nº Req.</th><th>Funcionário</th><th>Data</th><th class="text-end">Valor Total</th></tr></thead>
      <tbody>
      <?php if (empty($requisicoes)): ?>
        <tr><td colspan="4" class="text-center text-muted py-3">Sem requisições neste período.</td></tr>
      <?php else:
        $totalReq = 0;
        foreach ($requisicoes as $r):
        $totalReq += $r['valor_total']; ?>
        <tr>
          <td><?= htmlspecialchars($r['nr_requisicao']) ?></td>
          <td><?= htmlspecialchars($r['funcionario_nome']) ?></td>
          <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
          <td class="text-end"><?= $fmt($r['valor_total']) ?> MT</td>
        </tr>
      <?php endforeach; ?>
        <tr class="table-light fw-bold">
          <td colspan="3" class="text-end">TOTAL</td>
          <td class="text-end text-success"><?= $fmt($totalReq) ?> MT</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 3. Materiais por funcionário -->
<div class="card mb-4" id="sec-por-func">
  <div class="card-header py-2 px-3">
    <strong><i class="fas fa-users me-2 text-info"></i>Materiais Requisitados por Funcionário</strong>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0 table-sm">
      <thead><tr><th>Funcionário</th><th>Material</th><th>Unidade</th><th class="text-end">Qtd. Total</th><th class="text-end">Valor Total</th></tr></thead>
      <tbody>
      <?php if (empty($materiaisPorFuncionario)): ?>
        <tr><td colspan="5" class="text-center text-muted py-3">Sem movimentos neste período.</td></tr>
      <?php else:
        $prevFunc = null;
        foreach ($materiaisPorFuncionario as $row):
          $isNew = $row['funcionario'] !== $prevFunc;
          $prevFunc = $row['funcionario'];
      ?>
        <tr <?= $isNew ? 'class="table-light"' : '' ?>>
          <td class="<?= $isNew ? 'fw-bold' : 'text-muted' ?>"><?= $isNew ? htmlspecialchars($row['funcionario']) : '' ?></td>
          <td><?= htmlspecialchars($row['material']) ?></td>
          <td><?= htmlspecialchars($row['unidade']) ?></td>
          <td class="text-end"><?= $fmt($row['quantidade_total']) ?></td>
          <td class="text-end"><?= $fmt($row['valor_total']) ?> MT</td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 4. Total por fornecedor -->
<div class="card mb-4" id="sec-fornecedor">
  <div class="card-header py-2 px-3">
    <strong><i class="fas fa-building me-2 text-warning"></i>Faturas a Pagar por Fornecedor</strong>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0 table-sm">
      <thead><tr><th>Fornecedor</th><th class="text-end">Nº Faturas</th><th class="text-end">Total (MT)</th><th class="text-end">Pendente (MT)</th></tr></thead>
      <tbody>
      <?php if (empty($totalPorFornecedor)): ?>
        <tr><td colspan="4" class="text-center text-muted py-3">Sem dados.</td></tr>
      <?php else:
        foreach ($totalPorFornecedor as $row): ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($row['nome']) ?></td>
          <td class="text-end"><?= $row['nr_faturas'] ?></td>
          <td class="text-end"><?= $fmt($row['total_valor']) ?></td>
          <td class="text-end <?= $row['pendente'] > 0 ? 'text-danger fw-bold' : 'text-muted' ?>">
            <?= $fmt($row['pendente']) ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- 5. Stock actual -->
<div class="card mb-4" id="sec-stock">
  <div class="card-header py-2 px-3">
    <strong><i class="fas fa-warehouse me-2"></i>Stock Actual (no momento do relatório)</strong>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0 table-sm">
      <thead><tr><th>Código</th><th>Material</th><th>Un.</th><th class="text-end">Entradas</th><th class="text-end">Saídas</th><th class="text-end">Stock</th><th class="text-center">Estado</th></tr></thead>
      <tbody>
      <?php foreach ($stockActual as $m):
        $s = (float)$m['stock_actual'];
        $min = (float)$m['stock_minimo'];
        if ($s<=0)      { $bc='badge-out'; $lb='Esgotado'; }
        elseif($s<=$min){ $bc='badge-low'; $lb='Baixo'; }
        else            { $bc='badge-ok';  $lb='OK'; }
      ?>
        <tr>
          <td><code><?= htmlspecialchars($m['codigo']) ?></code></td>
          <td><?= htmlspecialchars($m['descricao']) ?></td>
          <td><?= htmlspecialchars($m['unidade']) ?></td>
          <td class="text-end text-success"><?= $fmt($m['total_entradas']) ?></td>
          <td class="text-end text-danger"><?= $fmt($m['total_saidas']) ?></td>
          <td class="text-end fw-bold <?= $s<=0 ? 'text-danger' : ($s<=$min ? 'text-warning' : '') ?>"><?= $fmt($s) ?></td>
          <td class="text-center"><span class="badge <?= $bc ?>"><?= $lb ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<style>
@media print {
  #sidebar, #topbar, #sidebar-toggle, .flash-container,
  .btn, a.btn, nav { display: none !important; }
  .card { box-shadow: none !important; border: 1px solid #ddd !important; }
  #main-content { padding: 0 !important; }
}
</style>
