<?php
include 'conexao.php';
$conexao = new mysqli("localhost", "root", "");

// Cria o banco de dados
$sql = "CREATE DATABASE IF NOT EXISTS webtrader";
if ($conexao->query($sql) === TRUE) {
    echo "Banco de dados criado com sucesso!<br>";
} else {
    echo "Erro ao criar banco: " . $conexao->error . "<br>";
}

// Seleciona o banco
$conexao->select_db("webtrader");

// Cria a tabela usuarios
$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    nome VARCHAR(255) NOT NULL,
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL
)";

if ($conexao->query($sql) === TRUE) {
    echo "Tabela usuarios criada com sucesso!";
} else {
    echo "Erro ao criar tabela: " . $conexao->error;
}
?> 