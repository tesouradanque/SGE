<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-cubes me-2 text-primary"></i>Materiais</h5>
  <a href="<?= BASE_URL ?>/material/create" class="btn btn-primary">
    <i class="fas fa-plus me-1"></i>Novo Material
  </a>
</div>

<!-- Pesquisa -->
<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" action="<?= BASE_URL ?>/material" class="d-flex gap-2">
      <input type="text" name="q" class="form-control form-control-sm" style="max-width:300px"
        value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Pesquisar por código ou descrição...">
      <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
      <?php if (!empty($search)): ?>
      <a href="<?= BASE_URL ?>/material" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></a>
      <?php endif; ?>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>Código</th><th>Descrição</th><th>Unidade</th><th class="text-end">Preço Padrão</th><th class="text-end">Stock Mínimo</th><th class="text-end">Acções</th></tr>
      </thead>
      <tbody>
      <?php if (empty($materiais)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Nenhum material encontrado.</td></tr>
      <?php else: foreach ($materiais as $m): ?>
        <tr>
          <td><code><?= htmlspecialchars($m['codigo']) ?></code></td>
          <td class="fw-semibold"><?= htmlspecialchars($m['descricao']) ?></td>
          <td><?= htmlspecialchars($m['unidade']) ?></td>
          <td class="text-end"><?= number_format($m['preco_unitario_padrao'], 2, ',', '.') ?> MT</td>
          <td class="text-end"><?= number_format($m['stock_minimo'], 2, ',', '.') ?></td>
          <td class="text-end">
            <a href="<?= BASE_URL ?>/material/edit/<?= $m['id'] ?>" class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-pen"></i></a>
            <?php if (($_SESSION['usuario']['perfil'] ?? '') === 'admin'): ?>
            <button class="btn btn-sm btn-outline-danger"
              data-bs-toggle="modal" data-bs-target="#delModal"
              data-url="<?= BASE_URL ?>/material/destroy/<?= $m['id'] ?>"
              data-nome="<?= htmlspecialchars($m['descricao']) ?>">
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

<?php require APP_PATH . '/views/partials/pagination.php'; ?>

<div class="modal fade" id="delModal" tabindex="-1">
  <div class="modal-dialog modal-sm"><div class="modal-content">
    <div class="modal-header"><h6 class="modal-title">Confirmar</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">Eliminar <strong id="delNome"></strong>?</div>
    <div class="modal-footer">
      <form id="delForm" method="POST">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
      </form>
    </div>
  </div></div>
</div>
<script>
document.getElementById('delModal').addEventListener('show.bs.modal', e => {
  document.getElementById('delNome').textContent = e.relatedTarget.dataset.nome;
  document.getElementById('delForm').action = e.relatedTarget.dataset.url;
});
</script>
