<?php
session_start();

// Configuração do banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "webtraderbinance";

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.html');
    exit();
}

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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Comprovante</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1976D2;
        }
        .back-button {
            display: inline-block;
            background-color: #757575;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        .back-button:hover {
            background-color: #616161;
        }
    </style>
</head>
<body>
    <h1>Enviar Comprovante de Depósito</h1>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="comprovante">Comprovante do Depósito</label>
            <input type="file" id="comprovante" name="comprovante" accept=".jpg,.jpeg,.png,.pdf" required>
        </div>

        <button type="submit">Enviar Comprovante</button>
    </form>

    <a href="plataforma.php" class="back-button">Voltar para Plataforma</a>
</body>
</html>
