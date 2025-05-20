<?php
session_start();
require_once 'conexao.php';

// Seleciona o banco de dados webtraderbinance
$conn->select_db('webtraderbinance');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Busca os planos ativos do usuário e suas informações do contador
$sql = "SELECT cb.*, p.*
        FROM contador_bot cb
        LEFT JOIN planos p ON cb.plano_id = p.id
        WHERE cb.usuario_id = ? AND cb.status_contador = 'ativo'
        ORDER BY cb.data_ativacao DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$planos_ativos = $result->fetch_all(MYSQLI_ASSOC);

// Função para calcular os dias restantes (considerando apenas dias úteis)
function calcularDiasRestantes($dataTermino) {
    $dataAtual = new DateTime();
    $dataTermino = new DateTime($dataTermino);
    
    $diasRestantes = 0;
    while ($dataAtual <= $dataTermino) {
        // Se for dia útil (segunda a sexta), conta como um dia
        if ($dataAtual->format('N') < 6) { // 1-5 são dias úteis
            $diasRestantes++;
        }
        $dataAtual->modify('+1 day');
    }
    
    return $diasRestantes;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos Ativos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .plano-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .contador {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
        }
        .status {
            font-weight: bold;
            color: #666;
        }
        .trading-info {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .trading-info p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Planos Ativos</h1>
        
        <?php if (empty($planos_ativos)): ?>
            <div class="alert alert-info">
                Você não tem planos ativos no momento.
            </div>
        <?php else: ?>
            <?php foreach ($planos_ativos as $plano): ?>
                <div class="plano-card">
                    <h3><?php echo htmlspecialchars($plano['nome']); ?></h3>
                    <div class="status">
                        <p><strong>Data de Início:</strong> <?php echo date('d/m/Y', strtotime($plano['data_ativacao'])); ?></p>
                        <p><strong>Data de Término:</strong> <?php echo date('d/m/Y', strtotime($plano['data_termino'])); ?></p>
                        <p><strong>Dias Restantes:</strong> <span class="contador"><?php echo calcularDiasRestantes($plano['data_termino']); ?></span></p>
                        <p><strong>Plano:</strong> <?php echo htmlspecialchars($plano['nome']); ?></p>
                        <p><strong>Status:</strong> <?php echo $plano['status_contador']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="plano.php" class="btn btn-primary">Voltar para Planos</a>
        </div>
    </div>
</body>
</html>
