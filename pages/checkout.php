<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (empty($_SESSION['carrinho'])) {
    header("Location: carrinho.php");
    exit();
}

$id_usuario = $_SESSION['user_id'];
$carrinho = $_SESSION['carrinho'];

// Buscar produtos do carrinho
$ids = implode(",", array_keys($carrinho));
$query = $pdo->query("SELECT * FROM produtos WHERE id_produto IN ($ids)");
$produtos = $query->fetchAll();

// 1) Verificar estoque
foreach ($produtos as $p) {
    $id = $p['id_produto'];
    $qtdSolicitada = $carrinho[$id];

    if ($p['estoque'] < $qtdSolicitada) {
        $msg = "Estoque insuficiente para o produto: " . $p['nome'];
        header("Location: carrinho.php?erro=" . urlencode($msg));
        exit();
    }
}

// 2) Calcular total
$total = 0;
foreach ($produtos as $p) {
    $total += $p['preco'] * $carrinho[$p['id_produto']];
}

// 3) Registrar pedido
$stmt = $pdo->prepare("INSERT INTO pedidos (id_usuario, total) VALUES (?, ?)");
$stmt->execute([$id_usuario, $total]);
$id_pedido = $pdo->lastInsertId();

// 4) Registrar itens do pedido
$stmtItem = $pdo->prepare("
    INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unit)
    VALUES (?, ?, ?, ?)
");

foreach ($produtos as $p) {
    $id = $p['id_produto'];
    $qtd = $carrinho[$id];

    $stmtItem->execute([
        $id_pedido,
        $id,
        $qtd,
        $p['preco']
    ]);

    // 5) Atualizar estoque
    $novoEstoque = $p['estoque'] - $qtd;
    $pdo->prepare("UPDATE produtos SET estoque = ? WHERE id_produto = ?")
        ->execute([$novoEstoque, $id]);
}

// 6) Limpar carrinho
unset($_SESSION['carrinho']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pedido Finalizado - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <div class="header-content">
        <a href="#" class="logo">ONG de Noé</a>
        <nav>
            <a href="principal.php">Home</a>
            <a href="logout.php" style="background: var(--cor-erro);">Sair</a>
        </nav>
    </div>
</header>

<div class="container">
    <div class="card">
        <h2>Compra concluída!</h2>
        <p>Seu pedido foi registrado com sucesso.</p>
        <p><strong>Nº do Pedido:</strong> <?= $id_pedido ?></p>
        <p><strong>Total:</strong> R$ <?= number_format($total, 2, ',', '.') ?></p>
        <br>
        <a href="principal.php" class="btn">Voltar à Loja</a>
    </div>
</div>

</body>
</html>
