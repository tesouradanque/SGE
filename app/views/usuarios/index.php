<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-users-gear me-2 text-primary"></i>Gestão de Utilizadores</h5>
  <a href="<?= BASE_URL ?>/usuarios/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Novo Utilizador</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>Nome</th><th>Email</th><th>Perfil</th><th>Estado</th><th>Desde</th><th class="text-end">Acções</th></tr>
      </thead>
      <tbody>
      <?php if (empty($usuarios)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Nenhum utilizador.</td></tr>
      <?php else: foreach ($usuarios as $u): ?>
        <tr>
          <td class="fw-semibold">
            <?= htmlspecialchars($u['nome']) ?>
            <?php if ((int)$u['id'] === (int)($_SESSION['usuario']['id'] ?? 0)): ?>
              <span class="badge bg-secondary ms-1">Eu</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td>
            <span class="badge <?= $u['perfil'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>">
              <?= ucfirst($u['perfil']) ?>
            </span>
          </td>
          <td>
            <span class="badge <?= $u['ativo'] ? 'bg-success' : 'bg-secondary' ?>">
              <?= $u['ativo'] ? 'Activo' : 'Inactivo' ?>
            </span>
          </td>
          <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
          <td class="text-end">
            <a href="<?= BASE_URL ?>/usuarios/edit/<?= $u['id'] ?>" class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-pen"></i></a>
            <?php if ((int)$u['id'] !== (int)($_SESSION['usuario']['id'] ?? 0)): ?>
            <button class="btn btn-sm btn-outline-danger"
              data-bs-toggle="modal" data-bs-target="#delModal"
              data-url="<?= BASE_URL ?>/usuarios/destroy/<?= $u['id'] ?>"
              data-nome="<?= htmlspecialchars($u['nome']) ?>">
              <i class="fas fa-trash"></i>
            </button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include APP_PATH . '/views/partials/pagination.php'; ?>

<div class="modal fade" id="delModal" tabindex="-1">
  <div class="modal-dialog modal-sm"><div class="modal-content">
    <div class="modal-header"><h6 class="modal-title">Confirmar</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">Eliminar utilizador <strong id="delNome"></strong>?</div>
    <div class="modal-footer"><form id="delForm" method="POST">
      <?= $csrf ?>
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
