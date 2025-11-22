<?php
require_once '../config/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'master') { die('Acesso Negado'); }

// DELETE
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id_produto = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: crud_produtos.php');
    exit();
}

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, descricao, preco, imagem_url, estoque) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['descricao'], $_POST['preco'], $_POST['imagem'], $_POST['estoque']]);
    $msg = "Produto adicionado!";
}

$produtos = $pdo->query("SELECT * FROM produtos")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Produtos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <a href="principal.php" class="btn btn-secondary">Voltar</a>
        <h2>Gerenciar Loja (CRUD Produtos)</h2>

        <div class="card">
            <h3>Novo Produto</h3>
            <form method="POST">
                <div class="form-group"><input type="text" name="nome" placeholder="Nome do Produto" required></div>
                <div class="form-group"><textarea name="descricao" placeholder="Descrição"></textarea></div>
                <div class="form-group"><input type="number" step="0.01" name="preco" placeholder="Preço (ex: 29.90)" required></div>
                <div class="form-group"><input type="number" name="estoque" placeholder="Quantidade em Estoque" required></div>
                <div class="form-group"><input type="text" name="imagem" placeholder="URL da Imagem" required></div>
                <button type="submit" class="btn">Salvar Produto</button>
            </form>
        </div>

        <h3>Lista de Produtos</h3>
        <table>
            <tr><th>ID</th><th>Nome</th><th>Preço</th><th>Estoque</th><th>Ação</th></tr>
            <?php foreach($produtos as $p): ?>
            <tr>
                <td><?= $p['id_produto'] ?></td>
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td>R$ <?= $p['preco'] ?></td>
                <td><?= $p['estoque'] ?></td>
                <td><a href="?delete=<?= $p['id_produto'] ?>" class="btn btn-danger" onclick="return confirm('Excluir?')">Excluir</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>