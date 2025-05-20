<?php
session_start();
require_once 'conexao.php';

// Debug para verificar a sessão
error_log("Verificando sessão em meus_depositos.php: " . print_r($_SESSION, true));

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    error_log("Usuário não está logado - redirecionando para login.php");
    header("Location: login.php");
    exit();
}

// Debug
error_log("Usuário logado - ID: " . $_SESSION['usuario_id'] . ", Email: " . $_SESSION['usuario_email']);

// Armazena o ID do usuário em uma variável e garante que é um inteiro
$usuario_id = (int)$_SESSION["usuario_id"];
error_log("ID do usuário na página de depósitos: " . $usuario_id);

$mensagem = '';

// Busca os depósitos do usuário
try {
    $sql = "SELECT * FROM depositos WHERE usuario_id = :usuario_id ORDER BY data_deposito DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    
    error_log("Buscando depósitos para o usuário ID: $usuario_id");
    
    if (!$stmt->execute()) {
        throw new Exception("Erro ao buscar depósitos");
    }
    
    $depositos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Número de depósitos encontrados: " . count($depositos));

} catch (Exception $e) {
    error_log("Erro ao buscar depósitos: " . $e->getMessage());
    $depositos = [];
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Depósitos</title>
    <style>
        :root {
            --primary-color: #2196F3;
            --success-color: #4CAF50;
            --warning-color: #FFC107;
            --danger-color: #F44336;
            --background-color: #f5f5f5;
            --card-background: #ffffff;
            --text-color: #333333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: var(--background-color);
            color: var(--text-color);
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background-color: var(--card-background);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }

        .wallet-info {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .wallet-address {
            font-family: monospace;
            background-color: rgba(255,255,255,0.1);
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .copy-button {
            background-color: white;
            color: var(--primary-color);
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .deposit-form {
            display: grid;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        label {
            font-weight: 600;
        }

        input[type="number"],
        input[type="file"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .submit-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .submit-button:hover {
            background-color: #1976D2;
        }

        .back-button {
            background-color: #757575;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            text-decoration: none;
            text-align: center;
        }

        .back-button:hover {
            background-color: #616161;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            th, td {
                min-width: 120px;
            }

            .button-group {
                flex-direction: column;
            }
        }

        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .message.success {
            background-color: #E8F5E9;
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }

        .message.error {
            background-color: #FFEBEE;
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status.pendente {
            background-color: #FFF3E0;
            color: #F57C00;
        }

        .status.aprovado {
            background-color: #E8F5E9;
            color: var(--success-color);
        }

        .status.rejeitado {
            background-color: #FFEBEE;
            color: var(--danger-color);
        }

        .view-receipt {
            color: var(--primary-color);
            text-decoration: none;
        }

        .view-receipt:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        // Função para enviar o depósito via AJAX
        function enviarDeposito(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('depositForm'));
            
            fetch('processar_deposito.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('message');
                messageDiv.className = `message ${data.status === 'success' ? 'success' : 'error'}`;
                messageDiv.textContent = data.message;
                
                if (data.status === 'success') {
                    // Recarrega a página após 2 segundos para atualizar a lista de depósitos
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            })
            .catch(error => {
                const messageDiv = document.getElementById('message');
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Erro ao processar depósito. Tente novamente.';
            });
        }

        function copyWalletAddress() {
            const address = 'TYour1WalletAddressHere2023';
            navigator.clipboard.writeText(address).then(() => {
                const button = document.querySelector('.copy-button');
                button.textContent = 'Copiado!';
                setTimeout(() => {
                    button.textContent = 'Copiar';
                }, 2000);
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Meus Depósitos</h1>

        <div class="card">
            <div class="wallet-info">
                <h2>Carteira para Depósito</h2>
                <div class="wallet-address">
                    <span>TRC20USDT: TYour1WalletAddressHere2023</span>
                    <button class="copy-button" onclick="copyWalletAddress()">Copiar</button>
                </div>
            </div>

            <div id="message" class="message" style="display: none;"></div>

            <form id="depositForm" class="deposit-form" onsubmit="enviarDeposito(event)">
                <div class="form-group">
                    <label for="valor">Valor do Depósito (USDT)</label>
                    <input type="number" id="valor" name="valor" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label for="comprovante">Comprovante do Depósito</label>
                    <input type="file" id="comprovante" name="comprovante" accept=".jpg,.jpeg,.png,.pdf" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="submit-button">Confirmar Depósito</button>
                    <a href="plataforma.php" class="back-button">Voltar para Plataforma</a>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Histórico de Depósitos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Valor (USDT)</th>
                        <th>Status</th>
                        <th>Comprovante</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($depositos)): ?>
                        <?php foreach ($depositos as $deposito): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($deposito['data_deposito'])); ?></td>
                                <td><?php echo number_format($deposito['valor'], 2, ',', '.'); ?></td>
                                <td><span class="status <?php echo $deposito['status']; ?>"><?php echo $deposito['status']; ?></span></td>
                                <td>
                                    <?php if ($deposito['comprovante']): ?>
                                        <a href="<?php echo htmlspecialchars($deposito['comprovante']); ?>" class="view-receipt" target="_blank">Ver comprovante</a>
                                    <?php else: ?>
                                        Não disponível
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Nenhum depósito encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
