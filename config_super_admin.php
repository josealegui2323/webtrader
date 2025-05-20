<?php
// Configurações de acesso do super admin
define('SUPER_ADMIN_KEY', 'super_admin_2025');

// Função para verificar se o usuário tem acesso ao painel super admin
function verificarAcessoSuperAdmin() {
    // Verifica se a chave de acesso está correta
    if (!isset($_GET['key']) || $_GET['key'] !== SUPER_ADMIN_KEY) {
        header('Location: index.html');
        exit();
    }
    
    // Verifica se o usuário está logado
    if (!isset($_SESSION['id_usuario'])) {
        header('Location: index.html');
        exit();
    }
}
?>
