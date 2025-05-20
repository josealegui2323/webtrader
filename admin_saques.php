<?php
session_start();
include 'conexao.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION["usuario_id"]) || $_SESSION["tipo_usuario"] !== "admin") {
    header("Location: login.html");
    exit();
}

// Processa a aprovação/rejeição do saque
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["saque_id"])) {
    $saque_id = $_POST["saque_id"];
    $acao = $_POST["acao"];
    $observacao = $_POST["observacao"] ?? '';
    
    // Inicia a transação
    $conn->begin_transaction();
    
    try {
        // Atualiza o status do saque
        $sql = "UPDATE saques SET 
                status = ?,
                data_aprovacao = CASE WHEN ? = 'aprovado' THEN NOW() ELSE NULL END,
                observacao = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $acao, $acao, $observacao, $saque_id);
        $stmt->execute();
        $stmt->close();
        
        // Confirma a transação
        $conn->commit();
        $mensagem = "Saque " . ($acao == 'aprovado' ? 'aprovado' : 'rejeitado') . " com sucesso!";
        
    } catch (Exception $e) {
        // Desfaz a transação em caso de erro
        $conn->rollback();
        $mensagem = "Erro ao processar saque: " . $e->getMessage();
    }
}

// Busca todos os saques pendentes
$sql = "SELECT s.*, u.email, u.nome 
        FROM saques s 
        JOIN usuarios u ON s.usuario_id = u.id 
        WHERE s.status = 'pendente' 
        ORDER BY s.data_solicitacao ASC";
$result = $conn->query($sql);
$saques_pendentes = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Saques</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .saque-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status-saque {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }
        .pendente {
            background-color: #fff3cd;
            color: #856404;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-aprovar {
            background-color: #28a745;
            color: white;
        }
        .btn-rejeitar {
            background-color: #dc3545;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .mensagem {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .nav-menu {
            margin-bottom: 20px;
        }
        .nav-menu a {
            display: inline-block;
            padding: 10px 15px;
            background-color: #f8f9fa;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .nav-menu a:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-menu">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_depositos.php">Depósitos</a>
            <a href="logout.php">Sair</a>
        </div>

        <h1>Gerenciar Saques</h1>

        <?php if (isset($mensagem)): ?>
            <div class="mensagem <?php echo strpos($mensagem, 'sucesso') !== false ? 'sucesso' : 'erro'; ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <?php if (count($saques_pendentes) > 0): ?>
            <?php foreach ($saques_pendentes as $saque): ?>
                <div class="saque-card">
                    <div class="status-saque pendente">
                        Status: Pendente
                    </div>
                    <p><strong>Usuário:</strong> <?php echo htmlspecialchars($saque['nome']); ?> (<?php echo htmlspecialchars($saque['email']); ?>)</p>
                    <p><strong>Valor:</strong> R$ <?php echo number_format($saque['valor'], 2, ',', '.'); ?></p>
                    <p><strong>Data da Solicitação:</strong> <?php echo date('d/m/Y H:i', strtotime($saque['data_solicitacao'])); ?></p>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="saque_id" value="<?php echo $saque['id']; ?>">
                        
                        <div class="form-group">
                            <label for="observacao_<?php echo $saque['id']; ?>">Observação:</label>
                            <textarea id="observacao_<?php echo $saque['id']; ?>" name="observacao" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" name="acao" value="aprovado" class="btn btn-aprovar">Aprovar Saque</button>
                        <button type="submit" name="acao" value="rejeitado" class="btn btn-rejeitar">Rejeitar Saque</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Não há saques pendentes no momento.</p>
        <?php endif; ?>
    </div>
</body>
</html> 