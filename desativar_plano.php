<?php
require_once 'verificar_super_admin.php';
require_once 'conexao.php';

if (isset($_GET['id'])) {
    $id_plano = $_GET['id'];
    
    // Atualiza o status do plano para inativo
    $sql = "UPDATE planos SET status = 'inativo' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_plano);
    
    if ($stmt->execute()) {
        header('Location: super_admin.php');
        exit();
    } else {
        echo "Erro ao desativar o plano: " . $conn->error;
    }
} else {
    header('Location: super_admin.php');
    exit();
}
?>
