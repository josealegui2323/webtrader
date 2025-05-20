<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "webtraderbinance";

try {
    // Conecta ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQL para criar a tabela wallets
    $sql = "CREATE TABLE IF NOT EXISTS wallets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        wallet VARCHAR(255) NOT NULL,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_usuario_id (usuario_id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // Executa o SQL
    $pdo->exec($sql);
    echo "Tabela 'wallets' criada com sucesso!<br>";
    
    // Verifica se a tabela foi criada
    $result = $pdo->query("SHOW TABLES LIKE 'wallets'");
    if ($result->rowCount() > 0) {
        echo "Verificação: A tabela 'wallets' existe no banco de dados.<br>";
        
        // Mostra a estrutura da tabela
        echo "<br>Estrutura da tabela 'wallets':<br>";
        $result = $pdo->query("DESCRIBE wallets");
        echo "<pre>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        echo "</pre>";
    }
    
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}
?> 