<?php
session_start();
session_destroy();
header("Location: index.html"); // Redireciona para o login após sair
exit();
?>
