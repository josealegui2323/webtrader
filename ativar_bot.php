<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $plano_id = $_POST["plano_id"] ?? null;

    if (!$plano_id) {
        $_SESSION['bot_activation_error'] = "Plano inválido.";
        header("Location: plano.php");
        exit();
    }

    // Caminho do arquivo de sinal para ativar o bot
    $signal_file = __DIR__ . DIRECTORY_SEPARATOR . 'bot' . DIRECTORY_SEPARATOR . 'activate_bot.signal';

    // Cria o arquivo de sinal para o serviço Windows detectar
    if (file_put_contents($signal_file, "activate\n") !== false) {
        $_SESSION['bot_activation_success'] = "Sinal para ativar o bot enviado com sucesso!";
    } else {
        $_SESSION['bot_activation_error'] = "Erro ao enviar sinal para ativar o bot.";
    }

    header("Location: plano.php");
    exit();
} else {
    header("Location: plano.php");
    exit();
}
?>
