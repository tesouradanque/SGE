<div class="d-flex align-items-center mb-4">
  <a href="<?= BASE_URL ?>/faturas" class="btn btn-sm btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
  <h5 class="page-title mb-0">Nova Fatura</h5>
</div>

<?php if (!empty($erro)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<form action="<?= BASE_URL ?>/faturas/store" method="POST">
<?= $csrf ?? '' ?>
<div class="card mb-3">
  <div class="card-header py-2 px-3"><strong>Dados da Fatura</strong></div>
  <div class="card-body p-3">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label fw-semibold">Nº Fatura <span class="text-danger">*</span></label>
        <input type="text" name="nr_fatura" class="form-control" required placeholder="ex: FAT-001">
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Data <span class="text-danger">*</span></label>
        <input type="text" name="data" id="fat-data" class="form-control" required
          placeholder="dd/mm/aaaa" autocomplete="off" value="<?= date('d/m/Y') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Fornecedor <span class="text-danger">*</span></label>
        <select name="fornecedor_id" class="form-select" required>
          <option value="">Seleccione...</option>
          <?php foreach ($fornecedores as $f): ?>
          <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Estado</label>
        <select name="estado" class="form-select">
          <option value="pendente">Pendente</option>
          <option value="pago">Pago</option>
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
    <strong>Itens da Fatura</strong>
    <button type="button" id="btn-add" class="btn btn-sm btn-outline-primary">
      <i class="fas fa-plus me-1"></i>Adicionar Item
    </button>
  </div>
  <div class="table-responsive">
    <table class="table mb-0" id="items-table">
      <thead>
        <tr>
          <th style="width:40%">Material</th>
          <th style="width:12%">Qtd.</th>
          <th style="width:18%">Preço Unit. (MT)</th>
          <th style="width:18%">Total (MT)</th>
          <th style="width:8%"></th>
        </tr>
      </thead>
      <tbody id="items-body"></tbody>
      <tfoot>
        <tr>
          <td colspan="3" class="text-end fw-bold">TOTAL GERAL:</td>
          <td class="fw-bold text-primary" id="grand-total">0,00 MT</td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<div class="d-flex gap-2">
  <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i>Guardar Fatura</button>
  <a href="<?= BASE_URL ?>/faturas" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>

<script>
const MATERIAIS = <?= json_encode(array_values($materiais)) ?>;
const UNIDADES  = ['un','kg','lt','m','m²','cx','pc','rolo','par'];
let idx = 0;

function addRow() {
  idx++;
  const i = idx;
  const opts = MATERIAIS.map(m =>
    `<option value="${m.id}" data-preco="${m.preco_unitario_padrao}">${m.codigo} – ${m.descricao}</option>`
  ).join('');
  const uOpts = UNIDADES.map(u => `<option value="${u}">${u}</option>`).join('');

  const tr = document.createElement('tr');
  tr.className = 'item-row';
  tr.id = `row-${i}`;
  tr.innerHTML = `
    <td>
      <select name="itens[${i}][material_id]" class="form-select form-select-sm" required onchange="onMatSelect(this,${i})">
        <option value="">Seleccione...</option>${opts}
        <option value="novo">➕ Novo material (não listado)...</option>
      </select>
      <div id="novo-panel-${i}" class="mt-2 p-2 border rounded bg-light" style="display:none">
        <div class="row g-1">
          <div class="col-6">
            <input type="text" name="itens[${i}][material_novo_descricao]" class="form-control form-control-sm"
              placeholder="Descrição *">
          </div>
          <div class="col-3">
            <input type="text" name="itens[${i}][material_novo_codigo]" class="form-control form-control-sm"
              placeholder="Código">
          </div>
          <div class="col-3">
            <select name="itens[${i}][material_novo_unidade]" class="form-select form-select-sm">${uOpts}</select>
          </div>
        </div>
      </div>
    </td>
    <td><input type="number" name="itens[${i}][quantidade]" id="qty-${i}" class="form-control form-control-sm"
          step="0.01" min="0.01" required oninput="calcRow(${i})"></td>
    <td><input type="number" name="itens[${i}][preco_unitario]" id="pu-${i}" class="form-control form-control-sm"
          step="0.01" min="0" required oninput="calcRow(${i})"></td>
    <td><input type="text" id="tot-${i}" class="form-control form-control-sm bg-light fw-semibold" readonly></td>
    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(${i})">
          <i class="fas fa-times"></i></button></td>`;
  document.getElementById('items-body').appendChild(tr);
}

function onMatSelect(sel, i) {
  const panel = document.getElementById(`novo-panel-${i}`);
  const isNovo = sel.value === 'novo';
  panel.style.display = isNovo ? '' : 'none';
  const descInput = panel.querySelector(`[name="itens[${i}][material_novo_descricao]"]`);
  if (descInput) descInput.required = isNovo;
  if (!isNovo) {
    const opt = sel.options[sel.selectedIndex];
    document.getElementById(`pu-${i}`).value = parseFloat(opt.dataset.preco || 0).toFixed(2);
    calcRow(i);
  } else {
    document.getElementById(`pu-${i}`).value = '';
    document.getElementById(`tot-${i}`).value = '';
    updateGrand();
  }
}

function calcRow(i) {
  const q = parseFloat(document.getElementById(`qty-${i}`)?.value) || 0;
  const p = parseFloat(document.getElementById(`pu-${i}`)?.value)  || 0;
  document.getElementById(`tot-${i}`).value = fmt(q * p);
  updateGrand();
}

function removeRow(i) {
  document.getElementById(`row-${i}`)?.remove();
  updateGrand();
}

function updateGrand() {
  let t = 0;
  document.querySelectorAll('[id^="tot-"]').forEach(el => t += parseFloat(el.value.replace(',','.')) || 0);
  document.getElementById('grand-total').textContent = fmt(t) + ' MT';
}

function fmt(n) {
  return n.toLocaleString('pt-AO', {minimumFractionDigits:2, maximumFractionDigits:2});
}

document.getElementById('btn-add').addEventListener('click', addRow);
addRow();
</script>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/flatpickr.min.css">
<script src="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/flatpickr.min.js"></script>
<script src="<?= BASE_URL ?>/public/theme/vendor/flatpickr/dist/l10n/pt.js"></script>
<script>
flatpickr('#fat-data', {
  dateFormat: 'd/m/Y',
  locale: 'pt',
  allowInput: true,
  defaultDate: '<?= date('d/m/Y') ?>'
});
</script>
