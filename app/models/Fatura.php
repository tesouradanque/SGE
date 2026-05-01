<?php
require_once CORE_PATH . '/Model.php';

class Fatura extends Model {
    protected string $table = 'faturas';

    public function allComFornecedor(): array {
        return $this->query(
            "SELECT f.*, fn.nome AS fornecedor_nome,
                    COALESCE(SUM(fi.quantidade * fi.preco_unitario), 0) AS valor_total
             FROM faturas f
             JOIN fornecedores fn ON fn.id = f.fornecedor_id
             LEFT JOIN itens_fatura fi ON fi.fatura_id = f.id
             GROUP BY f.id
             ORDER BY f.data DESC, f.id DESC"
        )->fetchAll();
    }

    public function findComItens(int $id) {
        $fatura = $this->query(
            "SELECT f.*, fn.nome AS fornecedor_nome
             FROM faturas f
             JOIN fornecedores fn ON fn.id = f.fornecedor_id
             WHERE f.id = ?",
            [$id]
        )->fetch();

        if ($fatura) {
            $fatura['itens'] = $this->query(
                "SELECT fi.*, m.descricao AS material_nome, m.unidade,
                        (fi.quantidade * fi.preco_unitario) AS subtotal
                 FROM itens_fatura fi
                 JOIN materiais m ON m.id = fi.material_id
                 WHERE fi.fatura_id = ?",
                [$id]
            )->fetchAll();
        }
        return $fatura;
    }

    public function save(array $d): int {
        $this->query(
            'INSERT INTO faturas (nr_fatura, data, fornecedor_id, observacao, estado) VALUES (?,?,?,?,?)',
            [$d['nr_fatura'], $d['data'], $d['fornecedor_id'], $d['observacao'], $d['estado'] ?? 'pendente']
        );
        return (int) $this->lastId();
    }

    public function saveItem(int $faturaId, array $item): void {
        $this->query(
            'INSERT INTO itens_fatura (fatura_id, material_id, quantidade, preco_unitario) VALUES (?,?,?,?)',
            [$faturaId, $item['material_id'], $item['quantidade'], $item['preco_unitario']]
        );
    }

    public function updateEstado(int $id, string $estado): void {
        $this->query('UPDATE faturas SET estado=? WHERE id=?', [$estado, $id]);
    }

    public function countPendentes(): int {
        return (int) $this->query("SELECT COUNT(*) AS n FROM faturas WHERE estado='pendente'")->fetch()['n'];
    }

    public function porPeriodo(int $mes, int $ano): array {
        return $this->query(
            "SELECT f.*, fn.nome AS fornecedor_nome,
                    COALESCE(SUM(fi.quantidade * fi.preco_unitario),0) AS valor_total
             FROM faturas f
             JOIN fornecedores fn ON fn.id = f.fornecedor_id
             LEFT JOIN itens_fatura fi ON fi.fatura_id = f.id
             WHERE MONTH(f.data)=? AND YEAR(f.data)=?
             GROUP BY f.id ORDER BY f.data DESC",
            [$mes, $ano]
        )->fetchAll();
    }

    public function totalPorFornecedor(int $mes, int $ano): array {
        return $this->query(
            "SELECT fn.id, fn.nome,
                    COUNT(DISTINCT f.id)                               AS nr_faturas,
                    COALESCE(SUM(fi.quantidade * fi.preco_unitario),0)  AS total_valor,
                    SUM(CASE WHEN f.estado='pendente'
                        THEN fi.quantidade * fi.preco_unitario ELSE 0 END) AS pendente
             FROM fornecedores fn
             LEFT JOIN faturas f  ON f.fornecedor_id = fn.id
                                  AND MONTH(f.data)=? AND YEAR(f.data)=?
             LEFT JOIN itens_fatura fi ON fi.fatura_id = f.id
             GROUP BY fn.id
             ORDER BY total_valor DESC",
            [$mes, $ano]
        )->fetchAll();
    }

    public function recentesCom(int $limit = 5): array {
        return $this->query(
            "SELECT f.*, fn.nome AS fornecedor_nome,
                    COALESCE(SUM(fi.quantidade * fi.preco_unitario),0) AS valor_total
             FROM faturas f
             JOIN fornecedores fn ON fn.id = f.fornecedor_id
             LEFT JOIN itens_fatura fi ON fi.fatura_id = f.id
             GROUP BY f.id
             ORDER BY f.created_at DESC
             LIMIT {$limit}"
        )->fetchAll();
    }
}
