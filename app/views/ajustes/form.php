<?php $fmt = fn($v) => number_format((float)$v, 2, ',', '.'); ?>
<div class="d-flex align-items-center mb-4">
  <a href="<?= BASE_URL ?>/ajuste" class="btn btn-sm btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
  <h5 class="page-title mb-0"><i class="fas fa-sliders me-2 text-warning"></i>Novo Ajuste de Stock</h5>
</div>

<div class="alert alert-warning py-2">
  <i class="fas fa-triangle-exclamation me-2"></i>
  Use apenas para corrigir diferenças entre a contagem física e o sistema. Esta operação é auditada.
</div>

<div class="card" style="max-width:640px">
  <div class="card-body p-4">
    <?php if (!empty($erro)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/ajuste/store" method="POST" id="ajusteForm">
      <?= $csrf ?? '' ?>

      <div class="mb-3">
        <label class="form-label fw-semibold">Material <span class="text-danger">*</span></label>
        <select name="material_id" class="form-select" required id="selMaterial" onchange="atualizarStock(this)">
          <option value="">— Seleccione —</option>
          <?php foreach ($materiais as $m): ?>
          <option value="<?= $m['id'] ?>"
                  data-stock="<?= (float)$m['stock_actual'] ?>"
                  data-unidade="<?= htmlspecialchars($m['unidade']) ?>"
                  <?= (int)($material_id ?? 0) === (int)$m['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($m['codigo'] . ' – ' . $m['descricao']) ?>
            (<?= $fmt($m['stock_actual']) ?> <?= htmlspecialchars($m['unidade']) ?>)
          </option>
          <?php endforeach; ?>
        </select>
        <div id="stockInfo" class="form-text text-muted mt-1" style="display:none">
          Stock actual: <strong id="stockVal">—</strong>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-sm-6">
          <label class="form-label fw-semibold">Tipo de Ajuste <span class="text-danger">*</span></label>
          <select name="tipo" class="form-select" required id="selTipo" onchange="atualizarTipo(this)">
            <option value="">— Seleccione —</option>
            <option value="entrada" <?= ($tipo ?? '') === 'entrada' ? 'selected' : '' ?>>
              ↓ Entrada (aumentar stock)
            </option>
            <option value="saida" <?= ($tipo ?? '') === 'saida' ? 'selected' : '' ?>>
              ↑ Saída (reduzir stock)
            </option>
          </select>
        </div>
        <div class="col-sm-6">
          <label class="form-label fw-semibold">Quantidade <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="number" name="quantidade" id="inpQty" class="form-control" step="0.01" min="0.01" required
              value="<?= htmlspecialchars($quantidade ?? '') ?>" placeholder="0.00">
            <span class="input-group-text" id="unLabel">—</span>
          </div>
          <div id="stockAlert" class="form-text text-danger" style="display:none">
            <i class="fas fa-exclamation-triangle me-1"></i>Quantidade excede o stock disponível.
          </div>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label fw-semibold">Motivo / Justificação <span class="text-danger">*</span></label>
        <textarea name="motivo" class="form-control" rows="3" required
          placeholder="Ex: Inventário físico realizado em 01/05/2026 — diferença detectada..."><?= htmlspecialchars($motivo ?? '') ?></textarea>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save me-1"></i>Registar Ajuste</button>
        <a href="<?= BASE_URL ?>/ajuste" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<script>
function atualizarStock(sel) {
  var opt = sel.options[sel.selectedIndex];
  var stock = parseFloat(opt.dataset.stock || 0);
  var un = opt.dataset.unidade || '—';
  document.getElementById('stockVal').textContent = stock.toLocaleString('pt', {minimumFractionDigits:2}) + ' ' + un;
  document.getElementById('stockInfo').style.display = opt.value ? '' : 'none';
  document.getElementById('unLabel').textContent = un;
  validarQty();
}

function atualizarTipo() { validarQty(); }

function validarQty() {
  var sel   = document.getElementById('selMaterial');
  var opt   = sel.options[sel.selectedIndex];
  var stock = parseFloat(opt.dataset.stock || 0);
  var tipo  = document.getElementById('selTipo').value;
  var qty   = parseFloat(document.getElementById('inpQty').value || 0);
  var alerta = document.getElementById('stockAlert');
  alerta.style.display = (tipo === 'saida' && qty > stock) ? '' : 'none';
}

document.getElementById('inpQty').addEventListener('input', validarQty);

// Init from server-side preselected value
window.addEventListener('DOMContentLoaded', function() {
  var sel = document.getElementById('selMaterial');
  if (sel.value) atualizarStock(sel);
});
</script>
