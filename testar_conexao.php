<?php
session_start();
include 'conexao.php';

// Verificar se a conexão está funcionando
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

echo "Conexão com o banco de dados funcionando!";

// Verificar se o usuário está logado
echo "\n\nInformações do usuário:\n";
if (isset($_SESSION["usuario_id"])) {
    echo "usuario_id: " . $_SESSION["usuario_id"] . "\n";
} else {
    echo "usuario_id não encontrado na sessão\n";
}

// Verificar se a tabela wallets existe
echo "\n\nVerificando tabela wallets:\n";
$result = $conn->query("SHOW TABLES LIKE 'wallets'");
if ($result->num_rows > 0) {
    echo "Tabela wallets existe\n";
    
    // Verificar estrutura da tabela
    echo "\nEstrutura da tabela:\n";
    $result = $conn->query("DESCRIBE wallets");
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Tabela wallets não existe\n";
}

$conn->close();
?>
