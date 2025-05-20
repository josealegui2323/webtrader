<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration for InfinityFree
define('DB_HOST', 'sql300.infinityfree.com'); // Your InfinityFree host
define('DB_NAME', 'if0_38455108_webtraderbinance'); // Your database name
define('DB_USER', 'if0_38455108'); // Your database username
define('DB_PASS', 'UmAryyli9TR'); // Your database password

try {
    // Create PDO connection with proper error handling
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );

    // Test the connection
    $pdo->query("SELECT 1");
} catch(PDOException $e) {
    // Log the error (in a production environment, you'd want to log this to a file)
    error_log("Database Connection Error: " . $e->getMessage());
    
    // Show a user-friendly error message
    die("Desculpe, ocorreu um erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}

// Function to sanitize user input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to hash password
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to verify password
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header("Location: index.html");
        exit();
    }
}
?> 