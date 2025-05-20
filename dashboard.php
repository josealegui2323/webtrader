<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>

<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];

?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard WebTrader</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Bem-vindo, <?php echo htmlspecialchars($user); ?>!</h2>
        <p>Escolha uma opção abaixo para começar a operar:</p>
        
        <div class="menu">
            <a href="trading.php" class="button">Ir para o WebTrader</a>
            <a href="account.php" class="button">Minha Conta</a>
            <a href="logout.php" class="button logout">Sair</a>
        </div>
    </div>
</body>
</html>
