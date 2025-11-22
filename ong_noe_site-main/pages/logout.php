<?php
session_start();
// Destrói todas as sessões
session_destroy();
// Redireciona para login
header('Location: login.php');
exit();
?>