<?php
require_once CORE_PATH . '/Model.php';

class Ajuste extends Model {
    protected string $table = 'ajustes_estoque';

    public function save(array $d): int {
        $this->query(
            'INSERT INTO ajustes_estoque (material_id, tipo, quantidade, motivo, usuario_id) VALUES (?,?,?,?,?)',
            [$d['material_id'], $d['tipo'], $d['quantidade'], $d['motivo'], $d['usuario_id']]
        );
        return (int) $this->lastId();
    }

    public function allComMaterial(): array {
        return $this->query(
            "SELECT a.*, m.descricao AS material, m.codigo, m.unidade, u.nome AS usuario
             FROM ajustes_estoque a
             JOIN materiais m ON m.id = a.material_id
             JOIN usuarios u  ON u.id = a.usuario_id
             ORDER BY a.created_at DESC"
        )->fetchAll();
    }
}
