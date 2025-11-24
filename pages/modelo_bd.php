<?php
require_once '../config/config.php';

// Segurança
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
if (!isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) { header('Location: 2fa.php'); exit(); }

$user_type = $_SESSION['user_type'] ?? 'comum';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Modelo BD - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
</head>
<body>

<header>
    <div class="header-content">
        <a href="principal.php" class="logo">ONG de Noé</a>
        <nav>
            <a href="principal.php">Home</a>

            <?php if ($user_type === 'master'): ?>
                <a href="consulta_usuarios.php">Usuários</a>
                <a href="crud_produtos.php">Loja</a>
                <a href="gerenciar_pedidos.php">Pedidos</a>
                <a href="logs.php">Logs</a>
            <?php endif; ?>

            <?php if ($user_type === 'comum'): ?>
                <a href="meus_pedidos.php">Meus Pedidos</a>
                <a href="alterar_senha.php">Senha</a>
            <?php endif; ?>

            <a href="modelo_bd.php" style="background: var(--cor-secundaria); color: var(--cor-primaria);">Modelo BD</a>
            <a href="logout.php" style="background: var(--cor-erro);">Sair</a>
        </nav>
    </div>
</header>

<div class="container">
    <h2 style="color: var(--cor-primaria); text-align: center; margin-bottom: 10px;">
        Diagrama Entidade-Relacionamento (DER)
    </h2>
    <p style="text-align: center; color: #666; margin-bottom: 30px;">
        Representação visual da estrutura do Banco de Dados <code>ong_noe_db</code>.
    </p>

    <div class="card" style="overflow-x: auto; text-align: center; background: #fff;">
        <svg width="1100" height="750" viewBox="0 0 1100 750" xmlns="http://www.w3.org/2000/svg" style="font-family: 'Nunito', sans-serif;">

            <!-- Marcadores -->
            <defs>
                <marker id="arrow" markerWidth="10" markerHeight="10" refX="9" refY="3" orient="auto">
                    <path d="M0,0 L0,6 L9,3 z" fill="#8B4513" />
                </marker>

                <marker id="crowfoot" markerWidth="12" markerHeight="12" refX="0" refY="6" orient="auto">
                    <path d="M0,6 L12,6 M12,0 L12,12 M6,6 L12,0 M6,6 L12,12" stroke="#8B4513" stroke-width="1" fill="none"/>
                </marker>
            </defs>

            <!-- USUARIOS -->
            <g transform="translate(450, 40)">
                <rect width="200" height="220" rx="10" ry="10" fill="#F5DEB3" stroke="#8B4513" stroke-width="3"/>
                <rect width="200" height="40" rx="10" ry="10" fill="#8B4513"/>
                <text x="100" y="28" text-anchor="middle" fill="white" font-weight="bold" font-size="16">USUARIOS</text>

                <text x="10" y="65">🔑 id_usuario (PK)</text>
                <text x="10" y="90">• nome</text>
                <text x="10" y="115">• email</text>
                <text x="10" y="140">• login</text>
                <text x="10" y="165">• senha</text>
                <text x="10" y="190">• tipo_usuario</text>
            </g>

            <!-- PRODUTOS -->
            <g transform="translate(800, 80)">
                <rect width="200" height="160" rx="10" ry="10" fill="#E8F5E9" stroke="#2E7D32" stroke-width="2"/>
                <rect width="200" height="35" rx="10" ry="10" fill="#2E7D32"/>
                <text x="100" y="24" text-anchor="middle" fill="white" font-size="14" font-weight="bold">PRODUTOS</text>

                <text x="10" y="60">🔑 id_produto (PK)</text>
                <text x="10" y="85">• nome</text>
                <text x="10" y="110">• preco</text>
                <text x="10" y="135">• estoque</text>
            </g>

            <!-- PEDIDOS -->
            <g transform="translate(100, 340)">
                <rect width="220" height="180" rx="10" ry="10" fill="#FFF3E0" stroke="#FF9800" stroke-width="2"/>
                <rect width="220" height="35" rx="10" ry="10" fill="#FF9800"/>
                <text x="110" y="24" text-anchor="middle" fill="#3E2723" font-size="14" font-weight="bold">PEDIDOS</text>

                <text x="10" y="60">🔑 id_pedido (PK)</text>
                <text x="10" y="85">🔗 id_usuario (FK)</text>
                <text x="10" y="110">• data</text>
                <text x="10" y="135">• total</text>
                <text x="10" y="160">• status</text>
            </g>

            <!-- PEDIDO_ITENS -->
            <g transform="translate(430, 360)">
                <rect width="240" height="170" rx="10" ry="10" fill="#E1F5FE" stroke="#0288D1" stroke-width="2"/>
                <rect width="240" height="35" rx="10" ry="10" fill="#0288D1"/>
                <text x="120" y="24" text-anchor="middle" fill="white" font-size="14" font-weight="bold">PEDIDO_ITENS</text>

                <text x="10" y="60">🔑 id_item (PK)</text>
                <text x="10" y="85">🔗 id_pedido (FK)</text>
                <text x="10" y="110">🔗 id_produto (FK)</text>
                <text x="10" y="135">• quantidade</text>
                <text x="10" y="160">• preco_unitario</text>
            </g>

            <!-- RELACIONAMENTOS -->

            <!-- USUARIOS → PEDIDOS (1:N) -->
            <path d="M 450 160 L 220 340" stroke="#8B4513" stroke-width="2" marker-end="url(#arrow)"/>
            <text x="300" y="260" fill="#8B4513" font-size="12" font-weight="bold">1:N</text>

            <!-- PEDIDOS → PEDIDO_ITENS (1:N) -->
            <path d="M 320 430 L 430 430" stroke="#8B4513" stroke-width="2" marker-end="url(#arrow)"/>
            <text x="360" y="415" fill="#8B4513" font-size="12" font-weight="bold">1:N</text>

            <!-- PRODUTOS → PEDIDO_ITENS (1:N) -->
            <path d="M 800 160 L 670 400" stroke="#8B4513" stroke-width="2" marker-end="url(#arrow)"/>
            <text x="720" y="300" fill="#8B4513" font-size="12" font-weight="bold">1:N</text>

        </svg>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>Legenda:</h3>
        <ul style="list-style: none; padding-left: 0;">
            <li>🔑 <b>PK:</b> Chave primária</li>
            <li>🔗 <b>FK:</b> Chave estrangeira</li>
            <li>1:1 Relação um para um</li>
            <li>1:N Relação um para muitos</li>
        </ul>
    </div>
</div>

</body>
</html>

<!-- Código SQL usado na criação das tabelas:
 CREATE DATABASE IF NOT EXISTS ong_noe_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ong_noe_db;

-- Tabela de Usu�rios (Requisito PDF: Login e Senha, Perfis Master/Comum)
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    login VARCHAR(6) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('master', 'comum') NOT NULL DEFAULT 'comum',
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Dados Pessoais (Requisito PDF: Nome Materno e Data Nasc para 2FA)
CREATE TABLE dados_pessoais (
    id_dados INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    nome_materno VARCHAR(80) NOT NULL,
    data_nascimento DATE NOT NULL,
    sexo ENUM('M', 'F', 'O'),
    telefone_celular VARCHAR(15),
    telefone_fixo VARCHAR(14),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabela de Endere�os (Requisito PDF: CEP para 2FA)
CREATE TABLE enderecos (
    id_endereco INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    cep VARCHAR(9) NOT NULL,
    logradouro VARCHAR(100),
    numero VARCHAR(10),
    complemento VARCHAR(50),
    bairro VARCHAR(50),
    cidade VARCHAR(50),
    estado VARCHAR(2),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabela de Logs (Requisito PDF: Registro de Autentica��o)
CREATE TABLE logs_autenticacao (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_login ENUM('sucesso', 'falha') NOT NULL,
    tipo_2fa VARCHAR(50),
    ip_origem VARCHAR(45),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabela de Produtos (Para o Carrossel e Loja - Seu Requisito Extra)
CREATE TABLE produtos (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    imagem_url VARCHAR(255),
    estoque INT DEFAULT 0
);

--Tabela de pedidos
CREATE TABLE pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'PENDENTE',  -- PENDENTE, ENVIADO, ENTREGUE, CANCELADO
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

--Tabela de itens que compõem cada pedido
CREATE TABLE itens_pedido (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unit DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_produto) REFERENCES produtos(id_produto)
);

-- Inser��o do Usu�rio Master Padr�o (Requisito PDF) 
-- Senha padr�o: admin123 (Hash gerado via password_hash)
INSERT INTO usuarios (nome, email, login, senha, tipo_usuario) VALUES 
('Administrador Master', 'admin@ongdenoe.com', 'master', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'master');

-- Inser��o de Produtos Iniciais para o Carrossel
INSERT INTO produtos (nome, descricao, preco, imagem_url, estoque) VALUES
('Ra��o Premium 15kg', 'Nutri��o completa para c�es adultos.', 149.90, 'https://via.placeholder.com/300x200?text=Racao+Premium', 20),
('Coleira Ajust�vel', 'Conforto e seguran�a para o passeio.', 35.00, 'https://via.placeholder.com/300x200?text=Coleira', 50),
('Brinquedo Mordedor', 'Resistente e divertido.', 19.90, 'https://via.placeholder.com/300x200?text=Brinquedo', 30); -->