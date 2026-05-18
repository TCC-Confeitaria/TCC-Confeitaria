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
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <?php include 'cabecalho.php'; ?>
    <main class="conteudo-principal">
        <div class="box">
                <h1>Criar Conta</h1>
                <?php if(isset($erro)) echo "<p style='color:red'>$erro</p>"; ?>
            <form method="POST">
                <div class="inputBox">
                    <input type="text" name="nome" id="nome" class="inputUser"required>
                    <label for="nome" class="labelInput">Nome Completo</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="email" id="email" class="inputUser"required>
                    <label for="email" class="labelInput">E-mail</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="password" name="senha" id="senha" class="inputUser"required>
                    <label for="senha" class="labelInput">Senha</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="telefone" id="telefone" class="inputUser"required>
                    <label for="telefone" class="labelInput">Telemóvel</label>
                </div>
                <br>
                <label for="data_nasc" class="labelDataNasc">Data de Nascimento</label>
                <input type="date" name="data_nasc" id="data_nasc" required>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="cpf" id="cpf" class="inputUser" required>
                    <label for="cpf" class="labelInput">CPF</label>
                </div>
                <br>
                <h4>Endereço</h4>
                <div class="inputBox">
                    <input type="text" name="rua" id="rua" class="inputUser" required>
                    <label for="rua" class="labelInput">Rua</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="numero" id="numero" class="inputUser" required>
                    <label for="numero" class="labelInput">Nº</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="bairro" id="bairro" class="inputUser" required>
                    <label for="bairro" class="labelInput">Bairro</label>
                </div>
                <br>
                <div class="inputBox">
                    <input type="text" name="cidade" id="cidade" class="inputUser" required>
                    <label for="cidade" class="labelInput">Cidade</label>
                </div>
                <br>
                <button type="submit" class="btn">Finalizar Cadastro</button>
            </form>
            <p><a href="login.php">Já tem conta? Faça login aqui</a></p>
        </div>
    </main>
</body>
</html>