<?php
session_start();
if (!isset($_SESSION['usuario_nome'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Confeitaria</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <?php include 'cabecalho.php'; ?>
    <main class="conteudo-principal">
        <div class="box2">
        <h1>Bem-vindo à Loja, <?php echo $_SESSION['usuario_nome']; ?>! 🍰</h1>
        <p>Aqui poderá ver os nossos bolos em breve.</p>
        </div>
    </main>
</body>
</html>