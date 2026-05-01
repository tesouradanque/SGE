<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-file-invoice me-2 text-primary"></i>Entradas – Faturas</h5>
  <a href="<?= BASE_URL ?>/faturas/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Nova Fatura</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>Nº Fatura</th><th>Fornecedor</th><th>Data</th><th class="text-end">Valor Total</th><th>Estado</th><th class="text-end">Acções</th></tr>
      </thead>
      <tbody>
      <?php if (empty($faturas)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Nenhuma fatura registada.</td></tr>
      <?php else: foreach ($faturas as $f): ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($f['nr_fatura']) ?></td>
          <td><?= htmlspecialchars($f['fornecedor_nome']) ?></td>
          <td><?= date('d/m/Y', strtotime($f['data'])) ?></td>
          <td class="text-end"><?= number_format($f['valor_total'], 2, ',', '.') ?> MT</td>
          <td><span class="badge <?= $f['estado'] === 'pago' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= ucfirst($f['estado']) ?></span></td>
          <td class="text-end">
            <a href="<?= BASE_URL ?>/faturas/show/<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-eye"></i></a>
            <button class="btn btn-sm btn-outline-danger"
              data-bs-toggle="modal" data-bs-target="#delModal"
              data-url="<?= BASE_URL ?>/faturas/destroy/<?= $f['id'] ?>"
              data-nome="<?= htmlspecialchars($f['nr_fatura']) ?>">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="delModal" tabindex="-1">
  <div class="modal-dialog modal-sm"><div class="modal-content">
    <div class="modal-header"><h6 class="modal-title">Confirmar</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">Eliminar fatura <strong id="delNome"></strong>? Todos os itens serão removidos.</div>
    <div class="modal-footer"><form id="delForm" method="POST">
      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
      <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
    </form></div>
  </div></div>
</div>
<script>
document.getElementById('delModal').addEventListener('show.bs.modal', e => {
  document.getElementById('delNome').textContent = e.relatedTarget.dataset.nome;
  document.getElementById('delForm').action = e.relatedTarget.dataset.url;
});
</script>
