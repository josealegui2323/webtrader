<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'conexao.php';

try {
    // Verificar se a tabela já existe
    $check_table = $pdo->query("SHOW TABLES LIKE 'depositos'");
    if ($check_table->rowCount() > 0) {
        echo "A tabela 'depositos' já existe.<br>";
        // Mostrar estrutura atual da tabela
        echo "<br>Estrutura atual da tabela:<br>";
        $structure = $pdo->query("DESCRIBE depositos");
        echo "<pre>";
        while ($row = $structure->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
        echo "</pre>";
    } else {
        // Criar tabela de depósitos
        $sql = "CREATE TABLE IF NOT EXISTS depositos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            valor DECIMAL(10,2) NOT NULL,
            comprovante VARCHAR(255),
            status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
            data_deposito DATETIME DEFAULT CURRENT_TIMESTAMP,
            data_aprovacao DATETIME,
            rede VARCHAR(10) DEFAULT 'TRC20',
            observacoes TEXT,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $pdo->exec($sql);
        echo "Tabela 'depositos' criada com sucesso!<br>";
    }

    // Criar diretório para comprovantes se não existir
    $upload_dir = 'uploads/comprovantes/';
    if (!file_exists($upload_dir)) {
        if (mkdir($upload_dir, 0777, true)) {
            echo "Diretório 'uploads/comprovantes' criado com sucesso!<br>";
        } else {
            echo "Erro ao criar diretório 'uploads/comprovantes'. Verifique as permissões.<br>";
        }
    } else {
        echo "Diretório 'uploads/comprovantes' já existe.<br>";
    }

    // Verificar permissões do diretório
    if (is_writable($upload_dir)) {
        echo "Diretório 'uploads/comprovantes' tem permissões de escrita.<br>";
    } else {
        echo "ATENÇÃO: Diretório 'uploads/comprovantes' NÃO tem permissões de escrita!<br>";
    }

    // Limpar dados de exemplo existentes
    $pdo->exec("DELETE FROM depositos WHERE comprovante LIKE 'uploads/comprovantes/exemplo%'");
    
    // Inserir alguns depósitos de exemplo (opcional)
    $sql_exemplo = "INSERT INTO depositos (usuario_id, valor, comprovante, status, data_deposito, rede) VALUES 
        (1, 100.00, 'uploads/comprovantes/exemplo1.jpg', 'aprovado', NOW(), 'TRC20'),
        (1, 250.00, 'uploads/comprovantes/exemplo2.jpg', 'pendente', NOW(), 'TRC20'),
        (2, 500.00, 'uploads/comprovantes/exemplo3.jpg', 'aprovado', NOW(), 'TRC20')";
    
    $pdo->exec($sql_exemplo);
    echo "Dados de exemplo inseridos com sucesso!<br>";

    // Verificar dados inseridos
    echo "<br>Dados atuais na tabela:<br>";
    $result = $pdo->query("SELECT * FROM depositos");
    echo "<pre>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";

} catch(PDOException $e) {
    echo "Erro ao criar tabela: " . $e->getMessage() . "<br>";
    echo "Código do erro: " . $e->getCode() . "<br>";
    echo "Stack trace:<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?> 