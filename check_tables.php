<?php
session_start();
include 'conexao.php';

// Verifica as tabelas existentes
echo "<pre>";
echo "Tabelas no banco de dados:\n";
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($result)) {
    echo $row[0] . "\n";
}

echo "\n\n";

// Verifica se o usuário está logado
echo "Usuario ID: " . (isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'não logado') . "\n";

echo "\n\n";

// Verifica dados do usuário
if (isset($_SESSION['usuario_id'])) {
    $sql_usuario = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql_usuario);
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Dados do usuário:\n";
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        print_r($usuario);
    } else {
        echo "Usuário não encontrado\n";
    }
}

echo "\n\n";

// Verifica planos do usuário
if (isset($_SESSION['usuario_id'])) {
    $sql_planos = "SELECT * FROM planos_usuarios WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql_planos);
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Planos do usuário:\n";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
}

echo "</pre>";
?>
