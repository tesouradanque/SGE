<?php $isEdit = $action === 'edit'; ?>
<div class="d-flex align-items-center mb-4">
  <a href="<?= BASE_URL ?>/usuario" class="btn btn-sm btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
  <h5 class="page-title mb-0"><?= $isEdit ? 'Editar' : 'Novo' ?> Utilizador</h5>
</div>

<div class="card" style="max-width:560px">
  <div class="card-body p-4">
    <?php if (!empty($erro)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/usuarios/<?= $isEdit ? 'update/' . $usuario['id'] : 'store' ?>" method="POST">
      <?= $csrf ?>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
          <input type="text" name="nome" class="form-control" required
            value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>">
        </div>
        <div class="col-md-8">
          <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" required
            value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Perfil</label>
          <select name="perfil" class="form-select">
            <option value="operador" <?= ($usuario['perfil'] ?? 'operador') === 'operador' ? 'selected' : '' ?>>Operador</option>
            <option value="admin"    <?= ($usuario['perfil'] ?? '') === 'admin'    ? 'selected' : '' ?>>Admin</option>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">
            Password <?= $isEdit ? '<small class="text-muted fw-normal">(deixar em branco para manter)</small>' : '<span class="text-danger">*</span>' ?>
          </label>
          <input type="password" name="senha" class="form-control" <?= $isEdit ? '' : 'required' ?>
            placeholder="<?= $isEdit ? 'Nova password (opcional)' : 'Mínimo 6 caracteres' ?>"
            autocomplete="new-password" minlength="6">
        </div>
        <?php if ($isEdit): ?>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Estado</label>
          <select name="ativo" class="form-select">
            <option value="1" <?= ($usuario['ativo'] ?? 1) ? 'selected' : '' ?>>Activo</option>
            <option value="0" <?= !($usuario['ativo'] ?? 1) ? 'selected' : '' ?>>Inactivo</option>
          </select>
        </div>
        <?php endif; ?>
        <div class="col-12 d-flex gap-2 pt-2">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
          <a href="<?= BASE_URL ?>/usuario" class="btn btn-outline-secondary">Cancelar</a>
        </div>
      </div>
    </form>
  </div>
</div>
