<?php
session_start();
require_once __DIR__ . '/Conexao.php';

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

$pdo = Conexao::getConn();
$usuarioId = $_SESSION['usuario_id'];

// Busca os dados do cliente para montar a mensagem do WhatsApp
$stmt = $pdo->prepare('SELECT nome, email, telefone, rua, numero, bairro, cidade FROM usuarios WHERE id = :id');
$stmt->execute([':id' => $usuarioId]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: logout.php');
    exit;
}

$carrinho = $_SESSION['carrinho'] ?? [];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remover_id'])) {
        $removerId = (int) $_POST['remover_id'];
        unset($carrinho[$removerId]);
        $_SESSION['carrinho'] = $carrinho;
        $mensagem = 'Item removido do carrinho.';
    }

    if (isset($_POST['atualizar_quantidade'])) {
        $itemId = (int) $_POST['item_id'];
        $quantidade = max(1, (int) $_POST['quantidade']);

        if (isset($carrinho[$itemId])) {
            $carrinho[$itemId]['quantidade'] = $quantidade;
            $_SESSION['carrinho'] = $carrinho;
            $mensagem = 'Quantidade atualizada com sucesso.';
        }
    }

}

$totalCarrinho = 0;
foreach ($carrinho as $item) {
    $totalCarrinho += $item['preco'] * $item['quantidade'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho - Confeitaria</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <?php include 'cabecalho.php'; ?>
    <main class="conteudo-principal flex-col">
        <div class="box2 box-cadastro box2-left">
            <h1>Meu Carrinho</h1>
            <?php if ($mensagem): ?>
                <div class="mensagem-alerta"><?php echo htmlspecialchars($mensagem); ?></div>
            <?php endif; ?>

            <?php if (empty($carrinho)): ?>
                <p class="cart-empty">Seu carrinho está vazio. Adicione doces no cardápio.</p>
            <?php else: ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrinho as $item): ?>
                            <tr>
                                <td><img src="imagens/produtos/<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" class="img-tabela"></td>
                                <td><?php echo htmlspecialchars($item['nome']); ?></td>
                                <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                <td>
                                    <form method="POST" class="quantidade-form">
                                        <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantidade" value="<?php echo $item['quantidade']; ?>" min="1" class="quantidade-input">
                                        <button type="submit" name="atualizar_quantidade" class="btn btn-pequeno">Atualizar</button>
                                    </form>
                                </td>
                                <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="remover_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn btn-pequeno">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-footer">
                    <strong class="cart-total">Total: R$ <?php echo number_format($totalCarrinho, 2, ',', '.'); ?></strong>
                    <div class="cart-actions">
                        <a href="revisar_pedido.php" class="btn">Revisar Pedido</a>
                    </div>
                </div>
                <p class="info-text">Após revisar, você poderá entrar em contato com a confeitaria para confirmar seu pedido.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
