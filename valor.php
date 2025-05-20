
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Meus Depósitos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 800px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 40px auto;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
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
            background-color:rgb(54, 93, 201);
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .status-pendente {
            color: #856404;
            background-color: #fff3cd;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-ativo {
            color: #155724;
            background-color: #d4edda;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-cancelado {
            color: #721c24;
            background-color: #f8d7da;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .no-deposits {
            text-align: center;
            color: #666;
            margin-top: 40px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['usuario_id'])) {
    // Redirect to login if user not logged in
    header('Location: login.php');
    exit;
}

include 'conexao.php';

$user_id = $_SESSION['usuario_id'];

// Query deposits for the logged-in user using procedural mysqli
$sql = "SELECT id, valor, data_deposito, status FROM depositos WHERE usuario_id = ? ORDER BY data_deposito DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container">
    <h1>Meus Depósitos</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Valor ($)</th>
                    <th>Data do Depósito</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($deposit = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($deposit['id']); ?></td>
                        <td><?php echo number_format($deposit['valor'], 2, ',', '.'); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($deposit['data_deposito'])); ?></td>
                        <td>
                            <?php
                            $status = strtolower($deposit['status']);
                            if ($status === 'pendente') {
                                echo '<span class="status-pendente">Pendente</span>';
                            } elseif ($status === 'aprovado') {
                                echo '<span class="status-aprovado">Aprovado</span>';
                            } elseif ($status === 'cancelado' || $status === 'encerrado') {
                                echo '<span class="status-cancelado">Cancelado</span>';
                            } else {
                                echo htmlspecialchars($deposit['status']);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-deposits">Você ainda não fez nenhum depósito.</p>
    <?php endif; ?><br><br>
    <a href="plataforma.php">Voltar</a>
</div>

    

</body>
</html>

