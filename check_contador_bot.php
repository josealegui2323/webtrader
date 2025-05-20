<?php
session_start();
include 'conexao.php';

// Get the structure of contador_bot table
echo "<pre>";
echo "Structure of contador_bot:\n";
$result = mysqli_query($conn, "DESCRIBE contador_bot");
while ($row = mysqli_fetch_array($result)) {
    print_r($row);
    echo "\n";
}

echo "\n\n";

// Get all records from contador_bot
echo "Records in contador_bot:\n";
$result = mysqli_query($conn, "SELECT * FROM contador_bot");
while ($row = mysqli_fetch_array($result)) {
    print_r($row);
    echo "\n";
}

echo "</pre>";
?>
