<?php
$fmt   = fn($v) => number_format((float)$v, 2, ',', '.');
$hoje  = date('d/m/Y H:i');
$titulo = "Relatório Mensal – {$nomeMes} {$ano}";
?><!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($titulo) ?></title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 11px; color: #222; background: #fff; padding: 20px; }
    h1 { font-size: 16px; margin-bottom: 4px; }
    .meta { color: #666; font-size: 10px; margin-bottom: 20px; }
    h2 { font-size: 12px; font-weight: bold; background: #f0f0f0; padding: 5px 8px; margin: 18px 0 6px; border-left: 3px solid #555; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th { background: #e8e8e8; font-weight: bold; text-align: left; padding: 5px 7px; border: 1px solid #ccc; font-size: 10px; }
    td { padding: 4px 7px; border: 1px solid #ddd; vertical-align: top; }
    tr:nth-child(even) td { background: #fafafa; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .total-row td { font-weight: bold; background: #f5f5f5; }
    .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 10px; }
    .badge-pago { background: #d4edda; color: #155724; }
    .badge-pendente { background: #fff3cd; color: #856404; }
    .badge-ok  { background: #d4edda; color: #155724; }
    .badge-low { background: #fff3cd; color: #856404; }
    .badge-out { background: #f8d7da; color: #721c24; }
    .text-danger { color: #c00; }
    .footer { margin-top: 24px; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 8px; }
    @media print {
      body { padding: 10px; }
      @page { margin: 15mm 12mm; }
    }
  </style>
</head>
<body>
  <h1><?= htmlspecialchars($titulo) ?></h1>
  <div class="meta">Gerado em <?= $hoje ?> &nbsp;|&nbsp; SGE – Sistema de Gestão de Estoque</div>

  <!-- 1. Faturas -->
  <h2>Faturas – <?= $nomeMes ?> <?= $ano ?></h2>
  <table>
    <thead><tr><th>Nº Fatura</th><th>Fornecedor</th><th>Data</th><th class="text-right">Valor Total (MT)</th><th class="text-center">Estado</th></tr></thead>
    <tbody>
    <?php if (empty($faturas)): ?>
      <tr><td colspan="5" class="text-center">Sem faturas neste período.</td></tr>
    <?php else:
      $totalFat = 0;
      foreach ($faturas as $f): $totalFat += $f['valor_total']; ?>
      <tr>
        <td><?= htmlspecialchars($f['nr_fatura']) ?></td>
        <td><?= htmlspecialchars($f['fornecedor_nome']) ?></td>
        <td><?= date('d/m/Y', strtotime($f['data'])) ?></td>
        <td class="text-right"><?= $fmt($f['valor_total']) ?></td>
        <td class="text-center"><span class="badge <?= $f['estado']==='pago' ? 'badge-pago' : 'badge-pendente' ?>"><?= ucfirst($f['estado']) ?></span></td>
      </tr>
    <?php endforeach; ?>
      <tr class="total-row"><td colspan="3" class="text-right">TOTAL</td><td class="text-right"><?= $fmt($totalFat) ?></td><td></td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <!-- 2. Requisições -->
  <h2>Requisições – <?= $nomeMes ?> <?= $ano ?></h2>
  <table>
    <thead><tr><th>Nº Requisição</th><th>Funcionário</th><th>Data</th><th class="text-right">Valor Total (MT)</th></tr></thead>
    <tbody>
    <?php if (empty($requisicoes)): ?>
      <tr><td colspan="4" class="text-center">Sem requisições neste período.</td></tr>
    <?php else:
      $totalReq = 0;
      foreach ($requisicoes as $r): $totalReq += $r['valor_total']; ?>
      <tr>
        <td><?= htmlspecialchars($r['nr_requisicao']) ?></td>
        <td><?= htmlspecialchars($r['funcionario_nome']) ?></td>
        <td><?= date('d/m/Y', strtotime($r['data'])) ?></td>
        <td class="text-right"><?= $fmt($r['valor_total']) ?></td>
      </tr>
    <?php endforeach; ?>
      <tr class="total-row"><td colspan="3" class="text-right">TOTAL</td><td class="text-right"><?= $fmt($totalReq) ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>

  <!-- 3. Materiais por funcionário -->
  <h2>Materiais Requisitados por Funcionário</h2>
  <table>
    <thead><tr><th>Funcionário</th><th>Material</th><th>Un.</th><th class="text-right">Qtd.</th><th class="text-right">Valor (MT)</th></tr></thead>
    <tbody>
    <?php if (empty($materiaisPorFuncionario)): ?>
      <tr><td colspan="5" class="text-center">Sem movimentos neste período.</td></tr>
    <?php else:
      $prevFunc = null;
      foreach ($materiaisPorFuncionario as $row):
        $isNew = $row['funcionario'] !== $prevFunc;
        $prevFunc = $row['funcionario']; ?>
      <tr>
        <td><?= $isNew ? '<strong>' . htmlspecialchars($row['funcionario']) . '</strong>' : '' ?></td>
        <td><?= htmlspecialchars($row['material']) ?></td>
        <td><?= htmlspecialchars($row['unidade']) ?></td>
        <td class="text-right"><?= $fmt($row['quantidade_total']) ?></td>
        <td class="text-right"><?= $fmt($row['valor_total']) ?></td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>

  <!-- 4. Por fornecedor -->
  <h2>Faturas a Pagar por Fornecedor</h2>
  <table>
    <thead><tr><th>Fornecedor</th><th class="text-right">Nº Faturas</th><th class="text-right">Total (MT)</th><th class="text-right">Pendente (MT)</th></tr></thead>
    <tbody>
    <?php foreach ($totalPorFornecedor as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['nome']) ?></td>
        <td class="text-right"><?= $row['nr_faturas'] ?></td>
        <td class="text-right"><?= $fmt($row['total_valor']) ?></td>
        <td class="text-right <?= $row['pendente'] > 0 ? 'text-danger' : '' ?>"><?= $fmt($row['pendente']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <!-- 5. Stock actual -->
  <h2>Stock Actual</h2>
  <table>
    <thead><tr><th>Código</th><th>Material</th><th>Un.</th><th class="text-right">Entradas</th><th class="text-right">Saídas</th><th class="text-right">Stock</th><th class="text-center">Estado</th></tr></thead>
    <tbody>
    <?php foreach ($stockActual as $m):
      $s = (float)$m['stock_actual']; $min = (float)$m['stock_minimo'];
      if ($s <= 0)      { $bc = 'badge-out'; $lb = 'Esgotado'; }
      elseif ($s<=$min) { $bc = 'badge-low'; $lb = 'Baixo'; }
      else              { $bc = 'badge-ok';  $lb = 'OK'; }
    ?>
      <tr>
        <td><?= htmlspecialchars($m['codigo']) ?></td>
        <td><?= htmlspecialchars($m['descricao']) ?></td>
        <td><?= htmlspecialchars($m['unidade']) ?></td>
        <td class="text-right"><?= $fmt($m['total_entradas']) ?></td>
        <td class="text-right"><?= $fmt($m['total_saidas']) ?></td>
        <td class="text-right <?= $s <= 0 ? 'text-danger' : '' ?>"><?= $fmt($s) ?></td>
        <td class="text-center"><span class="badge <?= $bc ?>"><?= $lb ?></span></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <div class="footer">SGE &mdash; Relatório gerado automaticamente em <?= $hoje ?></div>

  <script>window.onload = function() { window.print(); };</script>
</body>
</html>
