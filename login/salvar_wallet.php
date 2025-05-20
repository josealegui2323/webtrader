<?php
session_start();
require 'conexao.php'; // Inclui o arquivo de conexão

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_email"])) {
    header("Location: login.html");
    exit();
}

// Obtém o email do usuário e a wallet do formulário
$usuario_email = $_SESSION['usuario_email'];
$wallet = $_POST['wallet'];

// Verifica se o usuário já tem uma wallet cadastrada
$sql_verificar = "SELECT * FROM wallets WHERE usuario_email = ?";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("s", $usuario_email);
$stmt_verificar->execute();
$result = $stmt_verificar->get_result();

if ($result->num_rows > 0) {
    // Se já existe, atualiza a wallet
    $sql = "UPDATE wallets SET wallet = ? WHERE usuario_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $wallet, $usuario_email);
} else {
    // Se não existe, insere uma nova wallet
    $sql = "INSERT INTO wallets (usuario_email, wallet) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario_email, $wallet);
}

// Executa a query e verifica se foi bem-sucedida
if ($stmt->execute()) {
    echo "Wallet salva com sucesso!";
} else {
    echo "Erro ao salvar wallet: " . $conn->error;
}

// Fecha as conexões
$stmt_verificar->close();
$stmt->close();
$conn->close();
?>
