<div class="d-flex align-items-center mb-4">
  <a href="<?= BASE_URL ?>/requisicoes" class="btn btn-sm btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
  <h5 class="page-title mb-0">Nova Requisição</h5>
</div>

<?php if (!empty($erro)): ?>
  <div class="alert alert-danger"><i class="fas fa-circle-exclamation me-2"></i><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>/requisicoes/store" method="POST">
<div class="card mb-3">
  <div class="card-header py-2 px-3"><strong>Dados da Requisição</strong></div>
  <div class="card-body p-3">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label fw-semibold">Nº Requisição <span class="text-danger">*</span></label>
        <input type="text" name="nr_requisicao" class="form-control" required placeholder="ex: REQ-001">
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Data <span class="text-danger">*</span></label>
        <input type="text" name="data" id="req-data" class="form-control" required
          placeholder="dd/mm/aaaa" autocomplete="off" value="<?= date('d/m/Y') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Funcionário (Motorista) <span class="text-danger">*</span></label>
        <select name="funcionario_id" class="form-select" required>
          <option value="">Seleccione...</option>
          <?php foreach ($funcionarios as $f): ?>
          <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?> <?= $f['cargo'] ? '– ' . htmlspecialchars($f['cargo']) : '' ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-12">
        <label class="form-label fw-semibold">Observação</label>
        <input type="text" name="observacao" class="form-control" placeholder="Opcional">
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
    <strong>Materiais Requisitados</strong>
    <button type="button" id="btn-add" class="btn btn-sm btn-outline-success">
      <i class="fas fa-plus me-1"></i>Adicionar Material
    </button>
  </div>
  <div class="table-responsive">
    <table class="table mb-0" id="items-table">
      <thead>
        <tr>
          <th style="width:38%">Material</th>
          <th style="width:16%">Stock Disponível</th>
          <th style="width:12%">Qtd.</th>
          <th style="width:16%">Preço Unit. (MT)</th>
          <th style="width:12%">Total (MT)</th>
          <th style="width:6%"></th>
        </tr>
      </thead>
      <tbody id="items-body"></tbody>
      <tfoot>
        <tr>
          <td colspan="4" class="text-end fw-bold">TOTAL GERAL:</td>
          <td class="fw-bold text-success" id="grand-total">0,00</td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<div class="d-flex gap-2">
  <button type="submit" class="btn btn-success px-4"><i class="fas fa-save me-1"></i>Registar Requisição</button>
  <a href="<?= BASE_URL ?>/requisicoes" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>

<script>
const MATERIAIS  = <?= json_encode(array_values($materiais)) ?>;
const STOCK_MAP  = <?= json_encode($stockMap) ?>;
let idx = 0;

function addRow() {
  idx++;
  const opts = MATERIAIS.map(m =>
    `<option value="${m.id}" data-preco="${m.preco_unitario_padrao}">${m.codigo} – ${m.descricao}</option>`
  ).join('');

  const tr = document.createElement('tr');
  tr.className = 'item-row';
  tr.id = `row-${idx}`;
  tr.innerHTML = `
    <td>
      <select name="itens[${idx}][material_id]" class="form-select form-select-sm" required onchange="onMatChange(this,${idx})">
        <option value="">Seleccione...</option>${opts}
      </select>
    </td>
    <td>
      <span id="stk-${idx}" class="badge bg-secondary">—</span>
    </td>
    <td>
      <input type="number" name="itens[${idx}][quantidade]" id="qty-${idx}" class="form-control form-control-sm"
        step="0.01" min="0.01" required oninput="calcRow(${idx})" disabled>
    </td>
    <td>
      <input type="number" name="itens[${idx}][preco_unitario]" id="pu-${idx}" class="form-control form-control-sm"
        step="0.01" min="0" required oninput="calcRow(${idx})" disabled>
    </td>
    <td><input type="text" id="tot-${idx}" class="form-control form-control-sm bg-light fw-semibold" readonly></td>
    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(${idx})"><i class="fas fa-times"></i></button></td>`;
  document.getElementById('items-body').appendChild(tr);
}

function onMatChange(sel, i) {
  const matId = parseInt(sel.value);
  const opt   = sel.options[sel.selectedIndex];
  const stock = STOCK_MAP[matId] ?? 0;
  const badge = document.getElementById(`stk-${i}`);
  const qtyEl = document.getElementById(`qty-${i}`);
  const puEl  = document.getElementById(`pu-${i}`);

  if (!matId) { badge.textContent='—'; badge.className='badge bg-secondary'; qtyEl.disabled=true; puEl.disabled=true; return; }

  badge.textContent = fmt(stock);
  badge.className   = stock <= 0 ? 'badge bg-danger' : (stock < 5 ? 'badge bg-warning text-dark' : 'badge bg-success');
  qtyEl.disabled    = stock <= 0;
  puEl.disabled     = false;
  qtyEl.max         = stock;
  puEl.value        = parseFloat(opt.dataset.preco || 0).toFixed(2);
  calcRow(i);
}

function calcRow(i) {
  const q    = parseFloat(document.getElementById(`qty-${i}`)?.value) || 0;
  const p    = parseFloat(document.getElementById(`pu-${i}`)?.value)  || 0;
  const matSel = document.querySelector(`#row-${i} select`);
  const matId  = parseInt(matSel?.value);
  const stock  = STOCK_MAP[matId] ?? 0;
  const qtyEl  = document.getElementById(`qty-${i}`);

  if (q > stock) {
    qtyEl.classList.add('is-invalid');
  } else {
    qtyEl.classList.remove('is-invalid');
  }
  document.getElementById(`tot-${i}`).value = fmt(q * p);
  updateGrand();
}

function removeRow(i) { document.getElementById(`row-${i}`)?.remove(); updateGrand(); }

function updateGrand() {
  let t = 0;
  document.querySelectorAll('[id^="tot-"]').forEach(el => {
    t += parseFloat(el.value.replace(/\./g,'').replace(',','.')) || 0;
  });
  document.getElementById('grand-total').textContent = fmt(t);
}

function fmt(n) {
  return parseFloat(n).toLocaleString('pt-AO', {minimumFractionDigits:2, maximumFractionDigits:2});
}

document.getElementById('btn-add').addEventListener('click', addRow);
addRow();
</script>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/flatpickr.min.css">
<script src="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/flatpickr.min.js"></script>
<script src="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/l10n/pt.js"></script>
<script>
flatpickr('#req-data', {
  dateFormat: 'd/m/Y',
  locale: 'pt',
  allowInput: true,
  defaultDate: '<?= date('d/m/Y') ?>'
});
</script>
