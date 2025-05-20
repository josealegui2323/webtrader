<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.html");
    exit();
}

// Busca informações do usuário
$usuario_id = $_SESSION["usuario_id"];
$sql = "SELECT email, cpf, telefone FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

// Busca todos os planos do usuário
$sql = "SELECT * FROM planos_adquiridos WHERE usuario_id = ? ORDER BY data_inicio DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$planos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $valor = $_POST["valor"];
    
    // Verifica se o valor do depósito é válido
    if ($valor <= 0) {
        die("O valor do depósito deve ser maior que zero.");
    }

    // Processamento do arquivo de comprovante
    $comprovante = null;
    if (isset($_FILES['comprovante']) && $_FILES['comprovante']['error'] == 0) {
        $extensao = pathinfo($_FILES['comprovante']['name'], PATHINFO_EXTENSION);
        $extensoes_validas = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array($extensao, $extensoes_validas)) {
            $nome_arquivo = uniqid() . '.' . $extensao;
            $diretorio = 'comprovantes/';
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0777, true);
            }

            $caminho_completo = $diretorio . $nome_arquivo;
            if (move_uploaded_file($_FILES['comprovante']['tmp_name'], $caminho_completo)) {
                $comprovante = $caminho_completo;
            } else {
                die("Erro ao fazer upload do arquivo.");
            }
        } else {
            die("Formato de arquivo inválido. Apenas JPG, PNG, e PDF são permitidos.");
        }
    }

    // Insere no banco de dados
    $sql = "INSERT INTO depositos (usuario_id, valor, comprovante) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $usuario_id, $valor, $comprovante);

        if ($stmt->execute()) {
            echo "<script>alert('Depósito realizado com sucesso!'); window.location.href = 'plano.php';</script>";
        } else {
            echo "Erro ao realizar depósito: " . $stmt->error;
        }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Plano</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 40px auto;
        }
        .user-info {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .plan-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #45a049;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        h2 {
            color: #666;
            margin-bottom: 15px;
        }
        p {
            color: #555;
            line-height: 1.6;
        }
        .plan-status {
            font-size: 1.2em;
            color: #2c3e50;
            margin-top: 10px;
        }
        .plan-details {
            margin-top: 15px;
            padding: 15px;
            background-color: #fff;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .plano-info {
            margin-bottom: 20px;
        }
        .status-plano {
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin-bottom: 15px;
            display: inline-block;
        }
        .status-plano.pendente {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .status-plano.ativo {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-plano.encerrado {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        .plano-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .plano-detalhes {
            margin: 15px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .plano-detalhes p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

<?php
// Removido session_start() duplicado para evitar aviso
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['bot_activation_success'])) {
    echo '<div class="alert alert-success" style="max-width: 600px; margin: 20px auto;">' . $_SESSION['bot_activation_success'] . '</div>';
    unset($_SESSION['bot_activation_success']);
}
if (isset($_SESSION['bot_activation_error'])) {
    echo '<div class="alert alert-danger" style="max-width: 600px; margin: 20px auto;">' . $_SESSION['bot_activation_error'] . '</div>';
    unset($_SESSION['bot_activation_error']);
}
?>

<div class="container">
    <div class="user-info">
        <h1>Bem-vindo(a)!</h1>
        <p>Seu e-mail: <?php echo htmlspecialchars($usuario["email"]); ?></p>
        <p>CPF: <?php echo htmlspecialchars($usuario["cpf"]); ?></p>
        <p>Telefone: <?php echo htmlspecialchars($usuario["telefone"]); ?></p>
    </div>

    <div class="plano-info">
        <?php if (count($planos) > 0): ?>
            <h2>Seus Planos</h2>
            <?php foreach ($planos as $plano): ?>
                <div class="plano-card">
                    <div class="status-plano <?php echo $plano['status']; ?>">
                        Status: <?php echo ucfirst($plano['status']); ?>
                    </div>
                    <div class="plano-detalhes">
                        <p><strong>Plano:</strong> <?php echo htmlspecialchars($plano['plano']); ?></p>
                        <p><strong>Valor Investido:</strong> R$ <?php echo number_format($plano['valor_investido'], 2, ',', '.'); ?></p>
                        <p><strong>Taxa de Retorno:</strong> <?php echo $plano['taxa']; ?>% ao dia</p>
                        <p><strong>Data de Início:</strong> <?php echo date('d/m/Y H:i', strtotime($plano['data_inicio'])); ?></p>
                        <?php if ($plano['data_fim']): ?>
                            <p><strong>Data de Término:</strong> <?php echo date('d/m/Y H:i', strtotime($plano['data_fim'])); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($plano['status'] == 'pendente'): ?>
                        <div class="alert alert-warning">
                            Seu plano está aguardando aprovação do depósito. Assim que o depósito for aprovado, seu plano será ativado automaticamente.
                        </div>
                    <?php elseif ($plano['status'] == 'ativo'): ?>
                        <div class="alert alert-success">
                            Seu plano está ativo e gerando rendimentos diários.
                        </div>
                        <?php echo "<p>DEBUG: Plano ativo detectado, botão deve aparecer.</p>"; ?>
                        <form method="POST" action="ativar_bot.php" style="margin-top: 15px;">
                            <input type="hidden" name="plano_id" value="<?php echo $plano['id']; ?>">
                            <button type="submit" class="btn">Ativar Bot</button>
                        </form>
                    <?php elseif ($plano['status'] == 'encerrado'): ?>
                        <div class="alert alert-info">
                            Este plano foi encerrado.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">
                Você ainda não possui planos ativos. <a href="plataforma.php">Clique aqui</a> para conhecer nossos planos.
            </div>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        
        <a href="plataforma.php" class="btn" style="margin-top: 10px;">Conhecer Novos Planos</a>
    </div>
</div>

</body>
</html>
