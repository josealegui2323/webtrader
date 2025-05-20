<?php
session_start();
require_once 'config.php';
require_once 'wallet.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$wallet = new Wallet($pdo, $user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $description = sanitize_input($_POST['description']);

    if ($amount <= 0) {
        $error = "O valor deve ser maior que zero";
    } else {
        if ($wallet->deposit($amount, $description)) {
            header("Location: dashboard.php?deposit=success");
            exit();
        } else {
            $error = "Erro ao processar o depósito";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depositar - WebTrader</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .deposit-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="number"],
        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }
        .btn-deposit { background: #27ae60; }
        .btn-back { background: #7f8c8d; }
        .error {
            color: #e74c3c;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="deposit-container">
        <h2>Depositar na Carteira</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="amount">Valor (R$)</label>
                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required>
            </div>

            <div class="form-group">
                <label for="description">Descrição (opcional)</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn btn-deposit">Depositar</button>
                <a href="dashboard.php" class="btn btn-back">Voltar</a>
            </div>
        </form>
    </div>
</body>
</html> 