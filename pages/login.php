<?php
require_once '../config/config.php';

$erro = '';

// Exibe feedback visual se vier do cadastro
$sucesso_cadastro = isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $senha = trim($_POST['senha']);

    // Busca usuário
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE login = ? AND status = 'ativo'");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        // Login Correto
        $_SESSION['temp_user_id'] = $user['id_usuario'];
        $_SESSION['temp_user_type'] = $user['tipo_usuario'];
        $_SESSION['temp_user_nome'] = $user['nome'];
        
        header('Location: 2fa.php');
        exit();
    } else {
        $erro = "Login ou senha incorretos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js"></script>
</head>
<body>
    <div class="login-body-wrapper">
        
        <div class="login-box-fixed">
            
            <div class="login-header">
                <div class="login-icon-anim">🐾</div>
                <h2>Bem-vindo</h2>
                <p style="color: #666; font-size: 0.9rem;">Faça login para continuar</p>
            </div>

            <?php if($erro): ?>
                <div class="alert alert-danger" style="text-align:center; animation: shake 0.4s;">
                    <?= $erro ?>
                </div>
                <style>@keyframes shake {0%,100%{transform:translateX(0)} 25%{transform:translateX(-5px)} 75%{transform:translateX(5px)}}</style>
            <?php endif; ?>

            <?php if($sucesso_cadastro): ?>
                <div class="alert alert-success" style="text-align:center;">
                    Cadastro realizado! Entre agora.
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-animated-group">
                    <input type="text" id="login" name="login" class="input-animated" placeholder=" " maxlength="6" required>
                    <label for="login" class="label-animated">Login (6 Caracteres)</label>
                </div>

                <div class="input-animated-group">
                    <input type="password" id="senha" name="senha" class="input-animated" placeholder=" " required>
                    <label for="senha" class="label-animated">Senha</label>
                </div>

                <div class="login-actions">
                    <button type="reset" class="btn-clear">Limpar</button>
                    <button type="submit" class="btn-login">Entrar ➔</button>
                </div>
            </form>

            <div class="login-footer">
                <span>Novo por aqui? </span>
                <a href="cadastro.php">Crie sua conta</a>
            </div>
            
            <div style="text-align: center; margin-top: 15px;">
                <button onclick="toggleContrast()" style="background:none; border:none; color:#999; cursor:pointer; font-size:0.8rem;">
                    🌓 Alto Contraste
                </button>
            </div>
        </div>
    </div>
</body>
</html>