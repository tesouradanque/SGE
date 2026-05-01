<?php
require_once CORE_PATH . '/Model.php';

class Requisicao extends Model {
    protected string $table = 'requisicoes';

    public function allComFuncionario(): array {
        return $this->query(
            "SELECT r.*, fn.nome AS funcionario_nome,
                    COUNT(ri.id)                                       AS nr_itens,
                    COALESCE(SUM(ri.quantidade * ri.preco_unitario),0)  AS valor_total
             FROM requisicoes r
             JOIN funcionarios fn ON fn.id = r.funcionario_id
             LEFT JOIN itens_requisicao ri ON ri.requisicao_id = r.id
             GROUP BY r.id
             ORDER BY r.data DESC, r.id DESC"
        )->fetchAll();
    }

    public function findComItens(int $id) {
        $req = $this->query(
            "SELECT r.*, fn.nome AS funcionario_nome
             FROM requisicoes r
             JOIN funcionarios fn ON fn.id = r.funcionario_id
             WHERE r.id = ?",
            [$id]
        )->fetch();

        if ($req) {
            $req['itens'] = $this->query(
                "SELECT ri.*, m.descricao AS material_nome, m.unidade,
                        (ri.quantidade * ri.preco_unitario) AS subtotal
                 FROM itens_requisicao ri
                 JOIN materiais m ON m.id = ri.material_id
                 WHERE ri.requisicao_id = ?",
                [$id]
            )->fetchAll();
        }
        return $req;
    }

    public function nrExiste(string $nr): bool {
        return (bool) $this->query('SELECT id FROM requisicoes WHERE nr_requisicao=?', [$nr])->fetch();
    }

    public function save(array $d): int {
        $this->query(
            'INSERT INTO requisicoes (nr_requisicao, data, funcionario_id, observacao) VALUES (?,?,?,?)',
            [$d['nr_requisicao'], $d['data'], $d['funcionario_id'], $d['observacao']]
        );
        return (int) $this->lastId();
    }

    public function saveItem(int $reqId, array $item): void {
        $this->query(
            'INSERT INTO itens_requisicao (requisicao_id, material_id, quantidade, preco_unitario) VALUES (?,?,?,?)',
            [$reqId, $item['material_id'], $item['quantidade'], $item['preco_unitario']]
        );
    }

    public function countMes(): int {
        return (int) $this->query(
            'SELECT COUNT(*) AS n FROM requisicoes WHERE MONTH(data)=MONTH(NOW()) AND YEAR(data)=YEAR(NOW())'
        )->fetch()['n'];
    }

    public function recentesCom(int $limit = 5): array {
        return $this->query(
            "SELECT r.*, fn.nome AS funcionario_nome
             FROM requisicoes r
             JOIN funcionarios fn ON fn.id = r.funcionario_id
             ORDER BY r.created_at DESC
             LIMIT {$limit}"
        )->fetchAll();
    }

    public function porPeriodo(int $mes, int $ano): array {
        return $this->query(
            "SELECT r.*, fn.nome AS funcionario_nome,
                    COALESCE(SUM(ri.quantidade * ri.preco_unitario),0) AS valor_total
             FROM requisicoes r
             JOIN funcionarios fn ON fn.id = r.funcionario_id
             LEFT JOIN itens_requisicao ri ON ri.requisicao_id = r.id
             WHERE MONTH(r.data)=? AND YEAR(r.data)=?
             GROUP BY r.id ORDER BY r.data",
            [$mes, $ano]
        )->fetchAll();
    }

    public function materiaisPorFuncionario(int $mes, int $ano): array {
        return $this->query(
            "SELECT fn.nome AS funcionario, m.descricao AS material, m.unidade,
                    SUM(ri.quantidade)                      AS quantidade_total,
                    SUM(ri.quantidade * ri.preco_unitario)  AS valor_total
             FROM itens_requisicao ri
             JOIN requisicoes r  ON r.id  = ri.requisicao_id
             JOIN funcionarios fn ON fn.id = r.funcionario_id
             JOIN materiais m    ON m.id  = ri.material_id
             WHERE MONTH(r.data)=? AND YEAR(r.data)=?
             GROUP BY fn.id, m.id
             ORDER BY fn.nome, m.descricao",
            [$mes, $ano]
        )->fetchAll();
    }
}
