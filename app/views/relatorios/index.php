<div class="d-flex align-items-center mb-4">
  <h5 class="page-title mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Relatórios</h5>
</div>

<div class="card" style="max-width:460px">
  <div class="card-header py-2 px-3"><strong>Relatório Mensal</strong></div>
  <div class="card-body p-4">
    <form action="<?= BASE_URL ?>/relatorio/mensal" method="GET" class="row g-3">
      <div class="col-6">
        <label class="form-label fw-semibold">Mês</label>
        <select name="mes" class="form-select">
          <?php
          $meses = ['','Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                    'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
          for ($i = 1; $i <= 12; $i++):
          ?>
          <option value="<?= $i ?>" <?= $mes === $i ? 'selected' : '' ?>><?= $meses[$i] ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-6">
        <label class="form-label fw-semibold">Ano</label>
        <select name="ano" class="form-select">
          <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
          <option value="<?= $y ?>" <?= $ano === $y ? 'selected' : '' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary w-100">
          <i class="fas fa-search me-1"></i>Gerar Relatório
        </button>
      </div>
    </form>
  </div>
</div>
