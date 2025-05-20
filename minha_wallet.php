<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];
$sql = "SELECT * FROM wallets WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Wallet USDT TRC20</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebTrader</title>
    
    <style>
        :root {
            --primary:rgb(64, 97, 207);
            --secondary:rgb(59, 123, 197);
            --background: #f4f4f4;
            --text: #222222;
            --accent:rgb(129, 159, 199);
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

        #background-img {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.3;
            z-index: -1;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            flex: 1;
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

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .header h1 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .plans-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .plans-container h2 {
            color: var(--secondary);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: center;
            border: 1px solid #eee;
        }

        th {
            background: var(--primary);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        tr:nth-child(even) {
            background: #f8f8f8;
        }

        tr:hover {
            background: #f0f0f0;
        }

        button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        button:hover {
            background: var(--secondary);
        }

        .footer {
            text-align: center;
            padding: 1.5rem;
            background: var(--secondary);
            color: white;
            margin-top: auto;
        }

        .logout-link {
            display: inline-block;
            color: var(--primary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            margin-top: 1rem;
            border: 1px solid var(--primary);
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }

        .logout-link:hover {
            background: var(--primary);
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .nav-menu {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            th, td {
                padding: 0.75rem;
            }
        }

        .user-plan {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .user-plan h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            text-align: center;
        }

        .user-plan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .plan-info-card {
            background: var(--background);
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        .plan-info-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .plan-info-value {
            color: var(--text);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .plan-info-value.highlight {
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .user-plan-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="container">
<img id="background-img" src="imagens/2.png" alt="Background">
    <h1>Minha Wallet USDT TRC20</h1>

    <?php if ($row): ?>
        <p><strong>Wallet cadastrada:</strong> <span style="color: red;">(Oculta por segurança)</span></p>
    <?php else: ?>
        <p>Você ainda não cadastrou uma wallet.</p>
    <?php endif; ?>

    <h2>Editar Wallet</h2>
    <form action="editar_wallet.php" method="POST">
        <input type="text" name="nova_wallet" placeholder="Digite sua nova wallet" required>
        <button type="submit">Salvar Alterações</button>
    </form>

    <br><br>
    <a href="plataforma.php">Voltar</a>
    
</body>
</html>
