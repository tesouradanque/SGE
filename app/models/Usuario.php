<?php
require_once CORE_PATH . '/Model.php';

class Usuario extends Model {
    protected string $table = 'usuarios';

    public function findByEmail(string $email) {
        return $this->query('SELECT * FROM usuarios WHERE email=? AND ativo=1', [$email])->fetch();
    }

    public function save(array $d): int {
        $this->query(
            'INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?,?,?,?)',
            [$d['nome'], $d['email'], password_hash($d['senha'], PASSWORD_DEFAULT), $d['perfil'] ?? 'operador']
        );
        return (int) $this->lastId();
    }
}
