<?php
session_start();

// Database configuration
define('DB_HOST', '192.168.0.3'); // Atualize para o IP público ou domínio via Cloudflare Tunnel
define('DB_NAME', 'if0_38455108_webtraderbinance');      // Exemplo: if0_12345678_webtrader
define('DB_USER', 'if0_38455108');          // Exemplo: if0_12345678
define('DB_PASS', 'UmAryyli9TR');          // Sua senha do banco de dados

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper functions
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}
?>