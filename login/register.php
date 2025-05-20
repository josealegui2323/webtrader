<?php
require_once 'config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Debug: Print POST data
        echo "POST Data received:<br>";
        print_r($_POST);
        echo "<br><br>";

        // Get and sanitize input
        $email = sanitize_input($_POST['email']);
        $cpf = sanitize_input($_POST['cpf']);
        $password = $_POST['senha'];
        $confirm_password = $_POST['confirma_senha'];
        $phone = sanitize_input($_POST['telefone']);

        // Debug: Print sanitized data
        echo "Sanitized data:<br>";
        echo "Email: $email<br>";
        echo "CPF: $cpf<br>";
        echo "Phone: $phone<br>";
        echo "Password length: " . strlen($password) . "<br><br>";

        // Validate passwords match
        if ($password !== $confirm_password) {
            throw new Exception("As senhas não coincidem");
        }

        // Validate CPF format (basic validation)
        if (!preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf)) {
            throw new Exception("CPF inválido");
        }

        // Start transaction
        $pdo->beginTransaction();

        // Debug: Check if tables exist
        $tables = ['users', 'wallet'];
        foreach ($tables as $table) {
            $result = $pdo->query("SHOW TABLES LIKE '$table'");
            echo "Table '$table' exists: " . ($result->rowCount() > 0 ? "Yes" : "No") . "<br>";
        }
        echo "<br>";

        // Check if email or CPF already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR cpf = ?");
        $stmt->execute([$email, $cpf]);
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email ou CPF já cadastrado");
        }

        // Debug: Print SQL query
        echo "Attempting to insert user with:<br>";
        echo "Email: $email<br>";
        echo "CPF: $cpf<br>";
        echo "Phone: $phone<br><br>";

        // Insert new user
        $stmt = $pdo->prepare("
            INSERT INTO users (email, cpf, password, phone) 
            VALUES (?, ?, ?, ?)
        ");
        
        $hashed_password = hash_password($password);
        $stmt->execute([
            $email,
            $cpf,
            $hashed_password,
            $phone
        ]);

        $user_id = $pdo->lastInsertId();
        echo "User created with ID: $user_id<br><br>";

        // Initialize wallet with 0 balance
        $stmt = $pdo->prepare("
            INSERT INTO wallet (user_id, balance) 
            VALUES (?, 0.00)
        ");
        $stmt->execute([$user_id]);
        echo "Wallet initialized for user<br><br>";

        // Commit transaction
        $pdo->commit();
        echo "Transaction committed successfully<br><br>";

        // Redirect with success message
        header("Location: index.html?registration=success");
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
            echo "Transaction rolled back due to error<br>";
        }
        
        // Log the error
        error_log("Registration Error: " . $e->getMessage());
        
        // Show detailed error
        echo "Error occurred: " . $e->getMessage() . "<br>";
        echo "Error trace:<br>";
        echo $e->getTraceAsString();
        
        // Redirect with error message
        header("Location: index.html?registration=error&message=" . urlencode($e->getMessage()));
        exit();
    }
}
?> 