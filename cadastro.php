<?php
// Importações
include 'conexao.php';

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

    // Verificar se o CPF já existe
    $check_cpf = $conn->query("SELECT id FROM usuarios WHERE cpf = '$cpf'");
    if ($check_cpf->num_rows > 0) {
        die("Erro: Este CPF já está cadastrado no sistema!");
    }

    // Verificar se o e-mail já existe
    $check_email = $conn->query("SELECT id FROM usuarios WHERE email = '$email'");
    if ($check_email->num_rows > 0) {
        die("Erro: Este e-mail já está cadastrado no sistema!");
    }

    // Criptografar a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir no banco de dados
    $sql = "INSERT INTO usuarios (email, cpf, senha, telefone) VALUES ('$email', '$cpf', '$senha_hash', '$telefone')";

    if ($conn->query($sql) === TRUE) {
        // Preparar o conteúdo do e-mail
        $subject = "Confirmação de Cadastro";
        $message = "Olá!\n\nSeu cadastro foi realizado com sucesso.\n\nDados do seu cadastro:\n\nCPF: $cpf\nEmail: $email\nSenha: $senha\n\nPor favor, guarde essas informações em um local seguro.\n\nAtenciosamente,\nSua Empresa";
        
        // Enviar e-mail usando a função de configuração
        require_once 'config/phpmailer_config.php';
        if (enviarEmail($email, $subject, $message)) {
            echo "<div style='text-align: center; padding: 20px;'>
                <h2>Cadastro realizado com sucesso!</h2>
                <p>Um e-mail de confirmação foi enviado para $email. Verifique sua caixa de entrada.</p>
                <p>Você será redirecionado para a página inicial em 2 segundos...</p>
            </div>";
        } else {
            echo "<div style='text-align: center; padding: 20px;'>
                <h2>Cadastro realizado com sucesso!</h2>
                <p>Não foi possível enviar o e-mail de confirmação, mas seu cadastro foi realizado com sucesso.</p>
                <p>Você será redirecionado para a página inicial em 2 segundos...</p>
            </div>";
        }
        header("refresh:2;url=index.html");
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }
    $conn->close();
}
?>
