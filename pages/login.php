<?php
session_start();
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($login) || empty($senha)) {
        echo "<script>alert('Por favor, preencha todos os campos.');</script>";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id_usuario, nome, login, senha, tipo_usuario FROM usuarios WHERE login = :login");
            $stmt->execute([':login' => $login]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Cria a sessão
                $_SESSION['user_id'] = $usuario['id_usuario'];
                $_SESSION['user_login'] = $usuario['login'];
                $_SESSION['user_type'] = $usuario['tipo_usuario'];
                $_SESSION['user_name'] = $usuario['nome'];

                // Redireciona para o 2FA
                header('Location: 2fa.php');
                exit;
            } else {
                echo "<script>alert('Login ou senha incorretos.');</script>";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Erro de banco de dados: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="form fade-in">
            <div class="text-center mb-lg">
                <div class="logo">
                    <img src="../images/logo.png" alt="Logo ONG de Noé" onerror="this.style.display='none'">
                    <h1>ONG de Noé</h1>
                </div>
                <p style="color: var(--primary-brown); margin-top: var(--spacing-sm);">ONG de Noé</p>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="login" class="form-label">Login</label>
                    <input 
                        type="text" 
                        id="login" 
                        name="login" 
                        class="form-input" 
                        required 
                        maxlength="12"
                        placeholder="Digite seu login"
                    >
                </div>
                
                <div class="form-group">
                    <label for="senha" class="form-label">Senha</label>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        class="form-input" 
                        required
                        placeholder="Digite sua senha"
                    >
                </div>
                
                <div class="form-group" style="display: flex; gap: var(--spacing-md);">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Enviar
                    </button>
                    <button type="reset" class="btn btn-outline" style="flex: 1;">
                        Limpar
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-lg">
                <a href="cadastro.php" style="color: var(--primary-brown); text-decoration: none;">
                    Cadastre-se aqui
                </a>
            </div>
        </div>
    </div>
</body>
</html>