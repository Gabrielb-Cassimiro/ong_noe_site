<?php
require_once '../config/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><title>Modelo BD</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
    <header>
        <div class="header-content">
            <a href="principal.php" class="logo">ONG de Noé</a>
            <nav><a href="principal.php">Voltar</a></nav>
        </div>
    </header>
    <div class="container">
        <h2 style="color: var(--cor-primaria);">Modelo do Banco de Dados</h2>
        <div class="card" style="text-align: center;">
            <p>Abaixo está a representação visual das tabelas do sistema:</p>
            <img src="https://via.placeholder.com/800x600?text=Diagrama+ER+(Usuarios+Dados+Enderecos+Logs+Produtos)" alt="Modelo DER" style="max-width: 100%; border: 2px solid #8B4513;">
            
            <div style="margin-top: 20px; text-align: left;">
                <h4>Tabelas:</h4>
                <ul>
                    <li><strong>Usuarios:</strong> Login, Senha, Perfil (Master/Comum).</li>
                    <li><strong>Dados Pessoais:</strong> CPF, Mãe, Data Nasc (para 2FA).</li>
                    <li><strong>Endereços:</strong> CEP (para 2FA) e Logradouro.</li>
                    <li><strong>Logs:</strong> Auditoria de acessos.</li>
                    <li><strong>Produtos:</strong> Itens da loja (Carrossel).</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>