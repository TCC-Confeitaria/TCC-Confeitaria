<?php
// Garante que a sessão está iniciada para podermos ler o tipo de usuário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica o tipo de usuário logado (se não houver ninguém, assume que é um visitante/deslogado)
$tipo_usuario = isset($_SESSION['usuario_tipo']) ? $_SESSION['usuario_tipo'] : 'visitante';
?>

<header class="cabecalho">
    <div class="logo">
        <a href="index.php">
            <img src="imagens/logo.png" alt="Logo da Confeitaria">
        </a>
    </div>

    <nav class="menu-navegacao">
        <?php if ($tipo_usuario !== 'admin'): ?>
            <a href="index.php">Início</a>
            <a href="cardapio.php">Cardápio</a>
            <a href="carrinho.php">Carrinho</a>
        <?php endif; ?>

        <?php if ($tipo_usuario === 'admin'): ?>
            <a href="index.php">Início</a>
            <a href="painel_admin.php">Painel Administrativo</a>
            <a href="gerenciar_itens.php">Gerenciar Itens</a>
            <a href="logout.php">Sair</a>
        <?php endif; ?>

        <?php if ($tipo_usuario === 'cliente'): ?>
            <a href="perfil.php">Perfil</a>
            <a href="logout.php">Sair</a>
        <?php endif; ?>

        <?php if ($tipo_usuario === 'visitante'): ?>
            <a href="login.php" >Entrar</a>
            <a href="cadastro.php" class="btn-cadastrar">Cadastrar</a>
        <?php endif; ?>
    </nav>
</header>
