-- Adicionar colunas para recuperação de senha
ALTER TABLE usuarios ADD COLUMN reset_token VARCHAR(32) NULL;
ALTER TABLE usuarios ADD COLUMN reset_expiry DATETIME NULL;
