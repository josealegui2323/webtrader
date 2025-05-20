<?php
require_once 'conexao.php';

try {
    // Adiciona a coluna taxa se ela não existir
    $result = $conn->query("SHOW COLUMNS FROM planos LIKE 'taxa'");
    if ($result->num_rows == 0) {
        $conn->query("ALTER TABLE planos ADD COLUMN taxa DECIMAL(5,2) NOT NULL DEFAULT 0.00");
        echo "Coluna 'taxa' adicionada com sucesso!<br>";
    } else {
        echo "Coluna 'taxa' já existe.<br>";
    }
    
    // Executa o script de atualização dos planos
    include 'atualizar_planos.php';
    
} catch (Exception $e) {
    die("Erro ao adicionar coluna: " . $e->getMessage());
}

$conn->close();
?> <?php
require_once 'conexao.php';

try {
    // Adiciona a coluna taxa se ela não existir
    $result = $conn->query("SHOW COLUMNS FROM planos LIKE 'taxa'");
    if ($result->num_rows == 0) {
        $conn->query("ALTER TABLE planos ADD COLUMN taxa DECIMAL(5,2) NOT NULL DEFAULT 0.00");
        echo "Coluna 'taxa' adicionada com sucesso!<br>";
    } else {
        echo "Coluna 'taxa' já existe.<br>";
    }
    
    // Executa o script de atualização dos planos
    include 'atualizar_planos.php';
    
} catch (Exception $e) {
    die("Erro ao adicionar coluna: " . $e->getMessage());
}

$conn->close();
?> 