<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.html");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];
$mensagem = '';

// Processa o formulário de saque
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor = floatval($_POST["valor"]);
    
    // Verifica se o valor é válido
    if ($valor <= 0) {
        $mensagem = "O valor do saque deve ser maior que zero.";
    } else {
        // Inicia a transação
        $conn->begin_transaction();
        
        try {
            // Insere o saque
            $sql = "INSERT INTO saques (usuario_id, valor, status) VALUES (?, ?, 'pendente')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("id", $usuario_id, $valor);
            $stmt->execute();
            $stmt->close();
            
            // Confirma a transação
            $conn->commit();
            $mensagem = "Saque solicitado com sucesso! Aguarde a aprovação.";
            
        } catch (Exception $e) {
            // Desfaz a transação em caso de erro
            $conn->rollback();
            $mensagem = "Erro ao solicitar saque: " . $e->getMessage();
        }
    }
}

// Busca os saques do usuário
$sql = "SELECT * FROM saques WHERE usuario_id = ? ORDER BY data_solicitacao DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$saques = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Saque</title>
    <style>
        :root {
            --primary:rgb(30, 33, 85);
            --secondary:rgb(67, 69, 211);
            --background: #f4f4f4;
            --text: #222222;
            --accent:rgb(65, 76, 177);
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

        .btn-ativar {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .btn-ativar:hover {
            background: var(--secondary);
        }
    </style>
</head>
<body>
<img id="background-img" src="imagens/2.png" alt="Background">
    <div class="container">
        <div class="nav-menu">
            <a href="plano.php">Meu Plano</a>
            <a href="plataforma.php">Planos</a>
            <a href="logout.php">Sair</a>
        </div>

        <h1>Solicitar Saque</h1>

        <?php if ($mensagem): ?>
            <div class="mensagem <?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro'; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="valor">Valor do Saque (R$):</label>
                <input type="number" id="valor" name="valor" step="0.01" min="0.01" required>
            </div>
            <button type="submit">Solicitar Saque</button>
        </form>

        <h2>Histórico de Saques</h2>
        <?php if (count($saques) > 0): ?>
            <?php foreach ($saques as $saque): ?>
                <div class="saque-card">
                    <div class="status-saque <?php echo $saque['status']; ?>">
                        Status: <?php echo ucfirst($saque['status']); ?>
                    </div>
                    <p><strong>Valor:</strong> R$ <?php echo number_format($saque['valor'], 2, ',', '.'); ?></p>
                    <p><strong>Data da Solicitação:</strong> <?php echo date('d/m/Y H:i', strtotime($saque['data_solicitacao'])); ?></p>
                    <?php if ($saque['data_aprovacao']): ?>
                        <p><strong>Data da Aprovação:</strong> <?php echo date('d/m/Y H:i', strtotime($saque['data_aprovacao'])); ?></p>
                    <?php endif; ?>
                    <?php if ($saque['observacao']): ?>
                        <p><strong>Observação:</strong> <?php echo htmlspecialchars($saque['observacao']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nenhum saque solicitado ainda.</p>
        <?php endif; ?>
    </div>
</body>
</html> 