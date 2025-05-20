<?php
session_start();

require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

// Verifica se o usuário é super admin
$stmt = $conn->prepare("SELECT nivel_acesso FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if ($usuario['nivel_acesso'] !== 'super_admin') {
    header('Location: dashboard.php');
    exit();
}
?>
