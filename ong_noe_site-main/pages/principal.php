<?php
require_once '../config/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }

// Busca produtos para o carrossel
$produtos = $pdo->query("SELECT * FROM produtos ORDER BY id_produto DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Principal - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="#" class="logo">ONG de Noé</a>
            <nav>
                <a href="principal.php">Home</a>
                <?php if ($_SESSION['user_type'] === 'master'): ?>
                    <a href="consulta_usuarios.php">Gerenciar Usuários</a>
                    <a href="crud_produtos.php">Gerenciar Loja</a>
                    <a href="logs.php">Ver Logs</a>
                <?php endif; ?>
                <?php if ($_SESSION['user_type'] === 'comum'): ?>
                    <a href="alterar_senha.php">Alterar Senha</a>
                <?php endif; ?>
                <a href="modelo_bd.php">Modelo BD</a>
                <button onclick="toggleContrast()" class="btn btn-secondary" style="padding: 2px 10px; margin-left: 10px;">Acessibilidade</button>
                <a href="logout.php" style="background: var(--cor-erro);">Sair</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2 style="color: var(--cor-primaria);">Bem-vindo, <?= htmlspecialchars($_SESSION['user_nome']) ?>!</h2>
        
        <h3 style="margin-top: 30px;">Destaques da Nossa Loja</h3>
        <div class="carousel">
            <div class="carousel-inner">
                <?php foreach ($produtos as $prod): ?>
                <div class="carousel-item">
                    <img src="<?= htmlspecialchars($prod['imagem_url']) ?>" alt="<?= htmlspecialchars($prod['nome']) ?>">
                    <h4><?= htmlspecialchars($prod['nome']) ?></h4>
                    <p><?= htmlspecialchars($prod['descricao']) ?></p>
                    <p style="font-weight: bold; color: var(--cor-sucesso);">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control prev">&#10094;</button>
            <button class="carousel-control next">&#10095;</button>
        </div>
    </div>
</body>
</html>