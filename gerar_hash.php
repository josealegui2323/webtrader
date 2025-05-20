<?php
// Arquivo para gerar hash de senha para inserção no banco

$senha = '17Jo32Se@!'; // Substitua pela senha desejada
$hash = password_hash($senha, PASSWORD_DEFAULT);

echo "Senha original: " . htmlspecialchars($senha) . "<br>";
echo "Hash gerado: " . $hash . "<br>";
?>
