<?php
session_start();
include 'conexao.php'; // Inclui o arquivo de conexão

if (!isset($_SESSION["usuario_id"])) {
    header("Location: salvar_wallet.php");
    exit();
}

$nova_wallet = $_POST['nova_wallet'] ?? '';

if (empty($nova_wallet)) {
    echo "<script>alert('Por favor, insira uma nova wallet.'); window.location.href='minha_wallet.php';</script>";
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

$sql = "UPDATE wallets SET wallet = ? WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "<script>alert('Erro na preparação da consulta: " . $conn->error . "'); window.location.href='minha_wallet.php';</script>";
    exit();
}

$stmt->bind_param("si", $nova_wallet, $usuario_id);

if ($stmt->execute()) {
    // Redireciona para minha_wallet.php após atualizar
    header("Location: minha_wallet.php");
    exit(); // Garante que o script pare de executar após o redirecionamento
} else {
    echo "<script>alert('Erro ao atualizar wallet!'); window.location.href='minha_wallet.php';</script>";
}

$stmt->close();
$conn->close();
?>