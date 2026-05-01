<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-building me-2 text-primary"></i>Fornecedores</h5>
  <a href="<?= BASE_URL ?>/fornecedor/create" class="btn btn-primary">
    <i class="fas fa-plus me-1"></i>Novo Fornecedor
  </a>
</div>

<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" action="<?= BASE_URL ?>/fornecedor" class="d-flex gap-2 align-items-center">
      <input type="text" name="q" class="form-control form-control-sm" style="max-width:280px"
        placeholder="Pesquisar por nome ou NUIT…" value="<?= htmlspecialchars($search ?? '') ?>">
      <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
      <?php if (!empty($search)): ?>
        <a href="<?= BASE_URL ?>/fornecedor" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></a>
      <?php endif; ?>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>#</th><th>Nome</th><th>NUIT</th><th>Telefone</th><th>Email</th><th class="text-end">Acções</th></tr>
      </thead>
      <tbody>
      <?php if (empty($fornecedores)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Nenhum fornecedor cadastrado.</td></tr>
      <?php else: foreach ($fornecedores as $f): ?>
        <tr>
          <td class="text-muted small"><?= $f['id'] ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($f['nome']) ?></td>
          <td><?= htmlspecialchars($f['nif'] ?? '—') ?></td>
          <td><?= htmlspecialchars($f['telefone'] ?? '—') ?></td>
          <td><?= htmlspecialchars($f['email'] ?? '—') ?></td>
          <td class="text-end">
            <a href="<?= BASE_URL ?>/fornecedor/edit/<?= $f['id'] ?>" class="btn btn-sm btn-outline-secondary me-1">
              <i class="fas fa-pen"></i>
            </a>
            <button type="button" class="btn btn-sm btn-outline-danger"
              data-bs-toggle="modal" data-bs-target="#delModal"
              data-url="<?= BASE_URL ?>/fornecedor/destroy/<?= $f['id'] ?>"
              data-nome="<?= htmlspecialchars($f['nome']) ?>">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include APP_PATH . '/views/partials/pagination.php'; ?>

<!-- Delete modal -->
<div class="modal fade" id="delModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Confirmar eliminação</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Eliminar <strong id="delNome"></strong>?</div>
      <div class="modal-footer">
        <form id="delForm" method="POST">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('delModal').addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget;
  document.getElementById('delNome').textContent = btn.dataset.nome;
  document.getElementById('delForm').action = btn.dataset.url;
});
</script>
