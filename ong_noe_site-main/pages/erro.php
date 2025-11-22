<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Erro - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container" style="text-align: center; margin-top: 100px;">
        <div class="card" style="border-left: 5px solid var(--cor-erro);">
            <h1 style="color: var(--cor-erro);">Ops! Algo deu errado.</h1>
            <p style="font-size: 18px; margin: 20px 0;">
                <?php 
                // Exibe mensagem personalizada ou genérica
                echo isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : "Acesso negado ou erro inesperado."; 
                ?>
            </p>
            <a href="principal.php" class="btn btn-secondary">Voltar para o Início</a>
            <a href="login.php" class="btn">Ir para Login</a>
        </div>
    </div>
</body>
</html>