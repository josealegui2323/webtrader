<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebTrader</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        #menua {
            background: white;
            display: inline-block;
            padding: 20px;
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }
        #planos {
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            margin: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-image: url('imagens/imagesbf.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body id="menu">

    <div id="menua">
        <div id="area-cabecalho"><br>
            <a href="https://www.binance.com/pt-BR/activity/referral-entry/CPA?ref=CPA_00CH85W9RU" target="_blank">CADASTRO BINANCE</a>  <br>
            <a href="deposito.html">Fazer Depósito</a> | <br> 
            <a href="meus_depositos.php?plano=1&valor=100">Meus Depósitos</a><br>
            <a href="editar_wallet.php">Editar Wallet</a><br>
        </div>
    </div>

    <div id="principal">
        <h1>WebTrader Operações em Forex</h1>
    </div>
    <div>
        <h2>Planos Disponíveis</h2>
        <div class="planos">
            <?php
            // Array com os planos de 1 a 10
            $planos = [
                ['id' => 1, 'nome' => 'Plano Básico', 'valor' => 100, 'descricao' => 'Plano inicial com benefícios básicos'],
                ['id' => 2, 'nome' => 'Plano Prata', 'valor' => 200, 'descricao' => 'Plano com benefícios intermediários'],
                ['id' => 3, 'nome' => 'Plano Ouro', 'valor' => 300, 'descricao' => 'Plano completo com todos os benefícios'],
                ['id' => 4, 'nome' => 'Plano Premium', 'valor' => 400, 'descricao' => 'Plano premium com suporte exclusivo'],
                ['id' => 5, 'nome' => 'Plano VIP', 'valor' => 500, 'descricao' => 'Plano VIP com benefícios especiais'],
                ['id' => 6, 'nome' => 'Plano Master', 'valor' => 600, 'descricao' => 'Plano master com recursos avançados'],
                ['id' => 7, 'nome' => 'Plano Elite', 'valor' => 700, 'descricao' => 'Plano elite com suporte 24/7'],
                ['id' => 8, 'nome' => 'Plano Diamond', 'valor' => 800, 'descricao' => 'Plano diamond com benefícios premium'],
                ['id' => 9, 'nome' => 'Plano Platinum', 'valor' => 900, 'descricao' => 'Plano platinum com recursos exclusivos'],
                ['id' => 10, 'nome' => 'Plano Supreme', 'valor' => 1000, 'descricao' => 'Plano supreme com benefícios máximos']
            ];

            foreach ($planos as $plano) {
                echo '<div class="plano-item" style="background: white; padding: 20px; margin: 10px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">';
                echo '<h3>' . htmlspecialchars($plano['nome']) . '</h3>';
                echo '<p>' . htmlspecialchars($plano['descricao']) . '</p>';
                echo '<p>Valor: R$ ' . number_format($plano['valor'], 2, ',', '.') . '</p>';
                echo '<form action="ativar_plano.php" method="GET" style="margin-top: 15px;">';
                echo '<input type="hidden" name="plano" value="' . $plano['id'] . '">';
                echo '<input type="hidden" name="valor" value="' . $plano['valor'] . '">';
                echo '<button type="submit" class="btn-ativar" style="background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Ativar Plano</button>';
                echo '</form>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
    
    <img id="background-img" src="imagens/2.png">
    <a href="logout.php">Sair</a>

    <div id="rodape">
        Todos os Direitos Reservados
    </div>

</body>
</html>


<table border="1">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Idade</th>
        <th>Selecionar</th>
    </tr>
    <tr>
        <td>1</td>
        <td>João</td>
        <td>25</td>
        <td><input type="radio" name="selecionar" value="1"></td>
    </tr>
    <tr>
        <td>2</td>
        <td>Maria</td>
        <td>30</td>
        <td><input type="radio" name="selecionar" value="2"></td>
    </tr>
    <tr>
        <td>3</td>
        <td>Pedro</td>
        <td>28</td>
        <td><input type="radio" name="selecionar" value="3"></td>
    </tr>
</table>
<button onclick="redirecionar()">Ir para outra página</button>

<script>
function redirecionar() {
    let selecionado = document.querySelector('input[name="selecionar"]:checked');
    if (selecionado) {
        window.location.href = "pagina_destino.php?id=" + selecionado.value;
    } else {
        alert("Por favor, selecione um item antes de continuar!");
    }
}
</script>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Idade</th>
        <th>Selecionar</th>
    </tr>
    <tr>
        <td>1</td>
        <td>João</td>
        <td>25</td>
        <td><button onclick="redirecionar(1)">Selecionar</button></td>
    </tr>
    <tr>
        <td>2</td>
        <td>Maria</td>
        <td>30</td>
        <td><button onclick="redirecionar(2)">Selecionar</button></td>
    </tr>
    <tr>
        <td>3</td>
        <td>Pedro</td>
        <td>28</td>
        <td><button onclick="redirecionar(3)">Selecionar</button></td>
    </tr>
</table>

<script>
function redirecionar(id) {
    window.location.href = "pagina_destino.php?id=" + id;
}
</script>

