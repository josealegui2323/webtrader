<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperar Senha - WebTrader</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .recovery-container {
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
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        input[type="email"] {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 1rem;
            transition: border-color 0.3s;
        }
        input[type="email"]:focus {
            border-color: #2962ff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(41,98,255,0.1);
        }
        button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #1a237e, #0d47a1);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
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
            text-align: center;
            color: #2962ff;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .recovery-container {
                padding: 1.5rem;
            }
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php
    // Verifica se há mensagens de sucesso ou erro
    if (isset($_GET['success'])) {
        echo '<div class="message success">Instruções para recuperação de senha enviadas para seu e-mail!</div>';
    }
    if (isset($_GET['error'])) {
        echo '<div class="message error">Erro ao enviar instruções. Por favor, tente novamente.</div>';
    }
    ?>
    <div class="recovery-container">
        <h1>Recuperar Senha</h1>
        <form action="processar_recuperacao.php" method="POST" id="recoveryForm">
            <label for="email">Informe seu e-mail cadastrado</label>
            <input type="email" id="email" name="email" required placeholder="seuemail@exemplo.com" />
            <button type="submit" id="submitButton">Enviar Instruções</button>
        </form>
        <a href="index.html" class="back-link">Voltar ao Login</a>
    </div>
    <script>
        document.getElementById('recoveryForm').addEventListener('submit', function(e) {
            const button = document.getElementById('submitButton');
            const loader = document.createElement('div');
            loader.className = 'loader';
            
            button.appendChild(loader);
            button.disabled = true;
            
            // Remove loader e habilita botão após 2 segundos
            setTimeout(() => {
                loader.remove();
                button.disabled = false;
            }, 2000);
        });
    </script>
</body>
</html>
