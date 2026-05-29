<?php
session_start();
require_once __DIR__ . '/Conexao.php';

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

$carrinho = $_SESSION['carrinho'] ?? [];
if (empty($carrinho)) {
    header('Location: carrinho.php');
    exit;
}

$pdo = Conexao::getConn();
$stmt = $pdo->prepare('SELECT nome, email, telefone, rua, numero, bairro, cidade FROM usuarios WHERE id = :id');
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: logout.php');
    exit;
}

$totalCarrinho = 0;
$texto = "Olá, gostaria de fazer um pedido da Confeitaria.\n\n";

foreach ($carrinho as $item) {
    $subtotal = $item['preco'] * $item['quantidade'];
    $totalCarrinho += $subtotal;
    $texto .= "- {$item['nome']} x{$item['quantidade']} (R$ " . number_format($item['preco'], 2, ',', '.') . ") = R$ " . number_format($subtotal, 2, ',', '.') . "\n";
}

$texto .= "\nTotal do pedido: R$ " . number_format($totalCarrinho, 2, ',', '.') . "\n\n";
$texto .= "Dados do cliente:\n";
$texto .= "Nome: {$usuario['nome']}\n";
$texto .= "E-mail: {$usuario['email']}\n";
$texto .= "Telefone: {$usuario['telefone']}\n";
$texto .= "Endereço: {$usuario['rua']}, {$usuario['numero']} - {$usuario['bairro']}, {$usuario['cidade']}";

$contatoLink = 'https://wa.me/5511975536988?text=' . rawurlencode($texto);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Revisar Pedido - Confeitaria</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <?php include 'cabecalho.php'; ?>
    <main class="conteudo-principal flex-col">
        <div class="box2 box-cadastro box2-left">
            <h1>Revisão do Pedido</h1>

            <div class="table-container">
                <table class="review-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Item</th>
                            <th>Quantidade</th>
                            <th>Preço</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrinho as $item): ?>
                            <tr>
                                <td><img src="imagens/produtos/<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" class="img-tabela"></td>
                                <td><?php echo htmlspecialchars($item['nome']); ?></td>
                                <td><?php echo (int) $item['quantidade']; ?></td>
                                <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-footer">
                <strong class="cart-total">Total: R$ <?php echo number_format($totalCarrinho, 2, ',', '.'); ?></strong>
            </div>

            <div class="review-info">
                <p><strong>Dados de contato</strong></p>
                <p><?php echo htmlspecialchars($usuario['nome']); ?> | <?php echo htmlspecialchars($usuario['telefone']); ?> | <?php echo htmlspecialchars($usuario['email']); ?></p>
                <p><?php echo htmlspecialchars($usuario['rua']); ?>, <?php echo htmlspecialchars($usuario['numero']); ?> - <?php echo htmlspecialchars($usuario['bairro']); ?>, <?php echo htmlspecialchars($usuario['cidade']); ?></p>
                <p>Após clicar em entrar em contato, a confeitaria receberá uma solicitação para confirmar entrega e pagamento.</p>
            </div>

            <div class="cart-actions">
                <a href="carrinho.php" class="btn btn-pequeno btn-inline">Voltar ao Carrinho</a>
                <a href="<?php echo htmlspecialchars($contatoLink); ?>" target="_blank" class="btn btn-inline">Entrar em contato com a confeitaria</a>
            </div>
        </div>
    </main>
</body>
</html>
 