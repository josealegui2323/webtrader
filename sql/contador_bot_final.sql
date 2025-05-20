-- Garantir que o banco de dados existe
CREATE DATABASE IF NOT EXISTS webtraderbinance;
USE webtraderbinance;

-- Verificar se as tabelas referenciadas têm as chaves primárias corretas
ALTER TABLE planos ADD PRIMARY KEY (id);
ALTER TABLE usuarios ADD PRIMARY KEY (id);

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

-- Adicionar triggers para manter a consistência dos dados
DELIMITER //

CREATE TRIGGER before_contador_bot_insert
BEFORE INSERT ON contador_bot
FOR EACH ROW
BEGIN
    -- Verificar se já existe um contador ativo para o mesmo plano e usuário
    IF EXISTS (
        SELECT 1 FROM contador_bot 
        WHERE plano_id = NEW.plano_id 
        AND usuario_id = NEW.usuario_id 
        AND status_contador = 'ativo'
    ) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Já existe um contador ativo para este plano e usuário';
    END IF;
END//

DELIMITER ;
