<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Verifica se veio via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $nova_carteira = $_POST["nova_carteira"];
    
    // Verifica se a carteira já existe para este usuário
    $sql = "SELECT COUNT(*) as existe FROM wallets WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Atualiza a carteira existente
        $sql = "UPDATE wallets SET wallet = ?, data_atualizacao = NOW() WHERE usuario_id = ?";
    } else {
        // Insere nova carteira
        $sql = "INSERT INTO wallets (usuario_id, wallet) VALUES (?, ?)";
    }
    
    $stmt = $conn->prepare($sql);
    
    // Se for update, usa bind_param "si"
    if (strpos($sql, 'UPDATE') !== false) {
        $stmt->bind_param("si", $nova_carteira, $usuario_id);
    } else {
        // Se for insert, usa bind_param "is"
        $stmt->bind_param("is", $usuario_id, $nova_carteira);
    }
    
    if ($stmt->execute()) {
        header("Location: minha_carteira.php?success=1");
        exit();
    } else {
        header("Location: minha_carteira.php?error=1");
        exit();
    }
}
?>
