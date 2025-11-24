<?php
require_once '../config/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }

// Inicializa carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Atualizar quantidades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    foreach ($_POST['quantidades'] as $id_produto => $qtd) {
        if ($qtd <= 0) {
            unset($_SESSION['carrinho'][$id_produto]);
        } else {
            $_SESSION['carrinho'][$id_produto] = $qtd;
        }
    }
}

// Remover item individual
if (isset($_GET['remover'])) {
    unset($_SESSION['carrinho'][$_GET['remover']]);
    header("Location: carrinho.php");
    exit();
}

// Buscar detalhes dos produtos do carrinho
$produtos = [];
$total = 0;

if (!empty($_SESSION['carrinho'])) {
    $ids = implode(',', array_keys($_SESSION['carrinho']));
    $stmt = $pdo->query("SELECT * FROM produtos WHERE id_produto IN ($ids)");
    $produtos = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <a href="principal.php" class="btn btn-secondary">Voltar</a>
        <h2>Meu Carrinho</h2>

        <?php if (empty($produtos)): ?>
            <p>Seu carrinho está vazio.</p>
            <a href="principal.php" class="btn">Voltar às compras</a>

        <?php else: ?>

        <form method="POST">
            <table>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Total</th>
                    <th>Ação</th>
                </tr>

                <?php foreach ($produtos as $p): 
                    $qtd = $_SESSION['carrinho'][$p['id_produto']];
                    $subtotal = $p['preco'] * $qtd;
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                    <td>
                        <input type="number" name="quantidades[<?= $p['id_produto'] ?>]" 
                               value="<?= $qtd ?>" min="1" style="width: 60px;">
                    </td>
                    <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                    <td>
                        <a href="carrinho.php?remover=<?= $p['id_produto'] ?>" 
                           class="btn btn-danger">Remover</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <h3>Total Geral: 
                <span style="color: var(--cor-primaria);">
                    R$ <?= number_format($total, 2, ',', '.') ?>
                </span>
            </h3>

            <button type="submit" name="atualizar" class="btn">Atualizar Carrinho</button>
            <a href="checkout.php" class="btn btn-success">Finalizar Pedido</a>
        </form>

        <?php endif; ?>
    </div>
</body>
</html>
