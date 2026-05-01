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

    public function update(int $id, array $d): void {
        if (!empty($d['senha'])) {
            $this->query(
                'UPDATE usuarios SET nome=?, email=?, senha=?, perfil=?, ativo=? WHERE id=?',
                [$d['nome'], $d['email'], password_hash($d['senha'], PASSWORD_DEFAULT), $d['perfil'], $d['ativo'] ?? 1, $id]
            );
        } else {
            $this->query(
                'UPDATE usuarios SET nome=?, email=?, perfil=?, ativo=? WHERE id=?',
                [$d['nome'], $d['email'], $d['perfil'], $d['ativo'] ?? 1, $id]
            );
        }
    }

    public function emailExiste(string $email, int $excludeId = 0): bool {
        return (bool) $this->query('SELECT id FROM usuarios WHERE email=? AND id!=?', [$email, $excludeId])->fetch();
    }
}
