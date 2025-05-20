<?php
session_start();

// Configuração do banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "webtraderbinance";
$plano_id = $_GET['planos'];
$valor_plano = $_GET['valor'];

// Processar o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar se o comprovante foi enviado
        if (!isset($_FILES['comprovante'])) {
            throw new Exception('Por favor, selecione o comprovante');
        }

        // Processar upload do comprovante
        $upload_dir = 'uploads/comprovantes/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['comprovante']['name'], PATHINFO_EXTENSION));
        $file_name = uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES['comprovante']['tmp_name'], $file_path)) {
            throw new Exception('Erro ao fazer upload do comprovante');
        }

        // Inserir depósito no banco de dados
        $stmt = $pdo->prepare("
            INSERT INTO depositos 
            (usuario_id, comprovante) 
            VALUES (?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['usuario_id'],
            $file_path
        ]);

        // Redirecionar para meus_depositos.php com mensagem de sucesso
        header('Location: plataforma.php?success=1');
        exit();
        
    } catch(Exception $e) {
        // Redirecionar para meus_depositos.php com mensagem de erro
        header('Location: plataforma.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Depósitos - WebTrader</title>
    <style>
        :root {
            --primary: #4CAF50;
            --secondary: #388E3C;
            --background: #f4f4f4;
            --text: #222222;
            --accent: #81C784;
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

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        h1 {
            color: var(--primary);
            margin-bottom: 2rem;
            text-align: center;
        }

        .plano-info {
            background: var(--background);
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .plano-info p {
            margin-bottom: 0.5rem;
        }

        .plano-info strong {
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text);
        }

        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            font-size: 1rem;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: var(--secondary);
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
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav-menu">
            <a href="plataforma.php">Voltar para Planos</a>
            <a href="plano.php">Meu Plano</a>
            <a href="logout.php">Sair</a>
        </nav>

        <h1>Ativar Plano</h1>

        <div class="plano-info">
            <p><strong>Plano Selecionado:</strong> <?php echo htmlspecialchars($plano['nome']); ?></p>
            <p><strong>Valor do Plano:</strong> $<?php echo number_format($valor_plano, 2); ?></p>
            <p><strong>Taxa de Retorno:</strong> <?php echo htmlspecialchars($plano['taxa']); ?>% ao dia</p>
        </div>

        <form action="processar_deposito.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="plano_id" value="<?php echo $plano_id; ?>">
            <input type="hidden" name="valor_plano" value="<?php echo $valor_plano; ?>">
            
            <div class="form-group">
                <label for="valor">Valor do Depósito:</label>
                <input type="number" id="valor" name="valor" value="<?php echo $valor_plano; ?>" step="0.01" min="<?php echo $valor_plano; ?>" required>
            </div>

            <div class="form-group">
                <label for="comprovante">Comprovante de Depósito:</label>
                <input type="file" id="comprovante" name="comprovante" accept="image/*,.pdf" required>
            </div>

            <button type="submit" class="btn">Confirmar Depósito</button>
        </form>
    </div>
</body>
</html> 