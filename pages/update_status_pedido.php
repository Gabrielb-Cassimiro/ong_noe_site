<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'master') {
    header("Location: login.php");
    exit();
}

$id = $_POST['id_pedido'] ?? 0;
$status = $_POST['status'] ?? 'pendente';

$stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id_pedido = ?");
$stmt->execute([$status, $id]);

header("Location: ver_pedido_admin.php?id=" . $id);
exit();
