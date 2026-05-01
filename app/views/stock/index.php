<?php
$fmt = fn($v) => number_format((float)$v, 2, ',', '.');
$totalEntradas = array_sum(array_column($materiais, 'total_entradas'));
$totalSaidas   = array_sum(array_column($materiais, 'total_saidas'));
?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-warehouse me-2 text-primary"></i>Stock Actual</h5>
  <a href="<?= BASE_URL ?>/relatorio/mensal?mes=<?= date('m') ?>&ano=<?= date('Y') ?>" class="btn btn-sm btn-outline-primary">
    <i class="fas fa-chart-bar me-1"></i>Ver Relatório
  </a>
</div>

<!-- Resumo topo -->
<div class="row g-3 mb-4">
  <div class="col-sm-4">
    <div class="card p-3 text-center">
      <div class="text-muted small">Total de Materiais</div>
      <div class="fs-3 fw-bold text-primary"><?= count($materiais) ?></div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card p-3 text-center">
      <div class="text-muted small">Total Entrado (todas as un.)</div>
      <div class="fs-3 fw-bold text-success"><?= $fmt($totalEntradas) ?></div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card p-3 text-center">
      <div class="text-muted small">Total Saído</div>
      <div class="fs-3 fw-bold text-danger"><?= $fmt($totalSaidas) ?></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header py-2 px-3 d-flex gap-2 align-items-center">
    <strong>Inventário em tempo real</strong>
    <span class="badge bg-secondary ms-auto">Actualizado: <?= date('d/m/Y H:i') ?></span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Código</th>
          <th>Material</th>
          <th>Unidade</th>
          <th class="text-end">Total Entrado</th>
          <th class="text-end">Total Saído</th>
          <th class="text-end">Stock Actual</th>
          <th class="text-end">Stock Mínimo</th>
          <th class="text-center">Estado</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($materiais)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">Nenhum material cadastrado.</td></tr>
      <?php else: foreach ($materiais as $m):
        $stock = (float) $m['stock_actual'];
        $min   = (float) $m['stock_minimo'];
        if ($stock <= 0)       { $badgeClass = 'badge-out';  $label = 'Esgotado'; }
        elseif ($stock <= $min){ $badgeClass = 'badge-low';  $label = 'Stock Baixo'; }
        else                   { $badgeClass = 'badge-ok';   $label = 'OK'; }
      ?>
        <tr>
          <td><code><?= htmlspecialchars($m['codigo']) ?></code></td>
          <td class="fw-semibold"><?= htmlspecialchars($m['descricao']) ?></td>
          <td><?= htmlspecialchars($m['unidade']) ?></td>
          <td class="text-end text-success"><?= $fmt($m['total_entradas']) ?></td>
          <td class="text-end text-danger"><?= $fmt($m['total_saidas']) ?></td>
          <td class="text-end fw-bold <?= $stock <= 0 ? 'text-danger' : ($stock <= $min ? 'text-warning' : '') ?>">
            <?= $fmt($stock) ?>
          </td>
          <td class="text-end text-muted"><?= $fmt($min) ?></td>
          <td class="text-center"><span class="badge <?= $badgeClass ?>"><?= $label ?></span></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
