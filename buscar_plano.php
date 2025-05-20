

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Plano - WebTrader</title>
    <style>
        :root {
            --primary-color: #2962ff;
            --secondary-color: #1e88e5;
            --accent-color: #ffd700;
            --text-color: #333;
            --light-text: #666;
            --white: #ffffff;
            --dark-bg: #1a237e;
            --card-shadow: 0 8px 32px rgba(0,0,0,0.1);
            --gradient: linear-gradient(135deg, #1a237e, #0d47a1);
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f5f5f5;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .plan-card {
            background: var(--glass-bg);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .plan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(0,0,0,0.1);
        }

        .plan-title {
            font-size: 1.8rem;
            color: var(--dark-bg);
        }

        .plan-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            background: var(--gradient);
            color: var(--white);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: rgba(255,255,255,0.9);
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .info-label {
            font-size: 0.9rem;
            color: var(--light-text);
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .balance-section {
            background: var(--gradient);
            color: var(--white);
            padding: 2rem;
            border-radius: 20px;
            margin-top: 2rem;
        }

        .balance-header {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .balance-amount {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
        }

        .no-plan {
            text-align: center;
            padding: 3rem;
            background: var(--glass-bg);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }

        .no-plan h2 {
            color: var(--dark-bg);
            margin-bottom: 1rem;
        }

        .no-plan p {
            color: var(--light-text);
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: var(--gradient);
            color: var(--white);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($erro)): ?>
            <div class="error-message">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <?php if ($plano): ?>
            <div class="plan-card">
                <div class="plan-header">
                    <h1 class="plan-title"><?php echo htmlspecialchars($plano['nome_plano']); ?></h1>
                    <span class="plan-status">
                        <?php echo $plano['dias_restantes'] > 0 ? 'Ativo' : 'Expirado'; ?>
                    </span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Início do Plano</div>
                        <div class="info-value">
                            <?php echo date('d/m/Y', strtotime($plano['data_inicio'])); ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Término do Plano</div>
                        <div class="info-value">
                            <?php echo date('d/m/Y', strtotime($plano['data_fim'])); ?>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Dias Restantes</div>
                        <div class="info-value">
                            <?php echo max(0, $plano['dias_restantes']); ?> dias
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Valor do Plano</div>
                        <div class="info-value">
                            R$ <?php echo number_format($plano['valor_plano'], 2, ',', '.'); ?>
                        </div>
                    </div>
                </div>

                <div class="balance-section">
                    <div class="balance-header">Saldo Disponível</div>
                    <div class="balance-amount">
                        R$ <?php echo number_format($plano['saldo'], 2, ',', '.'); ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="no-plan">
                <h2>Nenhum Plano Ativo</h2>
                <p>Você ainda não possui um plano ativo. Adquira um plano para começar a operar.</p>
                <a href="processar_assinatura.php" class="btn">Ver Planos Disponíveis</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
