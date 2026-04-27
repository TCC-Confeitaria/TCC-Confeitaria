<?php
require_once __DIR__ . '/Usuario.php';
require_once __DIR__ . '/Conexao.php';

class Cliente extends Usuario {
    private $cpf;
    private $rua;
    private $numero;
    private $bairro;
    private $cidade;

    public function __construct($nome, $email, $telefone, $senha, $dataNascimento, $cpf, $rua, $numero, $bairro, $cidade) {
        // Criptografamos a senha antes de enviar para o construtor pai
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        parent::__construct($nome, $email, $telefone, $senhaHash, $dataNascimento);
        
        $this->cpf = $cpf;
        $this->rua = $rua;
        $this->numero = $numero;
        $this->bairro = $bairro;
        $this->cidade = $cidade;
    }

    public function salvarNoBanco() {
        try {
            $pdo = Conexao::getConn();
            $sql = "INSERT INTO usuarios (nome, email, telefone, senha, data_nascimento, cpf, rua, numero, bairro, cidade, tipo) 
                    VALUES (:nome, :email, :tel, :senha, :nasc, :cpf, :rua, :num, :bairro, :cid, 'cliente')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nome', $this->nome);
            $stmt->bindValue(':email', $this->email);
            $stmt->bindValue(':tel', $this->telefone);
            $stmt->bindValue(':senha', $this->senha);
            $stmt->bindValue(':nasc', $this->dataNascimento);
            $stmt->bindValue(':cpf', $this->cpf);
            $stmt->bindValue(':rua', $this->rua);
            $stmt->bindValue(':num', $this->numero);
            $stmt->bindValue(':bairro', $this->bairro);
            $stmt->bindValue(':cid', $this->cidade);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}