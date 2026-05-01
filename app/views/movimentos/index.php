<?php $fmt = fn($v) => number_format((float)$v, 2, ',', '.'); ?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-arrows-up-down me-2 text-info"></i>Histórico de Movimentos</h5>
</div>

<!-- Resumo -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card overflow-hidden">
      <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center pb-0 px-3 pt-3">
          <div class="clearfix">
            <h6 class="mb-0">Total Entradas</h6>
            <h3 class="mb-0 text-success"><?= $fmt($totalEntradas) ?></h3>
          </div>
          <div class="avatar avatar-sm avatar-success border-0"><i class="fas fa-arrow-down"></i></div>
        </div>
        <div class="px-3 pb-3 pt-1"><small class="text-muted">unidades recebidas (período)</small></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card overflow-hidden">
      <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center pb-0 px-3 pt-3">
          <div class="clearfix">
            <h6 class="mb-0">Total Saídas</h6>
            <h3 class="mb-0 text-danger"><?= $fmt($totalSaidas) ?></h3>
          </div>
          <div class="avatar avatar-sm avatar-danger border-0"><i class="fas fa-arrow-up"></i></div>
        </div>
        <div class="px-3 pb-3 pt-1"><small class="text-muted">unidades requisitadas (período)</small></div>
      </div>
    </div>
  </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" action="<?= BASE_URL ?>/movimentos" class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label form-label-sm mb-1">Material</label>
        <select name="material_id" class="form-select form-select-sm">
          <option value="">Todos</option>
          <?php foreach ($materiais as $m): ?>
          <option value="<?= $m['id'] ?>" <?= (int)($_GET['material_id'] ?? 0) === (int)$m['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($m['codigo'] . ' – ' . $m['descricao']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label form-label-sm mb-1">Tipo</label>
        <select name="tipo" class="form-select form-select-sm">
          <option value="">Todos</option>
          <option value="entrada" <?= ($_GET['tipo'] ?? '') === 'entrada' ? 'selected' : '' ?>>Entradas</option>
          <option value="saida"   <?= ($_GET['tipo'] ?? '') === 'saida'   ? 'selected' : '' ?>>Saídas</option>
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
        <a href="<?= BASE_URL ?>/movimentos" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Tipo</th>
          <th>Data</th>
          <th>Referência</th>
          <th>Origem / Destino</th>
          <th>Material</th>
          <th class="text-end">Qtd.</th>
          <th class="text-end">Preço Unit.</th>
          <th class="text-end">Subtotal</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($movimentos)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">Nenhum movimento encontrado.</td></tr>
      <?php else: foreach ($movimentos as $r):
            $isEntrada = $r['tipo'] === 'entrada'; ?>
        <tr>
          <td>
            <span class="badge <?= $isEntrada ? 'bg-success' : 'bg-danger' ?>">
              <i class="fas <?= $isEntrada ? 'fa-arrow-down' : 'fa-arrow-up' ?> me-1"></i>
              <?= $isEntrada ? 'Entrada' : 'Saída' ?>
            </span>
          </td>
          <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($r['referencia']) ?></td>
          <td class="text-muted small">
            <?= htmlspecialchars($isEntrada ? ($r['fornecedor'] ?? '—') : ($r['funcionario'] ?? '—')) ?>
          </td>
          <td><?= htmlspecialchars($r['material']) ?> <small class="text-muted">(<?= htmlspecialchars($r['unidade']) ?>)</small></td>
          <td class="text-end <?= $isEntrada ? 'text-success' : 'text-danger' ?>">
            <?= $isEntrada ? '+' : '-' ?><?= $fmt($r['quantidade']) ?>
          </td>
          <td class="text-end"><?= $fmt($r['preco_unitario']) ?> MT</td>
          <td class="text-end fw-semibold"><?= $fmt($r['subtotal']) ?> MT</td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include APP_PATH . '/views/partials/pagination.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/flatpickr.min.css">
<script src="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/flatpickr.min.js"></script>
<script src="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/l10n/pt.js"></script>
<script>flatpickr('.fp-date', { dateFormat: 'd/m/Y', locale: 'pt', allowInput: true });</script>
