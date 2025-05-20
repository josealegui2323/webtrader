<?php
require_once 'config.php';

try {
    // Test database connection
    $pdo->query("SELECT 1");
    echo "Conexão com o banco de dados estabelecida com sucesso!<br>";
    
    // Test if tables exist
    $tables = ['users', 'plans', 'user_plans', 'wallet', 'wallet_transactions', 'transactions'];
    foreach ($tables as $table) {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            echo "Tabela '$table' existe<br>";
        } else {
            echo "Tabela '$table' NÃO existe<br>";
        }
    }
    
    // Test if default plans exist
    $result = $pdo->query("SELECT COUNT(*) as count FROM plans");
    $count = $result->fetch()['count'];
    echo "Número de planos cadastrados: $count<br>";
    
    // Test if any users exist
    $result = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $result->fetch()['count'];
    echo "Número de usuários cadastrados: $count<br>";
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?> 