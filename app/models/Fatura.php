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
            "SELECT f.*, fn.nome AS fornecedor_nome, fn.nif AS fornecedor_nif,
                    fn.telefone AS fornecedor_tel, fn.email AS fornecedor_email,
                    f.created_at
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

    public function nrExiste(string $nr, int $excludeId = 0): bool {
        return (bool) $this->query(
            'SELECT id FROM faturas WHERE nr_fatura=? AND id!=?', [$nr, $excludeId]
        )->fetch();
    }

    public function countAll(array $filtros = []): int {
        [$where, $params] = $this->buildWhere($filtros);
        return (int) $this->query(
            "SELECT COUNT(*) AS n FROM faturas f JOIN fornecedores fn ON fn.id=f.fornecedor_id {$where}",
            $params
        )->fetch()['n'];
    }

    public function allComFornecedorFiltrado(array $filtros = [], int $limit = 20, int $offset = 0): array {
        [$where, $params] = $this->buildWhere($filtros);
        $params[] = $limit;
        $params[] = $offset;
        return $this->query(
            "SELECT f.*, fn.nome AS fornecedor_nome,
                    COALESCE(SUM(fi.quantidade * fi.preco_unitario), 0) AS valor_total
             FROM faturas f
             JOIN fornecedores fn ON fn.id = f.fornecedor_id
             LEFT JOIN itens_fatura fi ON fi.fatura_id = f.id
             {$where}
             GROUP BY f.id
             ORDER BY f.data DESC, f.id DESC
             LIMIT ? OFFSET ?",
            $params
        )->fetchAll();
    }

    private function buildWhere(array $filtros): array {
        $conds  = [];
        $params = [];
        if (!empty($filtros['estado'])) {
            $conds[]  = 'f.estado = ?';
            $params[] = $filtros['estado'];
        }
        if (!empty($filtros['fornecedor_id'])) {
            $conds[]  = 'f.fornecedor_id = ?';
            $params[] = (int) $filtros['fornecedor_id'];
        }
        if (!empty($filtros['de'])) {
            $conds[]  = 'f.data >= ?';
            $params[] = $filtros['de'];
        }
        if (!empty($filtros['ate'])) {
            $conds[]  = 'f.data <= ?';
            $params[] = $filtros['ate'];
        }
        if (!empty($filtros['nr'])) {
            $conds[]  = 'f.nr_fatura LIKE ?';
            $params[] = '%' . $filtros['nr'] . '%';
        }
        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        return [$where, $params];
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
             LIMIT ?",
            [$limit]
        )->fetchAll();
    }

    public function movimentosEntrada(array $filtros = []): array {
        $conds  = ['1=1'];
        $params = [];
        if (!empty($filtros['material_id'])) {
            $conds[]  = 'fi.material_id = ?';
            $params[] = (int) $filtros['material_id'];
        }
        if (!empty($filtros['de'])) {
            $conds[]  = 'f.data >= ?';
            $params[] = $filtros['de'];
        }
        if (!empty($filtros['ate'])) {
            $conds[]  = 'f.data <= ?';
            $params[] = $filtros['ate'];
        }
        $where = 'WHERE ' . implode(' AND ', $conds);
        return $this->query(
            "SELECT f.data, f.nr_fatura AS referencia, fn.nome AS fornecedor,
                    m.descricao AS material, m.unidade,
                    fi.quantidade, fi.preco_unitario,
                    (fi.quantidade * fi.preco_unitario) AS subtotal,
                    'entrada' AS tipo
             FROM itens_fatura fi
             JOIN faturas f      ON f.id  = fi.fatura_id
             JOIN fornecedores fn ON fn.id = f.fornecedor_id
             JOIN materiais m    ON m.id  = fi.material_id
             {$where}
             ORDER BY f.data DESC, f.id DESC",
            $params
        )->fetchAll();
    }

    public function allParaCsv(array $filtros = []): array {
        [$where, $params] = $this->buildWhere($filtros);
        return $this->query(
            "SELECT f.nr_fatura, fn.nome AS fornecedor, f.data, f.estado,
                    COALESCE(SUM(fi.quantidade * fi.preco_unitario),0) AS valor_total,
                    f.observacao
             FROM faturas f
             JOIN fornecedores fn ON fn.id = f.fornecedor_id
             LEFT JOIN itens_fatura fi ON fi.fatura_id = f.id
             {$where}
             GROUP BY f.id
             ORDER BY f.data DESC",
            $params
        )->fetchAll();
    }
}
