<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) { exit(); }

$carrinho = $_SESSION['carrinho'] ?? [];
if (empty($carrinho)) { exit("Carrinho vazio."); }

$ids = implode(',', array_keys($carrinho));
$produtos = $pdo->query("SELECT * FROM produtos WHERE id_produto IN ($ids)")->fetchAll();

$total = 0;
foreach ($produtos as $p) {
    $total += $p['preco'] * $carrinho[$p['id_produto']];
}

$pdo->beginTransaction();

$stmt = $pdo->prepare("INSERT INTO pedidos (id_usuario, total) VALUES (?, ?)");
$stmt->execute([$_SESSION['user_id'], $total]);
$id_pedido = $pdo->lastInsertId();

$stmtItem = $pdo->prepare("
    INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unit)
    VALUES (?, ?, ?, ?)
");

$stmtEstoque = $pdo->prepare("
    UPDATE produtos SET estoque = estoque - ? WHERE id_produto = ?
");

foreach ($produtos as $p) {
    $qtd = $carrinho[$p['id_produto']];

    $stmtItem->execute([$id_pedido, $p['id_produto'], $qtd, $p['preco']]);
    $stmtEstoque->execute([$qtd, $p['id_produto']]);
}

$pdo->commit();
unset($_SESSION['carrinho']);

header("Location: ver_pedido.php?id=$id_pedido");
exit();
