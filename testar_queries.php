<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION["usuario_id"])) {
    die("Usuário não logado");
}

$usuario_id = $_SESSION["usuario_id"];
$test_wallet = "teste_wallet_" . time();

// Primeiro, verificamos se o usuário existe na tabela usuarios
echo "Verificando usuário na tabela usuarios:\n";
$sql = "SELECT id FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Usuário encontrado com ID: " . $usuario_id . "\n";
} else {
    die("Usuário não encontrado na tabela usuarios");
}

// Agora tentamos inserir a carteira
echo "\nTentando inserir carteira:\n";
$sql = "INSERT INTO wallets (usuario_id, wallet) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $usuario_id, $test_wallet);

if ($stmt->execute()) {
    echo "Inserção bem-sucedida!\n";
    echo "ID da carteira inserida: " . $conn->insert_id . "\n";
} else {
    echo "Erro ao inserir: " . $conn->error . "\n";
}

// Verificamos se a carteira foi inserida
echo "\nVerificando carteira inserida:\n";
$sql = "SELECT * FROM wallets WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Carteira encontrada:\n";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
        echo "\n";
    }
} else {
    echo "Nenhuma carteira encontrada\n";
}

$conn->close();
?>
