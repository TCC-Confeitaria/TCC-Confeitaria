<?php
session_start();
require_once __DIR__ . '/Conexao.php';

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

$pdo = Conexao::getConn();
$stmt = $pdo->prepare('SELECT nome, email, telefone, data_nascimento, cpf, rua, numero, bairro, cidade FROM usuarios WHERE id = :id');
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: logout.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Perfil - Confeitaria</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <?php include 'cabecalho.php'; ?>
    <main class="conteudo-principal flex-col">
        <div class="box2 box-cadastro box2-narrow">
            <h1>Meu Perfil</h1>
            <p>Bem-vindo, <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong>.</p>
            <table>
                <tr><th>Nome</th><td><?php echo htmlspecialchars($usuario['nome']); ?></td></tr>
                <tr><th>E-mail</th><td><?php echo htmlspecialchars($usuario['email']); ?></td></tr>
                <tr><th>Telefone</th><td><?php echo htmlspecialchars($usuario['telefone']); ?></td></tr>
                <tr><th>Data de Nascimento</th><td><?php echo htmlspecialchars($usuario['data_nascimento']); ?></td></tr>
                <tr><th>CPF</th><td><?php echo htmlspecialchars($usuario['cpf']); ?></td></tr>
                <tr><th>Endereço</th><td><?php echo htmlspecialchars($usuario['rua']); ?>, <?php echo htmlspecialchars($usuario['numero']); ?>, <?php echo htmlspecialchars($usuario['bairro']); ?> - <?php echo htmlspecialchars($usuario['cidade']); ?></td></tr>
            </table>
            <p class="info-text"><a href="carrinho.php" class="btn">Ir para o Carrinho</a></p>
        </div>
    </main>
</body>
</html>
