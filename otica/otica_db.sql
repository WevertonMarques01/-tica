-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12-Abr-2026 às 15:52
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `otica_db`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `data_consulta` date NOT NULL,
  `hora_consulta` time NOT NULL,
  `tipo_consulta` varchar(50) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'agendado',
  `observacoes` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `cliente_id`, `data_consulta`, `hora_consulta`, `tipo_consulta`, `status`, `observacoes`, `criado_em`, `atualizado_em`) VALUES
(1, 1, '2026-04-12', '11:30:00', 'Exame de Vista', 'concluido', 'cuida', '2026-04-12 13:04:05', '2026-04-12 13:23:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `endereco` text DEFAULT NULL,
  `bairro` text DEFAULT NULL,
  `numero` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `telefone`, `cpf`, `email`, `data_nascimento`, `criado_em`, `endereco`, `bairro`, `numero`) VALUES
(1, 'Jefferson Computer', '8598446146', '645.165.750-60', 'jeffersoncomputer58@gmail.com', NULL, '2026-04-10 15:28:54', 'salaberga', '', 5),
(3, 'cirilo', '1111111111111111111', '000.000.121-21', 'ciriloerafaely@gmail.com', NULL, '2026-04-10 15:52:45', 'salaberga', 'OUTRA BANDA', 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `financeiro`
--

CREATE TABLE `financeiro` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `venda_id` int(11) DEFAULT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `forma_pagamento` varchar(30) DEFAULT NULL,
  `data_pagamento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs`
--

CREATE TABLE `logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(100) DEFAULT NULL,
  `detalhes` text DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `logs`
--

INSERT INTO `logs` (`id`, `usuario_id`, `acao`, `detalhes`, `data`) VALUES
(1, 2, 'logout', 'Logout realizado', '2025-08-17 01:27:57'),
(2, 2, 'logout', 'Logout realizado', '2025-08-17 14:53:05'),
(3, 2, 'logout', 'Logout realizado', '2025-08-17 14:53:47'),
(4, 3, 'venda_excluida', 'Venda ID: 2 excluída', '2026-04-11 22:48:28'),
(5, 3, 'cliente_excluido', 'Cliente ID: 2 (teste) excluído', '2026-04-11 22:48:44');

-- --------------------------------------------------------

--
-- Estrutura da tabela `ordens_servico`
--

CREATE TABLE `ordens_servico` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `receita_id` int(11) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `previsao_entrega` date DEFAULT NULL,
  `data_abertura` timestamp NOT NULL DEFAULT current_timestamp(),
  `obs` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `cor` varchar(30) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `estoque` int(11) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `codigo` varchar(50) DEFAULT NULL,
  `estoque_atual` int(11) DEFAULT NULL,
  `preco_venda` decimal(10,2) DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `tipo`, `marca`, `modelo`, `cor`, `preco`, `estoque`, `criado_em`, `codigo`, `estoque_atual`, `preco_venda`, `descricao`) VALUES
(1, 'teste', 're', 'er', 're', 'er', 1.00, 1, '2026-04-10 15:34:18', NULL, NULL, NULL, 'sadasd');

-- --------------------------------------------------------

--
-- Estrutura da tabela `receitas`
--

CREATE TABLE `receitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `esfera_od` varchar(10) DEFAULT NULL,
  `cilindro_od` varchar(10) DEFAULT NULL,
  `eixo_od` varchar(10) DEFAULT NULL,
  `adicao_od` varchar(10) DEFAULT NULL,
  `esfera_oe` varchar(10) DEFAULT NULL,
  `cilindro_oe` varchar(10) DEFAULT NULL,
  `eixo_oe` varchar(10) DEFAULT NULL,
  `adicao_oe` varchar(10) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `data_receita` date DEFAULT NULL,
  `validade` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `receitas`
--

INSERT INTO `receitas` (`id`, `cliente_id`, `esfera_od`, `cilindro_od`, `eixo_od`, `adicao_od`, `esfera_oe`, `cilindro_oe`, `eixo_oe`, `adicao_oe`, `obs`, `data_receita`, `validade`) VALUES
(1, 1, '12', '12', '12', '12', '12', '12', '12', '12', 'asda', '2026-04-10', '2028-01-23');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha_hash` text NOT NULL,
  `permissao` int(11) NOT NULL,
  `perfil` varchar(20) NOT NULL DEFAULT 'vendedor',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha_hash`, `permissao`, `perfil`, `ativo`, `criado_em`, `ultimo_login`, `updated_at`) VALUES
(2, 'Orcleison', 'admin@otica.com', '$2y$10$6uoCoFXT/t0ltzIrOXAqd.tJyTIDjG0tvtZLN4lNiv8bUnxV4piXi', 1, 'admin', 1, '0000-00-00 00:00:00', NULL, NULL),
(3, 'Dono', 'dono@otica.com', '$2y$10$hOs5f0jySdndayqjwaVATOsEoWWN6YqO4P75NsGvJCNnYs16Dl8qW', 0, 'admin', 1, '2026-04-09 17:30:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `forma_pagamento` varchar(30) DEFAULT NULL,
  `data_venda` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `vendas`
--

INSERT INTO `vendas` (`id`, `cliente_id`, `usuario_id`, `total`, `forma_pagamento`, `data_venda`) VALUES
(1, 1, 3, 1.00, 'dinheiro', '2026-04-10 15:35:31');

-- --------------------------------------------------------

--
-- Estrutura da tabela `venda_produtos`
--

CREATE TABLE `venda_produtos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `venda_id` int(11) DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `preco_unitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `venda_produtos`
--

INSERT INTO `venda_produtos` (`id`, `venda_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(1, 1, 1, 1, 1.00);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_agendamentos_cliente` (`cliente_id`),
  ADD KEY `idx_agendamentos_data` (`data_consulta`),
  ADD KEY `idx_agendamentos_status` (`status`);

--
-- Índices para tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_clientes_cpf` (`cpf`),
  ADD KEY `idx_clientes_email` (`email`);

--
-- Índices para tabela `financeiro`
--
ALTER TABLE `financeiro`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_financeiro_venda` (`venda_id`);

--
-- Índices para tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_usuario` (`usuario_id`),
  ADD KEY `idx_logs_data` (`data`);

--
-- Índices para tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ordens_servico_cliente` (`cliente_id`),
  ADD KEY `idx_ordens_servico_status` (`status`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `receitas`
--
ALTER TABLE `receitas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vendas_cliente` (`cliente_id`),
  ADD KEY `idx_vendas_usuario` (`usuario_id`);

--
-- Índices para tabela `venda_produtos`
--
ALTER TABLE `venda_produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venda_produtos_venda` (`venda_id`),
  ADD KEY `idx_venda_produtos_produto` (`produto_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `financeiro`
--
ALTER TABLE `financeiro`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `receitas`
--
ALTER TABLE `receitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `venda_produtos`
--
ALTER TABLE `venda_produtos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
