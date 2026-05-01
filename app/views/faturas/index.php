<?php $qs = $_GET; ?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-file-invoice me-2 text-primary"></i>Entradas – Faturas</h5>
  <div class="d-flex gap-2">
    <a href="<?= BASE_URL ?>/faturas/exportCsv?<?= htmlspecialchars(http_build_query(array_diff_key($qs, ['p'=>'']))) ?>"
       class="btn btn-sm btn-outline-success"><i class="fas fa-file-csv me-1"></i>Exportar CSV</a>
    <a href="<?= BASE_URL ?>/faturas/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Nova Fatura</a>
  </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" action="<?= BASE_URL ?>/faturas" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label form-label-sm mb-1">Nº Fatura</label>
        <input type="text" name="nr" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['nr'] ?? '') ?>" placeholder="Pesquisar...">
      </div>
      <div class="col-md-2">
        <label class="form-label form-label-sm mb-1">Estado</label>
        <select name="estado" class="form-select form-select-sm">
          <option value="">Todos</option>
          <option value="pendente" <?= ($_GET['estado'] ?? '') === 'pendente' ? 'selected' : '' ?>>Pendente</option>
          <option value="pago"     <?= ($_GET['estado'] ?? '') === 'pago'     ? 'selected' : '' ?>>Pago</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label form-label-sm mb-1">Fornecedor</label>
        <select name="fornecedor_id" class="form-select form-select-sm">
          <option value="">Todos</option>
          <?php foreach ($fornecedores as $fn): ?>
          <option value="<?= $fn['id'] ?>" <?= (int)($_GET['fornecedor_id'] ?? 0) === (int)$fn['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($fn['nome']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label form-label-sm mb-1">De</label>
        <input type="text" name="de" class="form-control form-control-sm fp-date" value="<?= htmlspecialchars($_GET['de'] ?? '') ?>" placeholder="dd/mm/aaaa" autocomplete="off">
      </div>
      <div class="col-md-2">
        <label class="form-label form-label-sm mb-1">Até</label>
        <input type="text" name="ate" class="form-control form-control-sm fp-date" value="<?= htmlspecialchars($_GET['ate'] ?? '') ?>" placeholder="dd/mm/aaaa" autocomplete="off">
      </div>
      <div class="col-auto d-flex gap-1">
        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        <a href="<?= BASE_URL ?>/faturas" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>Nº Fatura</th><th>Fornecedor</th><th>Data</th><th class="text-end">Valor Total</th><th>Estado</th><th class="text-end">Acções</th></tr>
      </thead>
      <tbody>
      <?php if (empty($faturas)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Nenhuma fatura encontrada.</td></tr>
      <?php else: foreach ($faturas as $f): ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($f['nr_fatura']) ?></td>
          <td><?= htmlspecialchars($f['fornecedor_nome']) ?></td>
          <td><?= date('d/m/Y', strtotime($f['data'])) ?></td>
          <td class="text-end"><?= number_format($f['valor_total'], 2, ',', '.') ?> MT</td>
          <td><span class="badge <?= $f['estado'] === 'pago' ? 'bg-success' : 'bg-warning text-dark' ?>"><?= ucfirst($f['estado']) ?></span></td>
          <td class="text-end">
            <a href="<?= BASE_URL ?>/faturas/show/<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-eye"></i></a>
            <?php if (($_SESSION['usuario']['perfil'] ?? '') === 'admin'): ?>
            <button class="btn btn-sm btn-outline-danger"
              data-bs-toggle="modal" data-bs-target="#delModal"
              data-url="<?= BASE_URL ?>/faturas/destroy/<?= $f['id'] ?>"
              data-nome="<?= htmlspecialchars($f['nr_fatura']) ?>">
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
    <div class="modal-body">Eliminar fatura <strong id="delNome"></strong>? Todos os itens serão removidos.</div>
    <div class="modal-footer"><form id="delForm" method="POST">
      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
      <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
    </form></div>
  </div></div>
</div>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/flatpickr.min.css">
<script src="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/flatpickr.min.js"></script>
<script src="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/l10n/pt.js"></script>
<script>
flatpickr('.fp-date', { dateFormat: 'd/m/Y', locale: 'pt', allowInput: true });
document.getElementById('delModal').addEventListener('show.bs.modal', e => {
  document.getElementById('delNome').textContent = e.relatedTarget.dataset.nome;
  document.getElementById('delForm').action = e.relatedTarget.dataset.url;
});
</script>
