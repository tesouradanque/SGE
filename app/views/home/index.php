<?php
$fmt = fn($v) => number_format((float)$v, 2, ',', '.');
?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <h5 class="page-title mb-0">Dashboard</h5>
  <span class="text-muted small"><?= date('d/m/Y H:i') ?></span>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">

  <div class="col-sm-6 col-xl-3">
    <div class="card overflow-hidden">
      <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center pb-0 px-3 pt-3">
          <div class="clearfix">
            <h6 class="mb-0">Materiais cadastrados</h6>
            <h3 class="mb-0"><?= $totalMateriais ?></h3>
          </div>
          <div class="avatar avatar-sm avatar-primary border-0">
            <i class="fas fa-cubes"></i>
          </div>
        </div>
        <div class="px-3 pb-3 pt-2">
          <a href="<?= BASE_URL ?>/material" class="text-primary small">Ver materiais &rarr;</a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card overflow-hidden">
      <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center pb-0 px-3 pt-3">
          <div class="clearfix">
            <h6 class="mb-0">Stock abaixo do mínimo</h6>
            <h3 class="mb-0 <?= $stockBaixo > 0 ? 'text-warning' : '' ?>"><?= $stockBaixo ?></h3>
          </div>
          <div class="avatar avatar-sm avatar-warning border-0">
            <i class="fas fa-triangle-exclamation"></i>
          </div>
        </div>
        <div class="px-3 pb-3 pt-2">
          <span class="text-muted small"><?= $stockBaixo > 0 ? 'Requer atenção' : 'Tudo em ordem' ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card overflow-hidden">
      <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center pb-0 px-3 pt-3">
          <div class="clearfix">
            <h6 class="mb-0">Faturas pendentes</h6>
            <h3 class="mb-0 <?= $faturasPendentes > 0 ? 'text-danger' : '' ?>"><?= $faturasPendentes ?></h3>
          </div>
          <div class="avatar avatar-sm avatar-danger border-0">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
        </div>
        <div class="px-3 pb-3 pt-2">
          <a href="<?= BASE_URL ?>/faturas" class="text-danger small">Ver faturas &rarr;</a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card overflow-hidden">
      <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center pb-0 px-3 pt-3">
          <div class="clearfix">
            <h6 class="mb-0">Requisições este mês</h6>
            <h3 class="mb-0"><?= $requisicoesMes ?></h3>
          </div>
          <div class="avatar avatar-sm avatar-success border-0">
            <i class="fas fa-truck-ramp-box"></i>
          </div>
        </div>
        <div class="px-3 pb-3 pt-2">
          <a href="<?= BASE_URL ?>/requisicoes" class="text-success small">Ver requisições &rarr;</a>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="row g-3">

  <!-- Alertas stock baixo -->
  <?php if (!empty($materiaisAlerta)): ?>
  <div class="col-12">
    <div class="card">
      <div class="card-header border-0 pb-0">
        <h4 class="card-title">
          <i class="fas fa-triangle-exclamation me-2 text-warning"></i>Alertas de Stock Baixo
        </h4>
      </div>
      <div class="card-body table-card-body px-0 pt-0 pb-2">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Código</th>
                <th>Material</th>
                <th>Unidade</th>
                <th>Stock Actual</th>
                <th>Mínimo</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($materiaisAlerta as $m): ?>
              <tr>
                <td><code><?= htmlspecialchars($m['codigo']) ?></code></td>
                <td><?= htmlspecialchars($m['descricao']) ?></td>
                <td><?= htmlspecialchars($m['unidade']) ?></td>
                <td class="fw-bold <?= $m['stock_actual'] <= 0 ? 'text-danger' : 'text-warning' ?>">
                  <?= $fmt($m['stock_actual']) ?>
                </td>
                <td><?= $fmt($m['stock_minimo']) ?></td>
                <td>
                  <?php if ($m['stock_actual'] <= 0): ?>
                    <span class="badge badge-out">Esgotado</span>
                  <?php else: ?>
                    <span class="badge badge-low">Stock Baixo</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Últimas faturas -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
          <i class="fas fa-file-invoice me-2 text-primary"></i>Últimas Faturas
        </h4>
        <a href="<?= BASE_URL ?>/faturas" class="btn btn-sm btn-outline-primary">Ver todas</a>
      </div>
      <div class="card-body table-card-body px-0 pt-0 pb-2">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Nº Fatura</th>
                <th>Fornecedor</th>
                <th>Data</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
            <?php if (empty($recentesFaturas)): ?>
              <tr><td colspan="4" class="text-center text-muted py-3">Sem registos</td></tr>
            <?php else: foreach ($recentesFaturas as $f): ?>
              <tr>
                <td>
                  <a href="<?= BASE_URL ?>/faturas/show/<?= $f['id'] ?>"><?= htmlspecialchars($f['nr_fatura']) ?></a>
                </td>
                <td><?= htmlspecialchars($f['fornecedor_nome']) ?></td>
                <td><?= date('d/m/Y', strtotime($f['data'])) ?></td>
                <td>
                  <span class="badge <?= $f['estado'] === 'pago' ? 'badge-ok' : 'badge-low' ?>">
                    <?= ucfirst($f['estado']) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Últimas requisições -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
          <i class="fas fa-truck-ramp-box me-2 text-success"></i>Últimas Requisições
        </h4>
        <a href="<?= BASE_URL ?>/requisicoes" class="btn btn-sm btn-outline-success">Ver todas</a>
      </div>
      <div class="card-body table-card-body px-0 pt-0 pb-2">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Nº Req.</th>
                <th>Funcionário</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
            <?php if (empty($recentesReqs)): ?>
              <tr><td colspan="3" class="text-center text-muted py-3">Sem registos</td></tr>
            <?php else: foreach ($recentesReqs as $r): ?>
              <tr>
                <td>
                  <a href="<?= BASE_URL ?>/requisicoes/show/<?= $r['id'] ?>"><?= htmlspecialchars($r['nr_requisicao']) ?></a>
                </td>
                <td><?= htmlspecialchars($r['funcionario_nome']) ?></td>
                <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
              </tr>
            <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
