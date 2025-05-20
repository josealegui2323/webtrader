<?php
require_once 'conexao.php';

try {
    // Primeiro, limpa a tabela de planos existentes
    $conn->query("TRUNCATE TABLE planos");
    
    // Insere os novos planos
    $sql = "INSERT INTO planos (nome, descricao, valor, duracao_dias, taxa) VALUES 
            (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    $planos = [
        ['Plano 1', 'Plano inicial com taxa de 1.0% ao dia', 10.00, 20, 1.0],
        ['Plano 2', 'Plano com taxa de 1.2% ao dia', 50.00, 20, 1.2],
        ['Plano 3', 'Plano com taxa de 1.3% ao dia', 100.00, 20, 1.3],
        ['Plano 4', 'Plano com taxa de 1.4% ao dia', 120.00, 20, 1.4],
        ['Plano 5', 'Plano com taxa de 1.5% ao dia', 130.00, 20, 1.5],
        ['Plano 6', 'Plano com taxa de 2.0% ao dia', 150.00, 20, 2.0],
        ['Plano 7', 'Plano com taxa de 2.1% ao dia', 200.00, 20, 2.1],
        ['Plano 8', 'Plano com taxa de 2.3% ao dia', 250.00, 20, 2.3],
        ['Plano 9', 'Plano com taxa de 2.5% ao dia', 300.00, 20, 2.5],
        ['Plano 10', 'Plano com taxa de 3.0% ao dia', 400.00, 20, 3.0]
    ];
    
    foreach ($planos as $plano) {
        $stmt->bind_param("ssdii", $plano[0], $plano[1], $plano[2], $plano[3], $plano[4]);
        $stmt->execute();
    }
    
    echo "Planos atualizados com sucesso!";
    
} catch (Exception $e) {
    die("Erro ao atualizar planos: " . $e->getMessage());
}

$stmt->close();
$conn->close();
?> 