<?php
session_start();
include 'conexao.php';

// Get the structure of planos_adquiridos table
echo "<pre>";
echo "Structure of planos_adquiridos:\n";
$result = mysqli_query($conn, "DESCRIBE planos_adquiridos");
while ($row = mysqli_fetch_array($result)) {
    print_r($row);
}

echo "\n\n";

// Get all records for the logged-in user
if (isset($_SESSION['usuario_id'])) {
    echo "Records for user ID: " . $_SESSION['usuario_id'] . "\n";
    $sql = "SELECT * FROM planos_adquiridos WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['usuario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
}

echo "</pre>";
?>
