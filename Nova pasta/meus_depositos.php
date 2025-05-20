<?php
session_start();
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.html");
    exit();
}

// Verifica se os parâmetros do plano foram fornecidos
if (!isset($_GET['plano']) || !isset($_GET['valor'])) {
    header("Location: plataforma.php");
    exit();
}

$plano_id = $_GET['plano'];
$valor_plano = $_GET['valor'];

// Busca informações do plano no banco de dados
$sql = "SELECT nome, taxa FROM planos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $plano_id);
$stmt->execute();
$result = $stmt->get_result();
$plano = $result->fetch_assoc();
$stmt->close();

if (!$plano) {
    header("Location: plataforma.php");
    exit();
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