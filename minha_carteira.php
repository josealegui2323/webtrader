<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

// Busca a carteira atual do usuário
$sql = "SELECT wallet FROM wallets WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$carteira = $result->fetch_assoc();

// Se não encontrou a carteira, cria uma nova linha vazia
if (!$carteira) {
    $sql = "INSERT INTO wallets (usuario_id) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $carteira = ['wallet' => ''];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Carteira - WebTrader</title>
    <style>
        :root {
            --primary: #4CAF50;
            --secondary: #388E3C;
            --background: #f4f4f4;
            --text: #222222;
            --accent: #81C784;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            min-height: 100vh;
            background: var(--background);
            color: var(--text);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        h1 {
            color: var(--primary);
            margin-bottom: 2rem;
            text-align: center;
        }

        .carteira-info {
            background: var(--background);
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .carteira-info h2 {
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .carteira-info p {
            margin-bottom: 1rem;
        }

        .carteira-info strong {
            color: var(--secondary);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text);
        }

        input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            font-size: 1rem;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: var(--secondary);
        }

        .nav-menu {
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-menu a {
            color: var(--primary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }

        .nav-menu a:hover {
            background: var(--accent);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-menu">
            <a href="plataforma.php">Voltar para Planos</a>
            <a href="logout.php">Sair</a>
        </nav>

        <h1>Minha Carteira USDT TRC20</h1>

        <div class="carteira-info">
            <h2>Informações da Carteira</h2>
            <p><strong>Carteira Atual:</strong> <?php echo htmlspecialchars($carteira['wallet'] ?? ''); ?></p>
        </div>

        <form action="editar_carteira.php" method="POST">
            <div class="form-group">
                <label for="nova_carteira">Nova Carteira USDT TRC20:</label>
                <input type="text" id="nova_carteira" name="nova_carteira" required>
            </div>
            <button type="submit" class="btn">Atualizar Carteira</button>
        </form>
    </div>
</body>
</html>
