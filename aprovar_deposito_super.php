<?php
require_once 'config_super_admin.php';
verificarAcessoSuperAdmin();
require_once 'conexao.php';

if (isset($_GET['id'])) {
    $id_deposito = $_GET['id'];
    
    // Atualiza o status do depósito para aprovado
    $sql = "UPDATE depositos SET status = 'aprovado', data_aprovacao = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_deposito);
    
    if ($stmt->execute()) {
        header('Location: painel_super_admin.php?key=' . SUPER_ADMIN_KEY);
        exit();
    } else {
        echo "Erro ao aprovar o depósito: " . $conn->error;
    }
} else {
    header('Location: painel_super_admin.php?key=' . SUPER_ADMIN_KEY);
    exit();
}
?>
