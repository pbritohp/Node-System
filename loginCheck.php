<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

$logado = true;
$tipoUsuario = $_SESSION['tipo_user'];
$cpf = $_SESSION['cpf'];
?>
