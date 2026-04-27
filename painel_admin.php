<?php
session_start();
require_once __DIR__ . '/Conexao.php';

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$pdo = Conexao::getConn();
$stmt = $pdo->query("SELECT nome, email, tipo, cidade FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f9; }
        table { width: 100%; background: white; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #333; color: white; }
    </style>
</head>
<body>
    <h1>Painel Administrativo ⚙️</h1>
    <p>Bem-vindo, <?php echo $_SESSION['usuario_nome']; ?> | <a href="index.php?sair=true">Sair</a></p>
    
    <h3>Lista de Utilizadores Registados no Banco de Dados:</h3>
    <table>
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Tipo</th>
            <th>Cidade</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?php echo $u['nome']; ?></td>
            <td><?php echo $u['email']; ?></td>
            <td><?php echo $u['tipo']; ?></td>
            <td><?php echo $u['cidade'] ?? '---'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>