<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.html");
    exit();
}

// Verifica se veio via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $wallet = $_POST["wallet"];
    
    // Verifica se a wallet já existe para este usuário
    $sql = "SELECT id FROM wallets WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Atualiza a wallet existente
        $sql = "UPDATE wallets SET wallet = ?, data_atualizacao = NOW() WHERE usuario_id = ?";
    } else {
        // Insere nova wallet
        $sql = "INSERT INTO wallets (usuario_id, wallet) VALUES (?, ?)";
    }
    
    $stmt = $conn->prepare($sql);
    
    // Se for update, usa bind_param "ss"
    if (strpos($sql, 'UPDATE') !== false) {
        $stmt->bind_param("si", $wallet, $usuario_id);
    } else {
        // Se for insert, usa bind_param "is"
        $stmt->bind_param("is", $usuario_id, $wallet);
    }
    
    if ($stmt->execute()) {
        header("Location: meus_depositos.php?plano=" . $_POST['plano'] . "&valor=" . $_POST['valor'] . "&success=1");
        exit();
    } else {
        header("Location: meus_depositos.php?plano=" . $_POST['plano'] . "&valor=" . $_POST['valor'] . "&error=1");
        exit();
    }
}
?>
