<?php
session_start();
require_once __DIR__ . '/Conexao.php';

$pdo = Conexao::getConn();
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_carrinho'])) {
    if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'cliente') {
        $mensagem = 'Faça login para adicionar produtos ao carrinho.';
    } else {
        $itemId = filter_input(INPUT_POST, 'adicionar_id', FILTER_VALIDATE_INT);
        $quantidade = max(1, filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT, ['options' => ['default' => 1]]));

        if ($itemId) {
            $stmt = $pdo->prepare('SELECT * FROM itens_cardapio WHERE id = :id AND status = "ativo"');
            $stmt->execute([':id' => $itemId]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($item) {
                if (!isset($_SESSION['carrinho'])) {
                    $_SESSION['carrinho'] = [];
                }

                if (isset($_SESSION['carrinho'][$itemId])) {
                    $_SESSION['carrinho'][$itemId]['quantidade'] += $quantidade;
                } else {
                    $_SESSION['carrinho'][$itemId] = [
                        'id' => $item['id'],
                        'nome' => $item['nome'],
                        'preco' => (float) $item['preco'],
                        'imagem' => $item['imagem'],
                        'quantidade' => $quantidade,
                    ];
                }

                $mensagem = 'Produto adicionado ao carrinho com sucesso.';
            } else {
                $mensagem = 'Produto não encontrado ou indisponível.';
            }
        }
    }
}

$pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
if (isset($_GET['pesquisa']) && $pesquisa === '') {
    header('Location: cardapio.php');
    exit;
}

$sql = 'SELECT * FROM itens_cardapio WHERE status = "ativo"';
$params = [];
if ($pesquisa !== '') {
    $sql .= ' AND nome LIKE :pesquisa';
    $params[':pesquisa'] = '%' . $pesquisa . '%';
}
$sql .= ' ORDER BY categoria ASC, nome ASC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$todos_itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cardapio_por_categoria = [];
foreach ($todos_itens as $item) {
    $cardapio_por_categoria[$item['categoria']][] = $item;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cardápio</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<?php include 'cabecalho.php'; ?>
<main class="conteudo-principal flex-col">
        <?php if ($mensagem): ?>
            <div class="mensagem-alerta"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <div class="box2 box2-transparente">
            <form method="GET" class="filtro-container">
                <div class="inputBox full-width">
                    <input type="text" name="pesquisa" id="pesquisa" class="inputUser" placeholder=" " value="<?php echo htmlspecialchars($pesquisa); ?>">
                    <label for="pesquisa" class="labelInput">Pesquisar</label>
                </div>
                <button type="submit" class="btn">Buscar</button>
            </form>
        </div>

    <?php if (empty($cardapio_por_categoria)): ?>
    <div>
        <h2>Nenhum doce encontrado com o nome "<?php echo htmlspecialchars($pesquisa); ?>".</h2>
    </div>
    
    <?php else: ?>
        <?php foreach ($cardapio_por_categoria as $nome_categoria => $itens): ?>
            <div class="box2 box2-transparente">
                <h2 class="categoria-titulo-cliente"><?php echo htmlspecialchars($nome_categoria); ?></h2>
        
        <div class="produtos-grid-cliente">
            
            <?php foreach ($itens as $item): ?>
                    <?php $modalId = 'modal-' . $item['id']; ?>
                    <input type="checkbox" id="<?php echo $modalId; ?>" class="modal-checkbox">
                    <div class="produto-card-container">
                        <label for="<?php echo $modalId; ?>" class="produto-card-cliente">
                            <img src="imagens/produtos/<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" class="card-img">
                            <div class="card-nome"><?php echo htmlspecialchars($item['nome']); ?></div>
                            <div class="card-preco">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></div>
                        </label>

                        <div class="modal-overlay">
                            <div class="modal-box">
                                <label for="<?php echo $modalId; ?>" class="modal-close">&times;</label>
                                <img src="imagens/produtos/<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" class="card-img">
                                <h2><?php echo htmlspecialchars($item['nome']); ?></h2>
                                <p class="descricao-modal"><?php echo nl2br(htmlspecialchars($item['descricao'])); ?></p>
                                <div class="detalhes-modal">
                                    <span>Categoria: <?php echo htmlspecialchars($item['categoria']); ?></span>
                                    <span>Preço: R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></span>
                                </div>

                                <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'cliente'): ?>
                                    <form method="POST" class="form-adicionar-modal">
                                        <input type="hidden" name="adicionar_id" value="<?php echo $item['id']; ?>">
                                        <label for="quantidade-<?php echo $item['id']; ?>">Quantidade</label>
                                        <input type="number" id="quantidade-<?php echo $item['id']; ?>" name="quantidade" value="1" min="1" class="quantidade-input">
                                        <button type="submit" name="adicionar_carrinho" class="btn">Adicionar ao Carrinho</button>
                                    </form>
                                <?php else: ?>
                                    <p class="aviso-login">Faça login para adicionar este produto ao carrinho.</p>
                                    <a href="login.php" class="btn">Entrar</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
            <?php endforeach; ?>
            
        </div>
    </div>
        <?php endforeach; ?>

    <?php endif; ?>
    </main>
</body>
</html>