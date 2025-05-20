<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'conexao.php';

// Ajuste na verificação de administrador para aceitar '1' ou true
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['is_admin']) || ($_SESSION['is_admin'] != 1 && $_SESSION['is_admin'] !== true)) {
    echo "Acesso negado. Você precisa ser administrador para acessar esta página.";
    exit;
}

// Função para aprovar depósito e ativar plano
function aprovarDepositoEAtivarPlano($conn, $deposito_id) {
    mysqli_begin_transaction($conn);
    try {
        $sqlDeposito = "UPDATE depositos SET status = 'ativo' WHERE id = ?";
        $stmtDeposito = mysqli_prepare($conn, $sqlDeposito);
        mysqli_stmt_bind_param($stmtDeposito, "i", $deposito_id);
        mysqli_stmt_execute($stmtDeposito);

        $sqlUsuario = "SELECT usuario_id FROM depositos WHERE id = ?";
        $stmtUsuario = mysqli_prepare($conn, $sqlUsuario);
        mysqli_stmt_bind_param($stmtUsuario, "i", $deposito_id);
        mysqli_stmt_execute($stmtUsuario);
        $result = mysqli_stmt_get_result($stmtUsuario);
        $row = mysqli_fetch_assoc($result);
        $usuario_id = $row['usuario_id'];

        $sqlPlano = "UPDATE planos_adquiridos SET status = 'ativo' WHERE usuario_id = ? AND status = 'pendente'";
        $stmtPlano = mysqli_prepare($conn, $sqlPlano);
        mysqli_stmt_bind_param($stmtPlano, "i", $usuario_id);
        mysqli_stmt_execute($stmtPlano);

        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}

// Processa aprovação se solicitado
if (isset($_GET['aprovar'])) {
    $deposito_id = intval($_GET['aprovar']);
    if (aprovarDepositoEAtivarPlano($conn, $deposito_id)) {
        $mensagem = "Depósito ID $deposito_id aprovado com sucesso.";
    } else {
        $mensagem = "Erro ao aprovar o depósito ID $deposito_id.";
    }
}

// Busca depósitos pendentes
$sql = "SELECT d.id, d.valor, d.data_deposito, u.nome FROM depositos d JOIN usuarios u ON d.usuario_id = u.id WHERE d.status = 'pendente' ORDER BY d.data_deposito ASC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    echo "Erro na consulta: " . mysqli_error($conn);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administração de Depósitos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 900px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 40px auto;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn-aprovar {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-aprovar:hover {
            background-color: #218838;
        }
        .mensagem {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Depósitos Pendentes</h1>

    <?php if (isset($mensagem)): ?>
        <div class="mensagem"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Depósito</th>
                    <th>Nome do Usuário</th>
                    <th>Valor</th>
                    <th>Data do Depósito</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($deposito = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($deposito['id']); ?></td>
                        <td><?php echo htmlspecialchars($deposito['nome']); ?></td>
                        <td>R$ <?php echo number_format($deposito['valor'], 2, ',', '.'); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($deposito['data_deposito'])); ?></td>
                        <td>
                            <a class="btn-aprovar" href="?aprovar=<?php echo $deposito['id']; ?>" onclick="return confirm('Tem certeza que deseja aprovar este depósito?');">Aprovar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum depósito pendente encontrado.</p>
    <?php endif; ?>
</div>
</body>
</html>
