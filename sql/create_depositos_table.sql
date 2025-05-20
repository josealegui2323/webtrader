-- SQL script to create the 'depositos' table for the deposits feature

CREATE TABLE depositos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    data_deposito DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) NOT NULL DEFAULT 'pendente',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Index for faster queries by user_id
CREATE INDEX idx_user_id ON depositos(user_id);
