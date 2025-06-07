-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02/06/2025 às 03:01
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `db_delicias_online`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_adm`
--

CREATE TABLE `tb_adm` (
  `id_adm` int(11) NOT NULL,
  `nome_adm` varchar(255) NOT NULL,
  `funcao_adm` varchar(255) DEFAULT NULL,
  `cpf_adm` varchar(20) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_cliente`
--

CREATE TABLE `tb_cliente` (
  `id_cliente` int(11) NOT NULL,
  `nome_cliente` varchar(255) DEFAULT NULL,
  `cpf_cliente` varchar(14) DEFAULT NULL,
  `data_nasc` date DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_cliente`
--

INSERT INTO `tb_cliente` (`id_cliente`, `nome_cliente`, `cpf_cliente`, `data_nasc`, `id_usuario`) VALUES
(1, 'Igor', '773.407.750-13', '2000-05-31', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_cobertura`
--

CREATE TABLE `tb_cobertura` (
  `id_cobertura` int(11) NOT NULL,
  `desc_cobertura` varchar(255) DEFAULT NULL,
  `valor_por_peso` decimal(10,2) DEFAULT NULL,
  `id_confeitaria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_cobertura`
--

INSERT INTO `tb_cobertura` (`id_cobertura`, `desc_cobertura`, `valor_por_peso`, `id_confeitaria`) VALUES
(1, 'Cobertura de Chocolate', 5.00, 2),
(2, 'Cobertura de Morango', 5.50, 2),
(3, 'Cobertura de Limão', 4.50, 2),
(4, 'Cobertura de Chocolate', 5.00, 3),
(5, 'Cobertura de Morango', 5.00, 3),
(6, 'Cobertura de Limão', 5.00, 3),
(7, 'Cobertura de Chocolate', 5.00, 4),
(8, 'Cobertura de Morango', 5.00, 4),
(9, 'Cobertura de Limão', 5.00, 4),
(10, 'Cobertura de Chocolate', 5.00, 5),
(11, 'Cobertura de Morango', 5.00, 5),
(12, 'Cobertura de Limão', 5.00, 5),
(13, 'Cobertura de Chocolate', 5.00, 7),
(14, 'Cobertura de Morango', 5.00, 7),
(15, 'Cobertura de Limão', 5.00, 7),
(16, 'Cobertura de Chocolate', 5.00, 7),
(17, 'Cobertura de Morango', 5.00, 7),
(18, 'Cobertura de Limão', 5.00, 7),
(19, 'Cobertura de Chocolate', 5.00, 6),
(20, 'Cobertura de Caramelo', 5.00, 6),
(21, 'Cobertura de Avelã', 5.00, 6),
(22, 'Cobertura de Chocolate', 5.00, 1),
(23, 'Cobertura de Caramelo', 5.00, 1),
(24, 'Cobertura de Avelã', 5.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_codigo`
--

CREATE TABLE `tb_codigo` (
  `id_codigo` int(11) NOT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_codigo`
--

INSERT INTO `tb_codigo` (`id_codigo`, `codigo`, `data_criacao`, `status`, `id_usuario`) VALUES
(1, '151891', '2025-05-31 22:34:22', 1, 1),
(2, '799851', '2025-05-31 22:43:34', 1, 2),
(3, '139728', '2025-06-01 21:09:02', 1, 3),
(4, '573585', '2025-06-01 21:11:16', 1, 4),
(5, '562772', '2025-06-01 21:13:31', 1, 5),
(6, '734810', '2025-06-01 21:16:12', 1, 6),
(7, '769204', '2025-06-01 21:18:32', 1, 7),
(8, '521920', '2025-06-01 21:25:00', 1, 8),
(9, '751361', '2025-06-01 21:26:54', 1, 9);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_confeitaria`
--

CREATE TABLE `tb_confeitaria` (
  `id_confeitaria` int(11) NOT NULL,
  `nome_confeitaria` varchar(255) DEFAULT NULL,
  `cnpj_confeitaria` varchar(18) DEFAULT NULL,
  `cep_confeitaria` varchar(9) DEFAULT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_saida` time DEFAULT NULL,
  `log_confeitaria` varchar(255) DEFAULT NULL,
  `num_local` varchar(20) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro_confeitaria` varchar(255) DEFAULT NULL,
  `cidade_confeitaria` varchar(255) DEFAULT NULL,
  `uf_confeitaria` varchar(2) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `img_confeitaria` varchar(255) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_confeitaria`
--

INSERT INTO `tb_confeitaria` (`id_confeitaria`, `nome_confeitaria`, `cnpj_confeitaria`, `cep_confeitaria`, `hora_entrada`, `hora_saida`, `log_confeitaria`, `num_local`, `complemento`, `bairro_confeitaria`, `cidade_confeitaria`, `uf_confeitaria`, `latitude`, `longitude`, `img_confeitaria`, `id_usuario`) VALUES
(1, 'Delicias da Nice', '63.331.355/0001-83', '08616-520', '09:30:00', '20:00:00', 'Rua Sonia', '30', NULL, 'Jardim Aeródromo Internacional', 'Suzano', 'SP', -23.57113680, -46.29613080, 'img/img-confeitaria/Captura de tela 2025-05-31 194311.png', 2),
(2, 'confeitaria_2', '73.315.731/0001-08', '08526-000', '09:00:00', '21:00:00', 'Avenida Governador Jânio Quadros', '100', NULL, 'Parque São Francisco', 'Ferraz de Vasconcelos', 'SP', -23.55533260, -46.38483650, 'img/img-confeitaria/Captura de tela 2025-06-01 180848.png', 3),
(3, 'confeitaria_3', '45.579.476/0001-67', '08616-520', '09:00:00', '19:30:00', 'Rua Sonia', '100', NULL, 'Jardim Aeródromo Internacional', 'Suzano', 'SP', -23.57113680, -46.29613080, 'img/img-confeitaria/Captura de tela 2025-06-01 181109.png', 4),
(4, 'confeitaria_4', '28.352.015/0001-38', '08560-590', '10:00:00', '21:00:00', 'Rua João de Godoy', '300', NULL, 'Biritiba', 'Poá', 'SP', -23.53638990, -46.33995100, 'img/img-confeitaria/Captura de tela 2025-06-01 181322.png', 5),
(5, 'confeitaria_5', '26.689.334/0001-08', '08674-011', '16:00:00', '23:00:00', 'Rua Benjamin Constant', '90', NULL, 'Centro', 'Suzano', 'SP', -23.54706220, -46.31429280, 'img/img-confeitaria/Captura de tela 2025-06-01 181604.png', 6),
(6, 'confeitaria_6', '41.942.098/0001-83', '08502-000', '10:00:00', '20:00:00', 'Avenida Brasil', '900', NULL, 'Vila Correa', 'Ferraz de Vasconcelos', 'SP', -23.53633450, -46.35900170, 'img/img-confeitaria/Captura de tela 2025-06-01 181822.png', 7),
(7, 'confeitaria_7', '86.329.576/0001-12', '08502-000', '14:00:00', '23:00:00', 'Avenida Brasil', '600', NULL, 'Vila Correa', 'Ferraz de Vasconcelos', 'SP', -23.53633450, -46.35900170, 'img/img-confeitaria/Captura de tela 2025-06-01 182452.png', 8),
(8, 'confeitaria_8', '02.085.760/0001-74', '08500-010', '11:00:00', '22:00:00', 'Praça Independência', '12', NULL, 'Vila Romanópolis', 'Ferraz de Vasconcelos', 'SP', -23.54131940, -46.36824760, 'img/img-confeitaria/Captura de tela 2025-06-01 182636.png', 9);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_conversa`
--

CREATE TABLE `tb_conversa` (
  `id_conversa` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_remetente` int(11) DEFAULT NULL,
  `id_destinatario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_conversa_mensagem`
--

CREATE TABLE `tb_conversa_mensagem` (
  `id_conversa_mensagem` int(11) NOT NULL,
  `id_mensagem` int(11) DEFAULT NULL,
  `id_conversa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_cupom`
--

CREATE TABLE `tb_cupom` (
  `id_cupom` int(11) NOT NULL,
  `titulo_cupom` varchar(255) DEFAULT NULL,
  `mensagem` varchar(255) DEFAULT NULL,
  `desc_cupom` varchar(255) DEFAULT NULL,
  `porcen_desconto` decimal(5,2) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_validade` datetime DEFAULT NULL,
  `cupom_usado` tinyint(1) DEFAULT 0,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_cupom`
--

INSERT INTO `tb_cupom` (`id_cupom`, `titulo_cupom`, `mensagem`, `desc_cupom`, `porcen_desconto`, `data_criacao`, `data_validade`, `cupom_usado`, `id_usuario`) VALUES
(1, '', '', '', 5.00, '2025-06-01 23:31:23', '2025-06-08 20:31:23', 0, 1),
(2, 'Cupom de Aniversário', 'Parabéns! Aproveite seu desconto especial de aniversário.', 'Aniversario5', 5.00, '2025-06-02 00:59:50', '2025-06-08 20:31:23', 0, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_ddd`
--

CREATE TABLE `tb_ddd` (
  `id_ddd` int(11) NOT NULL,
  `ddd` varchar(3) DEFAULT NULL,
  `uf_ddd` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_ddd`
--

INSERT INTO `tb_ddd` (`id_ddd`, `ddd`, `uf_ddd`) VALUES
(1, '11', 'SP'),
(2, '12', 'SP'),
(3, '13', 'SP'),
(4, '14', 'SP'),
(5, '15', 'SP'),
(6, '16', 'SP'),
(7, '17', 'SP'),
(8, '18', 'SP'),
(9, '19', 'SP'),
(10, '21', 'RJ'),
(11, '22', 'RJ'),
(12, '24', 'RJ'),
(13, '27', 'ES'),
(14, '28', 'ES'),
(15, '31', 'MG'),
(16, '32', 'MG'),
(17, '33', 'MG'),
(18, '34', 'MG'),
(19, '35', 'MG'),
(20, '37', 'MG'),
(21, '38', 'MG'),
(22, '41', 'PR'),
(23, '42', 'PR'),
(24, '43', 'PR'),
(25, '44', 'PR'),
(26, '45', 'PR'),
(27, '46', 'PR'),
(28, '47', 'SC'),
(29, '48', 'SC'),
(30, '49', 'SC'),
(31, '51', 'RS'),
(32, '53', 'RS'),
(33, '54', 'RS'),
(34, '55', 'RS'),
(35, '61', 'DF'),
(36, '62', 'GO'),
(37, '63', 'TO'),
(38, '64', 'GO'),
(39, '65', 'MT'),
(40, '66', 'MT'),
(41, '67', 'MS'),
(42, '68', 'AC'),
(43, '69', 'RO'),
(44, '71', 'BA'),
(45, '73', 'BA'),
(46, '74', 'BA'),
(47, '75', 'BA'),
(48, '77', 'BA'),
(49, '79', 'SE'),
(50, '81', 'PE'),
(51, '82', 'AL'),
(52, '83', 'PB'),
(53, '84', 'RN'),
(54, '85', 'CE'),
(55, '86', 'PI'),
(56, '87', 'PE'),
(57, '88', 'CE'),
(58, '89', 'PI'),
(59, '91', 'PA'),
(60, '92', 'AM'),
(61, '93', 'PA'),
(62, '94', 'PA'),
(63, '95', 'RR'),
(64, '96', 'AP'),
(65, '97', 'AM'),
(66, '98', 'MA'),
(67, '99', 'MA');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_decoracao`
--

CREATE TABLE `tb_decoracao` (
  `id_decoracao` int(11) NOT NULL,
  `desc_decoracao` varchar(255) DEFAULT NULL,
  `valor_por_peso` decimal(10,2) DEFAULT NULL,
  `id_confeitaria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_decoracao`
--

INSERT INTO `tb_decoracao` (`id_decoracao`, `desc_decoracao`, `valor_por_peso`, `id_confeitaria`) VALUES
(1, 'Granulado Colorido', 2.00, 2),
(2, 'Confete de Chocolate', 2.50, 2),
(3, 'Flores comestíveis', 3.00, 2),
(4, 'Granulado Colorido', 2.50, 3),
(5, 'Confete de Chocolate', 2.50, 3),
(6, 'Flores comestíveis', 2.50, 3),
(7, 'Granulado Colorido', 2.50, 4),
(8, 'Confete de Chocolate', 2.50, 4),
(9, 'Flores comestíveis', 2.50, 4),
(10, 'Granulado Colorido', 2.50, 5),
(11, 'Confete de Chocolate', 2.50, 5),
(12, 'Flores comestíveis', 2.50, 5),
(13, 'Granulado Colorido', 2.50, 7),
(14, 'Confete de Chocolate', 2.50, 7),
(15, 'Flores comestíveis', 2.50, 7),
(16, 'Granulado Colorido', 2.50, 7),
(17, 'Confete de Chocolate', 2.50, 7),
(18, 'Flores comestíveis', 2.50, 7),
(19, 'Granulado Colorido', 2.50, 6),
(20, 'Raspas de Chocolate', 2.50, 6),
(21, 'Pedaços de Nozes', 3.00, 6),
(22, 'Granulado Colorido', 2.50, 1),
(23, 'Raspas de Chocolate', 2.50, 1),
(24, 'Pedaços de Nozes', 3.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_endereco_cliente`
--

CREATE TABLE `tb_endereco_cliente` (
  `id_endereco_cliente` int(11) NOT NULL,
  `cep_cliente` varchar(9) DEFAULT NULL,
  `log_cliente` varchar(255) DEFAULT NULL,
  `num_local` varchar(20) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro_cliente` varchar(255) DEFAULT NULL,
  `cidade_cliente` varchar(255) DEFAULT NULL,
  `uf_cliente` varchar(2) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_endereco_cliente`
--

INSERT INTO `tb_endereco_cliente` (`id_endereco_cliente`, `cep_cliente`, `log_cliente`, `num_local`, `complemento`, `bairro_cliente`, `cidade_cliente`, `uf_cliente`, `latitude`, `longitude`, `id_cliente`) VALUES
(1, '08560-590', 'Rua João de Godoy', '120', NULL, 'Biritiba', 'Poá', 'SP', -23.53638990, -46.33995100, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_formato`
--

CREATE TABLE `tb_formato` (
  `id_formato` int(11) NOT NULL,
  `desc_formato` varchar(255) DEFAULT NULL,
  `valor_por_peso` decimal(10,2) DEFAULT NULL,
  `id_confeitaria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_formato`
--

INSERT INTO `tb_formato` (`id_formato`, `desc_formato`, `valor_por_peso`, `id_confeitaria`) VALUES
(1, 'Redondo', 3.00, 2),
(2, 'Quadrado', 3.50, 2),
(3, 'Coração', 4.00, 2),
(4, 'Redondo', 3.50, 3),
(5, 'Quadrado', 3.50, 3),
(6, 'Coração', 3.50, 3),
(7, 'Redondo', 3.50, 4),
(8, 'Quadrado', 3.50, 4),
(9, 'Coração', 3.50, 4),
(10, 'Redondo', 3.50, 5),
(11, 'Quadrado', 3.50, 5),
(12, 'Coração', 3.50, 5),
(13, 'Redondo', 3.50, 7),
(14, 'Quadrado', 3.50, 7),
(15, 'Coração', 3.50, 7),
(16, 'Redondo', 3.50, 7),
(17, 'Quadrado', 3.50, 7),
(18, 'Coração', 3.50, 7),
(19, 'Redondo', 3.50, 6),
(20, 'Quadrado', 3.50, 6),
(21, 'Estrela', 4.00, 6),
(22, 'Redondo', 3.50, 1),
(23, 'Quadrado', 3.50, 1),
(24, 'Estrela', 4.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_forma_pagamento`
--

CREATE TABLE `tb_forma_pagamento` (
  `id_forma_pagamento` int(11) NOT NULL,
  `forma_pagamento` varchar(255) DEFAULT NULL,
  `descricao` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_forma_pagamento`
--

INSERT INTO `tb_forma_pagamento` (`id_forma_pagamento`, `forma_pagamento`, `descricao`) VALUES
(1, 'Pix', 'Pagamento eletrônico instantâneo, disponível 24 horas por dia, 7 dias por semana.'),
(2, 'Cartão de Debito', 'Pagamento realizado através da movimentação dos fundos disponíveis na conta corrente ou poupança do titular do cartão.'),
(3, 'Cartão de Credito', 'Pagamento realizado através de crédito concedido por uma instituição financeira, com cobrança futura em fatura.'),
(4, 'Boleto', 'Documento de cobrança bancária pagável em agências bancárias, casas lotéricas e internet banking até a data de vencimento.'),
(5, 'Vale Presente', 'Crédito pré-pago utilizado para adquirir produtos ou serviços em um determinado estabelecimento ou plataforma.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_itens_pedido`
--

CREATE TABLE `tb_itens_pedido` (
  `id_itens_pedido` int(11) NOT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `id_produto` int(11) DEFAULT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_cupom` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_itens_pedido`
--

INSERT INTO `tb_itens_pedido` (`id_itens_pedido`, `quantidade`, `id_produto`, `id_pedido`, `id_cupom`) VALUES
(1, 1, 4, 1, NULL),
(2, 1, 6, 1, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_massa`
--

CREATE TABLE `tb_massa` (
  `id_massa` int(11) NOT NULL,
  `desc_massa` varchar(255) DEFAULT NULL,
  `valor_por_peso` decimal(10,2) DEFAULT NULL,
  `id_confeitaria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_massa`
--

INSERT INTO `tb_massa` (`id_massa`, `desc_massa`, `valor_por_peso`, `id_confeitaria`) VALUES
(1, 'Massa de Chocolate', 7.00, 2),
(2, 'Massa de Baunilha', 6.50, 2),
(3, 'Massa de Cenoura', 6.00, 2),
(4, 'Massa de Chocolate', 7.00, 3),
(5, 'Massa de Baunilha', 7.00, 3),
(6, 'Massa de Cenoura', 7.00, 3),
(7, 'Massa de Chocolate', 7.00, 4),
(8, 'Massa de Baunilha', 7.00, 4),
(9, 'Massa de Cenoura', 7.00, 4),
(10, 'Massa de Chocolate', 7.00, 5),
(11, 'Massa de Baunilha', 7.00, 5),
(12, 'Massa de Cenoura', 7.00, 5),
(13, 'Massa de Chocolate', 7.00, 7),
(14, 'Massa de Baunilha', 7.00, 7),
(15, 'Massa de Cenoura', 7.00, 7),
(16, 'Massa de Chocolate', 7.00, 7),
(17, 'Massa de Baunilha', 7.00, 7),
(18, 'Massa de Cenoura', 7.00, 7),
(19, 'Massa de Chocolate', 7.00, 6),
(20, 'Massa de Baunilha', 7.00, 6),
(21, 'Massa de Aveia', 7.00, 6),
(22, 'Massa de Chocolate', 7.00, 1),
(23, 'Massa de Baunilha', 7.00, 1),
(24, 'Massa de Aveia', 7.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_mensagem`
--

CREATE TABLE `tb_mensagem` (
  `id_mensagem` int(11) NOT NULL,
  `desc_mensagem` varchar(255) DEFAULT NULL,
  `hora_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_mensagem` varchar(20) DEFAULT 'nao_lido',
  `id_remetente` int(11) DEFAULT NULL,
  `id_destinatario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_pedido`
--

CREATE TABLE `tb_pedido` (
  `id_pedido` int(11) NOT NULL,
  `valor_total` decimal(10,2) DEFAULT NULL,
  `desconto` decimal(5,2) DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) DEFAULT NULL,
  `frete` decimal(10,2) DEFAULT 0.00,
  `id_endereco_cliente` int(11) DEFAULT NULL,
  `id_forma_pagamento` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_pedido`
--

INSERT INTO `tb_pedido` (`id_pedido`, `valor_total`, `desconto`, `data_pedido`, `status`, `frete`, `id_endereco_cliente`, `id_forma_pagamento`, `id_cliente`) VALUES
(1, 117.00, 0.00, '2025-06-01 23:56:13', 'Cancelado pelo Cliente', 5.00, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_pedido_personalizado`
--

CREATE TABLE `tb_pedido_personalizado` (
  `id_pedido_personalizado` int(11) NOT NULL,
  `valor_total` decimal(10,2) DEFAULT NULL,
  `desconto` decimal(5,2) DEFAULT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `peso` decimal(6,3) DEFAULT NULL,
  `frete` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `id_endereco_cliente` int(11) DEFAULT NULL,
  `id_forma_pagamento` int(11) DEFAULT NULL,
  `id_cobertura` int(11) DEFAULT NULL,
  `id_decoracao` int(11) DEFAULT NULL,
  `id_formato` int(11) DEFAULT NULL,
  `id_massa` int(11) DEFAULT NULL,
  `id_recheio` int(11) DEFAULT NULL,
  `id_personalizado` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_pedido_personalizado`
--

INSERT INTO `tb_pedido_personalizado` (`id_pedido_personalizado`, `valor_total`, `desconto`, `data_pedido`, `peso`, `frete`, `status`, `id_endereco_cliente`, `id_forma_pagamento`, `id_cobertura`, `id_decoracao`, `id_formato`, `id_massa`, `id_recheio`, `id_personalizado`, `id_cliente`) VALUES
(1, 180.00, 0.00, '2025-06-01 23:57:08', 0.750, 0.00, 'Entregue!', NULL, NULL, 7, 7, 7, 7, 7, 7, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_personalizado`
--

CREATE TABLE `tb_personalizado` (
  `id_personalizado` int(11) NOT NULL,
  `nome_personalizado` varchar(255) DEFAULT NULL,
  `desc_personalizado` text DEFAULT NULL,
  `img_personalizado` varchar(255) DEFAULT NULL,
  `cobertura_ativa` tinyint(1) DEFAULT 0,
  `decoracao_ativa` tinyint(1) DEFAULT 0,
  `formato_ativa` tinyint(1) DEFAULT 0,
  `massa_ativa` tinyint(1) DEFAULT 0,
  `recheio_ativa` tinyint(1) DEFAULT 0,
  `personalizado_ativo` tinyint(1) DEFAULT 1,
  `limite_entrega` int(11) DEFAULT NULL,
  `id_tipo_produto` int(11) DEFAULT NULL,
  `id_confeitaria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_personalizado`
--

INSERT INTO `tb_personalizado` (`id_personalizado`, `nome_personalizado`, `desc_personalizado`, `img_personalizado`, `cobertura_ativa`, `decoracao_ativa`, `formato_ativa`, `massa_ativa`, `recheio_ativa`, `personalizado_ativo`, `limite_entrega`, `id_tipo_produto`, `id_confeitaria`) VALUES
(1, 'Bolo Especial', 'Bolo personalizado especial', 'img/img-personalizado/bolo_especial.png', 1, 1, 1, 1, 1, 1, 20, 5, 2),
(2, 'Torta Decorada', 'Torta com decoração personalizada', 'img/img-personalizado/torta_decorada.png', 1, 1, 1, 1, 1, 1, 15, 6, 2),
(3, 'Cupcake Temático', 'Cupcake com tema especial', 'img/img-personalizado/cupcake_tematico.png', 1, 1, 1, 1, 1, 1, 10, 7, 2),
(4, 'Bolo Especial', 'Bolo personalizado especial', 'img/img-personalizado/bolo_especial.png', 1, 1, 1, 1, 1, 1, 20, 8, 3),
(5, 'Torta Decorada', 'Torta com decoração personalizada', 'img/img-personalizado/torta_decorada.png', 1, 1, 1, 1, 1, 1, 20, 9, 3),
(6, 'Cupcake Temático', 'Cupcake com tema especial', 'img/img-personalizado/cupcake_tematico.png', 1, 1, 1, 1, 1, 1, 20, 10, 3),
(7, 'Bolo Especial', 'Bolo personalizado especial', 'img/img-personalizado/bolo_especial.png', 1, 1, 1, 1, 1, 1, 20, 11, 4),
(8, 'Torta Decorada', 'Torta com decoração personalizada', 'img/img-personalizado/torta_decorada.png', 1, 1, 1, 1, 1, 1, 20, 12, 4),
(9, 'Cupcake Temático', 'Cupcake com tema especial', 'img/img-personalizado/cupcake_tematico.png', 1, 1, 1, 1, 1, 1, 20, 13, 4),
(10, 'Bolo Especial', 'Bolo personalizado especial', 'img/img-personalizado/bolo_especial.png', 1, 1, 1, 1, 1, 1, 20, 14, 5),
(11, 'Torta Decorada', 'Torta com decoração personalizada', 'img/img-personalizado/torta_decorada.png', 1, 1, 1, 1, 1, 1, 20, 15, 5),
(12, 'Cupcake Temático', 'Cupcake com tema especial', 'img/img-personalizado/cupcake_tematico.png', 1, 1, 1, 1, 1, 1, 20, 16, 5),
(13, 'Bolo Especial', 'Bolo personalizado especial', 'img/img-personalizado/bolo_especial.png', 1, 1, 1, 1, 1, 1, 20, 17, 7),
(14, 'Torta Decorada', 'Torta com decoração personalizada', 'img/img-personalizado/torta_decorada.png', 1, 1, 1, 1, 1, 1, 20, 18, 7),
(15, 'Cupcake Temático', 'Cupcake com tema especial', 'img/img-personalizado/cupcake_tematico.png', 1, 1, 1, 1, 1, 1, 20, 19, 7),
(19, 'Pão de Mel Especial', 'Pão de mel personalizado', 'img/img-personalizado/pao_mel_especial.png', 1, 1, 1, 1, 1, 1, 20, 23, 6),
(20, 'Brownie Decorado', 'Brownie com decoração personalizada', 'img/img-personalizado/brownie_decorado.png', 1, 1, 1, 1, 1, 1, 20, 24, 6),
(21, 'Cookie Temático', 'Cookie com tema especial', 'img/img-personalizado/cookie_tematico.png', 1, 1, 1, 1, 1, 1, 20, 25, 6),
(22, 'Pão de Mel Especial', 'Pão de mel personalizado', 'img/img-personalizado/pao_mel_especial.png', 1, 1, 1, 1, 1, 1, 20, 26, 1),
(23, 'Brownie Decorado', 'Brownie com decoração personalizada', 'img/img-personalizado/brownie_decorado.png', 1, 1, 1, 1, 1, 1, 20, 27, 1),
(24, 'Cookie Temático', 'Cookie com tema especial', 'img/img-personalizado/cookie_tematico.png', 1, 1, 1, 1, 1, 1, 20, 28, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_produto`
--

CREATE TABLE `tb_produto` (
  `id_produto` int(11) NOT NULL,
  `nome_produto` varchar(255) DEFAULT NULL,
  `desc_produto` text DEFAULT NULL,
  `valor_produto` decimal(10,2) DEFAULT NULL,
  `frete` decimal(10,2) DEFAULT NULL,
  `produto_ativo` tinyint(1) DEFAULT 1,
  `limite_entrega` int(11) DEFAULT NULL,
  `img_produto` varchar(255) DEFAULT NULL,
  `id_tipo_produto` int(11) DEFAULT NULL,
  `id_confeitaria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_produto`
--

INSERT INTO `tb_produto` (`id_produto`, `nome_produto`, `desc_produto`, `valor_produto`, `frete`, `produto_ativo`, `limite_entrega`, `img_produto`, `id_tipo_produto`, `id_confeitaria`) VALUES
(1, 'Bolo de Chocolate', 'bolo de chocolate com cobertura de brigadeiro', 75.00, 7.00, 1, 35, 'img/img-produto/Captura de tela 2025-06-01 190141.png', 2, 1),
(2, 'Cupcake de Baunilha', 'cupcake de baunilha', 25.00, 5.00, 1, 30, 'img/img-produto/Captura de tela 2025-06-01 190327.png', 4, 1),
(3, 'Torta de Bolacha', 'torta de chocolate com recheio de bolacha', 60.00, 5.00, 1, 15, 'img/img-produto/Captura de tela 2025-06-01 190450.png', 3, 1),
(4, 'Bolo de Morango', 'Delicioso bolo de morango', 80.00, 7.00, 1, 20, 'img/img-produto/bolo_morango.png', 5, 2),
(5, 'Torta de Limão', 'Torta com creme de limão', 60.00, 5.00, 1, 15, 'img/img-produto/torta_limao.png', 6, 2),
(6, 'Cupcake de Chocolate', 'Cupcake com cobertura de chocolate', 25.00, 5.00, 1, 10, 'img/img-produto/cupcake_chocolate.png', 7, 2),
(7, 'Bolo de Frutas', 'Delicioso bolo de frutas', 75.00, 5.00, 1, 20, 'img/img-produto/bolo_frutas.png', 8, 3),
(8, 'Torta de Maracujá', 'Torta com creme de maracujá', 75.00, 5.00, 1, 20, 'img/img-produto/torta_maracuja.png', 9, 3),
(9, 'Cupcake de Baunilha', 'Cupcake macio de baunilha', 75.00, 5.00, 1, 20, 'img/img-produto/cupcake_baunilha.png', 10, 3),
(10, 'Bolo de Frutas', 'Delicioso bolo de frutas', 75.00, 5.00, 1, 20, 'img/img-produto/bolo_frutas.png', 11, 4),
(11, 'Torta de Maracujá', 'Torta com creme de maracujá', 75.00, 5.00, 1, 20, 'img/img-produto/torta_maracuja.png', 12, 4),
(12, 'Cupcake de Baunilha', 'Cupcake macio de baunilha', 75.00, 5.00, 1, 20, 'img/img-produto/cupcake_baunilha.png', 13, 4),
(13, 'Bolo de Frutas', 'Delicioso bolo de frutas', 75.00, 5.00, 1, 20, 'img/img-produto/bolo_frutas.png', 14, 5),
(14, 'Torta de Maracujá', 'Torta com creme de maracujá', 75.00, 5.00, 1, 20, 'img/img-produto/torta_maracuja.png', 15, 5),
(15, 'Cupcake de Baunilha', 'Cupcake macio de baunilha', 75.00, 5.00, 1, 20, 'img/img-produto/cupcake_baunilha.png', 16, 5),
(16, 'Bolo de Frutas', 'Delicioso bolo de frutas', 75.00, 5.00, 1, 20, 'img/img-produto/bolo_frutas.png', 17, 7),
(17, 'Torta de Maracujá', 'Torta com creme de maracujá', 75.00, 5.00, 1, 20, 'img/img-produto/torta_maracuja.png', 18, 7),
(18, 'Cupcake de Baunilha', 'Cupcake macio de baunilha', 75.00, 5.00, 1, 20, 'img/img-produto/cupcake_baunilha.png', 19, 7),
(22, 'Pão de Mel Recheado', 'Pão de mel com recheio de doce de leite', 50.00, 5.00, 1, 20, 'img/img-produto/pao_mel.png', 23, 6),
(23, 'Brownie de Chocolate', 'Brownie macio de chocolate', 60.00, 5.00, 1, 20, 'img/img-produto/brownie.png', 24, 6),
(24, 'Cookie de Baunilha', 'Cookie crocante com gotas de chocolate', 40.00, 5.00, 1, 20, 'img/img-produto/cookie.png', 25, 6),
(25, 'Pão de Mel Recheado', 'Pão de mel com recheio de doce de leite', 50.00, 5.00, 1, 20, 'img/img-produto/pao_mel.png', 26, 1),
(26, 'Brownie de Chocolate', 'Brownie macio de chocolate', 60.00, 5.00, 1, 20, 'img/img-produto/brownie.png', 27, 1),
(27, 'Cookie de Baunilha', 'Cookie crocante com gotas de chocolate', 40.00, 5.00, 1, 20, 'img/img-produto/cookie.png', 28, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_recheio`
--

CREATE TABLE `tb_recheio` (
  `id_recheio` int(11) NOT NULL,
  `desc_recheio` varchar(255) DEFAULT NULL,
  `valor_por_peso` decimal(10,2) DEFAULT NULL,
  `id_confeitaria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_recheio`
--

INSERT INTO `tb_recheio` (`id_recheio`, `desc_recheio`, `valor_por_peso`, `id_confeitaria`) VALUES
(1, 'Recheio de Brigadeiro', 6.00, 2),
(2, 'Recheio de Morango', 5.50, 2),
(3, 'Recheio de Doce de Leite', 6.50, 2),
(4, 'Recheio de Brigadeiro', 6.00, 3),
(5, 'Recheio de Morango', 6.00, 3),
(6, 'Recheio de Doce de Leite', 6.00, 3),
(7, 'Recheio de Brigadeiro', 6.00, 4),
(8, 'Recheio de Morango', 6.00, 4),
(9, 'Recheio de Doce de Leite', 6.00, 4),
(10, 'Recheio de Brigadeiro', 6.00, 5),
(11, 'Recheio de Morango', 6.00, 5),
(12, 'Recheio de Doce de Leite', 6.00, 5),
(13, 'Recheio de Brigadeiro', 6.00, 7),
(14, 'Recheio de Morango', 6.00, 7),
(15, 'Recheio de Doce de Leite', 6.00, 7),
(16, 'Recheio de Brigadeiro', 6.00, 7),
(17, 'Recheio de Morango', 6.00, 7),
(18, 'Recheio de Doce de Leite', 6.00, 7),
(19, 'Recheio de Brigadeiro', 6.00, 6),
(20, 'Recheio de Doce de Leite', 6.00, 6),
(21, 'Recheio de Creme', 6.00, 6),
(22, 'Recheio de Brigadeiro', 6.00, 1),
(23, 'Recheio de Doce de Leite', 6.00, 1),
(24, 'Recheio de Creme', 6.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_suporte`
--

CREATE TABLE `tb_suporte` (
  `id_suporte` int(11) NOT NULL,
  `titulo_suporte` varchar(255) DEFAULT NULL,
  `desc_suporte` text DEFAULT NULL,
  `resolvido` tinyint(1) DEFAULT 0,
  `id_tipo_suporte` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_telefone`
--

CREATE TABLE `tb_telefone` (
  `id_telefone` int(11) UNSIGNED NOT NULL,
  `num_telefone` varchar(20) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_ddd` int(11) NOT NULL,
  `id_tipo_telefone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_telefone`
--

INSERT INTO `tb_telefone` (`id_telefone`, `num_telefone`, `id_usuario`, `id_ddd`, `id_tipo_telefone`) VALUES
(1, '99999-0001', 3, 11, 4),
(2, '98888-0002', 3, 12, 3),
(3, '99999-3001', 4, 13, 4),
(4, '98888-3002', 4, 14, 3),
(5, '99999-4001', 5, 14, 4),
(6, '98888-4002', 5, 15, 3),
(7, '99999-5001', 6, 15, 4),
(8, '98888-5002', 6, 16, 3),
(9, '99999-7001', 8, 17, 4),
(10, '98888-7002', 8, 18, 3),
(13, '99999-6001', 7, 16, 4),
(14, '98888-6002', 7, 17, 3),
(15, '99999-1001', 2, 11, 4),
(16, '98888-1002', 2, 12, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_tipo_produto`
--

CREATE TABLE `tb_tipo_produto` (
  `id_tipo_produto` int(11) NOT NULL,
  `desc_tipo_produto` varchar(255) DEFAULT NULL,
  `id_confeitaria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_tipo_produto`
--

INSERT INTO `tb_tipo_produto` (`id_tipo_produto`, `desc_tipo_produto`, `id_confeitaria`) VALUES
(2, 'Bolo', 1),
(3, 'Torta', 1),
(4, 'Cupcake', 1),
(5, 'Bolo', 2),
(6, 'Torta', 2),
(7, 'Cupcake', 2),
(8, 'Bolo', 3),
(9, 'Torta', 3),
(10, 'Cupcake', 3),
(11, 'Bolo', 4),
(12, 'Torta', 4),
(13, 'Cupcake', 4),
(14, 'Bolo', 5),
(15, 'Torta', 5),
(16, 'Cupcake', 5),
(17, 'Bolo', 7),
(18, 'Torta', 7),
(19, 'Cupcake', 7),
(20, 'Bolo', 7),
(21, 'Torta', 7),
(22, 'Cupcake', 7),
(23, 'Pão de Mel', 6),
(24, 'Brownie', 6),
(25, 'Cookie', 6),
(26, 'Pão de Mel', 1),
(27, 'Brownie', 1),
(28, 'Cookie', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_tipo_suporte`
--

CREATE TABLE `tb_tipo_suporte` (
  `id_tipo_suporte` int(11) NOT NULL,
  `tipo_suporte` varchar(255) DEFAULT NULL,
  `id_tipo_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_tipo_suporte`
--

INSERT INTO `tb_tipo_suporte` (`id_tipo_suporte`, `tipo_suporte`, `id_tipo_usuario`) VALUES
(1, 'Problemas com Vendas', 3),
(2, 'Problemas com Entrega', 3),
(3, 'Problemas com Pagamento', 3),
(4, 'Problemas com Cadastro', 3),
(5, 'Problemas com Dados', 3),
(6, 'Ofensas Pessoais', 3),
(7, 'Problemas com Produto', 3),
(8, 'Dados Imprecisos no Banco', 3),
(9, 'Problemas com Compras', 2),
(10, 'Problemas com Entrega', 2),
(11, 'Problemas com Pagamento', 2),
(12, 'Problemas com Cadastro', 2),
(13, 'Problemas com Dados', 2),
(14, 'Ofensas Pessoais', 2),
(15, 'Problemas com Produto', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_tipo_telefone`
--

CREATE TABLE `tb_tipo_telefone` (
  `id_tipo_telefone` int(11) NOT NULL,
  `tipo_telefone` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_tipo_telefone`
--

INSERT INTO `tb_tipo_telefone` (`id_tipo_telefone`, `tipo_telefone`) VALUES
(1, 'Residencial'),
(2, 'Comercial'),
(3, 'WhatsApp'),
(4, 'Celular');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_tipo_usuario`
--

CREATE TABLE `tb_tipo_usuario` (
  `id_tipo_usuario` int(11) NOT NULL,
  `tipo_usuario` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_tipo_usuario`
--

INSERT INTO `tb_tipo_usuario` (`id_tipo_usuario`, `tipo_usuario`) VALUES
(1, 'Administrador'),
(2, 'Cliente'),
(3, 'Confeitaria');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_usuario`
--

CREATE TABLE `tb_usuario` (
  `id_usuario` int(11) NOT NULL,
  `email_usuario` varchar(255) DEFAULT NULL,
  `email_verificado` tinyint(1) DEFAULT NULL,
  `conta_ativa` tinyint(1) DEFAULT 1,
  `senha_usuario` varchar(255) DEFAULT NULL,
  `online` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_tipo_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_usuario`
--

INSERT INTO `tb_usuario` (`id_usuario`, `email_usuario`, `email_verificado`, `conta_ativa`, `senha_usuario`, `online`, `data_criacao`, `id_tipo_usuario`) VALUES
(1, 'emulador.igor2@gmail.com', 1, 1, '$2y$10$eMl4KkChyoXHRl6Er2L3euenbCZU28qsOTtIUo8ujhZPjwZ5donoS', '2025-06-01 23:31:22', '2025-05-31 22:34:22', 2),
(2, 'igor.cardoso4@fatec.sp.gov.br', 1, 1, '$2y$10$obAsbd9vUA8VQJg3462TWuXeXG2Og8T9giscsk0NoaXnNdrZ/bYK6', '2025-06-01 22:13:33', '2025-05-31 22:43:34', 3),
(3, 'confeitaria.2@teste.com', 1, 1, '$2y$10$Ox0jBS5eGdGF89G7Yn2S9.2n5yKOZ2H.ch/CrTeDDwDUgOQ8rkv/C', '2025-06-01 21:09:21', '2025-06-01 21:09:02', 3),
(4, 'confeitaria.3@teste.com', 1, 1, '$2y$10$QEc40uYVsXVrBurfntb/vOEXKaKlQc.vscjS9VX7ECWFVQLBB5zPq', '2025-06-01 21:11:34', '2025-06-01 21:11:16', 3),
(5, 'confeitaria.4@teste.com', 1, 1, '$2y$10$U09A9LYmJN6HkaZjNBOezewmpgy2.uYbazNMTSSSqmr6nZiru6Iye', '2025-06-01 23:57:46', '2025-06-01 21:13:31', 3),
(6, 'confeitaria.5@teste.com', 1, 1, '$2y$10$fChyxOX/wV5Y8wsYRb/6PuZjwzBAf80zEFUacpHon5bY69KWSwtG6', '2025-06-01 21:16:26', '2025-06-01 21:16:12', 3),
(7, 'confeitaria.6@teste.com', 1, 1, '$2y$10$gu47muf31GmWoNDNPsTOr.COwUOXZ5FQPGW4ozW7ykA6YRmLoZfYi', '2025-06-01 21:18:49', '2025-06-01 21:18:32', 3),
(8, 'confeitaria.7@teste.com', 1, 1, '$2y$10$iCXRIo8cjd5RviG8ZSMHzed2BULerCwPi.5aoAGdIWyug1//sQAF6', '2025-06-01 21:25:14', '2025-06-01 21:25:00', 3),
(9, 'confeitaria.8@teste.com', 1, 1, '$2y$10$/MCxfqPTEyFKGh0U5URP6.kdCFL9iyPDCEoCz1dyIShyth4tetK1e', '2025-06-01 21:27:09', '2025-06-01 21:26:54', 3);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tb_adm`
--
ALTER TABLE `tb_adm`
  ADD PRIMARY KEY (`id_adm`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `tb_cliente`
--
ALTER TABLE `tb_cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `tb_cobertura`
--
ALTER TABLE `tb_cobertura`
  ADD PRIMARY KEY (`id_cobertura`),
  ADD KEY `id_confeitaria` (`id_confeitaria`);

--
-- Índices de tabela `tb_codigo`
--
ALTER TABLE `tb_codigo`
  ADD PRIMARY KEY (`id_codigo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `tb_confeitaria`
--
ALTER TABLE `tb_confeitaria`
  ADD PRIMARY KEY (`id_confeitaria`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `tb_conversa`
--
ALTER TABLE `tb_conversa`
  ADD PRIMARY KEY (`id_conversa`),
  ADD KEY `id_remetente` (`id_remetente`),
  ADD KEY `id_destinatario` (`id_destinatario`);

--
-- Índices de tabela `tb_conversa_mensagem`
--
ALTER TABLE `tb_conversa_mensagem`
  ADD PRIMARY KEY (`id_conversa_mensagem`),
  ADD KEY `id_mensagem` (`id_mensagem`),
  ADD KEY `id_conversa` (`id_conversa`);

--
-- Índices de tabela `tb_cupom`
--
ALTER TABLE `tb_cupom`
  ADD PRIMARY KEY (`id_cupom`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `tb_ddd`
--
ALTER TABLE `tb_ddd`
  ADD PRIMARY KEY (`id_ddd`);

--
-- Índices de tabela `tb_decoracao`
--
ALTER TABLE `tb_decoracao`
  ADD PRIMARY KEY (`id_decoracao`),
  ADD KEY `id_confeitaria` (`id_confeitaria`);

--
-- Índices de tabela `tb_endereco_cliente`
--
ALTER TABLE `tb_endereco_cliente`
  ADD PRIMARY KEY (`id_endereco_cliente`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `tb_formato`
--
ALTER TABLE `tb_formato`
  ADD PRIMARY KEY (`id_formato`),
  ADD KEY `id_confeitaria` (`id_confeitaria`);

--
-- Índices de tabela `tb_forma_pagamento`
--
ALTER TABLE `tb_forma_pagamento`
  ADD PRIMARY KEY (`id_forma_pagamento`);

--
-- Índices de tabela `tb_itens_pedido`
--
ALTER TABLE `tb_itens_pedido`
  ADD PRIMARY KEY (`id_itens_pedido`),
  ADD KEY `id_produto` (`id_produto`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_cupom` (`id_cupom`);

--
-- Índices de tabela `tb_massa`
--
ALTER TABLE `tb_massa`
  ADD PRIMARY KEY (`id_massa`),
  ADD KEY `id_confeitaria` (`id_confeitaria`);

--
-- Índices de tabela `tb_mensagem`
--
ALTER TABLE `tb_mensagem`
  ADD PRIMARY KEY (`id_mensagem`),
  ADD KEY `id_remetente` (`id_remetente`),
  ADD KEY `id_destinatario` (`id_destinatario`);

--
-- Índices de tabela `tb_pedido`
--
ALTER TABLE `tb_pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_endereco_cliente` (`id_endereco_cliente`),
  ADD KEY `id_forma_pagamento` (`id_forma_pagamento`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `tb_pedido_personalizado`
--
ALTER TABLE `tb_pedido_personalizado`
  ADD PRIMARY KEY (`id_pedido_personalizado`),
  ADD KEY `id_endereco_cliente` (`id_endereco_cliente`),
  ADD KEY `id_forma_pagamento` (`id_forma_pagamento`),
  ADD KEY `id_cobertura` (`id_cobertura`),
  ADD KEY `id_decoracao` (`id_decoracao`),
  ADD KEY `id_formato` (`id_formato`),
  ADD KEY `id_massa` (`id_massa`),
  ADD KEY `id_recheio` (`id_recheio`),
  ADD KEY `id_personalizado` (`id_personalizado`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `tb_personalizado`
--
ALTER TABLE `tb_personalizado`
  ADD PRIMARY KEY (`id_personalizado`),
  ADD KEY `id_tipo_produto` (`id_tipo_produto`),
  ADD KEY `id_confeitaria` (`id_confeitaria`);

--
-- Índices de tabela `tb_produto`
--
ALTER TABLE `tb_produto`
  ADD PRIMARY KEY (`id_produto`),
  ADD KEY `id_tipo_produto` (`id_tipo_produto`),
  ADD KEY `id_confeitaria` (`id_confeitaria`);

--
-- Índices de tabela `tb_recheio`
--
ALTER TABLE `tb_recheio`
  ADD PRIMARY KEY (`id_recheio`),
  ADD KEY `id_confeitaria` (`id_confeitaria`);

--
-- Índices de tabela `tb_suporte`
--
ALTER TABLE `tb_suporte`
  ADD PRIMARY KEY (`id_suporte`),
  ADD KEY `id_tipo_suporte` (`id_tipo_suporte`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `tb_telefone`
--
ALTER TABLE `tb_telefone`
  ADD PRIMARY KEY (`id_telefone`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_ddd` (`id_ddd`),
  ADD KEY `id_tipo_telefone` (`id_tipo_telefone`);

--
-- Índices de tabela `tb_tipo_produto`
--
ALTER TABLE `tb_tipo_produto`
  ADD PRIMARY KEY (`id_tipo_produto`),
  ADD KEY `id_confeitaria` (`id_confeitaria`);

--
-- Índices de tabela `tb_tipo_suporte`
--
ALTER TABLE `tb_tipo_suporte`
  ADD PRIMARY KEY (`id_tipo_suporte`),
  ADD KEY `id_tipo_usuario` (`id_tipo_usuario`);

--
-- Índices de tabela `tb_tipo_telefone`
--
ALTER TABLE `tb_tipo_telefone`
  ADD PRIMARY KEY (`id_tipo_telefone`);

--
-- Índices de tabela `tb_tipo_usuario`
--
ALTER TABLE `tb_tipo_usuario`
  ADD PRIMARY KEY (`id_tipo_usuario`);

--
-- Índices de tabela `tb_usuario`
--
ALTER TABLE `tb_usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_tipo_usuario` (`id_tipo_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tb_adm`
--
ALTER TABLE `tb_adm`
  MODIFY `id_adm` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_cliente`
--
ALTER TABLE `tb_cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_cobertura`
--
ALTER TABLE `tb_cobertura`
  MODIFY `id_cobertura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `tb_codigo`
--
ALTER TABLE `tb_codigo`
  MODIFY `id_codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `tb_confeitaria`
--
ALTER TABLE `tb_confeitaria`
  MODIFY `id_confeitaria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `tb_conversa`
--
ALTER TABLE `tb_conversa`
  MODIFY `id_conversa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_conversa_mensagem`
--
ALTER TABLE `tb_conversa_mensagem`
  MODIFY `id_conversa_mensagem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_cupom`
--
ALTER TABLE `tb_cupom`
  MODIFY `id_cupom` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tb_ddd`
--
ALTER TABLE `tb_ddd`
  MODIFY `id_ddd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de tabela `tb_decoracao`
--
ALTER TABLE `tb_decoracao`
  MODIFY `id_decoracao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `tb_endereco_cliente`
--
ALTER TABLE `tb_endereco_cliente`
  MODIFY `id_endereco_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_formato`
--
ALTER TABLE `tb_formato`
  MODIFY `id_formato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `tb_forma_pagamento`
--
ALTER TABLE `tb_forma_pagamento`
  MODIFY `id_forma_pagamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tb_itens_pedido`
--
ALTER TABLE `tb_itens_pedido`
  MODIFY `id_itens_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tb_massa`
--
ALTER TABLE `tb_massa`
  MODIFY `id_massa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `tb_mensagem`
--
ALTER TABLE `tb_mensagem`
  MODIFY `id_mensagem` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_pedido`
--
ALTER TABLE `tb_pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_pedido_personalizado`
--
ALTER TABLE `tb_pedido_personalizado`
  MODIFY `id_pedido_personalizado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_personalizado`
--
ALTER TABLE `tb_personalizado`
  MODIFY `id_personalizado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `tb_produto`
--
ALTER TABLE `tb_produto`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `tb_recheio`
--
ALTER TABLE `tb_recheio`
  MODIFY `id_recheio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `tb_suporte`
--
ALTER TABLE `tb_suporte`
  MODIFY `id_suporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_telefone`
--
ALTER TABLE `tb_telefone`
  MODIFY `id_telefone` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `tb_tipo_produto`
--
ALTER TABLE `tb_tipo_produto`
  MODIFY `id_tipo_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `tb_tipo_suporte`
--
ALTER TABLE `tb_tipo_suporte`
  MODIFY `id_tipo_suporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `tb_tipo_telefone`
--
ALTER TABLE `tb_tipo_telefone`
  MODIFY `id_tipo_telefone` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tb_tipo_usuario`
--
ALTER TABLE `tb_tipo_usuario`
  MODIFY `id_tipo_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tb_usuario`
--
ALTER TABLE `tb_usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tb_adm`
--
ALTER TABLE `tb_adm`
  ADD CONSTRAINT `tb_adm_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Restrições para tabelas `tb_cliente`
--
ALTER TABLE `tb_cliente`
  ADD CONSTRAINT `tb_cliente_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Restrições para tabelas `tb_cobertura`
--
ALTER TABLE `tb_cobertura`
  ADD CONSTRAINT `tb_cobertura_ibfk_1` FOREIGN KEY (`id_confeitaria`) REFERENCES `tb_confeitaria` (`id_confeitaria`);

--
-- Restrições para tabelas `tb_codigo`
--
ALTER TABLE `tb_codigo`
  ADD CONSTRAINT `tb_codigo_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Restrições para tabelas `tb_confeitaria`
--
ALTER TABLE `tb_confeitaria`
  ADD CONSTRAINT `tb_confeitaria_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Restrições para tabelas `tb_conversa`
--
ALTER TABLE `tb_conversa`
  ADD CONSTRAINT `tb_conversa_ibfk_1` FOREIGN KEY (`id_remetente`) REFERENCES `tb_usuario` (`id_usuario`),
  ADD CONSTRAINT `tb_conversa_ibfk_2` FOREIGN KEY (`id_destinatario`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Restrições para tabelas `tb_conversa_mensagem`
--
ALTER TABLE `tb_conversa_mensagem`
  ADD CONSTRAINT `tb_conversa_mensagem_ibfk_1` FOREIGN KEY (`id_mensagem`) REFERENCES `tb_mensagem` (`id_mensagem`),
  ADD CONSTRAINT `tb_conversa_mensagem_ibfk_2` FOREIGN KEY (`id_conversa`) REFERENCES `tb_conversa` (`id_conversa`);

--
-- Restrições para tabelas `tb_cupom`
--
ALTER TABLE `tb_cupom`
  ADD CONSTRAINT `tb_cupom_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Restrições para tabelas `tb_decoracao`
--
ALTER TABLE `tb_decoracao`
  ADD CONSTRAINT `tb_decoracao_ibfk_1` FOREIGN KEY (`id_confeitaria`) REFERENCES `tb_confeitaria` (`id_confeitaria`);

--
-- Restrições para tabelas `tb_endereco_cliente`
--
ALTER TABLE `tb_endereco_cliente`
  ADD CONSTRAINT `tb_endereco_cliente_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_cliente` (`id_cliente`);

--
-- Restrições para tabelas `tb_formato`
--
ALTER TABLE `tb_formato`
  ADD CONSTRAINT `tb_formato_ibfk_1` FOREIGN KEY (`id_confeitaria`) REFERENCES `tb_confeitaria` (`id_confeitaria`);

--
-- Restrições para tabelas `tb_itens_pedido`
--
ALTER TABLE `tb_itens_pedido`
  ADD CONSTRAINT `tb_itens_pedido_ibfk_1` FOREIGN KEY (`id_produto`) REFERENCES `tb_produto` (`id_produto`),
  ADD CONSTRAINT `tb_itens_pedido_ibfk_2` FOREIGN KEY (`id_pedido`) REFERENCES `tb_pedido` (`id_pedido`),
  ADD CONSTRAINT `tb_itens_pedido_ibfk_3` FOREIGN KEY (`id_cupom`) REFERENCES `tb_cupom` (`id_cupom`);

--
-- Restrições para tabelas `tb_massa`
--
ALTER TABLE `tb_massa`
  ADD CONSTRAINT `tb_massa_ibfk_1` FOREIGN KEY (`id_confeitaria`) REFERENCES `tb_confeitaria` (`id_confeitaria`);

--
-- Restrições para tabelas `tb_mensagem`
--
ALTER TABLE `tb_mensagem`
  ADD CONSTRAINT `tb_mensagem_ibfk_1` FOREIGN KEY (`id_remetente`) REFERENCES `tb_usuario` (`id_usuario`),
  ADD CONSTRAINT `tb_mensagem_ibfk_2` FOREIGN KEY (`id_destinatario`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Restrições para tabelas `tb_pedido`
--
ALTER TABLE `tb_pedido`
  ADD CONSTRAINT `tb_pedido_ibfk_1` FOREIGN KEY (`id_endereco_cliente`) REFERENCES `tb_endereco_cliente` (`id_endereco_cliente`),
  ADD CONSTRAINT `tb_pedido_ibfk_2` FOREIGN KEY (`id_forma_pagamento`) REFERENCES `tb_forma_pagamento` (`id_forma_pagamento`),
  ADD CONSTRAINT `tb_pedido_ibfk_3` FOREIGN KEY (`id_cliente`) REFERENCES `tb_cliente` (`id_cliente`);

--
-- Restrições para tabelas `tb_pedido_personalizado`
--
ALTER TABLE `tb_pedido_personalizado`
  ADD CONSTRAINT `tb_pedido_personalizado_ibfk_1` FOREIGN KEY (`id_endereco_cliente`) REFERENCES `tb_endereco_cliente` (`id_endereco_cliente`),
  ADD CONSTRAINT `tb_pedido_personalizado_ibfk_2` FOREIGN KEY (`id_forma_pagamento`) REFERENCES `tb_forma_pagamento` (`id_forma_pagamento`),
  ADD CONSTRAINT `tb_pedido_personalizado_ibfk_3` FOREIGN KEY (`id_cobertura`) REFERENCES `tb_cobertura` (`id_cobertura`),
  ADD CONSTRAINT `tb_pedido_personalizado_ibfk_4` FOREIGN KEY (`id_decoracao`) REFERENCES `tb_decoracao` (`id_decoracao`),
  ADD CONSTRAINT `tb_pedido_personalizado_ibfk_5` FOREIGN KEY (`id_formato`) REFERENCES `tb_formato` (`id_formato`),
  ADD CONSTRAINT `tb_pedido_personalizado_ibfk_6` FOREIGN KEY (`id_massa`) REFERENCES `tb_massa` (`id_massa`),
  ADD CONSTRAINT `tb_pedido_personalizado_ibfk_7` FOREIGN KEY (`id_recheio`) REFERENCES `tb_recheio` (`id_recheio`),
  ADD CONSTRAINT `tb_pedido_personalizado_ibfk_8` FOREIGN KEY (`id_personalizado`) REFERENCES `tb_personalizado` (`id_personalizado`),
  ADD CONSTRAINT `tb_pedido_personalizado_ibfk_9` FOREIGN KEY (`id_cliente`) REFERENCES `tb_cliente` (`id_cliente`);

--
-- Restrições para tabelas `tb_personalizado`
--
ALTER TABLE `tb_personalizado`
  ADD CONSTRAINT `tb_personalizado_ibfk_1` FOREIGN KEY (`id_tipo_produto`) REFERENCES `tb_tipo_produto` (`id_tipo_produto`),
  ADD CONSTRAINT `tb_personalizado_ibfk_2` FOREIGN KEY (`id_confeitaria`) REFERENCES `tb_confeitaria` (`id_confeitaria`);

--
-- Restrições para tabelas `tb_produto`
--
ALTER TABLE `tb_produto`
  ADD CONSTRAINT `tb_produto_ibfk_1` FOREIGN KEY (`id_tipo_produto`) REFERENCES `tb_tipo_produto` (`id_tipo_produto`),
  ADD CONSTRAINT `tb_produto_ibfk_2` FOREIGN KEY (`id_confeitaria`) REFERENCES `tb_confeitaria` (`id_confeitaria`);

--
-- Restrições para tabelas `tb_recheio`
--
ALTER TABLE `tb_recheio`
  ADD CONSTRAINT `tb_recheio_ibfk_1` FOREIGN KEY (`id_confeitaria`) REFERENCES `tb_confeitaria` (`id_confeitaria`);

--
-- Restrições para tabelas `tb_suporte`
--
ALTER TABLE `tb_suporte`
  ADD CONSTRAINT `tb_suporte_ibfk_1` FOREIGN KEY (`id_tipo_suporte`) REFERENCES `tb_tipo_suporte` (`id_tipo_suporte`),
  ADD CONSTRAINT `tb_suporte_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuario` (`id_usuario`);

--
-- Restrições para tabelas `tb_telefone`
--
ALTER TABLE `tb_telefone`
  ADD CONSTRAINT `tb_telefone_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuario` (`id_usuario`),
  ADD CONSTRAINT `tb_telefone_ibfk_2` FOREIGN KEY (`id_ddd`) REFERENCES `tb_ddd` (`id_ddd`),
  ADD CONSTRAINT `tb_telefone_ibfk_3` FOREIGN KEY (`id_tipo_telefone`) REFERENCES `tb_tipo_telefone` (`id_tipo_telefone`);

--
-- Restrições para tabelas `tb_tipo_produto`
--
ALTER TABLE `tb_tipo_produto`
  ADD CONSTRAINT `tb_tipo_produto_ibfk_1` FOREIGN KEY (`id_confeitaria`) REFERENCES `tb_confeitaria` (`id_confeitaria`);

--
-- Restrições para tabelas `tb_tipo_suporte`
--
ALTER TABLE `tb_tipo_suporte`
  ADD CONSTRAINT `tb_tipo_suporte_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tb_tipo_usuario` (`id_tipo_usuario`);

--
-- Restrições para tabelas `tb_usuario`
--
ALTER TABLE `tb_usuario`
  ADD CONSTRAINT `tb_usuario_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tb_tipo_usuario` (`id_tipo_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
