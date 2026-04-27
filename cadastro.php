<?php
require_once __DIR__ . '/Cliente.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = new Cliente(
        $_POST['nome'], $_POST['email'], $_POST['telefone'], 
        $_POST['senha'], $_POST['data_nasc'], $_POST['cpf'],
        $_POST['rua'], $_POST['numero'], $_POST['bairro'], $_POST['cidade']
    );

    if ($cliente->salvarNoBanco()) {
        echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href='index.php';</script>";
    } else {
        $erro = "Erro ao cadastrar. O e-mail pode já estar em uso.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Confeitaria</title>
    <style>
        body { font-family: sans-serif; background: #fff0f3; display: flex; justify-content: center; padding: 20px; }
        form { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        input { width: 100%; padding: 8px; margin: 5px 0; box-sizing: border-box; border: 1px solid #ddd; }
        button { width: 100%; padding: 10px; background: #d81b60; color: white; border: none; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Criar Conta 🧁</h2>
        <?php if(isset($erro)) echo "<p style='color:red'>$erro</p>"; ?>
        <input type="text" name="nome" placeholder="Nome Completo" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <input type="text" name="telefone" placeholder="Telemóvel">
        <input type="date" name="data_nasc" required>
        <input type="text" name="cpf" placeholder="CPF">
        <h4>Endereço</h4>
        <input type="text" name="rua" placeholder="Rua">
        <input type="text" name="numero" placeholder="Nº">
        <input type="text" name="bairro" placeholder="Bairro">
        <input type="text" name="cidade" placeholder="Cidade">
        <button type="submit">Finalizar Cadastro</button>
    </form>
</body>
</html>