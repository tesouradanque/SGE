<?php
class Model {

    protected PDO $db;
    protected string $table = '';

    public function __construct() {
        static $pdo = null;
        if ($pdo === null) {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        $this->db = $pdo;
    }

    public function all(string $order = 'id DESC'): array {
        return $this->query("SELECT * FROM {$this->table} ORDER BY {$order}")->fetchAll();
    }

    public function find(int $id) {
        return $this->query("SELECT * FROM {$this->table} WHERE id = ?", [$id])->fetch();
    }

    public function delete(int $id): bool {
        return $this->query("DELETE FROM {$this->table} WHERE id = ?", [$id])->rowCount() > 0;
    }

    protected function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function lastId(): string {
        return $this->db->lastInsertId();
    }
}
