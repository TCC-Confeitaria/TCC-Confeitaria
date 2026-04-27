<?php
class Conexao {
    private static $instance;

    public static function getConn() {
        if (!isset(self::$instance)) {
            try {
                // Configurações padrão do XAMPP: host=localhost, user=root, password vazio
                self::$instance = new PDO("mysql:host=localhost;dbname=confeitaria_tcc;charset=utf8", "root", "");
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro na conexão com o Banco de Dados: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}