<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.html");
    exit();
}

$usuario_id = $_SESSION["usuario_id"];
$sql = "SELECT mensagem, data_enviada, lida FROM notificacoes WHERE usuario_id = ? ORDER BY data_enviada DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Notificações</title>
</head>
<body>
    <h2>Minhas Notificações</h2>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <?= htmlspecialchars($row["mensagem"]) ?> <br>
                <small><?= $row["data_enviada"] ?><?= $row["lida"] ? "" : " <strong>(Nova)</strong>" ?></small>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
