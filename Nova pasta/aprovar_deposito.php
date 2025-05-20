<?php
session_start();
include 'conexao.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION["usuario_id"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deposito_id"])) {
    $deposito_id = $_POST["deposito_id"];
    
    // Inicia a transação
    $conn->begin_transaction();
    
    try {
        // Atualiza o status do depósito para aprovado
        $sql = "UPDATE depositos SET status = 'aprovado', data_aprovacao = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deposito_id);
        $stmt->execute();
        $stmt->close();
        
        // Busca o usuário e valor do depósito
        $sql = "SELECT usuario_id, valor FROM depositos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deposito_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $deposito = $result->fetch_assoc();
        $stmt->close();
        
        // Atualiza o status do plano para ativo
        $sql = "UPDATE planos_adquiridos SET status = 'ativo' WHERE usuario_id = ? AND status = 'pendente'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deposito['usuario_id']);
        $stmt->execute();
        $stmt->close();
        
        // Confirma a transação
        $conn->commit();
        
        header("Location: admin_depositos.php?success=1");
        exit();
        
    } catch (Exception $e) {
        // Desfaz a transação em caso de erro
        $conn->rollback();
        die("Erro ao aprovar depósito: " . $e->getMessage());
    }
} else {
    header("Location: admin_depositos.php");
    exit();
}
?> 