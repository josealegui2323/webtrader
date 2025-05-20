-- Remove a tabela se ela já existir
DROP TABLE IF EXISTS depositos;

-- Cria a tabela depositos
CREATE TABLE depositos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    comprovante VARCHAR(255),
    status ENUM('pendente', 'aprovado', 'rejeitado') NOT NULL DEFAULT 'pendente',
    data_deposito TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_aprovacao TIMESTAMP NULL,
    observacao TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adiciona índice para melhorar a performance das consultas
CREATE INDEX idx_usuario_id ON depositos(usuario_id);
CREATE INDEX idx_status ON depositos(status);
CREATE INDEX idx_data_deposito ON depositos(data_deposito); 