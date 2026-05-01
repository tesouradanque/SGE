<?php
/**
 * SGE – Instalador
 * Aceder UMA vez: http://localhost/sge/install.php
 * Depois apagar ou proteger este ficheiro.
 */

$host    = 'localhost';
$user    = 'root';
$pass    = '';
$dbName  = 'sge';
$charset = 'utf8mb4';

$errors = [];

try {
    $pdo = new PDO("mysql:host={$host};charset={$charset}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $sql = file_get_contents(__DIR__ . '/database/sge.sql');
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        if ($stmt !== '') $pdo->exec($stmt);
    }

    // Admin por defeito: admin@sge.com / admin123
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("USE {$dbName}");
    $check = $pdo->query("SELECT id FROM usuarios WHERE email = 'admin@sge.com'")->fetch();
    if (!$check) {
        $ins = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, 'admin')");
        $ins->execute(['Administrador', 'admin@sge.com', $hash]);
    }

    $success = true;
} catch (PDOException $e) {
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>SGE – Instalação</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container" style="max-width:600px;margin-top:80px">
  <div class="card shadow">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">SGE – Instalação</h5></div>
    <div class="card-body">
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $e): ?><p class="mb-0"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="alert alert-success">
          <strong>Instalação concluída com sucesso!</strong><br>
          Base de dados <strong><?= $dbName ?></strong> criada.<br>
          Utilizador administrador criado:<br>
          &nbsp;&nbsp;Email: <code>admin@sge.com</code><br>
          &nbsp;&nbsp;Password: <code>admin123</code>
        </div>
        <div class="alert alert-warning">
          <strong>Importante:</strong> Apague ou restrinja o acesso a este ficheiro após a instalação.
        </div>
        <a href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/sge' ?>" class="btn btn-primary">
          Entrar no sistema
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
