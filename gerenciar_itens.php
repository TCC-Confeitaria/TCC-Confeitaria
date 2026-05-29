<?php
session_start();
require_once __DIR__ . '/Conexao.php';

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$pdo = Conexao::getConn();

// 1. AÇÃO: CADASTRAR ITEM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = trim($_POST['preco']);
    
    $categoria_nova = trim($_POST['categoria_nova']);
    $categoria_existente = isset($_POST['categoria_existente']) ? trim($_POST['categoria_existente']) : '';
    $categoria = ($categoria_nova !== '') ? $categoria_nova : $categoria_existente;

    $status = 'ativo';
    $nome_imagem = 'padrao.png';

    if ($nome === '' || $descricao === '' || $preco === '' || $categoria === '') {
        $erro_cadastro = "Todos os campos são obrigatórios!";
    } else {
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $arquivo = $_FILES['imagem'];
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $nome_imagem = uniqid() . "." . $extensao;
            $diretorio_destino = __DIR__ . '/imagens/produtos/';
            
            if (!is_dir($diretorio_destino)) mkdir($diretorio_destino, 0777, true);
            move_uploaded_file($arquivo['tmp_name'], $diretorio_destino . $nome_imagem);
        }

        $stmt = $pdo->prepare("INSERT INTO itens_cardapio (nome, descricao, preco, imagem, categoria, status) VALUES (:nome, :descricao, :preco, :imagem, :categoria, :status)");
        $stmt->execute([':nome' => $nome, ':descricao' => $descricao, ':preco' => $preco, ':imagem' => $nome_imagem, ':categoria' => $categoria, ':status' => $status]);
        header("Location: gerenciar_itens.php");
        exit;
    }
}

// 2. AÇÃO: ALTERAR STATUS
if (isset($_GET['alterar_status']) && isset($_GET['id'])) {
    $novo_status = $_GET['alterar_status'] === 'ativo' ? 'pausado' : 'ativo';
    $stmt = $pdo->prepare("UPDATE itens_cardapio SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $novo_status, ':id' => $_GET['id']]);
    header("Location: gerenciar_itens.php");
    exit;
}

// 3. AÇÃO: EXCLUIR ITEM
if (isset($_GET['excluir']) && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM itens_cardapio WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    header("Location: gerenciar_itens.php");
    exit;
}

// 4. BUSCAR CATEGORIAS E FILTRAR
$stmt_cat = $pdo->query("SELECT DISTINCT categoria FROM itens_cardapio WHERE categoria != '' ORDER BY categoria ASC");
$categorias_existentes = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);

$pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
$filtro_categoria = isset($_GET['filtro_categoria']) ? trim($_GET['filtro_categoria']) : '';

$sql = "SELECT * FROM itens_cardapio WHERE 1=1";
$params = [];

if ($pesquisa !== '') { $sql .= " AND nome LIKE :pesquisa"; $params[':pesquisa'] = "%$pesquisa%"; }
if ($filtro_categoria !== '') { $sql .= " AND categoria = :categoria"; $params[':categoria'] = $filtro_categoria; }

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Cardápio - Admin</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <?php include 'cabecalho.php'; ?>

    <main class="conteudo-principal flex-col">
        
        <div class="box2 box-cadastro">
            <h1>Cadastrar Novo Item</h1>
            
            <?php if(isset($erro_cadastro)): ?>
                <p class="alert-danger"><?php echo $erro_cadastro; ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="cadastrar" value="1">
                
                <div class="inputBox">
                    <input type="text" name="nome" class="inputUser" placeholder=" " required>
                    <label class="labelInput">Nome do Doce</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="descricao" class="inputUser" placeholder=" " required>
                    <label class="labelInput">Descrição</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="number" step="0.01" name="preco" class="inputUser" placeholder=" " required>
                    <label class="labelInput">Preço (R$)</label>
                </div>
                <br>
                <div class="categoria-bloco">
                    <label><strong>Categoria do Produto</strong></label>
                    <?php if (!empty($categorias_existentes)): ?>
                        <span>Escolher existente:</span>
                        <select name="categoria_existente" class="inputUser">
                            <?php foreach ($categorias_existentes as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="center-note">— OU —</p>
                    <?php endif; ?>
                    <span>Criar nova categoria:</span>
                    <input type="text" name="categoria_nova" placeholder=" " class="inputUser">
                </div>

                <div class="form-group-left">
                    <label><strong>Foto do Produto:</strong></label>
                    <input type="file" name="imagem" accept="image/*" required>
                </div>
                <br>
                <button type="submit" class="btn">Adicionar ao Cardápio</button>
            </form>
        </div>

        <div class="box2 filtro-container">
            <form method="GET" class="filtro-container filtro-form">
                <input type="text" name="pesquisa" placeholder="Buscar por nome..." value="<?php echo htmlspecialchars($pesquisa); ?>" class="inputUser small">
                <select name="filtro_categoria" class="inputUser medium">
                    <option value="">Todas as Categorias</option>
                    <?php foreach ($categorias_existentes as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $filtro_categoria === $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn2">Filtrar</button>
                <?php if ($pesquisa !== '' || $filtro_categoria !== ''): ?>
                    <a href="gerenciar_itens.php">Limpar Filtros</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="box2 box2-compact">
            <h1>Itens do Cardápio</h1>
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($itens)): ?>
                        <tr><td colspan="7">Nenhum item encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($itens as $item): ?>
                            <tr class="<?php echo $item['status'] === 'pausado' ? 'linha-pausada' : ''; ?>">
                                <td><img src="imagens/produtos/<?php echo $item['imagem']; ?>" alt="Foto" class="img-tabela"></td>
                                <td><strong><?php echo htmlspecialchars($item['nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($item['categoria']); ?></td>
                                <td><?php echo htmlspecialchars($item['descricao']); ?></td>
                                <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                <td><span class="status-badge <?php echo $item['status']; ?>"><?php echo ucfirst($item['status']); ?></span></td>
                                <td>
                                    <a href="gerenciar_itens.php?id=<?php echo $item['id']; ?>&alterar_status=<?php echo $item['status']; ?>">Pausar/Ativar</a> |
                                    <a href="editar_item.php?id=<?php echo $item['id']; ?>">Editar</a> |
                                    <a href="gerenciar_itens.php?id=<?php echo $item['id']; ?>&excluir=1" onclick="return confirm('Excluir?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>