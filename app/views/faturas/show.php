<?php
$valorTotal = array_sum(array_map(fn($i) => $i['subtotal'], $fatura['itens']));
?>
<div class="d-flex align-items-center mb-4 gap-3">
  <a href="<?= BASE_URL ?>/faturas" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i></a>
  <h5 class="page-title mb-0">Fatura: <?= htmlspecialchars($fatura['nr_fatura']) ?></h5>
  <span class="badge <?= $fatura['estado'] === 'pago' ? 'bg-success' : 'bg-warning text-dark' ?> ms-1">
    <?= ucfirst($fatura['estado']) ?>
  </span>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-8">
    <div class="card h-100">
      <div class="card-body p-3">
        <div class="row g-2 text-sm">
          <div class="col-sm-6">
            <span class="text-muted small">Fornecedor</span>
            <div class="fw-semibold"><?= htmlspecialchars($fatura['fornecedor_nome']) ?></div>
          </div>
          <div class="col-sm-3">
            <span class="text-muted small">Data</span>
            <div class="fw-semibold"><?= date('d/m/Y', strtotime($fatura['data'])) ?></div>
          </div>
          <div class="col-sm-3">
            <span class="text-muted small">Registada em</span>
            <div class="fw-semibold"><?= date('d/m/Y', strtotime($fatura['created_at'])) ?></div>
          </div>
          <?php if ($fatura['observacao']): ?>
          <div class="col-12">
            <span class="text-muted small">Observação</span>
            <div><?= htmlspecialchars($fatura['observacao']) ?></div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100 text-center bg-primary text-white">
      <div class="card-body d-flex flex-column justify-content-center">
        <div class="small opacity-75">Valor Total da Fatura</div>
        <div class="fs-3 fw-bold"><?= number_format($valorTotal, 2, ',', '.') ?> MT</div>
        <div class="mt-3">
          <form action="<?= BASE_URL ?>/faturas/estado/<?= $fatura['id'] ?>" method="POST" class="d-flex justify-content-center gap-2">
            <select name="estado" class="form-select form-select-sm w-auto text-dark">
              <option value="pendente" <?= $fatura['estado'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
              <option value="pago"     <?= $fatura['estado'] === 'pago'     ? 'selected' : '' ?>>Pago</option>
            </select>
            <button type="submit" class="btn btn-sm btn-light text-primary fw-semibold">Actualizar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header py-2 px-3"><strong>Itens</strong></div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>#</th><th>Material</th><th>Unidade</th><th class="text-end">Quantidade</th><th class="text-end">Preço Unit.</th><th class="text-end">Subtotal</th></tr></thead>
      <tbody>
      <?php $n=0; foreach ($fatura['itens'] as $item): $n++; ?>
        <tr>
          <td class="text-muted"><?= $n ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($item['material_nome']) ?></td>
          <td><?= htmlspecialchars($item['unidade']) ?></td>
          <td class="text-end"><?= number_format($item['quantidade'], 2, ',', '.') ?></td>
          <td class="text-end"><?= number_format($item['preco_unitario'], 2, ',', '.') ?> MT</td>
          <td class="text-end fw-semibold"><?= number_format($item['subtotal'], 2, ',', '.') ?> MT</td>
        </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr class="table-light">
          <td colspan="5" class="text-end fw-bold">TOTAL</td>
          <td class="text-end fw-bold text-primary"><?= number_format($valorTotal, 2, ',', '.') ?> MT</td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
