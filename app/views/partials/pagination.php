<?php
// $pag = ['page','totalPages','total','perPage']
// $baseUrl = URL base sem ?p=
if (!isset($pag) || $pag['totalPages'] <= 1) return;

$qs = $_GET;
$mk = function(int $p) use ($qs): string {
    $qs['p'] = $p;
    return '?' . http_build_query($qs);
};
?>
<div class="d-flex align-items-center justify-content-between mt-3 px-1">
  <small class="text-muted">
    <?= number_format($pag['total']) ?> registos &mdash;
    página <?= $pag['page'] ?> de <?= $pag['totalPages'] ?>
  </small>
  <nav aria-label="Paginação">
    <ul class="pagination pagination-sm mb-0">
      <li class="page-item <?= $pag['page'] <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= htmlspecialchars($mk(1)) ?>">&laquo;</a>
      </li>
      <li class="page-item <?= $pag['page'] <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= htmlspecialchars($mk($pag['page'] - 1)) ?>">&lsaquo;</a>
      </li>

      <?php
      $start = max(1, $pag['page'] - 2);
      $end   = min($pag['totalPages'], $pag['page'] + 2);
      for ($i = $start; $i <= $end; $i++): ?>
        <li class="page-item <?= $i === $pag['page'] ? 'active' : '' ?>">
          <a class="page-link" href="<?= htmlspecialchars($mk($i)) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <li class="page-item <?= $pag['page'] >= $pag['totalPages'] ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= htmlspecialchars($mk($pag['page'] + 1)) ?>">&rsaquo;</a>
      </li>
      <li class="page-item <?= $pag['page'] >= $pag['totalPages'] ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= htmlspecialchars($mk($pag['totalPages'])) ?>">&raquo;</a>
      </li>
    </ul>
  </nav>
</div>
