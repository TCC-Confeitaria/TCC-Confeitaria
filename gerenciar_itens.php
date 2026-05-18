<?php
session_start();
require_once __DIR__ . '/Conexao.php';

// Segurança: Se não for admin, chuta de volta para a index
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$pdo = Conexao::getConn();

// 1. AÇÃO: CADASTRAR ITEM (CREATE)
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
        $erro_cadastro = "Todos os campos são obrigatórios e não podem ficar em branco!";
    } else {
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $arquivo = $_FILES['imagem'];
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $nome_imagem = uniqid() . "." . $extensao;
            $diretorio_destino = __DIR__ . '/imagens/produtos/';
            
            if (!is_dir($diretorio_destino)) {
                mkdir($diretorio_destino, 0777, true);
            }
            move_uploaded_file($arquivo['tmp_name'], $diretorio_destino . $nome_imagem);
        }

        $stmt = $pdo->prepare("INSERT INTO itens_cardapio (nome, descricao, preco, imagem, categoria, status) VALUES (:nome, :descricao, :preco, :imagem, :categoria, :status)");
        $stmt->execute([
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':preco' => $preco,
            ':imagem' => $nome_imagem,
            ':categoria' => $categoria,
            ':status' => $status
        ]);
        header("Location: gerenciar_itens.php");
        exit;
    }
}

// 2. AÇÃO: ALTERAR STATUS (PAUSAR/ATIVAR)
if (isset($_GET['alterar_status']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $novo_status = $_GET['alterar_status'] === 'ativo' ? 'pausado' : 'ativo';

    $stmt = $pdo->prepare("UPDATE itens_cardapio SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $novo_status, ':id' => $id]);
    header("Location: gerenciar_itens.php");
    exit;
}

// 3. AÇÃO: EXCLUIR ITEM (DELETE)
if (isset($_GET['excluir']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM itens_cardapio WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: gerenciar_itens.php");
    exit;
}

// 4. BUSCAR CATEGORIAS EXISTENTES DINAMICAMENTE
$stmt_cat = $pdo->query("SELECT DISTINCT categoria FROM itens_cardapio WHERE categoria != '' ORDER BY categoria ASC");
$categorias_existentes = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);

// 5. LÓGICA DE FILTRO E PESQUISA (READ DINÂMICO)
$pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
$filtro_categoria = isset($_GET['filtro_categoria']) ? trim($_GET['filtro_categoria']) : '';

$sql = "SELECT * FROM itens_cardapio WHERE 1=1";
$params = [];

if ($pesquisa !== '') {
    $sql .= " AND nome LIKE :pesquisa";
    $params[':pesquisa'] = "%" . $pesquisa . "%";
}

if ($filtro_categoria !== '') {
    $sql .= " AND categoria = :categoria";
    $params[':categoria'] = $filtro_categoria;
}

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

    <main class="conteudo-principal" style="flex-direction: column; justify-content: flex-start; padding: 20px;">
        
        <div class="box2" style="width: 60%; margin-bottom: 30px; padding: 30px;">
            <h2>Cadastrar Novo Item</h2>
            
            <?php if(isset($erro_cadastro)): ?>
                <p style="color: #ff4d4d; font-weight: bold;"><?php echo $erro_cadastro; ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="cadastrar" value="1">
                
                <div class="inputBox">
                    <input type="text" name="nome" class="inputUser" required>
                    <label class="labelInput">Nome do Doce</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="descricao" class="inputUser" required>
                    <label class="labelInput">Descrição</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="number" step="0.01" name="preco" class="inputUser" required>
                    <label class="labelInput">Preço (R$)</label>
                </div>
                <br>

                <div style="text-align: left; margin-top: 15px; background: rgba(255,255,255,0.05); padding: 15px; border-radius: 10px;">
                    <label style="color: white; display: block; margin-bottom: 10px; font-weight: bold;">Categoria do Produto</label>
                    
                    <?php if (!empty($categorias_existentes)): ?>
                        <div style="margin-bottom: 10px;">
                            <span style="color: #ddd; font-size: 14px; display: block; margin-bottom: 3px;">Escolher existente:</span>
                            <select name="categoria_existente" style="width: 100%; padding: 8px; border-radius: 5px; border: none;">
                                <?php foreach ($categorias_existentes as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <p style="color: #bbb; font-size: 13px; text-align: center; margin: 5px 0;">— OU —</p>
                    <?php endif; ?>

                    <div>
                        <span style="color: #ddd; font-size: 14px; display: block; margin-bottom: 3px;">Criar nova categoria:</span>
                        <input type="text" name="categoria_nova" placeholder="Ex: Tortas Geladas, Bebidas..." style="width: 97%; padding: 8px; border-radius: 5px; border: none;">
                    </div>
                </div>
                <br>

                <div style="text-align: left; margin-top: 15px;">
                    <label style="color: white; display: block; margin-bottom: 5px;">Foto do Produto:</label>
                    <input type="file" name="imagem" accept="image/*" required style="color: white;">
                </div>
                <br><br>
                <button type="submit" class="btn">Adicionar ao Cardápio</button>
            </form>
        </div>

        <div class="box2" style="width: 80%; padding: 20px; margin-bottom: 20px; background-color: rgba(0,0,0,0.1); box-shadow: none;">
            <form method="GET" style="display: flex; gap: 15px; align-items: center; justify-content: center; flex-wrap: wrap;">
                
                <input type="text" name="pesquisa" placeholder="Buscar por nome..." value="<?php echo htmlspecialchars($pesquisa); ?>" style="padding: 10px; border-radius: 5px; border: none; width: 250px;">
                
                <select name="filtro_categoria" style="padding: 10px; border-radius: 5px; border: none;">
                    <option value="">Todas as Categorias</option>
                    <?php foreach ($categorias_existentes as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $filtro_categoria === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn" style="padding: 10px 20px; margin: 0; width: auto; border-radius: 5px;">Filtrar</button>
                
                <?php if ($pesquisa !== '' || $filtro_categoria !== ''): ?>
                    <a href="gerenciar_itens.php" style="color: white; text-decoration: none; font-size: 14px;">Limpar Filtros</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="box2" style="width: 80%; padding: 30px;">
            <h2>Itens do Cardápio</h2>
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
                        <tr>
                            <td colspan="7">Nenhum item encontrado para essa busca.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($itens as $item): ?>
                            <tr class="<?php echo $item['status'] === 'pausado' ? 'linha-pausada' : ''; ?>">
                                <td>
                                    <img src="imagens/produtos/<?php echo $item['imagem']; ?>" alt="Foto" style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px;">
                                </td>
                                <td><?php echo htmlspecialchars($item['nome']); ?></td>
                                <td><span style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 5px; font-size: 13px;"><?php echo htmlspecialchars($item['categoria']); ?></span></td>
                                <td><?php echo htmlspecialchars($item['descricao']); ?></td>
                                <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $item['status']; ?>">
                                        <?php echo ucfirst($item['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="gerenciar_itens.php?id=<?php echo $item['id']; ?>&alterar_status=<?php echo $item['status']; ?>" class="btn-acao btn-status">
                                        <?php echo $item['status'] === 'ativo' ? 'Pausar' : 'Ativar'; ?>
                                    </a>

                                    <a href="editar_item.php?id=<?php echo $item['id']; ?>" class="btn-acao btn-editar">
                                        Editar
                                    </a>

                                    <a href="gerenciar_itens.php?id=<?php echo $item['id']; ?>&excluir=1" class="btn-acao btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este item?')">
                                        Excluir
                                    </a>
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