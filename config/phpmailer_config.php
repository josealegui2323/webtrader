<?php
// Incluir os arquivos do PHPMailer diretamente
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function enviarEmail($emailDestino, $assunto, $mensagem, $nomeDestino = '') {
    $config = require __DIR__ . '/email_config.php';
    
    $mail = new PHPMailer(true);
    
    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['port'];
        
        // Remetente
        $mail->setFrom($config['from_email'], $config['from_name']);
        
        // Destinatário
        $mail->addAddress($emailDestino, $nomeDestino);
        
        // Conteúdo do e-mail
        $mail->isHTML(false);
        $mail->Subject = $assunto;
        $mail->Body = $mensagem;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar email: {$mail->ErrorInfo}");
        return false;
    }
}
