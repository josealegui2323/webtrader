<?php
session_start();
require_once 'conexao.php';

// Testar conexão com o banco
try {
    $token = $_GET['token'] ?? '';
    
    if (empty($token)) {
        echo "Nenhum token fornecido";
    } else {
        // Verificar token na tabela
        $stmt = $conn->prepare("SELECT * FROM recuperacao_senha WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo "Token não encontrado na tabela";
        } else {
            $recuperacao = $result->fetch_assoc();
            echo "Token encontrado!\n";
            echo "Dados: ";
            print_r($recuperacao);
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}

$conn->close();
