<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-users me-2 text-primary"></i>Funcionários</h5>
  <a href="<?= BASE_URL ?>/funcionario/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Novo Funcionário</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>#</th><th>Nome</th><th>Cargo</th><th>Telefone</th><th>Email</th><th>Estado</th><th class="text-end">Acções</th></tr></thead>
      <tbody>
      <?php if (empty($funcionarios)): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Nenhum funcionário cadastrado.</td></tr>
      <?php else: foreach ($funcionarios as $f): ?>
        <tr>
          <td class="text-muted small"><?= $f['id'] ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($f['nome']) ?></td>
          <td><?= htmlspecialchars($f['cargo'] ?? '—') ?></td>
          <td><?= htmlspecialchars($f['telefone'] ?? '—') ?></td>
          <td><?= htmlspecialchars($f['email'] ?? '—') ?></td>
          <td><span class="badge <?= $f['ativo'] ? 'bg-success' : 'bg-secondary' ?>"><?= $f['ativo'] ? 'Activo' : 'Inactivo' ?></span></td>
          <td class="text-end">
            <a href="<?= BASE_URL ?>/funcionario/edit/<?= $f['id'] ?>" class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-pen"></i></a>
            <button class="btn btn-sm btn-outline-danger"
              data-bs-toggle="modal" data-bs-target="#delModal"
              data-url="<?= BASE_URL ?>/funcionario/destroy/<?= $f['id'] ?>"
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

<div class="modal fade" id="delModal" tabindex="-1">
  <div class="modal-dialog modal-sm"><div class="modal-content">
    <div class="modal-header"><h6 class="modal-title">Confirmar</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">Eliminar <strong id="delNome"></strong>?</div>
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
