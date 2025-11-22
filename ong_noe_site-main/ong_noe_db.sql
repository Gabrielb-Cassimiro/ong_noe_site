-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22-Nov-2025 às 22:40
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
-- Banco de dados: `ong_noe_db`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `dados_pessoais`
--

CREATE TABLE `dados_pessoais` (
  `id_dados` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `nome_materno` varchar(80) NOT NULL,
  `data_nascimento` date NOT NULL,
  `sexo` enum('M','F','O') DEFAULT NULL,
  `telefone_celular` varchar(15) DEFAULT NULL,
  `telefone_fixo` varchar(14) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `dados_pessoais`
--

INSERT INTO `dados_pessoais` (`id_dados`, `id_usuario`, `cpf`, `nome_materno`, `data_nascimento`, `sexo`, `telefone_celular`, `telefone_fixo`) VALUES
(1, 2, '18545430752', 'MARIA APARECIDA DOS SANTOS CONCENTINO', '1997-02-12', 'M', '21980455978', '21980465978'),
(2, 3, '12133321242', 'bolsonara', '1957-02-12', 'M', '21940028922', '2140028922');

-- --------------------------------------------------------

--
-- Estrutura da tabela `enderecos`
--

CREATE TABLE `enderecos` (
  `id_endereco` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `enderecos`
--

INSERT INTO `enderecos` (`id_endereco`, `id_usuario`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `estado`) VALUES
(1, 2, '23085110', 'cg', '931', 'FUNDOS', 'CAMPO GRANDE', 'RIO DE JANEIRO', 'RJ'),
(2, 3, '23085110', 'Avenida Cesário de Melo', '931', 'FUNDOS', 'Senador Vasconcelos', 'Rio de Janeiro', 'RJ');

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs_autenticacao`
--

CREATE TABLE `logs_autenticacao` (
  `id_log` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_login` enum('sucesso','falha') NOT NULL,
  `tipo_2fa` varchar(50) DEFAULT NULL,
  `ip_origem` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `logs_autenticacao`
--

INSERT INTO `logs_autenticacao` (`id_log`, `id_usuario`, `data_hora`, `status_login`, `tipo_2fa`, `ip_origem`) VALUES
(1, 2, '2025-11-22 17:48:41', 'sucesso', 'nome_materno', '::1'),
(2, 2, '2025-11-22 17:49:15', 'sucesso', 'nome_materno', '::1'),
(3, 2, '2025-11-22 17:51:34', 'sucesso', 'nome_materno', '::1'),
(4, 2, '2025-11-22 18:06:13', 'sucesso', 'nome_materno', '::1'),
(5, 2, '2025-11-22 18:06:26', 'sucesso', 'nome_materno', '::1'),
(6, 2, '2025-11-22 18:08:12', 'sucesso', 'nome_materno', '::1'),
(7, 2, '2025-11-22 18:31:33', 'sucesso', 'nome_materno', '::1'),
(8, 3, '2025-11-22 19:24:30', 'sucesso', 'nome_materno', '::1'),
(9, 2, '2025-11-22 19:25:21', 'falha', 'data_nascimento', '::1'),
(10, 2, '2025-11-22 19:25:38', 'sucesso', 'data_nascimento', '::1'),
(11, 2, '2025-11-22 21:00:56', 'sucesso', 'cep', '::1'),
(12, 2, '2025-11-22 21:06:31', 'sucesso', 'data_nascimento', '::1'),
(13, 2, '2025-11-22 21:06:53', 'sucesso', 'cep', '::1');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagem_url` varchar(255) DEFAULT NULL,
  `estoque` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `nome`, `descricao`, `preco`, `imagem_url`, `estoque`) VALUES
(1, 'Ração Premium 15kg', 'Nutrição completa para cães adultos.', 149.90, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQdHk-B2GKwAi5cyaebVJ344fn4RXaGebdVdQ&s', 20),
(2, 'Coleira Peitoral (todas as cores)', 'Conforto e segurança para o passeio.', 35.00, 'https://down-br.img.susercontent.com/file/6b11aa711cff910a68d1451bdc882320', 50),
(3, 'Brinquedo Mordedor (Grita cócó)', 'Resistente e divertido.', 19.90, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS9lDJJhx2rGHw3KA6Eosf1T9yhC_c3T3hCag&s', 30),
(4, 'Erva de Gato (Bob Marley)', 'Prensadona do Jaca (cheirou,pancou,brigou na laje...)', 19.99, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSEMVzdfgaqPQIUq10L8iw0jFAmKfSpVr6zWQ&s', 100);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `email` varchar(100) NOT NULL,
  `login` varchar(6) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo_usuario` enum('master','comum') NOT NULL DEFAULT 'comum',
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `email`, `login`, `senha`, `tipo_usuario`, `status`, `data_cadastro`) VALUES
(1, 'Administrador Master', 'admin@ongdenoe.com', 'master', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'master', 'ativo', '2025-11-22 17:36:08'),
(2, 'LUCAS CONCENTINO', 'lucasconcentino17@gmail.com', 'ratone', '$2y$10$e.T4aKkyREZPyeL0EZt6v.HHtqw7Ul0ree/aSVCrOEhlCvhqz3rX6', 'master', 'ativo', '2025-11-22 17:47:47'),
(3, 'Jair Messias Bolsonaro', 'topreso@gmail.com', 'bonoro', '$2y$10$IOG50wxhCE1KW.P.FeZZt.8560fHVJvbziLpxn9aK1Lz7/ZV6O67m', 'comum', 'ativo', '2025-11-22 19:23:44');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `dados_pessoais`
--
ALTER TABLE `dados_pessoais`
  ADD PRIMARY KEY (`id_dados`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices para tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`id_endereco`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices para tabela `logs_autenticacao`
--
ALTER TABLE `logs_autenticacao`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id_produto`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `dados_pessoais`
--
ALTER TABLE `dados_pessoais`
  MODIFY `id_dados` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
  MODIFY `id_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `logs_autenticacao`
--
ALTER TABLE `logs_autenticacao`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id_produto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `dados_pessoais`
--
ALTER TABLE `dados_pessoais`
  ADD CONSTRAINT `dados_pessoais_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `logs_autenticacao`
--
ALTER TABLE `logs_autenticacao`
  ADD CONSTRAINT `logs_autenticacao_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
