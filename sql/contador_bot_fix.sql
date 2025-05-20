-- Garantir que o banco de dados existe
CREATE DATABASE IF NOT EXISTS webtraderbinance;
USE webtraderbinance;

-- Criar tabela contador_bot
CREATE TABLE IF NOT EXISTS contador_bot (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plano_id INT NOT NULL,
    usuario_id INT NOT NULL,
    status_contador ENUM('ativo', 'inativo') DEFAULT 'inativo',
    data_ativacao DATETIME,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_contador (plano_id, usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar chaves estrangeiras separadamente
ALTER TABLE contador_bot
ADD CONSTRAINT fk_contador_bot_plano
FOREIGN KEY (plano_id) REFERENCES planos(id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE contador_bot
ADD CONSTRAINT fk_contador_bot_usuario
FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE;
