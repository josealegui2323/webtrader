<?php
$servidor = "sql210.infinityfree.com"; // Host do banco de dados (substitua XXX pelo número correto)
$usuario = "if0_38304088"; // Usuário do banco de dados
$senha = "2XvkYobU1pQyl"; // Senha do banco de dados
$banco = "sql210.infinityfree.com"; // Nome do banco de dados

// Criar conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}
?>
