<?php
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_produto = $_GET['id'] ?? null;

if (!$id_produto) { exit("Produto inválido!"); }

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (!isset($_SESSION['carrinho'][$id_produto])) {
    $_SESSION['carrinho'][$id_produto] = 1;
} else {
    $_SESSION['carrinho'][$id_produto]++;
}

header("Location: carrinho.php");
exit();
