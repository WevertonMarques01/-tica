-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/08/2025 às 02:14
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

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

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_relatorio_estoque_baixo` ()   BEGIN
    SELECT 
        p.id,
        p.codigo,
        p.nome,
        c.nome as categoria,
        m.nome as marca,
        p.estoque_atual,
        p.estoque_minimo,
        p.preco_venda
    FROM produtos p
    LEFT JOIN categorias_produtos c ON p.categoria_id = c.id
    LEFT JOIN marcas m ON p.marca_id = m.id
    WHERE p.estoque_atual <= p.estoque_minimo
    AND p.ativo = 1
    ORDER BY p.estoque_atual ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_relatorio_receitas_vencidas` ()   BEGIN
    SELECT 
        r.id,
        r.numero_receita,
        r.data_receita,
        r.validade_receita,
        c.nome as cliente_nome,
        c.documento as cliente_documento,
        c.telefone as cliente_telefone,
        DATEDIFF(r.validade_receita, CURDATE()) as dias_vencimento
    FROM receitas r
    INNER JOIN clientes c ON r.cliente_id = c.id
    WHERE r.validade_receita < CURDATE()
    AND r.status = 'ativa'
    ORDER BY r.validade_receita ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_relatorio_vendas_periodo` (IN `p_data_inicio` DATE, IN `p_data_fim` DATE)   BEGIN
    SELECT 
        v.id,
        v.numero_venda,
        v.data_venda,
        c.nome as cliente_nome,
        c.documento as cliente_documento,
        u.nome as vendedor_nome,
        v.subtotal,
        v.desconto_valor,
        v.valor_total,
        v.forma_pagamento,
        v.status
    FROM vendas v
    INNER JOIN clientes c ON v.cliente_id = c.id
    INNER JOIN usuarios u ON v.usuario_id = u.id
    WHERE DATE(v.data_venda) BETWEEN p_data_inicio AND p_data_fim
    ORDER BY v.data_venda DESC;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias_produtos`
--

CREATE TABLE `categorias_produtos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias_produtos`
--

INSERT INTO `categorias_produtos` (`id`, `nome`, `descricao`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Óculos de Grau', 'Óculos com lentes corretivas', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(2, 'Óculos de Sol', 'Óculos escuros', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(3, 'Lentes', 'Lentes para óculos', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(4, 'Armações', 'Armações de óculos', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(5, 'Acessórios', 'Acessórios diversos', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(6, 'Produtos de Limpeza', 'Produtos para limpeza de óculos', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `documento` varchar(18) NOT NULL,
  `tipo_documento` enum('cpf','cnpj') NOT NULL DEFAULT 'cpf',
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `sexo` enum('M','F','O') DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('string','integer','decimal','boolean','json') NOT NULL DEFAULT 'string',
  `categoria` varchar(50) DEFAULT 'geral',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `descricao`, `tipo`, `categoria`, `created_at`, `updated_at`) VALUES
(1, 'empresa_nome', 'Ótica Exemplo', 'Nome da empresa', 'string', 'empresa', '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(2, 'empresa_cnpj', '00.000.000/0000-00', 'CNPJ da empresa', 'string', 'empresa', '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(3, 'empresa_endereco', 'Rua Exemplo, 123', 'Endereço da empresa', 'string', 'empresa', '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(4, 'empresa_telefone', '(11) 99999-9999', 'Telefone da empresa', 'string', 'empresa', '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(5, 'empresa_email', 'contato@otica.com', 'Email da empresa', 'string', 'empresa', '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(6, 'sistema_tema', 'light', 'Tema do sistema (light/dark)', 'string', 'sistema', '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(7, 'estoque_alerta_minimo', '5', 'Quantidade mínima para alerta de estoque', 'integer', 'estoque', '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(8, 'receita_validade_dias', '365', 'Validade padrão das receitas em dias', 'integer', 'receita', '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(9, 'venda_desconto_maximo', '20.00', 'Desconto máximo permitido em vendas (%)', 'decimal', 'venda', '2025-08-24 23:51:37', '2025-08-24 23:51:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_ordens`
--

CREATE TABLE `historico_ordens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ordem_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `status_anterior` enum('pendente','em_andamento','concluida','cancelada','entregue') DEFAULT NULL,
  `status_novo` enum('pendente','em_andamento','concluida','cancelada','entregue') NOT NULL,
  `observacao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_venda`
--

CREATE TABLE `itens_venda` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `venda_id` bigint(20) UNSIGNED NOT NULL,
  `produto_id` bigint(20) UNSIGNED DEFAULT NULL,
  `descricao_produto` varchar(200) DEFAULT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `preco_unitario` decimal(10,2) NOT NULL,
  `desconto_percentual` decimal(5,2) DEFAULT 0.00,
  `desconto_valor` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Acionadores `itens_venda`
--
DELIMITER $$
CREATE TRIGGER `tr_itens_venda_estoque_saida` AFTER INSERT ON `itens_venda` FOR EACH ROW BEGIN
    IF NEW.produto_id IS NOT NULL THEN
        UPDATE produtos 
        SET estoque_atual = estoque_atual - NEW.quantidade 
        WHERE id = NEW.produto_id;
        
        INSERT INTO movimentacao_estoque (produto_id, usuario_id, tipo_movimentacao, quantidade, quantidade_anterior, quantidade_atual, motivo, documento_referencia)
        SELECT 
            NEW.produto_id,
            v.usuario_id,
            'saida',
            NEW.quantidade,
            p.estoque_atual + NEW.quantidade,
            p.estoque_atual,
            'Venda',
            v.numero_venda
        FROM vendas v, produtos p
        WHERE v.id = NEW.venda_id AND p.id = NEW.produto_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs_sistema`
--

CREATE TABLE `logs_sistema` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `acao` varchar(100) NOT NULL,
  `tabela` varchar(50) DEFAULT NULL,
  `registro_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dados_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dados_anteriores`)),
  `dados_novos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dados_novos`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `detalhes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `marcas`
--

CREATE TABLE `marcas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `marcas`
--

INSERT INTO `marcas` (`id`, `nome`, `descricao`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Ray-Ban', 'Marca premium de óculos', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(2, 'Oakley', 'Óculos esportivos', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(3, 'Hoya', 'Fabricante de lentes', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(4, 'Essilor', 'Fabricante de lentes', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(5, 'Zeiss', 'Fabricante de lentes premium', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(6, 'Carrera', 'Marca de óculos esportivos', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(7, 'Polaroid', 'Óculos polarizados', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37'),
(8, 'Vogue', 'Marca de óculos fashion', 1, '2025-08-24 23:51:37', '2025-08-24 23:51:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacao_estoque`
--

CREATE TABLE `movimentacao_estoque` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produto_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_movimentacao` enum('entrada','saida','ajuste','transferencia') NOT NULL,
  `quantidade` int(11) NOT NULL,
  `quantidade_anterior` int(11) NOT NULL,
  `quantidade_atual` int(11) NOT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `documento_referencia` varchar(50) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_movimentacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordens_servico`
--

CREATE TABLE `ordens_servico` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_os` varchar(20) DEFAULT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `receita_id` bigint(20) UNSIGNED DEFAULT NULL,
  `venda_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo_servico` enum('fabricacao','reparo','ajuste','limpeza') NOT NULL DEFAULT 'fabricacao',
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pendente','em_andamento','concluida','cancelada','entregue') NOT NULL DEFAULT 'pendente',
  `prioridade` enum('baixa','normal','alta','urgente') NOT NULL DEFAULT 'normal',
  `data_abertura` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_inicio` timestamp NULL DEFAULT NULL,
  `data_conclusao` timestamp NULL DEFAULT NULL,
  `data_entrega` timestamp NULL DEFAULT NULL,
  `previsao_entrega` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `observacoes_internas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Acionadores `ordens_servico`
--
DELIMITER $$
CREATE TRIGGER `tr_ordens_historico_status` AFTER UPDATE ON `ordens_servico` FOR EACH ROW BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO historico_ordens (ordem_id, usuario_id, status_anterior, status_novo, observacao)
        VALUES (NEW.id, NEW.usuario_id, OLD.status, NEW.status, 'Mudança de status automática');
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `tr_ordens_numero_auto` BEFORE INSERT ON `ordens_servico` FOR EACH ROW BEGIN
    IF NEW.numero_os IS NULL THEN
        SET NEW.numero_os = CONCAT('OS', YEAR(NOW()), LPAD(MONTH(NOW()), 2, '0'), LPAD((SELECT COUNT(*) + 1 FROM ordens_servico WHERE YEAR(data_abertura) = YEAR(NOW()) AND MONTH(data_abertura) = MONTH(NOW())), 4, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `venda_id` bigint(20) UNSIGNED NOT NULL,
  `numero_parcela` int(11) NOT NULL DEFAULT 1,
  `valor_parcela` decimal(10,2) NOT NULL,
  `data_vencimento` date NOT NULL,
  `data_pagamento` timestamp NULL DEFAULT NULL,
  `forma_pagamento` enum('dinheiro','cartao_credito','cartao_debito','pix','boleto','transferencia','cheque') DEFAULT NULL,
  `status` enum('pendente','pago','atrasado','cancelado') NOT NULL DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `categoria_id` bigint(20) UNSIGNED DEFAULT NULL,
  `marca_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `cor` varchar(30) DEFAULT NULL,
  `tamanho` varchar(20) DEFAULT NULL,
  `material` varchar(50) DEFAULT NULL,
  `preco_custo` decimal(10,2) DEFAULT 0.00,
  `preco_venda` decimal(10,2) NOT NULL DEFAULT 0.00,
  `preco_promocional` decimal(10,2) DEFAULT NULL,
  `estoque_minimo` int(11) DEFAULT 0,
  `estoque_atual` int(11) NOT NULL DEFAULT 0,
  `unidade_medida` varchar(10) DEFAULT 'UN',
  `codigo_barras` varchar(50) DEFAULT NULL,
  `fornecedor` varchar(100) DEFAULT NULL,
  `garantia_dias` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `receitas`
--

CREATE TABLE `receitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `numero_receita` varchar(20) DEFAULT NULL,
  `indicacao` varchar(100) DEFAULT NULL,
  `nome_paciente` varchar(100) DEFAULT NULL,
  `endereco_paciente` varchar(200) DEFAULT NULL,
  `bairro_paciente` varchar(100) DEFAULT NULL,
  `numero_paciente` varchar(10) DEFAULT NULL,
  `cpf_paciente` varchar(14) DEFAULT NULL,
  `telefone_paciente` varchar(20) DEFAULT NULL,
  `fiador_nome` varchar(100) DEFAULT NULL,
  `fiador_endereco` varchar(200) DEFAULT NULL,
  `fiador_cpf` varchar(14) DEFAULT NULL,
  `od_esfera` decimal(4,2) DEFAULT NULL,
  `od_cilindro` decimal(4,2) DEFAULT NULL,
  `od_eixo` int(3) DEFAULT NULL,
  `od_dnp` decimal(4,2) DEFAULT NULL,
  `od_altura` decimal(4,2) DEFAULT NULL,
  `oe_esfera` decimal(4,2) DEFAULT NULL,
  `oe_cilindro` decimal(4,2) DEFAULT NULL,
  `oe_eixo` int(3) DEFAULT NULL,
  `oe_dnp` decimal(4,2) DEFAULT NULL,
  `oe_altura` decimal(4,2) DEFAULT NULL,
  `adicao` decimal(4,2) DEFAULT NULL,
  `distancia_pupilar` decimal(4,2) DEFAULT NULL,
  `altura_segmento` decimal(4,2) DEFAULT NULL,
  `armacoes_selecionadas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`armacoes_selecionadas`)),
  `lentes_selecionadas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`lentes_selecionadas`)),
  `marca_lente` varchar(50) DEFAULT NULL,
  `tipos_lentes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tipos_lentes`)),
  `tratamento_antirreflexo` tinyint(1) DEFAULT 0,
  `tratamento_fotossensivel` tinyint(1) DEFAULT 0,
  `tratamento_transitions` tinyint(1) DEFAULT 0,
  `tratamento_blue_control` tinyint(1) DEFAULT 0,
  `observacoes` text DEFAULT NULL,
  `data_receita` date NOT NULL,
  `validade_receita` date DEFAULT NULL,
  `status` enum('ativa','expirada','cancelada') NOT NULL DEFAULT 'ativa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','vendedor','optico','gerente') NOT NULL DEFAULT 'vendedor',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `ativo`, `ultimo_login`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@otica.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, '2025-08-24 23:51:36', '2025-08-24 23:51:36'),
(2, 'Dono da Ótica', 'dono@otica.com', '$2y$10$14cLmTHt0ft/TR/JFQ.rkOh1RQ8SPZZWVcojjZb/E9Hm0UbCB3h9m', 'admin', 1, NULL, '2025-08-25 00:01:28', '2025-08-25 00:01:28');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_venda` varchar(20) DEFAULT NULL,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `receita_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo_venda` enum('produto','servico','misto') NOT NULL DEFAULT 'produto',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `desconto_percentual` decimal(5,2) DEFAULT 0.00,
  `desconto_valor` decimal(10,2) DEFAULT 0.00,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `forma_pagamento` enum('dinheiro','cartao_credito','cartao_debito','pix','boleto','transferencia','cheque') NOT NULL,
  `parcelas` int(11) DEFAULT 1,
  `valor_parcela` decimal(10,2) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('pendente','paga','cancelada','estornada') NOT NULL DEFAULT 'pendente',
  `data_venda` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_pagamento` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Acionadores `vendas`
--
DELIMITER $$
CREATE TRIGGER `tr_vendas_numero_auto` BEFORE INSERT ON `vendas` FOR EACH ROW BEGIN
    IF NEW.numero_venda IS NULL THEN
        SET NEW.numero_venda = CONCAT('VDA', YEAR(NOW()), LPAD(MONTH(NOW()), 2, '0'), LPAD((SELECT COUNT(*) + 1 FROM vendas WHERE YEAR(data_venda) = YEAR(NOW()) AND MONTH(data_venda) = MONTH(NOW())), 4, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_dashboard_vendas`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_dashboard_vendas` (
`data` date
,`total_vendas` bigint(21)
,`valor_total` decimal(32,2)
,`ticket_medio` decimal(14,6)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_ordens_pendentes`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_ordens_pendentes` (
`id` bigint(20) unsigned
,`numero_os` varchar(20)
,`cliente_id` bigint(20) unsigned
,`usuario_id` bigint(20) unsigned
,`receita_id` bigint(20) unsigned
,`venda_id` bigint(20) unsigned
,`tipo_servico` enum('fabricacao','reparo','ajuste','limpeza')
,`descricao` text
,`valor` decimal(10,2)
,`status` enum('pendente','em_andamento','concluida','cancelada','entregue')
,`prioridade` enum('baixa','normal','alta','urgente')
,`data_abertura` timestamp
,`data_inicio` timestamp
,`data_conclusao` timestamp
,`data_entrega` timestamp
,`previsao_entrega` date
,`observacoes` text
,`observacoes_internas` text
,`created_at` timestamp
,`updated_at` timestamp
,`cliente_nome` varchar(100)
,`cliente_telefone` varchar(20)
,`responsavel_nome` varchar(100)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_produtos_estoque_baixo`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_produtos_estoque_baixo` (
`id` bigint(20) unsigned
,`codigo` varchar(50)
,`nome` varchar(100)
,`descricao` text
,`categoria_id` bigint(20) unsigned
,`marca_id` bigint(20) unsigned
,`tipo` varchar(50)
,`modelo` varchar(50)
,`cor` varchar(30)
,`tamanho` varchar(20)
,`material` varchar(50)
,`preco_custo` decimal(10,2)
,`preco_venda` decimal(10,2)
,`preco_promocional` decimal(10,2)
,`estoque_minimo` int(11)
,`estoque_atual` int(11)
,`unidade_medida` varchar(10)
,`codigo_barras` varchar(50)
,`fornecedor` varchar(100)
,`garantia_dias` int(11)
,`observacoes` text
,`ativo` tinyint(1)
,`created_at` timestamp
,`updated_at` timestamp
,`categoria_nome` varchar(50)
,`marca_nome` varchar(50)
);

-- --------------------------------------------------------

--
-- Estrutura para view `vw_dashboard_vendas`
--
DROP TABLE IF EXISTS `vw_dashboard_vendas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_dashboard_vendas`  AS SELECT cast(`vendas`.`data_venda` as date) AS `data`, count(0) AS `total_vendas`, sum(`vendas`.`valor_total`) AS `valor_total`, avg(`vendas`.`valor_total`) AS `ticket_medio` FROM `vendas` WHERE `vendas`.`status` = 'paga' GROUP BY cast(`vendas`.`data_venda` as date) ORDER BY cast(`vendas`.`data_venda` as date) DESC ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_ordens_pendentes`
--
DROP TABLE IF EXISTS `vw_ordens_pendentes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_ordens_pendentes`  AS SELECT `os`.`id` AS `id`, `os`.`numero_os` AS `numero_os`, `os`.`cliente_id` AS `cliente_id`, `os`.`usuario_id` AS `usuario_id`, `os`.`receita_id` AS `receita_id`, `os`.`venda_id` AS `venda_id`, `os`.`tipo_servico` AS `tipo_servico`, `os`.`descricao` AS `descricao`, `os`.`valor` AS `valor`, `os`.`status` AS `status`, `os`.`prioridade` AS `prioridade`, `os`.`data_abertura` AS `data_abertura`, `os`.`data_inicio` AS `data_inicio`, `os`.`data_conclusao` AS `data_conclusao`, `os`.`data_entrega` AS `data_entrega`, `os`.`previsao_entrega` AS `previsao_entrega`, `os`.`observacoes` AS `observacoes`, `os`.`observacoes_internas` AS `observacoes_internas`, `os`.`created_at` AS `created_at`, `os`.`updated_at` AS `updated_at`, `c`.`nome` AS `cliente_nome`, `c`.`telefone` AS `cliente_telefone`, `u`.`nome` AS `responsavel_nome` FROM ((`ordens_servico` `os` join `clientes` `c` on(`os`.`cliente_id` = `c`.`id`)) join `usuarios` `u` on(`os`.`usuario_id` = `u`.`id`)) WHERE `os`.`status` in ('pendente','em_andamento') ORDER BY `os`.`prioridade` DESC, `os`.`data_abertura` ASC ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_produtos_estoque_baixo`
--
DROP TABLE IF EXISTS `vw_produtos_estoque_baixo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_produtos_estoque_baixo`  AS SELECT `p`.`id` AS `id`, `p`.`codigo` AS `codigo`, `p`.`nome` AS `nome`, `p`.`descricao` AS `descricao`, `p`.`categoria_id` AS `categoria_id`, `p`.`marca_id` AS `marca_id`, `p`.`tipo` AS `tipo`, `p`.`modelo` AS `modelo`, `p`.`cor` AS `cor`, `p`.`tamanho` AS `tamanho`, `p`.`material` AS `material`, `p`.`preco_custo` AS `preco_custo`, `p`.`preco_venda` AS `preco_venda`, `p`.`preco_promocional` AS `preco_promocional`, `p`.`estoque_minimo` AS `estoque_minimo`, `p`.`estoque_atual` AS `estoque_atual`, `p`.`unidade_medida` AS `unidade_medida`, `p`.`codigo_barras` AS `codigo_barras`, `p`.`fornecedor` AS `fornecedor`, `p`.`garantia_dias` AS `garantia_dias`, `p`.`observacoes` AS `observacoes`, `p`.`ativo` AS `ativo`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at`, `c`.`nome` AS `categoria_nome`, `m`.`nome` AS `marca_nome` FROM ((`produtos` `p` left join `categorias_produtos` `c` on(`p`.`categoria_id` = `c`.`id`)) left join `marcas` `m` on(`p`.`marca_id` = `m`.`id`)) WHERE `p`.`estoque_atual` <= `p`.`estoque_minimo` AND `p`.`ativo` = 1 ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias_produtos`
--
ALTER TABLE `categorias_produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categorias_nome` (`nome`),
  ADD KEY `idx_categorias_ativo` (`ativo`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documento` (`documento`),
  ADD UNIQUE KEY `uk_clientes_documento` (`documento`),
  ADD KEY `idx_clientes_nome` (`nome`),
  ADD KEY `idx_clientes_email` (`email`),
  ADD KEY `idx_clientes_telefone` (`telefone`),
  ADD KEY `idx_clientes_ativo` (`ativo`),
  ADD KEY `idx_clientes_cidade` (`cidade`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`),
  ADD UNIQUE KEY `uk_configuracoes_chave` (`chave`),
  ADD KEY `idx_configuracoes_categoria` (`categoria`);

--
-- Índices de tabela `historico_ordens`
--
ALTER TABLE `historico_ordens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_historico_ordem` (`ordem_id`),
  ADD KEY `idx_historico_usuario` (`usuario_id`),
  ADD KEY `idx_historico_data` (`created_at`);

--
-- Índices de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_itens_venda_venda` (`venda_id`),
  ADD KEY `idx_itens_venda_produto` (`produto_id`);

--
-- Índices de tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_usuario` (`usuario_id`),
  ADD KEY `idx_logs_acao` (`acao`),
  ADD KEY `idx_logs_tabela` (`tabela`),
  ADD KEY `idx_logs_data` (`created_at`),
  ADD KEY `idx_logs_ip` (`ip_address`);

--
-- Índices de tabela `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_marcas_nome` (`nome`),
  ADD KEY `idx_marcas_ativo` (`ativo`);

--
-- Índices de tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_movimentacao_produto` (`produto_id`),
  ADD KEY `idx_movimentacao_usuario` (`usuario_id`),
  ADD KEY `idx_movimentacao_tipo` (`tipo_movimentacao`),
  ADD KEY `idx_movimentacao_data` (`data_movimentacao`);

--
-- Índices de tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ordens_numero` (`numero_os`),
  ADD KEY `idx_ordens_cliente` (`cliente_id`),
  ADD KEY `idx_ordens_usuario` (`usuario_id`),
  ADD KEY `idx_ordens_receita` (`receita_id`),
  ADD KEY `idx_ordens_venda` (`venda_id`),
  ADD KEY `idx_ordens_status` (`status`),
  ADD KEY `idx_ordens_prioridade` (`prioridade`),
  ADD KEY `idx_ordens_data_abertura` (`data_abertura`),
  ADD KEY `idx_ordens_previsao` (`previsao_entrega`),
  ADD KEY `idx_ordens_status_prioridade` (`status`,`prioridade`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pagamentos_venda` (`venda_id`),
  ADD KEY `idx_pagamentos_status` (`status`),
  ADD KEY `idx_pagamentos_vencimento` (`data_vencimento`),
  ADD KEY `idx_pagamentos_pagamento` (`data_pagamento`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD UNIQUE KEY `uk_produtos_codigo` (`codigo`),
  ADD KEY `idx_produtos_nome` (`nome`),
  ADD KEY `idx_produtos_categoria` (`categoria_id`),
  ADD KEY `idx_produtos_marca` (`marca_id`),
  ADD KEY `idx_produtos_tipo` (`tipo`),
  ADD KEY `idx_produtos_ativo` (`ativo`),
  ADD KEY `idx_produtos_estoque` (`estoque_atual`),
  ADD KEY `idx_produtos_codigo_barras` (`codigo_barras`),
  ADD KEY `idx_produtos_categoria_ativo` (`categoria_id`,`ativo`);

--
-- Índices de tabela `receitas`
--
ALTER TABLE `receitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_receitas_cliente` (`cliente_id`),
  ADD KEY `idx_receitas_usuario` (`usuario_id`),
  ADD KEY `idx_receitas_data` (`data_receita`),
  ADD KEY `idx_receitas_status` (`status`),
  ADD KEY `idx_receitas_validade` (`validade_receita`),
  ADD KEY `idx_receitas_cliente_status` (`cliente_id`,`status`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuarios_email` (`email`),
  ADD KEY `idx_usuarios_perfil` (`perfil`),
  ADD KEY `idx_usuarios_ativo` (`ativo`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_vendas_numero` (`numero_venda`),
  ADD KEY `idx_vendas_cliente` (`cliente_id`),
  ADD KEY `idx_vendas_usuario` (`usuario_id`),
  ADD KEY `idx_vendas_receita` (`receita_id`),
  ADD KEY `idx_vendas_data` (`data_venda`),
  ADD KEY `idx_vendas_status` (`status`),
  ADD KEY `idx_vendas_forma_pagamento` (`forma_pagamento`),
  ADD KEY `idx_vendas_cliente_data` (`cliente_id`,`data_venda`),
  ADD KEY `idx_vendas_status_data` (`status`,`data_venda`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias_produtos`
--
ALTER TABLE `categorias_produtos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `historico_ordens`
--
ALTER TABLE `historico_ordens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `logs_sistema`
--
ALTER TABLE `logs_sistema`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ordens_servico`
--
ALTER TABLE `ordens_servico`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `receitas`
--
ALTER TABLE `receitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `historico_ordens`
--
ALTER TABLE `historico_ordens`
  ADD CONSTRAINT `historico_ordens_ibfk_1` FOREIGN KEY (`ordem_id`) REFERENCES `ordens_servico` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historico_ordens_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD CONSTRAINT `itens_venda_ibfk_1` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itens_venda_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `logs_sistema`
--
ALTER TABLE `logs_sistema`
  ADD CONSTRAINT `logs_sistema_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `movimentacao_estoque`
--
ALTER TABLE `movimentacao_estoque`
  ADD CONSTRAINT `movimentacao_estoque_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimentacao_estoque_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `ordens_servico`
--
ALTER TABLE `ordens_servico`
  ADD CONSTRAINT `ordens_servico_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ordens_servico_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ordens_servico_ibfk_3` FOREIGN KEY (`receita_id`) REFERENCES `receitas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ordens_servico_ibfk_4` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_produtos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `produtos_ibfk_2` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `receitas`
--
ALTER TABLE `receitas`
  ADD CONSTRAINT `receitas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `receitas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendas_ibfk_3` FOREIGN KEY (`receita_id`) REFERENCES `receitas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
