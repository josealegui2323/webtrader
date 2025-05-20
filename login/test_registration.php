<?php
require_once 'config.php';

try {
    // Test database connection
    $pdo->query("SELECT 1");
    echo "Conexão com o banco de dados estabelecida com sucesso!<br>";
    
    // Test users table structure
    $result = $pdo->query("DESCRIBE users");
    echo "<h3>Estrutura da tabela users:</h3>";
    while ($row = $result->fetch()) {
        echo "Campo: {$row['Field']}, Tipo: {$row['Type']}, Null: {$row['Null']}, Key: {$row['Key']}<br>";
    }
    
    // Test wallet table structure
    $result = $pdo->query("DESCRIBE wallet");
    echo "<h3>Estrutura da tabela wallet:</h3>";
    while ($row = $result->fetch()) {
        echo "Campo: {$row['Field']}, Tipo: {$row['Type']}, Null: {$row['Null']}, Key: {$row['Key']}<br>";
    }
    
    // Test password hashing
    $test_password = "test123";
    $hashed = hash_password($test_password);
    echo "<h3>Teste de hash de senha:</h3>";
    echo "Senha original: $test_password<br>";
    echo "Hash gerado: $hashed<br>";
    echo "Verificação: " . (verify_password($test_password, $hashed) ? "OK" : "FALHOU") . "<br>";
    
    // Test if tables have proper indexes
    $result = $pdo->query("SHOW INDEX FROM users");
    echo "<h3>Índices da tabela users:</h3>";
    while ($row = $result->fetch()) {
        echo "Índice: {$row['Key_name']}, Coluna: {$row['Column_name']}<br>";
    }
    
    // Test if foreign key constraints exist
    $result = $pdo->query("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            REFERENCED_TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    echo "<h3>Chaves estrangeiras:</h3>";
    while ($row = $result->fetch()) {
        echo "Tabela: {$row['TABLE_NAME']}, Coluna: {$row['COLUMN_NAME']}, Referência: {$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']}<br>";
    }
    
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?> 