

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebTrader - Planos</title>
    <meta name="description" content="WebTrader - Plataforma de Operações em Forex" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary:rgb(32, 90, 197);
            --secondary:rgb(77, 92, 233);
            --background: #f4f4f4;
            --text: #222222;
            --accent:rgb(26, 64, 99);
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

        #background-img {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.3;
            z-index: -1;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            flex: 1;
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

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .header h1 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .plans-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .plans-container h2 {
            color: var(--secondary);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        /* Hide all columns except the first one */
        table tr th:not(:first-child),
        table tr td:not(:first-child) {
            display: none;
        }

        /* On row hover, show all columns */
        table tr:hover th,
        table tr:hover td {
            display: table-cell;
        }

        /* Smooth transition for showing/hiding columns */
        table tr th,
        table tr td {
            transition: all 0.3s ease;
        }

        th, td {
            padding: 1rem;
            text-align: center;
            border: 1px solid #eee;
        }

        th {
            background: var(--primary);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        tr:nth-child(even) {
            background: #f8f8f8;
        }

        tr:hover {
            background: #f0f0f0;
        }

        button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        button:hover {
            background: var(--secondary);
        }

        .footer {
            text-align: center;
            padding: 1.5rem;
            background: var(--secondary);
            color: white;
            margin-top: auto;
        }

        .logout-link {
            display: inline-block;
            color: var(--primary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            margin-top: 1rem;
            border: 1px solid var(--primary);
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }

        .logout-link:hover {
            background: var(--primary);
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header h1 {
                font-size: 2rem;
            }

            .nav-menu {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            th, td {
                padding: 0.75rem;
            }
        }

        .user-plan {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .user-plan h2 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            text-align: center;
        }

        .user-plan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .plan-info-card {
            background: var(--background);
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        .plan-info-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .plan-info-value {
            color: var(--text);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .plan-info-value.highlight {
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .user-plan-grid {
                grid-template-columns: 1fr;
            }
        }

        .btn-ativar {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .btn-ativar:hover {
            background: var(--secondary);
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }

        .plan-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.2);
        }

        .plan-name {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .plan-details {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.4s ease;
            color: var(--text);
            font-size: 0.95rem;
        }

        .plan-card:hover .plan-details {
            max-height: 500px; /* enough to show all details */
            opacity: 1;
            margin-top: 0.5rem;
        }

        .bot-arbitrage-button {
            margin: 15px;
            text-align: center;
        }
        .bot-arbitrage-button .btn {
            background: linear-gradient(45deg, #2196F3, #1976D2);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            width: 100%;
        }
        .bot-arbitrage-button .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
            background: linear-gradient(45deg, #1976D2, #2196F3);
        }
        .bot-arbitrage-button .btn i {
            margin-right: 8px;
        }
        .user-info {
            color: #fff;
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #495057;
        }
    </style>
</head>
<body>
    <img id="background-img" src="imagens/2.png" alt="Background">
    
    <div class="container">
        <nav class="nav-menu">
            <a href="https://www.binance.com/pt-BR/activity/referral-entry/CPA?ref=CPA_00CH85W9RU" target="_blank">CADASTRO BINANCE</a>
            <a href="saque.php">Saques</a>  
            <a href="minha_wallet.php">minha Wallet</a>  
            <a href="valor.php">Depósitos</a>     
            <a href="plano.php">planos adiquiridos</a>   
            
            <a href="logout.php" class="logout-link">Sair</a>
        </nav>

        <header class="header">
            <h1>WebTrader Operações em Forex</h1>
        </header>

        <div class="plans-container">
            <h2>Planos de Investimento</h2>
            <div class="plans-grid">
                <div class="plan-card">
                    <div class="plan-name">Plano 1</div>
                    <div class="plan-details">
                        <p>Taxa (%): 1.0%</p>
                        <p>Preço ($): 10.00</p>
                        <p>Retorno Diário ($): 0.10</p>
                        <p>Retorno em 20 Dias ($): 2.00</p>
                        <p>Retorno Total ($): 12.00</p>
                        <a href="meus_depositos.php?plano=1&valor=10.00" class="btn-ativar">Ativar</a>
                    </div>
                </div>
                <div class="plan-card">
                    <div class="plan-name">Plano 2</div>
                    <div class="plan-details">
                        <p>Taxa (%): 1.2%</p>
                        <p>Preço ($): 50.00</p>
                        <p>Retorno Diário ($): 0.60</p>
                        <p>Retorno em 20 Dias ($): 12.00</p>
                        <p>Retorno Total ($): 62.00</p>
                        <a href="meus_depositos.php?plano=2&valor=50.00" class="btn-ativar">Ativar</a>
                    </div>
                </div>
                <div class="plan-card">
                    <div class="plan-name">Plano 3</div>
                    <div class="plan-details">
                        <p>Taxa (%): 1.3%</p>
                        <p>Preço ($): 100.00</p>
                        <p>Retorno Diário ($): 1.30</p>
                        <p>Retorno em 20 Dias ($): 26.00</p>
                        <p>Retorno Total ($): 126.00</p>
                        <a href="meus_depositos.php?plano=3&valor=100.00" class="btn-ativar">Ativar</a>
                    </div>
                </div>
                <div class="plan-card">
                    <div class="plan-name">Plano 4</div>
                    <div class="plan-details">
                        <p>Taxa (%): 1.4%</p>
                        <p>Preço ($): 120.00</p>
                        <p>Retorno Diário ($): 1.68</p>
                        <p>Retorno em 20 Dias ($): 33.68</p>
                        <p>Retorno Total ($): 153.68</p>
                        <a href="meus_depositos.php?plano=4&valor=120.00" class="btn-ativar">Ativar</a>
                    </div>
                </div>
                <div class="plan-card">
                    <div class="plan-name">Plano 5</div>
                    <div class="plan-details">
                        <p>Taxa (%): 1.5%</p>
                        <p>Preço ($): 130.00</p>
                        <p>Retorno Diário ($): 1.95</p>
                        <p>Retorno em 20 Dias ($): 39.00</p>
                        <p>Retorno Total ($): 169.00</p>
                        <a href="meus_depositos.php?plano=5&valor=130.00" class="btn-ativar">Ativar</a>
                    </div>
                </div>
                <div class="plan-card">
                    <div class="plan-name">Plano 6</div>
                    <div class="plan-details">
                        <p>Taxa (%): 2.0%</p>
                        <p>Preço ($): 150.00</p>
                        <p>Retorno Diário ($): 3.00</p>
                        <p>Retorno em 20 Dias ($): 60.00</p>
                        <p>Retorno Total ($): 210.00</p>
                        <a href="meus_depositos.php?plano=6&valor=150.00" class="btn-ativar">Ativar</a>
                    </div>
                </div>
                <div class="plan-card">
                    <div class="plan-name">Plano 7</div>
                    <div class="plan-details">
                        <p>Taxa (%): 2.1%</p>
                        <p>Preço ($): 200.00</p>
                        <p>Retorno Diário ($): 4.20</p>
                        <p>Retorno em 20 Dias ($): 84.00</p>
                        <p>Retorno Total ($): 284.00</p>
                        <a href="meus_depositos.php?plano=7&valor=200.00" class="btn-ativar">Ativar</a>
                    </div>
                </div>
                <div class="plan-card">
                    <div class="plan-name">Plano 8</div>
                    <div class="plan-details">
                        <p>Taxa (%): 2.3%</p>
                        <p>Preço ($): 250.00</p>
                        <p>Retorno Diário ($): 5.75</p>
                        <p>Retorno em 20 Dias ($): 115.00</p>
                        <p>Retorno Total ($): 365.00</p>
                        <a href="meus_depositos.php?plano=8&valor=250.00" class="btn-ativar">Ativar</a>
                    </div>
                </div>
                <div class="plan-card">
                    <div class="plan-name">Plano 9</div>
                    <div class="plan-details">
                        <p>Taxa (%): 2.5%</p>
                        <p>Preço ($): 300.00</p>
                        <p>Retorno Diário ($): 7.50</p>
                        <p>Retorno em 20 Dias ($): 150.00</p>
                        <p>Retorno Total ($): 450.00</p>
                        <a href="meus_depositos.php?plano=9&valor=300.00" class="btn-ativar">Ativar</a>
                    </div>
                </div>
                <div class="plan-card">
                    <div class="plan-name">Plano 10</div>
                    <div class="plan-details">
                        <p>Taxa (%): 3.0%</p>
                        <p>Preço ($): 400.00</p>
                        <p>Retorno Diário ($): 12.00</p>
                        <p>Retorno em 20 Dias ($): 240.00</p>
                        <p>Retorno Total ($): 640.00</p>
                        <a href="meus_depositos.php?plano=10&valor=400.00" class="btn-ativar">Ativar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>Todos os Direitos Reservados</p>
    </footer>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>s