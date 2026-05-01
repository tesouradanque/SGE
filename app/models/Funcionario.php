<?php
require_once CORE_PATH . '/Model.php';

class Funcionario extends Model {
    protected string $table = 'funcionarios';

    public function save(array $d): int {
        $this->query(
            'INSERT INTO funcionarios (nome, cargo, telefone, email) VALUES (?,?,?,?)',
            [$d['nome'], $d['cargo'], $d['telefone'], $d['email']]
        );
        return (int) $this->lastId();
    }

    public function update(int $id, array $d): void {
        $this->query(
            'UPDATE funcionarios SET nome=?, cargo=?, telefone=?, email=?, ativo=? WHERE id=?',
            [$d['nome'], $d['cargo'], $d['telefone'], $d['email'], $d['ativo'] ?? 1, $id]
        );
    }

    public function ativos(): array {
        return $this->query('SELECT * FROM funcionarios WHERE ativo=1 ORDER BY nome')->fetchAll();
    }
}
