<?php $fmt = fn($v) => number_format((float)$v, 2, ',', '.'); ?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-sliders me-2 text-warning"></i>Ajustes de Stock</h5>
  <a href="<?= BASE_URL ?>/ajuste/create" class="btn btn-warning text-white"><i class="fas fa-plus me-1"></i>Novo Ajuste</a>
</div>

<div class="alert alert-warning py-2">
  <i class="fas fa-triangle-exclamation me-2"></i>
  Os ajustes são operações de correcção do stock. Use apenas quando a contagem física difere do sistema.
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>Data</th><th>Material</th><th>Tipo</th><th class="text-end">Quantidade</th><th>Motivo</th><th>Registado por</th></tr>
      </thead>
      <tbody>
      <?php if (empty($ajustes)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Nenhum ajuste registado.</td></tr>
      <?php else: foreach ($ajustes as $a): ?>
        <tr>
          <td><?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></td>
          <td>
            <code><?= htmlspecialchars($a['codigo']) ?></code>
            <?= htmlspecialchars($a['material']) ?>
            <small class="text-muted">(<?= htmlspecialchars($a['unidade']) ?>)</small>
          </td>
          <td>
            <span class="badge <?= $a['tipo'] === 'entrada' ? 'bg-success' : 'bg-danger' ?>">
              <i class="fas <?= $a['tipo'] === 'entrada' ? 'fa-arrow-down' : 'fa-arrow-up' ?> me-1"></i>
              <?= $a['tipo'] === 'entrada' ? 'Entrada' : 'Saída' ?>
            </span>
          </td>
          <td class="text-end fw-bold <?= $a['tipo'] === 'entrada' ? 'text-success' : 'text-danger' ?>">
            <?= $a['tipo'] === 'entrada' ? '+' : '-' ?><?= $fmt($a['quantidade']) ?>
          </td>
          <td><?= htmlspecialchars($a['motivo']) ?></td>
          <td class="text-muted small"><?= htmlspecialchars($a['usuario']) ?></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include APP_PATH . '/views/partials/pagination.php'; ?>
