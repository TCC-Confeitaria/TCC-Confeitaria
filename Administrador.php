<?php
require_once __DIR__ . '/Usuario.php';

class Administrador extends Usuario {
    private $cargo;

    public function __construct($nome, $email, $telefone, $senha, $dataNascimento, $cargo) {
        parent::__construct($nome, $email, $telefone, $senha, $dataNascimento);
        $this->cargo = $cargo;
    }

    public function getCargo() { return $this->cargo; }
}