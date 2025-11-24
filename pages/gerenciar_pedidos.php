<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'master') { die('Acesso Negado'); }

$pedidos = $pdo->query("
    SELECT p.*, u.nome AS usuario
    FROM pedidos p
    JOIN usuarios u ON u.id_usuario = p.id_usuario
    ORDER BY p.id_pedido DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Pedidos</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">

        <a href="principal.php" class="btn btn-secondary">Voltar</a>
        <h2>Gerenciar Pedidos</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Total</th>
                <th>Status</th>
                <th>Ação</th>
            </tr>

            <?php foreach ($pedidos as $p): ?>
            <tr>
                <td><?= $p['id_pedido'] ?></td>
                <td><?= htmlspecialchars($p['usuario']) ?></td>
                <td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars($p['status']) ?></td>
                <td>
                    <a href="ver_pedido_admin.php?id=<?= $p['id_pedido'] ?>" class="btn">Ver Pedido</a>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>
</body>
</html>
