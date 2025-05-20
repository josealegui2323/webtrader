<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>

<?php
session_start();
require_once 'config.php';
require_once 'wallet.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$wallet = new Wallet($pdo, $user_id);

// Get user information
$stmt = $pdo->prepare("SELECT email, cpf, phone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get current balance
$balance = $wallet->getBalance();

// Get transaction history
$transactions = $wallet->getTransactionHistory();

// Get active plans
$stmt = $pdo->prepare("
    SELECT p.name, p.description, up.expiration_date 
    FROM user_plans up 
    JOIN plans p ON up.plan_id = p.id 
    WHERE up.user_id = ? AND up.status = 'active' 
    AND up.expiration_date > NOW()
");
$stmt->execute([$user_id]);
$active_plans = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WebTrader</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .wallet-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .balance {
            font-size: 2em;
            color: #2c3e50;
            margin: 10px 0;
        }
        .transaction-list {
            margin-top: 20px;
        }
        .transaction-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .transaction-item:last-child {
            border-bottom: none;
        }
        .deposit { color: #27ae60; }
        .withdrawal { color: #e74c3c; }
        .plan_purchase { color: #3498db; }
        .plans-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .plan-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
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
        .btn-withdraw { background: #e74c3c; }
        .btn-logout { background: #7f8c8d; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Bem-vindo ao WebTrader</h1>
        
        <div class="wallet-section">
            <h2>Suas Informações</h2>
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <p>CPF: <?php echo htmlspecialchars($user['cpf']); ?></p>
            <p>Telefone: <?php echo htmlspecialchars($user['phone']); ?></p>
            
            <h2>Saldo da Carteira</h2>
            <div class="balance">R$ <?php echo number_format($balance, 2, ',', '.'); ?></div>
            
            <div class="action-buttons">
                <a href="deposit.php" class="btn btn-deposit">Depositar</a>
                <a href="withdraw.php" class="btn btn-withdraw">Sacar</a>
            </div>
        </div>

        <div class="wallet-section">
            <h2>Histórico de Transações</h2>
            <div class="transaction-list">
                <?php foreach ($transactions as $transaction): ?>
                    <div class="transaction-item">
                        <span class="<?php echo $transaction['type']; ?>">
                            <?php echo ucfirst($transaction['type']); ?>: 
                            R$ <?php echo number_format($transaction['amount'], 2, ',', '.'); ?>
                        </span>
                        <br>
                        <small>
                            <?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?> - 
                            <?php echo htmlspecialchars($transaction['description']); ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="wallet-section">
            <h2>Planos Ativos</h2>
            <div class="plans-section">
                <?php foreach ($active_plans as $plan): ?>
                    <div class="plan-card">
                        <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
                        <p><?php echo htmlspecialchars($plan['description']); ?></p>
                        <p>Expira em: <?php echo date('d/m/Y', strtotime($plan['expiration_date'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="action-buttons">
            <a href="logout.php" class="btn btn-logout">Sair</a>
        </div>
    </div>
</body>
</html>
