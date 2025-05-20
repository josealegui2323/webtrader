<?php
require_once 'verificar_super_admin.php';
require_once 'conexao.php';

if (isset($_GET['id'])) {
    $id_deposito = $_GET['id'];
    
    // Atualiza o status do depósito para reprovado
    $sql = "UPDATE depositos SET status = 'reprovado' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_deposito);
    
    if ($stmt->execute()) {
        header('Location: super_admin.php');
        exit();
    } else {
        echo "Erro ao reprovar o depósito: " . $conn->error;
    }
} else {
    header('Location: super_admin.php');
    exit();
}
?>
