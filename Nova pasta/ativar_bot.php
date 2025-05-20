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

    // Aqui você deve colocar a lógica para ativar o bot para o usuário/plano
    // Por exemplo, atualizar um campo no banco de dados, chamar um script, etc.
    // Vou simular a ativação com uma mensagem de sucesso.

    // Exemplo: Atualizar status do bot no banco (exemplo fictício)
    /*
    $sql = "UPDATE planos_adquiridos SET bot_ativo = 1 WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $plano_id, $usuario_id);
    if ($stmt->execute()) {
        $_SESSION['bot_activation_success'] = "Bot ativado com sucesso!";
    } else {
        $_SESSION['bot_activation_error'] = "Erro ao ativar o bot: " . $stmt->error;
    }
    $stmt->close();
    */

    // Para agora, apenas definir sucesso
    $_SESSION['bot_activation_success'] = "Bot ativado com sucesso!";

    header("Location: plano.php");
    exit();
} else {
    header("Location: plano.php");
    exit();
}
?>
