<?php
include 'conexao.php';  // Inclui o arquivo de conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $cpf = $_POST["cpf"];
    $senha = $_POST["senha"];
    $confirma_senha = $_POST["confirma_senha"];
    $telefone = $_POST["telefone"];

    // Verificar se as senhas coincidem
    if ($senha !== $confirma_senha) {
        die("Erro: As senhas não coincidem!");
    }

    // Criptografar a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir no banco de dados
    $sql = "INSERT INTO usuarios (email, cpf, senha, telefone) VALUES ('$email', '$cpf', '$senha_hash', '$telefone')";

    if ($conn->query($sql) === TRUE) {
        echo "Cadastro realizado com sucesso!";
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }

    // Fechar a conexão
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cadastro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: rgb(227, 235, 162);
        }
        a:hover, a:link, a:visited {
            padding: 10px 20px;
            background-color: rgb(227, 235, 162);
            margin: 10px 20px;
            text-decoration: none;
            color: green;
            text-align: center;

        }
        #area-cabecalho a:link, #area-cabecalho a:visited {
        color: black;
         padding: 8px 12px;
        }

        #area-cabecalho a:hover {
            color: #f7b600;
            background: #fff;

        }
        a {
            padding: auto;
            margin: 20px
        }
        .color {
    background-color: #cde790;
}
h1{
    background: yellowgreen;
    padding: 20px;
    margin: 15px;
    width: 300px;
}
h2{
    background: rgb(238, 206, 23);
    padding: 20px;
    margin: 15px;
    width: 600px;
}
h3{
    background: rgb(204, 202, 190);
    padding: 20px;
    margin: 15px;
    width: 600px;
}
    </style>
</head>
<body>
    
    <a href="index.html">HOME</a> 
    
   
    <img src="imagens/2.png" width="1024px">

    
</body>
</html>