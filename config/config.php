<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // ou a senha configurada no seu MySQL
$dbname = 'ong_noe_db';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>