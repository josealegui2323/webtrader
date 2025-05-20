<?php
require_once 'config.php';

try {
    echo "Starting database verification and fix...<br><br>";

    // Check and create users table if it doesn't exist
    $pdo->query("
        CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            email VARCHAR(255) NOT NULL,
            cpf VARCHAR(14) NOT NULL,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_email (email),
            UNIQUE KEY unique_cpf (cpf)
        )
    ");
    echo "Users table verified/created<br>";

    // Check and create wallet table if it doesn't exist
    $pdo->query("
        CREATE TABLE IF NOT EXISTS wallet (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            balance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "Wallet table verified/created<br>";

    // Check and create plans table if it doesn't exist
    $pdo->query("
        CREATE TABLE IF NOT EXISTS plans (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            duration_days INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Plans table verified/created<br>";

    // Check and create user_plans table if it doesn't exist
    $pdo->query("
        CREATE TABLE IF NOT EXISTS user_plans (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            plan_id INT NOT NULL,
            purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expiration_date TIMESTAMP NOT NULL,
            status VARCHAR(20) DEFAULT 'active',
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
        )
    ");
    echo "User plans table verified/created<br>";

    // Check and create wallet_transactions table if it doesn't exist
    $pdo->query("
        CREATE TABLE IF NOT EXISTS wallet_transactions (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            type VARCHAR(20) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            balance_before DECIMAL(10,2) NOT NULL,
            balance_after DECIMAL(10,2) NOT NULL,
            status VARCHAR(20) DEFAULT 'completed',
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "Wallet transactions table verified/created<br>";

    // Check and create transactions table if it doesn't exist
    $pdo->query("
        CREATE TABLE IF NOT EXISTS transactions (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            type VARCHAR(20) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "Transactions table verified/created<br>";

    // Insert default plans if they don't exist
    $result = $pdo->query("SELECT COUNT(*) as count FROM plans");
    if ($result->fetch()['count'] == 0) {
        $pdo->query("
            INSERT INTO plans (name, description, price, duration_days) VALUES
            ('Basic', 'Plano básico com acesso às operações principais', 99.90, 30),
            ('Premium', 'Plano premium com acesso a todas as operações e suporte prioritário', 199.90, 30),
            ('Pro', 'Plano profissional com acesso completo e análise personalizada', 299.90, 30)
        ");
        echo "Default plans inserted<br>";
    }

    echo "<br>Database verification and fix completed successfully!<br>";
    echo "You can now try to register a new user.<br>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    echo "<br>Stack trace:<br>";
    echo $e->getTraceAsString();
}
?> 