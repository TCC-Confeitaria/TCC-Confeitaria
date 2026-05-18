<?php
session_start();
session_destroy(); // Limpa todas as variáveis salvas (nome, tipo, id)
header("Location: index.php"); // Redireciona para a página inicial
exit;
?>