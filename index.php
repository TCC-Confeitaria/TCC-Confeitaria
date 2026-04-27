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
        
        if ($user['tipo'] === 'admin') {
            header("Location: painel_admin.php");
        } else {
            header("Location: dashboard.php");
        }
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
    <style>
        body { font-family: sans-serif; background: #fff0f3; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        .btn { background: #4caf50; color: white; border: none; padding: 10px; width: 100%; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🧁 Login Confeitaria</h1>
        <?php if(isset($erro)) echo "<p style='color:red'>$erro</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit" class="btn">Entrar</button>
        </form>
        <p><a href="cadastro.php">Não tem conta? Registe-se aqui</a></p>
    </div>
</body>
</html>