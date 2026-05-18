<?php
session_start();
require_once __DIR__ . '/Conexao.php';

// Segurança: Se não for admin, tchau
if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$pdo = Conexao::getConn();

// Verifica se o ID do item foi passado na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: gerenciar_itens.php");
    exit;
}

$id = $_GET['id'];

// Busca os dados atuais do item para colocar no formulário
$stmt = $pdo->prepare("SELECT * FROM itens_cardapio WHERE id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

// Se o item não existir no banco, volta para a listagem
if (!$item) {
    header("Location: gerenciar_itens.php");
    exit;
}

// Busca as categorias para o admin poder mudar se quiser
$stmt_cat = $pdo->query("SELECT DISTINCT categoria FROM itens_cardapio WHERE categoria != '' ORDER BY categoria ASC");
$categorias_existentes = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);


// PROCESSA A ATUALIZAÇÃO (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $preco = trim($_POST['preco']);
    
    $categoria_nova = trim($_POST['categoria_nova']);
    $categoria_existente = isset($_POST['categoria_existente']) ? trim($_POST['categoria_existente']) : '';
    $categoria = ($categoria_nova !== '') ? $categoria_nova : $categoria_existente;

    // Mantém o nome da imagem antiga por padrão
    $nome_imagem = $item['imagem']; 

    // Validação estrita contra nulos
    if ($nome === '' || $descricao === '' || $preco === '' || $categoria === '') {
        $erro = "Todos os campos são obrigatórios!";
    } else {
        // Se o usuário enviou uma NOVA imagem, processa o upload dela
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $arquivo = $_FILES['imagem'];
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            $nome_imagem = uniqid() . "." . $extensao;
            $diretorio_destino = __DIR__ . '/imagens/produtos/';
            
            if (!is_dir($diretorio_destino)) {
                mkdir($diretorio_destino, 0777, true);
            }
            move_uploaded_file($arquivo['tmp_name'], $diretorio_destino . $nome_imagem);
            
            // Opcional: Você poderia deletar a foto antiga aqui para não acumular lixo na pasta
        }

        // Executa o UPDATE no banco de dados
        $stmt_update = $pdo->prepare("UPDATE itens_cardapio SET nome = :nome, descricao = :descricao, preco = :preco, imagem = :imagem, categoria = :categoria WHERE id = :id");
        $stmt_update->execute([
            ':nome' => $nome,
            ':descricao' => $descricao,
            ':preco' => $preco,
            ':imagem' => $nome_imagem,
            ':categoria' => $categoria,
            ':id' => $id
        ]);

        // Sucesso! Volta para a página de gerenciamento
        header("Location: gerenciar_itens.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Item - Confeitaria</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <?php include 'cabecalho.php'; ?>

    <main class="conteudo-principal">
        <div class="box2" style="width: 50%; padding: 30px;">
            <h2>Editar Item do Cardápio</h2>
            <p style="color: #ddd; margin-bottom: 20px;">Modificando: <strong><?php echo htmlspecialchars($item['nome']); ?></strong></p>

            <?php if(isset($erro)): ?>
                <p style="color: #ff4d4d; font-weight: bold;"><?php echo $erro; ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                
                <div class="inputBox">
                    <input type="text" name="nome" class="inputUser" value="<?php echo htmlspecialchars($item['nome']); ?>" required>
                    <label class="labelInput">Nome do Doce</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="descricao" class="inputUser" value="<?php echo htmlspecialchars($item['descricao']); ?>" required>
                    <label class="labelInput">Descrição</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="number" step="0.01" name="preco" class="inputUser" value="<?php echo $item['preco']; ?>" required>
                    <label class="labelInput">Preço (R$)</label>
                </div>
                <br>

                <div style="text-align: left; margin-top: 15px; background: rgba(255,255,255,0.05); padding: 15px; border-radius: 10px;">
                    <label style="color: white; display: block; margin-bottom: 10px; font-weight: bold;">Categoria</label>
                    
                    <div style="margin-bottom: 10px;">
                        <span style="color: #ddd; font-size: 14px; display: block; margin-bottom: 3px;">Mudar para existente:</span>
                        <select name="categoria_existente" style="width: 100%; padding: 8px; border-radius: 5px; border: none;">
                            <?php foreach ($categorias_existentes as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $item['categoria'] === $cat ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p style="color: #bbb; font-size: 13px; text-align: center; margin: 5px 0;">— OU —</p>
                    <div>
                        <span style="color: #ddd; font-size: 14px; display: block; margin-bottom: 3px;">Mudar para uma nova:</span>
                        <input type="text" name="categoria_nova" placeholder="Nova categoria..." style="width: 97%; padding: 8px; border-radius: 5px; border: none;">
                    </div>
                </div>
                <br>

                <div style="text-align: left; margin-top: 15px;">
                    <label style="color: white; display: block; margin-bottom: 5px;">Foto Atual:</label>
                    <img src="imagens/produtos/<?php echo $item['imagem']; ?>" alt="Atual" style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px; margin-bottom: 10px; display: block;">
                    
                    <label style="color: white; display: block; margin-bottom: 5px;">Substituir Foto (Opcional):</label>
                    <input type="file" name="imagem" accept="image/*" style="color: white;">
                </div>
                <br><br>

                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn">Salvar Alterações</button>
                    <a href="gerenciar_itens.php" style="background: #7f8c8d; color: white; text-decoration: none; padding: 12px; border-radius: 25px; width: 100%; text-align: center; font-weight: bold;">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>