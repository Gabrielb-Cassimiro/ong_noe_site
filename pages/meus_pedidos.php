<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'comum') {
    header('Location: login.php');
    exit();
}

$id_usuario = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT * FROM pedidos 
    WHERE id_usuario = ? 
    ORDER BY data_pedido DESC
");
$stmt->execute([$id_usuario]);
$pedidos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Meus Pedidos</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <div class="header-content">
        <a href="#" class="logo">ONG de Noé</a>
        <nav>
            <a href="principal.php">Home</a>
            <a href="meus_pedidos.php">Meus Pedidos</a>
            <a href="alterar_senha.php">Alterar Senha</a>
            <a href="logout.php" style="background: var(--cor-erro);">Sair</a>
        </nav>
    </div>
</header>

<div class="container">

<h2>Meus Pedidos</h2>

<?php if (empty($pedidos)): ?>
    <p>Você ainda não fez nenhum pedido.</p>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Data</th>
            <th>Total</th>
            <th>Status</th>
            <th>Detalhes</th>
        </tr>

        <?php foreach ($pedidos as $p): ?>
        <tr>
            <td><?= $p['id_pedido'] ?></td>
            <td><?= date('d/m/Y H:i', strtotime($p['data_pedido'])) ?></td>
            <td>R$ <?= number_format($p['total'], 2, ',', '.') ?></td>
            <td><?= htmlspecialchars($p['status']) ?></td>
            <td><a href="ver_pedido.php?id=<?= $p['id_pedido'] ?>" class="btn">Ver</a></td>
        </tr>
        <?php endforeach; ?>

    </table>
<?php endif; ?>

</div>
</body>
</html>
