<?php
session_start();

// Registra log de logout se o usuário estava logado
if (isset($_SESSION['user_id'])) {
    $logout_log = [
        'user_id' => $_SESSION['user_id'],
        'user_name' => $_SESSION['user_name'] ?? 'Usuário',
        'logout_time' => date('Y-m-d H:i:s'),
        'action' => 'logout'
    ];
    
    // Em um sistema real, salvaria no banco de dados
    // Por enquanto, apenas simula o registro
}

// Destrói todas as variáveis de sessão
$_SESSION = array();

// Se for desejado destruir a sessão completamente, apague também o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrói a sessão
session_destroy();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .logout-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--beige) 0%, var(--light-brown) 100%);
        }
        
        .logout-card {
            background: var(--white);
            padding: var(--spacing-xxl);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        
        .logout-icon {
            width: 80px;
            height: 80px;
            background: var(--success-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-lg);
            font-size: 2rem;
            color: var(--white);
            animation: checkmark 0.6s ease-in-out;
        }
        
        @keyframes checkmark {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .logout-title {
            font-size: var(--font-size-xl);
            color: var(--primary-brown);
            margin-bottom: var(--spacing-lg);
        }
        
        .logout-message {
            color: var(--light-brown);
            margin-bottom: var(--spacing-xl);
            line-height: 1.6;
        }
        
        .countdown {
            background: var(--beige);
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            font-weight: 600;
            color: var(--primary-brown);
        }
        
        .logout-actions {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            flex-wrap: wrap;
        }
        
        @media (max-width: 480px) {
            .logout-actions {
                flex-direction: column;
            }
            
            .logout-card {
                padding: var(--spacing-lg);
            }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-card fade-in">
            <div class="logout-icon">
                ✅
            </div>
            
            <h1 class="logout-title">
                Logout Realizado com Sucesso
            </h1>
            
            <p class="logout-message">
                Sua sessão foi encerrada com segurança. Obrigado por usar o sistema da ONG de Noé!
            </p>
            
            <div class="countdown">
                Redirecionando para login em <span id="countdown">5</span> segundos...
            </div>
            
            <div class="logout-actions">
                <a href="login.php" class="btn btn-primary">
                    🔐 Fazer Login Novamente
                </a>
                <button onclick="window.close()" class="btn btn-outline">
                    ❌ Fechar Janela
                </button>
            </div>
            
            <div style="margin-top: var(--spacing-xl); padding-top: var(--spacing-lg); border-top: 1px solid var(--beige); font-size: var(--font-size-small); color: var(--light-brown);">
                <p><strong>Dicas de Segurança:</strong></p>
                <ul style="text-align: left; margin-top: var(--spacing-sm);">
                    <li>Sempre faça logout ao usar computadores públicos</li>
                    <li>Não compartilhe suas credenciais de acesso</li>
                    <li>Use senhas fortes e únicas</li>
                    <li>Mantenha seu navegador atualizado</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Controles de Acessibilidade -->
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
    
    <script>
        // Countdown para redirecionamento
        let timeLeft = 5;
        const countdownElement = document.getElementById('countdown');
        
        const countdownTimer = setInterval(() => {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(countdownTimer);
                window.location.href = 'login.php';
            }
        }, 1000);
        
        // Permite cancelar o redirecionamento clicando em qualquer lugar
        document.addEventListener('click', () => {
            clearInterval(countdownTimer);
            document.querySelector('.countdown').textContent = 'Redirecionamento cancelado.';
        });
        
        // Log do logout para análise
        console.log('Logout realizado:', {
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent
        });
        
        // Limpa dados locais relacionados à sessão
        localStorage.removeItem('user_preferences');
        sessionStorage.clear();
        
        // Previne uso do botão voltar para acessar páginas protegidas
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
</body>
</html>

