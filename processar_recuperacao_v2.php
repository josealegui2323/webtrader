<?php
// processar_recuperacao_v2.php
// Script to process password recovery request

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load required files
require_once 'conexao.php';
require_once 'lib/ModernEmailSender.php';

// Initialize email sender
Dashboard\ModernEmailSender::init();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate email
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    
    if (!$email) {
        echo "Invalid email address. Please try again.";
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Email not found. Please check and try again.";
        exit;
    }

    $user = $result->fetch_assoc();
    
    // Generate token and expiry
    $token = bin2hex(random_bytes(16));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Generate reset link
    $resetLink = "http://localhost/dashboard/resetar_senha.php?token=" . $token;

    // Prepare email message
    $mensagem = "
        <p>Hello {$user['nome']},</p>
        <p>You requested a password recovery. Click the link below to reset your password:</p>
        <p><a href='{$resetLink}'>Reset Password</a></p>
        <p>This link is valid for 1 hour.</p>
        <p>If you did not request this recovery, please ignore this email.</p>
    ";

    // Send email
    if (Dashboard\ModernEmailSender::enviar($email, $user['nome'], 'Password Recovery - WebTrader', $mensagem)) {
        echo "Password recovery instructions have been sent to your email. Please check your inbox and spam folder.";
    } else {
        echo "Error sending email. Please try again later.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
