<?php $qs = $_GET; ?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-truck-ramp-box me-2 text-success"></i>Saídas – Requisições</h5>
  <div class="d-flex gap-2">
    <?php $qs_exp = http_build_query(array_diff_key($qs, ['p'=>'','url'=>''])); ?>
    <a href="<?= BASE_URL ?>/requisicoes/exportCsv<?= $qs_exp ? '?'.$qs_exp : '' ?>"
       class="btn btn-sm btn-outline-success"><i class="fas fa-file-csv me-1"></i>CSV</a>
    <a href="<?= BASE_URL ?>/requisicoes/exportXlsx<?= $qs_exp ? '?'.$qs_exp : '' ?>"
       class="btn btn-sm btn-outline-success"><i class="fas fa-file-excel me-1"></i>Excel</a>
    <a href="<?= BASE_URL ?>/requisicoes/create" class="btn btn-success"><i class="fas fa-plus me-1"></i>Nova Requisição</a>
  </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" action="<?= BASE_URL ?>/requisicoes" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label form-label-sm mb-1">Nº Requisição</label>
        <input type="text" name="nr" class="form-control form-control-sm" value="<?= htmlspecialchars($_GET['nr'] ?? '') ?>" placeholder="Pesquisar...">
      </div>
      <div class="col-md-3">
        <label class="form-label form-label-sm mb-1">Funcionário</label>
        <select name="funcionario_id" class="form-select form-select-sm">
          <option value="">Todos</option>
          <?php foreach ($funcionarios as $fn): ?>
          <option value="<?= $fn['id'] ?>" <?= (int)($_GET['funcionario_id'] ?? 0) === (int)$fn['id'] ? 'selected' : '' ?>>
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
        <a href="<?= BASE_URL ?>/requisicoes" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>Nº Req.</th><th>Funcionário</th><th>Data</th><th class="text-end">Itens</th><th class="text-end">Valor Total</th><th class="text-end">Acções</th></tr>
      </thead>
      <tbody>
      <?php if (empty($requisicoes)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">Nenhuma requisição encontrada.</td></tr>
      <?php else: foreach ($requisicoes as $r): ?>
        <tr>
          <td class="fw-semibold"><?= htmlspecialchars($r['nr_requisicao']) ?></td>
          <td><?= htmlspecialchars($r['funcionario_nome']) ?></td>
          <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
          <td class="text-end"><?= $r['nr_itens'] ?></td>
          <td class="text-end"><?= number_format($r['valor_total'], 2, ',', '.') ?> MT</td>
          <td class="text-end">
            <a href="<?= BASE_URL ?>/requisicoes/show/<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-eye"></i></a>
            <?php if (($_SESSION['usuario']['perfil'] ?? '') === 'admin'): ?>
            <button class="btn btn-sm btn-outline-danger"
              data-bs-toggle="modal" data-bs-target="#delModal"
              data-url="<?= BASE_URL ?>/requisicoes/destroy/<?= $r['id'] ?>"
              data-nome="<?= htmlspecialchars($r['nr_requisicao']) ?>">
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
    <div class="modal-body">Eliminar requisição <strong id="delNome"></strong>? O stock será restaurado.</div>
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
