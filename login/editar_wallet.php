<?php
session_start();
include 'conexao.php'; // Conexão com o banco

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_email"])) {
    header("Location: login.html");
    exit();
}

$usuario_email = $_SESSION['usuario_email'];
$nova_wallet = $_POST['nova_wallet'];

// Atualiza a wallet do usuário no banco de dados
$sql = "UPDATE wallets SET wallet = ? WHERE usuario_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $nova_wallet, $usuario_email);

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
