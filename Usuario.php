<?php
abstract class Usuario {
    protected $nome;
    protected $email;
    protected $telefone;
    protected $senha;
    protected $dataNascimento;

    public function __construct($nome, $email, $telefone, $senha, $dataNascimento) {
        $this->nome = $nome;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->senha = $senha; // A senha já deve chegar criptografada ou ser tratada aqui
        $this->dataNascimento = $dataNascimento;
    }

    public function getNome() { return $this->nome; }
    public function getEmail() { return $this->email; }
    public function getSenha() { return $this->senha; }
}