-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql311.infinityfree.com
-- Tempo de geração: 11/04/2025 às 20:29
-- Versão do servidor: 10.6.19-MariaDB
-- Versão do PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `if0_38506942_webtraderbiance`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `depositos`
--

CREATE TABLE `depositos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `comprovante` varchar(255) DEFAULT NULL,
  `status` enum('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
  `data_deposito` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_aprovacao` timestamp NULL DEFAULT NULL,
  `observacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `duracao_dias` int(11) NOT NULL,
  `taxa` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `descricao`, `valor`, `duracao_dias`, `taxa`, `created_at`) VALUES
(1, 'Plano 1', 'Plano inicial com taxa de 1.0% ao dia', '10.00', 20, '1.00', '2025-04-06 01:19:25'),
(2, 'Plano 2', 'Plano com taxa de 1.2% ao dia', '50.00', 20, '1.20', '2025-04-06 01:19:25'),
(3, 'Plano 3', 'Plano com taxa de 1.3% ao dia', '100.00', 20, '1.30', '2025-04-06 01:19:25'),
(4, 'Plano 4', 'Plano com taxa de 1.4% ao dia', '120.00', 20, '1.40', '2025-04-06 01:19:25'),
(5, 'Plano 5', 'Plano com taxa de 1.5% ao dia', '130.00', 20, '1.50', '2025-04-06 01:19:25'),
(6, 'Plano 6', 'Plano com taxa de 2.0% ao dia', '150.00', 20, '2.00', '2025-04-06 01:19:25'),
(7, 'Plano 7', 'Plano com taxa de 2.1% ao dia', '200.00', 20, '2.10', '2025-04-06 01:19:25'),
(8, 'Plano 8', 'Plano com taxa de 2.3% ao dia', '250.00', 20, '2.30', '2025-04-06 01:19:25'),
(9, 'Plano 9', 'Plano com taxa de 2.5% ao dia', '300.00', 20, '2.50', '2025-04-06 01:19:25'),
(10, 'Plano 10', 'Plano com taxa de 3.0% ao dia', '400.00', 20, '3.00', '2025-04-06 01:19:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos_adquiridos`
--

CREATE TABLE `planos_adquiridos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `plano` varchar(100) NOT NULL,
  `valor_investido` decimal(10,2) NOT NULL,
  `taxa` decimal(5,2) NOT NULL,
  `status` enum('pendente','ativo','encerrado') NOT NULL DEFAULT 'pendente',
  `data_inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_fim` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `saques`
--

CREATE TABLE `saques` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` enum('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
  `data_solicitacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_aprovacao` timestamp NULL DEFAULT NULL,
  `observacao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `data_nascimento` date NOT NULL,
  `endereco` text NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `tipo_usuario` enum('usuario','admin') NOT NULL DEFAULT 'usuario',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `cpf`, `telefone`, `data_nascimento`, `endereco`, `cidade`, `estado`, `cep`, `tipo_usuario`, `created_at`, `updated_at`) VALUES
(2, '', 'josealexandregui@gmail.com', '$2y$10$rBtvbw122Gz8PBlZ6OijS.TvjixX.5y9ZLwFYCS1sY1KfmJ0zczGu', '345.517.698-43', '94009-6934', '0000-00-00', '', '', '', '', 'usuario', '2025-04-06 01:24:26', '2025-04-06 01:24:26'),
(3, '', 'scott19092006@gmail.com', '$2y$10$pvky9P3a434MRbHMJwhKWeLnM3svbrVq5EpZ/oahMLJTykp2QMAzG', '556.755.108-85', '91307-4645', '0000-00-00', '', '', '', '', 'usuario', '2025-04-06 15:49:40', '2025-04-06 15:49:40'),
(7, '', 'josealexandregui1@gmail.com', '$2y$10$54JlmZnvQmBjqeySSWumreYgHkM0fc1OO2lPU31m726eZzDhbGXyS', '365.639.648-50', '94759-3720', '0000-00-00', '', '', '', '', 'usuario', '2025-04-06 16:38:53', '2025-04-06 16:38:53'),
(8, '', 'cicerosilvaaguimaraes@gmail.com', '$2y$10$jkQ5Btcux/02wENQWwS8.ONKHed4egUokFPwZx4I3SHscrzqqMjJK', '239.122.272-68', '98155-3601', '0000-00-00', '', '', '', '', 'usuario', '2025-04-06 16:47:31', '2025-04-06 16:47:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `wallet`
--

CREATE TABLE `wallet` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saldo_bloqueado` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `wallets`
--

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL,
  `usuario_email` varchar(255) NOT NULL,
  `wallet` varchar(255) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `wallets`
--

INSERT INTO `wallets` (`id`, `usuario_email`, `wallet`, `data_criacao`, `data_atualizacao`) VALUES
(1, 'josealexandregui@gmail.com', 'TG5gZzXX58ZAkZqCecRCz4CESug2UDGdGk', '2025-04-06 01:46:07', '2025-04-06 01:46:07');

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `depositos`
--
ALTER TABLE `depositos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_deposito_usuario` (`usuario_id`),
  ADD KEY `idx_deposito_status` (`status`),
  ADD KEY `idx_deposito_data` (`data_deposito`);

--
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_plano_nome` (`nome`);

--
-- Índices de tabela `planos_adquiridos`
--
ALTER TABLE `planos_adquiridos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_plano_adquirido_usuario` (`usuario_id`),
  ADD KEY `idx_plano_adquirido_status` (`status`),
  ADD KEY `idx_plano_adquirido_data` (`data_inicio`);

--
-- Índices de tabela `saques`
--
ALTER TABLE `saques`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_saque_usuario` (`usuario_id`),
  ADD KEY `idx_saque_status` (`status`),
  ADD KEY `idx_saque_data` (`data_solicitacao`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_usuario_email` (`email`),
  ADD KEY `idx_usuario_cpf` (`cpf`);

--
-- Índices de tabela `wallet`
--
ALTER TABLE `wallet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario` (`usuario_id`);

--
-- Índices de tabela `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_email` (`usuario_email`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `depositos`
--
ALTER TABLE `depositos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `planos_adquiridos`
--
ALTER TABLE `planos_adquiridos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `saques`
--
ALTER TABLE `saques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `wallet`
--
ALTER TABLE `wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para dumps de tabelas
--

--
-- Restrições para tabelas `depositos`
--
ALTER TABLE `depositos`
  ADD CONSTRAINT `depositos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `planos_adquiridos`
--
ALTER TABLE `planos_adquiridos`
  ADD CONSTRAINT `planos_adquiridos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `saques`
--
ALTER TABLE `saques`
  ADD CONSTRAINT `saques_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `wallet`
--
ALTER TABLE `wallet`
  ADD CONSTRAINT `wallet_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
