<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-truck-ramp-box me-2 text-success"></i>Saídas – Requisições</h5>
  <a href="<?= BASE_URL ?>/requisicoes/create" class="btn btn-success"><i class="fas fa-plus me-1"></i>Nova Requisição</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>Nº Req.</th><th>Funcionário</th><th>Data</th><th class="text-end">Itens</th><th class="text-end">Valor Total</th><th class="text-end">Acções</th></tr>
      </thead>
      <tbody>
      <?php if (empty($requisicoes)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Nenhuma requisição registada.</td></tr>
      <?php else: foreach ($requisicoes as $r): ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($r['nr_requisicao']) ?></td>
          <td><?= htmlspecialchars($r['funcionario_nome']) ?></td>
          <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
          <td class="text-end"><?= $r['nr_itens'] ?></td>
          <td class="text-end"><?= number_format($r['valor_total'], 2, ',', '.') ?> MT</td>
          <td class="text-end">
            <a href="<?= BASE_URL ?>/requisicoes/show/<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-eye"></i></a>
            <button class="btn btn-sm btn-outline-danger"
              data-bs-toggle="modal" data-bs-target="#delModal"
              data-url="<?= BASE_URL ?>/requisicoes/destroy/<?= $r['id'] ?>"
              data-nome="<?= htmlspecialchars($r['nr_requisicao']) ?>">
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
    <div class="modal-body">Eliminar requisição <strong id="delNome"></strong>? O stock será restaurado.</div>
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
