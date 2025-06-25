<?php
session_start();

// Impede cache para páginas protegidas
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Verifica se o usuário está autenticado
if (empty($_SESSION['usuario'])) {
    header('Location: login.php?erro=true');
    exit;
}
?>