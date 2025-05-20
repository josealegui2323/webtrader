<?php
// processar_recuperacao.php
// Script to process password recovery request

// Load required files
require_once 'conexao.php';
require_once 'config/phpmailer_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate email
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    
    if (!$email) {
        header('Location: recuperar_senha.php?error=1');
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Simula que o email existe para segurança
        header('Location: recuperar_senha.php?success=1');
        exit;
    }

    $user = $result->fetch_assoc();
    
    // Generate token and expiry
    $token = bin2hex(random_bytes(16));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store token in database
    $stmt = $conn->prepare("INSERT INTO recuperacao_senha (usuario_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user['id'], $token, $expiry);
    $stmt->execute();
    
    // Generate reset link
    $resetLink = "https://webtraderbinance.com.br/dashboard/resetar_senha.php?token=" . $token;

    // Prepare email message
    $subject = "Recuperação de Senha - WebTrader";
    $message = "Olá {$user['nome']},\n\nVocê solicitou a recuperação de senha.\n\nPara redefinir sua senha, clique no link abaixo:\n\n{$resetLink}\n\nEste link é válido por 1 hora.\n\nSe você não solicitou esta recuperação, por favor, ignore este email.\n\nAtenciosamente,\nEquipe WebTrader";

    // Send email using our configuration
        if (enviarEmail($email, $subject, $message)) {
        header('Location: recuperar_senha.php?success=1');
    } else {
        header('Location: recuperar_senha.php?error=1');
    }
    exit;

    // Send email
    if (ModernEmailSender::enviar($email, $user['nome'], 'Recuperação de Senha - WebTrader', $mensagem)) {
        echo "Instruções de recuperação de senha foram enviadas para seu email. Por favor, verifique sua caixa de entrada e spam.";
    } else {
        echo "Erro ao enviar email. Por favor, tente novamente mais tarde.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
