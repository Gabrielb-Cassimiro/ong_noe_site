<?php
session_start();

// Verifica se o usuário está logado e é Comum
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'comum') {
    header('Location: login.php');
    exit();
}

// Verifica se passou pelo 2FA
if (!isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) {
    header('Location: 2fa.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .password-form {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--light-brown);
            font-size: 18px;
        }
        
        .password-toggle:hover {
            color: var(--primary-brown);
        }
        
        .password-strength {
            margin-top: var(--spacing-sm);
            height: 4px;
            background: var(--beige);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak {
            background: var(--error-red);
            width: 33%;
        }
        
        .strength-medium {
            background: var(--warning-yellow);
            width: 66%;
        }
        
        .strength-strong {
            background: var(--success-green);
            width: 100%;
        }
        
        .password-requirements {
            background: var(--beige);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-top: var(--spacing-md);
            font-size: var(--font-size-small);
        }
        
        .requirement {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-xs);
        }
        
        .requirement.met {
            color: var(--success-green);
        }
        
        .requirement.unmet {
            color: var(--error-red);
        }
        
        .security-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-brown);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-lg);
            font-size: 1.5rem;
            color: var(--white);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="principal.php" class="logo">
                <img src="../images/logo.png" alt="Logo ONG de Noé" onerror="this.style.display='none'">
                ONG de Noé
            </a>
            
            <nav class="nav">
                <a href="principal.php">Home</a>
                <a href="alterar_senha.php">Alterar Senha</a>
                <a href="modelo_bd.php">Modelo BD</a>
            </nav>
            
            <div class="accessibility-controls">
                <span style="color: var(--white); margin-right: var(--spacing-md);">
                    Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <button id="contrast-toggle" class="accessibility-btn" title="Alternar Contraste">
                    🌓
                </button>
                <button data-font-action="decrease" class="accessibility-btn" title="Diminuir Fonte">
                    A-
                </button>
                <button data-font-action="increase" class="accessibility-btn" title="Aumentar Fonte">
                    A+
                </button>
                <button onclick="logout()" class="accessibility-btn" title="Sair">
                    🚪
                </button>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="password-form fade-in">
            <div class="text-center mb-lg">
                <div class="security-icon">
                    🔐
                </div>
                <h1 style="color: var(--primary-brown); margin-bottom: var(--spacing-sm);">
                    Alterar Senha
                </h1>
                <p style="color: var(--light-brown);">
                    Para sua segurança, digite sua senha atual e a nova senha
                </p>
            </div>
            
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <label for="senha_atual" class="form-label">Senha Atual *</label>
                    <div class="password-field">
                        <input 
                            type="password" 
                            id="senha_atual" 
                            name="senha_atual" 
                            class="form-input" 
                            required
                            placeholder="Digite sua senha atual"
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword('senha_atual')">
                            👁️
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="nova_senha" class="form-label">Nova Senha *</label>
                    <div class="password-field">
                        <input 
                            type="password" 
                            id="nova_senha" 
                            name="nova_senha" 
                            class="form-input" 
                            required
                            minlength="8"
                            placeholder="Digite sua nova senha"
                            oninput="checkPasswordStrength(this.value)"
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword('nova_senha')">
                            👁️
                        </button>
                    </div>
                    
                    <!-- Indicador de força da senha -->
                    <div class="password-strength">
                        <div id="strength-bar" class="password-strength-bar"></div>
                    </div>
                    <div id="strength-text" style="margin-top: var(--spacing-xs); font-size: var(--font-size-small); text-align: center;"></div>
                    
                    <!-- Requisitos da senha -->
                    <div class="password-requirements">
                        <strong>Requisitos da senha:</strong>
                        <div id="req-length" class="requirement unmet">
                            <span>❌</span> Pelo menos 8 caracteres
                        </div>
                        <div id="req-uppercase" class="requirement unmet">
                            <span>❌</span> Pelo menos 1 letra maiúscula
                        </div>
                        <div id="req-lowercase" class="requirement unmet">
                            <span>❌</span> Pelo menos 1 letra minúscula
                        </div>
                        <div id="req-number" class="requirement unmet">
                            <span>❌</span> Pelo menos 1 número
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirma_senha" class="form-label">Confirmar Nova Senha *</label>
                    <div class="password-field">
                        <input 
                            type="password" 
                            id="confirma_senha" 
                            name="confirma_senha" 
                            class="form-input" 
                            required
                            placeholder="Digite a nova senha novamente"
                            oninput="checkPasswordMatch()"
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword('confirma_senha')">
                            👁️
                        </button>
                    </div>
                    <div id="match-message" style="margin-top: var(--spacing-xs); font-size: var(--font-size-small);"></div>
                </div>
                
                <div class="form-group" style="display: flex; gap: var(--spacing-md);">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Salvar Alterações
                    </button>
                    <button type="button" class="btn btn-outline" style="flex: 1;" onclick="window.location.href='principal.php'">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/main.js"></script>
    
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
        }
        
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            
            // Requisitos
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);
            
            // Atualiza indicadores visuais
            updateRequirement('req-length', hasLength);
            updateRequirement('req-uppercase', hasUppercase);
            updateRequirement('req-lowercase', hasLowercase);
            updateRequirement('req-number', hasNumber);
            
            // Calcula força
            const score = [hasLength, hasUppercase, hasLowercase, hasNumber].filter(Boolean).length;
            
            strengthBar.className = 'password-strength-bar';
            
            if (score < 2) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = 'Fraca';
                strengthText.style.color = 'var(--error-red)';
            } else if (score < 4) {
                strengthBar.classList.add('strength-medium');
                strengthText.textContent = 'Média';
                strengthText.style.color = 'var(--warning-yellow)';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Forte';
                strengthText.style.color = 'var(--success-green)';
            }
            
            checkPasswordMatch();
        }
        
        function updateRequirement(reqId, met) {
            const req = document.getElementById(reqId);
            const span = req.querySelector('span');
            
            if (met) {
                req.className = 'requirement met';
                span.textContent = '✅';
            } else {
                req.className = 'requirement unmet';
                span.textContent = '❌';
            }
        }
        
        function checkPasswordMatch() {
            const novaSenha = document.getElementById('nova_senha').value;
            const confirmaSenha = document.getElementById('confirma_senha').value;
            const matchMessage = document.getElementById('match-message');
            
            if (confirmaSenha === '') {
                matchMessage.textContent = '';
                return;
            }
            
            if (novaSenha === confirmaSenha) {
                matchMessage.textContent = '✅ Senhas coincidem';
                matchMessage.style.color = 'var(--success-green)';
            } else {
                matchMessage.textContent = '❌ Senhas não coincidem';
                matchMessage.style.color = 'var(--error-red)';
            }
        }
        
        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $senha_atual = trim($_POST['senha_atual'] ?? '');
        $nova_senha = trim($_POST['nova_senha'] ?? '');
        $confirma_senha = trim($_POST['confirma_senha'] ?? '');
        
        $erros = [];
        
        // Validações
        if (empty($senha_atual) || empty($nova_senha) || empty($confirma_senha)) {
            $erros[] = 'Todos os campos são obrigatórios';
        }
        
        // Simula verificação da senha atual (em um sistema real, verificaria no banco)
        if ($senha_atual !== 'user1234') {
            $erros[] = 'Senha atual incorreta';
        }
        
        if (strlen($nova_senha) < 8) {
            $erros[] = 'Nova senha deve ter pelo menos 8 caracteres';
        }
        
        if ($nova_senha !== $confirma_senha) {
            $erros[] = 'Nova senha e confirmação não coincidem';
        }
        
        if ($senha_atual === $nova_senha) {
            $erros[] = 'A nova senha deve ser diferente da senha atual';
        }
        
        if (empty($erros)) {
            // Em um sistema real, atualizaria a senha no banco de dados
            echo "<script>
                showAlert('Senha alterada com sucesso!', 'success');
                setTimeout(() => {
                    window.location.href = 'principal.php';
                }, 2000);
            </script>";
        } else {
            foreach ($erros as $erro) {
                echo "<script>showAlert('$erro', 'error');</script>";
            }
        }
    }
    ?>
</body>
</html>

