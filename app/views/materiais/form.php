<?php $isEdit = $action === 'edit'; ?>
<div class="d-flex align-items-center mb-4">
  <a href="<?= BASE_URL ?>/material" class="btn btn-sm btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
  <h5 class="page-title mb-0"><?= $isEdit ? 'Editar' : 'Novo' ?> Material</h5>
</div>

<div class="card" style="max-width:600px">
  <div class="card-body p-4">
    <?php if (!empty($erro)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/material/<?= $isEdit ? 'update/' . $material['id'] : 'store' ?>" method="POST">
      <?= $csrf ?? '' ?>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
          <input type="text" name="codigo" class="form-control" required
            value="<?= htmlspecialchars($material['codigo'] ?? '') ?>">
        </div>
        <div class="col-md-8">
          <label class="form-label fw-semibold">Descrição <span class="text-danger">*</span></label>
          <input type="text" name="descricao" class="form-control" required
            value="<?= htmlspecialchars($material['descricao'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Unidade</label>
          <select name="unidade" class="form-select">
            <?php foreach (['un','kg','lt','m','m²','cx','pc','rolo','par'] as $u): ?>
            <option value="<?= $u ?>" <?= ($material['unidade'] ?? 'un') === $u ? 'selected' : '' ?>><?= $u ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Preço Unitário Padrão</label>
          <div class="input-group">
            <input type="number" name="preco_unitario_padrao" class="form-control" step="0.01" min="0"
              value="<?= $material['preco_unitario_padrao'] ?? '0.00' ?>">
            <span class="input-group-text">MT</span>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Stock Mínimo</label>
          <input type="number" name="stock_minimo" class="form-control" step="0.01" min="0"
            value="<?= $material['stock_minimo'] ?? '0.00' ?>">
        </div>
        <div class="col-12 d-flex gap-2 pt-2">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
          <a href="<?= BASE_URL ?>/material" class="btn btn-outline-secondary">Cancelar</a>
        </div>
      </div>
    </form>
  </div>
</div>
