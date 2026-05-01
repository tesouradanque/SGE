<?php $isEdit = $action === 'edit'; ?>
<div class="d-flex align-items-center mb-4">
  <a href="<?= BASE_URL ?>/fornecedor" class="btn btn-sm btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
  <h5 class="page-title mb-0"><?= $isEdit ? 'Editar' : 'Novo' ?> Fornecedor</h5>
</div>

<div class="card" style="max-width:640px">
  <div class="card-body p-4">
    <?php if (!empty($erro)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/fornecedor/<?= $isEdit ? 'update/' . $fornecedor['id'] : 'store' ?>" method="POST">
      <?= $csrf ?? '' ?>
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
          <input type="text" name="nome" class="form-control" required
            value="<?= htmlspecialchars($fornecedor['nome'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">NUIT</label>
          <input type="text" name="nif" class="form-control"
            value="<?= htmlspecialchars($fornecedor['nif'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Telefone</label>
          <input type="text" name="telefone" class="form-control"
            value="<?= htmlspecialchars($fornecedor['telefone'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Email</label>
          <input type="email" name="email" class="form-control"
            value="<?= htmlspecialchars($fornecedor['email'] ?? '') ?>">
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Endereço</label>
          <textarea name="endereco" class="form-control" rows="2"><?= htmlspecialchars($fornecedor['endereco'] ?? '') ?></textarea>
        </div>
        <div class="col-12 d-flex gap-2 pt-2">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
          <a href="<?= BASE_URL ?>/fornecedor" class="btn btn-outline-secondary">Cancelar</a>
        </div>
      </div>
    </form>
  </div>
</div>
