<?php
session_start();
if (!isset($_SESSION['usuario_nome'])) {
    header("Location: index.php");
    exit;
}
?>
<h1>Bem-vindo à Loja, <?php echo $_SESSION['usuario_nome']; ?>! 🍰</h1>
<p>Aqui poderá ver os nossos bolos em breve.</p>
<a href="index.php">Sair</a>