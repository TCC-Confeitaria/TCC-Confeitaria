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
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <?php include 'cabecalho.php'; ?>
    <main class="conteudo-principal flex-col">
        <div class="box2 box2-compact box2-left">
            <h1>Painel Administrativo</h1>
            <p>Bem-vindo, <b><?php echo $_SESSION['usuario_nome']; ?></b>!</p>
            <h3>Lista de Utilizadores Registados no Banco de Dados:</h3>
            <div class="table-container">
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
            </div>
        </div>
    </main>
</body>
</html>