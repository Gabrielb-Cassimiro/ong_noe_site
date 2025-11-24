<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'master') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT p.*, u.nome AS usuario
    FROM pedidos p
    JOIN usuarios u ON u.id_usuario = p.id_usuario
    WHERE id_pedido = ?
");
$stmt->execute([$id]);
$pedido = $stmt->fetch();

$itens = $pdo->prepare("
    SELECT i.*, pr.nome
    FROM itens_pedido i
    JOIN produtos pr ON pr.id_produto = i.id_produto
    WHERE i.id_pedido = ?
");
$itens->execute([$id]);
$itens = $itens->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedido #<?= $id ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">
    <a href="gerenciar_pedidos.php" class="btn btn-secondary">Voltar</a>

    <h2>Pedido #<?= $pedido['id_pedido'] ?> - <?= htmlspecialchars($pedido['usuario']) ?></h2>

    <h3>Itens do Pedido</h3>
    <table>
        <tr><th>Produto</th><th>Qtd</th><th>Subtotal</th></tr>
        <?php foreach ($itens as $i): ?>
        <tr>
            <td><?= htmlspecialchars($i['nome']) ?></td>
            <td><?= $i['quantidade'] ?></td>
            <td>R$ <?= number_format($i['quantidade'] * $i['preco_unit'], 2, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Status Atual: <?= htmlspecialchars($pedido['status']) ?></h3>

    <form action="update_status_pedido.php" method="POST">
        <input type="hidden" name="id_pedido" value="<?= $pedido['id_pedido'] ?>">

        <select name="status" class="form-group">
            <option value="pendente">Pendente</option>
            <option value="pago">Pago</option>
            <option value="enviado">Enviado</option>
            <option value="concluido">Concluído</option>
            <option value="cancelado">Cancelado</option>
        </select>

        <button type="submit" class="btn">Atualizar Status</button>
    </form>

</div>

</body>
</html>
