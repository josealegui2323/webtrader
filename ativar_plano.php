<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.html");
    exit();
}

// Busca os planos disponíveis
$sql = "SELECT id, nome, descricao, valor, duracao_dias FROM planos";
$result = $conn->query($sql);
$planos = $result->fetch_all(MYSQLI_ASSOC);

// Verifica se os dados do formulário foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $plano_id = $_POST["plano_id"];
    $valor_plano = $_POST["valor_plano"];
    $valor_deposito = $_POST["valor"];

    // Verifica se o valor do depósito é válido
    if ($valor_deposito < $valor_plano) {
        die("O valor do depósito deve ser igual ou maior que o valor do plano.");
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

    // Inicia a transação
    $conn->begin_transaction();

    try {
        // Insere o depósito
        $sql = "INSERT INTO depositos (usuario_id, valor, comprovante, status) VALUES (?, ?, ?, 'pendente')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ids", $usuario_id, $valor_deposito, $comprovante);
        $stmt->execute();
        $deposito_id = $stmt->insert_id;
        $stmt->close();

        // Busca informações do plano
        $sql = "SELECT nome, taxa FROM planos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $plano_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $plano = $result->fetch_assoc();
        $stmt->close();

        // Atualiza ou insere o plano do usuário
        $sql = "INSERT INTO planos_adquiridos (usuario_id, plano, valor_investido, taxa, data_inicio) 
                VALUES (?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                plano = VALUES(plano),
                valor_investido = VALUES(valor_investido),
                taxa = VALUES(taxa),
                data_inicio = NOW()";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdd", $usuario_id, $plano['nome'], $valor_deposito, $plano['taxa']);
        $stmt->execute();
        $stmt->close();

        // Confirma a transação
        $conn->commit();

        // Redireciona para a página de sucesso
        header("Location: plano.php?success=1");
        exit();

    } catch (Exception $e) {
        // Desfaz a transação em caso de erro
        $conn->rollback();
        die("Erro ao processar o depósito: " . $e->getMessage());
    }
} else {
    header("Location: plataforma.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ativar Plano</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 40px auto;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .plan-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .plan-description {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .plan-value {
            color: #2c3e50;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ativar Plano</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="plano_id">Selecione o Plano:</label>
                <select name="plano_id" id="plano_id" required onchange="updatePlanDetails(this.value)">
                    <option value="">Selecione um plano</option>
                    <?php foreach ($planos as $plano): ?>
                        <option value="<?php echo $plano['id']; ?>" 
                                data-valor="<?php echo $plano['valor']; ?>"
                                data-descricao="<?php echo htmlspecialchars($plano['descricao']); ?>"
                                data-duracao="<?php echo $plano['duracao_dias']; ?>">
                            <?php echo htmlspecialchars($plano['nome']); ?> - R$ <?php echo number_format($plano['valor'], 2, ',', '.'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="plan-details" class="plan-details" style="display: none;">
                <h3>Detalhes do Plano</h3>
                <p id="plan-description" class="plan-description"></p>
                <p id="plan-duration" class="plan-value"></p>
                <p id="plan-value" class="plan-value"></p>
            </div>
            
            <div class="form-group">
                <label for="valor_investido">Valor do Investimento (R$):</label>
                <input type="number" name="valor_investido" id="valor_investido" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="comprovante">Comprovante de Depósito:</label>
                <input type="file" name="comprovante" id="comprovante" accept=".jpg,.png,.pdf">
            </div>
            
            <button type="submit" class="btn">Ativar Plano</button>
        </form>
    </div>

    <script>
        function updatePlanDetails(planoId) {
            const select = document.getElementById('plano_id');
            const option = select.options[select.selectedIndex];
            const detailsDiv = document.getElementById('plan-details');
            
            if (planoId) {
                detailsDiv.style.display = 'block';
                document.getElementById('plan-description').textContent = option.getAttribute('data-descricao');
                document.getElementById('plan-duration').textContent = 'Duração: ' + option.getAttribute('data-duracao') + ' dias';
                document.getElementById('plan-value').textContent = 'Valor do Plano: R$ ' + parseFloat(option.getAttribute('data-valor')).toFixed(2).replace('.', ',');
                document.getElementById('valor_investido').min = option.getAttribute('data-valor');
                document.getElementById('valor_investido').value = option.getAttribute('data-valor');
            } else {
                detailsDiv.style.display = 'none';
            }
        }
    </script>
</body>
</html> 