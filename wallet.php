<?php
include 'conexao.php';
class Wallet {
    private $pdo;
    private $user_id;

    public function __construct($pdo, $user_id) {
        $this->pdo = $pdo;
        $this->user_id = $user_id;
    }

    public function getBalance() {
        $stmt = $this->pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
        $stmt->execute([$this->user_id]);
        $result = $stmt->fetch();
        return $result ? $result['balance'] : 0.00;
    }

    public function deposit($amount, $description = '') {
        try {
            $this->pdo->beginTransaction();

            // Add to wallet balance
            $stmt = $this->pdo->prepare("
                UPDATE wallets 
                SET balance = balance + ?, last_updated = CURRENT_TIMESTAMP 
                WHERE user_id = ?
            ");
            $stmt->execute([$amount, $this->user_id]);

            // Record transaction
            $stmt = $this->pdo->prepare("
                INSERT INTO transactions (user_id, type, amount, status, description) 
                VALUES (?, 'deposit', ?, 'completed', ?)
            ");
            $stmt->execute([$this->user_id, $amount, $description]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function withdraw($amount, $description = '') {
        try {
            $this->pdo->beginTransaction();

            // Check balance
            $current_balance = $this->getBalance();
            if ($current_balance < $amount) {
                throw new Exception("Insufficient funds");
            }

            // Subtract from wallet balance
            $stmt = $this->pdo->prepare("
                UPDATE wallets 
                SET balance = balance - ?, last_updated = CURRENT_TIMESTAMP 
                WHERE user_id = ?
            ");
            $stmt->execute([$amount, $this->user_id]);

            // Record transaction
            $stmt = $this->pdo->prepare("
                INSERT INTO transactions (user_id, type, amount, status, description) 
                VALUES (?, 'withdrawal', ?, 'completed', ?)
            ");
            $stmt->execute([$this->user_id, $amount, $description]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getTransactionHistory($limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM transactions 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$this->user_id, $limit]);
        return $stmt->fetchAll();
    }
}
?>