<?php
require_once CORE_PATH . '/Model.php';

class Fornecedor extends Model {
    protected string $table = 'fornecedores';

    public function save(array $d): int {
        $this->query(
            'INSERT INTO fornecedores (nome, nif, telefone, email, endereco) VALUES (?,?,?,?,?)',
            [$d['nome'], $d['nif'], $d['telefone'], $d['email'], $d['endereco']]
        );
        return (int) $this->lastId();
    }

    public function update(int $id, array $d): void {
        $this->query(
            'UPDATE fornecedores SET nome=?, nif=?, telefone=?, email=?, endereco=? WHERE id=?',
            [$d['nome'], $d['nif'], $d['telefone'], $d['email'], $d['endereco'], $id]
        );
    }

    public function totalFaturasPorFornecedor(?int $mes = null, ?int $ano = null): array {
        $where  = '';
        $params = [];
        if ($mes && $ano) {
            $where  = 'AND MONTH(f.data)=? AND YEAR(f.data)=?';
            $params = [$mes, $ano];
        }
        return $this->query(
            "SELECT fn.id, fn.nome, fn.nif,
                    COUNT(DISTINCT f.id)                              AS nr_faturas,
                    COALESCE(SUM(fi.quantidade * fi.preco_unitario),0) AS total_valor
             FROM fornecedores fn
             LEFT JOIN faturas f  ON f.fornecedor_id = fn.id {$where}
             LEFT JOIN itens_fatura fi ON fi.fatura_id = f.id
             GROUP BY fn.id
             ORDER BY total_valor DESC",
            $params
        )->fetchAll();
    }
}
