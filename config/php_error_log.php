<?php
// Configuração de logs
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp\htdocs\dashboard\logs\php_error.log');

// Cria diretório de logs se não existir
if (!file_exists('C:\xampp\htdocs\dashboard\logs')) {
    mkdir('C:\xampp\htdocs\dashboard\logs', 0777, true);
}

// Testa se o log está funcionando
error_log("Teste de log: " . date('Y-m-d H:i:s'));
