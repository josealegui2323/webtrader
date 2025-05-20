<?php
require_once 'lib/SimpleEmail.php';

// Exemplo de envio de email simples
$email = new Email\SimpleEmail(
    'destinatario@email.com',
    'Assunto do Email',
    'Esta é uma mensagem de teste.'
);

if ($email->send()) {
    echo "Email enviado com sucesso!";
} else {
    echo "Erro ao enviar email.";
}

// Exemplo de envio de email em HTML
$emailHtml = new Email\SimpleEmail(
    'destinatario@email.com',
    'Assunto do Email em HTML',
    '<h1>Olá!</h1><p>Esta é uma mensagem em <strong>HTML</strong>.</p>'
);
$emailHtml->setHtml(true);

if ($emailHtml->send()) {
    echo "Email em HTML enviado com sucesso!";
} else {
    echo "Erro ao enviar email em HTML.";
}
