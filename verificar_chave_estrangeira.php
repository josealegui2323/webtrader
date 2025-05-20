<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "webtraderbinance";

try {
    // Conecta ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verifica se a chave estrangeira está funcionando
    $sql = "SELECT TABLE_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'wallets' AND REFERENCED_TABLE_NAME IS NOT NULL";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dbname]);
    
    echo "Chaves estrangeiras na tabela wallets:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo "\n";
    }
    
    // Verifica se há registros na tabela usuarios
    $sql = "SELECT COUNT(*) as total FROM usuarios";
    $result = $pdo->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    echo "\nTotal de usuários na tabela usuarios: " . $row['total'] . "\n";
    
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?>
