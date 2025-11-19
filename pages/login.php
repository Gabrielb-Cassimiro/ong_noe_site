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
                <p style="color: var(--primary-brown); margin-top: var(--spacing-sm);">#5C4033</p>
            </div>
            
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <label for="login" class="form-label">Login</label>
                    <input 
                        type="text" 
                        id="login" 
                        name="login" 
                        class="form-input" 
                        required 
                        maxlength="6"
                        placeholder="Digite seu login (6 caracteres)"
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
    
    <!-- Controles de Acessibilidade  -->
    <div style="position: fixed; top: 20px; right: 20px; display: flex; gap: 8px; z-index: 1000;">
        <button id="contrast-toggle" class="accessibility-btn" title="Alternar Contraste">
            🌓
        </button>
        <button data-font-action="decrease" class="accessibility-btn" title="Diminuir Fonte">
            A-
        </button>
        <button data-font-action="increase" class="accessibility-btn" title="Aumentar Fonte">
            A+
        </button>
    </div>

    <script src="../js/main.js"></script>
    
    <?php
    session_start();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = trim($_POST['login'] ?? '');
        $senha = trim($_POST['senha'] ?? '');
        
        // Validações básicas
        if (empty($login) || empty($senha)) {
            echo "<script>showAlert('Por favor, preencha todos os campos', 'error');</script>";
        } elseif (strlen($login) !== 6 || !ctype_alpha($login)) {
            echo "<script>showAlert('Login deve ter exatamente 6 caracteres alfabéticos', 'error');</script>";
        } elseif (strlen($senha) < 8) {
            echo "<script>showAlert('Senha deve ter pelo menos 8 caracteres', 'error');</script>";
        } else {
            // Simulação de autenticação
            // Em um sistema real, aqui seria feita a consulta no banco de dados
            
            // Usuário Master padrão: admin / admin123
            if ($login === 'admin' && $senha === 'admin123') {
                $_SESSION['user_id'] = 1;
                $_SESSION['user_login'] = $login;
                $_SESSION['user_type'] = 'master';
                $_SESSION['user_name'] = 'Administrador';
                
                // Redireciona para 2FA
                header('Location: 2fa.php');
                exit();
            }
            // Usuário comum de exemplo: user01 / user1234
            elseif ($login === 'user01' && $senha === 'user1234') {
                $_SESSION['user_id'] = 2;
                $_SESSION['user_login'] = $login;
                $_SESSION['user_type'] = 'comum';
                $_SESSION['user_name'] = 'Usuário Comum';
                
                // Redireciona para 2FA
                header('Location: 2fa.php');
                exit();
            } else {
                echo "<script>showAlert('Login ou senha incorretos', 'error');</script>";
            }
        }
    }
    ?>
</body>
</html>

