<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Carrega os dados do usuário
$usuario_id = $_SESSION['usuario_id'];
// Carrega os dados do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $usuario_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();

// Carrega os planos do usuário
$sql_planos = "SELECT pa.*, p.* 
                FROM planos_adquiridos pa 
                LEFT JOIN planos p ON pa.plano = p.nome
                WHERE pa.usuario_id = ?";
$stmt_planos = $conn->prepare($sql_planos);
$stmt_planos->bind_param("i", $usuario_id);
$stmt_planos->execute();
$result_planos = $stmt_planos->get_result();
$planos = $result_planos->fetch_all(MYSQLI_ASSOC);

// Verifica se o usuário está logado

// Função para verificar se o contador está ativo para um plano
function contadorEstaAtivo($plano_id, $usuario_id) {
    global $conn;
    try {
        $sql = "SELECT status_contador, data_ativacao FROM contador_bot 
                WHERE plano_id = ? AND usuario_id = ?";
        
        // Debug: Show SQL query
        error_log("Query: " . $sql);
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . mysqli_error($conn));
            return ['ativo' => false, 'data_ativacao' => null];
        }
        
        $stmt->bind_param("ii", $plano_id, $usuario_id);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return ['ativo' => false, 'data_ativacao' => null];
        }
        
        $result = $stmt->get_result();
        if ($result === false) {
            error_log("Get result failed: " . $stmt->error);
            return ['ativo' => false, 'data_ativacao' => null];
        }
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            error_log("Found active counter: " . print_r($row, true));
            return [
                'ativo' => $row['status_contador'] == 'ativo',
                'data_ativacao' => $row['data_ativacao']
            ];
        }
        
        error_log("No active counter found for plano_id=$plano_id usuario_id=$usuario_id");
        return ['ativo' => false, 'data_ativacao' => null];
    } catch (Exception $e) {
        error_log("Error in contadorEstaAtivo: " . $e->getMessage());
        return ['ativo' => false, 'data_ativacao' => null];
    }
}

// Função para ativar o contador
function ativarContador($plano_id, $usuario_id) {
    global $conn;
    
    // Tenta inserir um novo registro
    $sql = "INSERT INTO contador_bot (plano_id, usuario_id, data_ativacao, status_contador) 
            VALUES (?, ?, NOW(), 'ativo')";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        return false;
    }
    
    $stmt->bind_param("ii", $plano_id, $usuario_id);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
    
    return true;
}

// Se houver requisição para ativar contador
if (isset($_POST['ativar_contador'])) {
    $plano_adquirido_id = $_POST['plano_id'];
    $usuario_id = $_SESSION['usuario_id'];
    
    // Verifica se o usuário está logado
    if (!$usuario_id) {
        die('Usuário não logado');
    }
    
    // Verifica se o plano existe e pertence ao usuário
    $sql = "SELECT pa.*, p.* 
            FROM planos_adquiridos pa 
            LEFT JOIN planos p ON pa.plano = p.nome
            WHERE pa.id = ? AND pa.usuario_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Erro ao preparar consulta: ' . mysqli_error($conn));
    }
    
    $stmt->bind_param("ii", $plano_adquirido_id, $usuario_id);
    if (!$stmt->execute()) {
        die('Erro ao executar consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result === false) {
        die('Erro ao obter resultados: ' . $stmt->error);
    }

    if ($result->num_rows == 0) {
        die('Plano não encontrado ou não pertence ao usuário');
    }

    $plano = $result->fetch_assoc();
    $plano_id = $plano['id'];  // Use the ID from the planos table

    // Verifica se já existe um contador ativo para este usuário
    $sql = "SELECT id FROM contador_bot WHERE usuario_id = ? AND status_contador = 'ativo'";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Erro ao preparar consulta: ' . mysqli_error($conn));
    }
    
    $stmt->bind_param("i", $usuario_id);
    if (!$stmt->execute()) {
        die('Erro ao executar consulta: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result === false) {
        die('Erro ao obter resultados: ' . $stmt->error);
    }
    
    if ($result->num_rows > 0) {
        die('Você já tem um contador ativo para outro plano. Por favor, aguarde o término do contador atual.');
    }
    
    // Ativa o contador apenas para este plano específico
    if (ativarContador($plano_id, $usuario_id)) {  // Use plano_id from planos table
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        die('Erro ao ativar contador');
    }
}

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

// Processa depósito se for um formulário de depósito
if (isset($_POST['valor'])) {
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




<div class="container">
    <div class="user-info">
        <h1>Bem-vindo(a)!</h1>
        <p>Seu e-mail: <?php echo htmlspecialchars($usuario["email"]); ?></p>
        <div style="text-align: center; margin-top: 20px;">        
            <a href="plataforma.php" class="btn" style="margin-top: 10px;">Conhecer Novos Planos</a>
        </div>
    </div>

    <div class="plano-info">
        <?php if (count($planos) > 0): ?>
            <h2>Seus Planos</h2>
            <?php foreach ($planos as $plano): ?>
                <div class="plano-card">
                    <div class="status-plano <?php echo $plano['status']; ?>">
                        Status: <?php echo ucfirst($plano['status']); ?>
                    </div>
                    <?php 
                    $contadorStatus = contadorEstaAtivo($plano['id'], $_SESSION['usuario_id']);
                    $contadorAtivo = $contadorStatus['ativo'];
                    $dataAtivacao = $contadorStatus['data_ativacao'];
                    ?>
                    <div class="plano-detalhes">
                        <div class="alert alert-success" style="margin-bottom: 10px;">
                            
                            <?php if ($plano['status'] == 'ativo'): ?>
                                <form method="POST" style="display: inline; margin-left: 10px;">
                                    <input type="hidden" name="plano_id" value="<?php echo $plano['id']; ?>">
                                    <button type="submit" name="ativar_contador" class="btn" <?php echo $contadorAtivo ? 'disabled' : ''; ?>>
                                        <?php echo $contadorAtivo ? 'Contador Ativo' : 'Ativar Contador'; ?>
                                    </button>
                                </form>
                                <div id="contador<?php echo $plano['id']; ?>" style="display: <?php echo $contadorAtivo ? 'block' : 'none'; ?>; margin-top: 10px;">
                                    <div id="botStatus<?php echo $plano['id']; ?>" class="btn">
                                        <span id="countdown<?php echo $plano['id']; ?>">
                                            <?php 
                                            if ($contadorAtivo) {
                                                // Calcular dias úteis restantes do servidor
                                                $dataAtual = new DateTime();
                                                $dataAtivacao = new DateTime($dataAtivacao);
                                                $diasRestantes = 0;
                                                $diaAtual = clone $dataAtivacao;
                                                while ($diaAtual <= $dataAtual) {
                                                    if ($diaAtual->format('N') < 6) { // 1-5 são dias úteis
                                                        $diasRestantes++;
                                                    }
                                                    $diaAtual->modify('+1 day');
                                                }
                                                echo (20 - $diasRestantes) . ' dias úteis restantes';
                                            } else {
                                                echo '20 dias úteis restantes';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <script>
                                    // Função para calcular dias úteis
                                    function getBusinessDays(startDate, endDate) {
                                        var count = 0;
                                        var currentDate = new Date(startDate);
                                        while (currentDate <= endDate) {
                                            if (currentDate.getDay() !== 0 && currentDate.getDay() !== 6) {
                                                count++;
                                            }
                                            currentDate.setDate(currentDate.getDate() + 1);
                                        }
                                        return count;
                                    }

                                    // Função para atualizar o contador específico deste plano
                                    function updateCountdown<?php echo $plano['id']; ?>() {
                                        var startDate = new Date('<?php echo $dataAtivacao ? $dataAtivacao : date("Y-m-d H:i:s"); ?>');
                                        var endDate = new Date(startDate);
                                        endDate.setDate(endDate.getDate() + 20); // Adiciona 20 dias

                                        var businessDays = getBusinessDays(startDate, endDate);
                                        var countdownElement = document.getElementById('countdown<?php echo $plano['id']; ?>');
                                        
                                        if (businessDays > 0) {
                                            countdownElement.textContent = businessDays + ' dias úteis restantes';
                                        } else {
                                            countdownElement.textContent = 'Bot ativado!';
                                        }

                                        // Atualiza o contador a cada segundo
                                        setTimeout(function() {
                                            updateCountdown<?php echo $plano['id']; ?>();
                                        }, 1000);
                                    }

                                    // Inicia o contador se estiver ativo
                                    <?php if ($contadorAtivo): ?>
                                        updateCountdown<?php echo $plano['id']; ?>();
                                    <?php endif; ?>
                                </script>
                            <?php endif; ?>
                        </div>
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
                        
                        <div class="alert alert-info" style="margin-top: 15px;">
                            <div id="botStatus" class="btn">
                                <span id="countdown">20 dias úteis restantes</span>
                            </div>
                            
                        </div>
                    <?php elseif ($plano['status'] == 'encerrado'): ?>
                        <div class="alert alert-info">
                            Este plano foi encerrado.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            
        <?php endif; ?>
    </div>

    
</div>

</body>
</html>



