<?php
session_start();
require_once 'conexao.php';
require_once 'config/phpmailer_config.php';

$token = trim($_GET['token'] ?? '');

// Verificar se o token está vazio
if (empty($token)) {
    $message = "Token não fornecido. Por favor, use o link enviado por email.";
    $showForm = false;
    exit;
}

// Verificar se o token tem o formato correto
if (strlen($token) !== 32) {
    $message = "Token inválido. Por favor, use o link enviado por email.";
    $showForm = false;
    exit;
}
$message = '';
$showForm = false;

if (empty($token)) {
    $message = "Token não fornecido. Por favor, use o link enviado por email.";
    $showForm = false;
} else {
    // Verificar token na tabela de recuperação de senha
    $stmt = $conn->prepare("SELECT usuario_id, expires_at FROM recuperacao_senha WHERE token = ?");
    if (!$stmt) {
        $message = "Erro no sistema. Por favor, tente novamente mais tarde.";
        $showForm = false;
    } else {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $message = "Token inválido. Por favor, use o link enviado por email.";
            $showForm = false;
        } else {
            $recuperacao = $result->fetch_assoc();
            $expires_at = strtotime($recuperacao['expires_at']);
            $now = time();

            if ($expires_at < $now) {
                $message = "Token expirado. Por favor, solicite uma nova recuperação de senha.";
                $showForm = false;
            } else {
                // Buscar informações do usuário
                $stmt = $conn->prepare("SELECT id, email, nome FROM usuarios WHERE id = ?");
                $stmt->bind_param("i", $recuperacao['usuario_id']);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();

                if (!$user) {
                    $message = "Usuário não encontrado. Por favor, use o link enviado por email.";
                    $showForm = false;
                } else {
                    $showForm = true;
                }
            }
        }
    }
}

// Processar o formulário de reset de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $showForm) {
    $nova_senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (empty($nova_senha) || empty($confirmar_senha)) {
        $message = "Por favor, preencha todas as senhas.";
    } elseif ($nova_senha !== $confirmar_senha) {
        $message = "As senhas não coincidem.";
    } else {
        // Criptografar a nova senha
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        // Atualizar senha do usuário
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->bind_param("si", $senha_hash, $recuperacao['usuario_id']);
        
        if ($stmt->execute()) {
            // Remover o token usado
            $stmt = $conn->prepare("DELETE FROM recuperacao_senha WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            
            // Enviar email de confirmação
            $subject = "Senha Redefinida - WebTrader";
            $message = "Olá {$user['nome']},\n\nSua senha foi redefinida com sucesso.\n\nVocê pode fazer login com sua nova senha.\n\nAtenciosamente,\nEquipe WebTrader";
            
            if (enviarEmail($user['email'], $subject, $message)) {
                $message = "Sua senha foi redefinida com sucesso! Você receberá um email de confirmação.";
                $showForm = false;
            } else {
                $message = "Sua senha foi redefinida com sucesso! Não foi possível enviar o email de confirmação.";
            }
        } else {
            $message = "Erro ao redefinir senha. Por favor, tente novamente.";
        }
    }
}

// Close database statements
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Redefinir Senha - WebTrader</title>
    <style>
        .body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .reset-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        h1 {
            color: #1a237e;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .message {
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        label {
            font-weight: 600;
            color: #333;
        }
        input {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }
        button {
            background: linear-gradient(135deg, #1976d2, #1565c0);
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
            position: relative;
            overflow: hidden;
        }
        button:hover {
            background: linear-gradient(135deg, #0d47a1, #1a237e);
        }
        button:disabled {
            background: #cccccc;
            cursor: not-allowed;
        }
        button:disabled:hover {
            background: #cccccc;
        }
        .loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 3px solid #fff;
            border-radius: 50%;
            border-top: 3px solid transparent;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        .back-link {
            display: block;
            margin-top: 1rem;
            color: #1a237e;
            text-decoration: none;
            text-align: center;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .reset-container {
                padding: 1.5rem;
            }
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h1>Redefinir Senha</h1>
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'Erro') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <?php if ($showForm): ?>
            <form method="POST" id="resetForm">
                <div class="form-group">
                    <label for="senha">Nova Senha</label>
                    <input type="password" id="senha" name="senha" required placeholder="Digite sua nova senha" />
                </div>
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Nova Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required placeholder="Confirme sua nova senha" />
                </div>
                <button type="submit" id="submitButton">Redefinir Senha</button>
            </form>
        <?php endif; ?>
        <a href="index.html" class="back-link">Voltar ao Login</a>
    </div>
    <script>
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const button = document.getElementById('submitButton');
            const loader = document.createElement('div');
            loader.className = 'loader';
            
            button.appendChild(loader);
            button.disabled = true;
        });
    </script>
</body>
</html>
