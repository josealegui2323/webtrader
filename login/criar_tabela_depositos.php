<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "sql300.infinityfree.com";
$user = "if0_38455108";
$pass = "UmAryyli9TR";
$dbname = "if0_38455108_webtraderbinance";

try {
    // Conecta ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQL para criar a tabela depositos
    $sql = "CREATE TABLE IF NOT EXISTS depositos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        valor DECIMAL(10,2) NOT NULL,
        comprovante VARCHAR(255),
        status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
        data_deposito TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        data_aprovacao TIMESTAMP NULL,
        observacao TEXT,
        hash_transacao VARCHAR(255),
        rede VARCHAR(50) DEFAULT 'TRC20',
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // Executa o SQL
    $pdo->exec($sql);
    echo "Tabela 'depositos' criada com sucesso!<br>";
    
    // Verifica se a tabela foi criada
    $result = $pdo->query("SHOW TABLES LIKE 'depositos'");
    if ($result->rowCount() > 0) {
        echo "Verificação: A tabela 'depositos' existe no banco de dados.<br>";
        
        // Mostra a estrutura da tabela
        echo "<br>Estrutura da tabela 'depositos':<br>";
        $result = $pdo->query("DESCRIBE depositos");
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