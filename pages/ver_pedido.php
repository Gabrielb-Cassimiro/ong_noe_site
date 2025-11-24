<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) { exit(); }

$id_pedido = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
$stmt->execute([$id_pedido]);
$pedido = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT p.nome, p.imagem_url, i.quantidade, i.preco_unit
    FROM itens_pedido i
    JOIN produtos p ON p.id_produto = i.id_produto
    WHERE i.id_pedido = ?
");
$stmt->execute([$id_pedido]);
$itens = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Pedido #<?= $id_pedido ?></title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <h2>Pedido #<?= $id_pedido ?></h2>
    <p>Status: <strong><?= $pedido['status'] ?></strong></p>
    <p>Total: R$ <?= number_format($pedido['total'], 2, ',', '.') ?></p>

    <h3>Itens</h3>
    <table>
        <tr>
            <th>Produto</th>
            <th>Qtd</th>
            <th>Preço</th>
            <th>Subtotal</th>
        </tr>

        <?php foreach ($itens as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['nome']) ?></td>
            <td><?= $item['quantidade'] ?></td>
            <td>R$ <?= number_format($item['preco_unit'], 2, ',', '.') ?></td>
            <td>R$ <?= number_format($item['quantidade'] * $item['preco_unit'], 2, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a href="meus_pedidos.php" class="btn btn-secondary">Voltar</a>
</div>

</body>
</html>
