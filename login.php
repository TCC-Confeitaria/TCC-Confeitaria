<?php
session_start();
require_once __DIR__ . '/Conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $pdo = Conexao::getConn();
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_tipo'] = $user['tipo'];
        header("Location: index.php");
        exit;
    }
    $erro = "E-mail ou senha inválidos!";
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
        <div class="box">
            <h1>Login</h1>
            <br>
            <?php if(isset($erro)) echo "<p style='color:red'>$erro</p>"; ?>
            <form method="POST">
                <div class="inputBox">
                    <input type="text" name="email" id="email" class="inputUser" placeholder=" " value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    <label for="email" class="labelInput">E-mail</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="password" name="senha" id="senha" class="inputUser" placeholder=" " required>
                    <label for="senha" class="labelInput">Senha</label>
                </div>
                <br>
                <button type="submit" class="btn">Entrar</button>
            </form>
            <p><a href="cadastro.php">Não tem conta? Registe-se aqui</a></p>
        </div>
    </main>
</body>
</html>