<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION["usuario_id"])) {
    die("Usuário não logado");
}

$usuario_id = $_SESSION["usuario_id"];
$test_wallet = "teste_wallet_" . time();

// Primeiro tenta atualizar se já existir
$sql = "UPDATE wallets SET wallet = ? WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $test_wallet, $usuario_id);

if ($stmt->execute()) {
    echo "Atualização bem-sucedida\n";
} else {
    // Se não conseguiu atualizar, tenta inserir
    $sql = "INSERT INTO wallets (usuario_id, wallet) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $usuario_id, $test_wallet);
    
    if ($stmt->execute()) {
        echo "Inserção bem-sucedida\n";
    } else {
        echo "Erro ao tentar inserir: " . $conn->error . "\n";
    }
}

// Verificar se a carteira foi salva
$sql = "SELECT * FROM wallets WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    echo "\nWallet encontrada no banco:\n";
    print_r($row);
} else {
    echo "\nNenhuma wallet encontrada para este usuário\n";
}

$conn->close();
?>
