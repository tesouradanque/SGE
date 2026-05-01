<?php
require_once CORE_PATH . '/Model.php';

class Material extends Model {
    protected string $table = 'materiais';

    public function save(array $d): int {
        $this->query(
            'INSERT INTO materiais (codigo, descricao, unidade, preco_unitario_padrao, stock_minimo) VALUES (?,?,?,?,?)',
            [$d['codigo'], $d['descricao'], $d['unidade'], $d['preco_unitario_padrao'], $d['stock_minimo']]
        );
        return (int) $this->lastId();
    }

    public function update(int $id, array $d): void {
        $this->query(
            'UPDATE materiais SET codigo=?, descricao=?, unidade=?, preco_unitario_padrao=?, stock_minimo=? WHERE id=?',
            [$d['codigo'], $d['descricao'], $d['unidade'], $d['preco_unitario_padrao'], $d['stock_minimo'], $id]
        );
    }

    public function getStockActual(): array {
        return $this->query(
            "SELECT m.*,
                    COALESCE(e.total_entradas, 0)                        AS total_entradas,
                    COALESCE(s.total_saidas, 0)                          AS total_saidas,
                    COALESCE(e.total_entradas,0) - COALESCE(s.total_saidas,0) AS stock_actual
             FROM materiais m
             LEFT JOIN (SELECT material_id, SUM(quantidade) AS total_entradas
                        FROM itens_fatura GROUP BY material_id) e ON e.material_id = m.id
             LEFT JOIN (SELECT material_id, SUM(quantidade) AS total_saidas
                        FROM itens_requisicao GROUP BY material_id) s ON s.material_id = m.id
             ORDER BY m.descricao"
        )->fetchAll();
    }

    public function getStockById(int $id): float {
        $row = $this->query(
            "SELECT COALESCE(SUM(fi.quantidade),0) - COALESCE(SUM(ri.quantidade),0) AS stock
             FROM materiais m
             LEFT JOIN itens_fatura    fi ON fi.material_id = m.id
             LEFT JOIN itens_requisicao ri ON ri.material_id = m.id
             WHERE m.id = ?",
            [$id]
        )->fetch();
        return (float) ($row['stock'] ?? 0);
    }

    public function getLowStock(): array {
        return $this->query(
            "SELECT m.*,
                    COALESCE(en.e,0) - COALESCE(sa.s,0) AS stock_actual
             FROM materiais m
             LEFT JOIN (SELECT material_id, SUM(quantidade) AS e FROM itens_fatura    GROUP BY material_id) en ON en.material_id = m.id
             LEFT JOIN (SELECT material_id, SUM(quantidade) AS s FROM itens_requisicao GROUP BY material_id) sa ON sa.material_id = m.id
             WHERE (COALESCE(en.e,0) - COALESCE(sa.s,0)) <= m.stock_minimo
             ORDER BY stock_actual ASC"
        )->fetchAll();
    }
}
