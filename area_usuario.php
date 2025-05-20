<?php
include 'conexao.php';  // Incluindo o arquivo de conexão

// Agora você pode usar a variável $conn para fazer operações no banco de dados
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>

<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION["usuario"])) {
    header("Location: login.html"); // Redireciona para a página de login se não estiver logado
    exit;
}

$usuario = $_SESSION["usuario"];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Usuário</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($usuario); ?>!</h1>
        <p>Você está logado na sua conta.</p>

        <div class="menu">
            <a href="trading.php" class="button">Ir para o WebTrader</a>
            <a href="account.php" class="button">Minha Conta</a>
            <a href="logout.php" class="button logout">Sair</a>
        </div>
    </div>
</body>
</html>
