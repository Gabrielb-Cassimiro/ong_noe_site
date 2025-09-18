<?php
session_start();

// Verifica se o usuário fez login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verifica se já passou pelo 2FA
if (isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] === true) {
    header('Location: principal.php');
    exit();
}

// Inicializa tentativas se não existir
if (!isset($_SESSION['2fa_attempts'])) {
    $_SESSION['2fa_attempts'] = 0;
}

// Dados do usuário para 2FA (simulados)
$user_data = [
    'nome_materno' => 'Maria Silva Santos',
    'data_nascimento' => '1990-05-15',
    'cep' => '23456-789'
];

// Gera pergunta aleatória se não existir
if (!isset($_SESSION['2fa_question'])) {
    $questions = [
        'nome_materno' => 'Qual o nome de solteira da sua mãe?',
        'data_nascimento' => 'Qual a sua data de nascimento (DD/MM/AAAA)?',
        'cep' => 'Qual o seu CEP?'
    ];
    
    $question_key = array_rand($questions);
    $_SESSION['2fa_question'] = $question_key;
    $_SESSION['2fa_question_text'] = $questions[$question_key];
}

$question_text = $_SESSION['2fa_question_text'];
$remaining_attempts = 3 - $_SESSION['2fa_attempts'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação 2FA - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .security-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-brown);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-lg);
            font-size: 2rem;
            color: var(--white);
        }
        
        .attempts-counter {
            background: var(--warning-yellow);
            color: var(--dark-brown);
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--border-radius);
            text-align: center;
            margin-bottom: var(--spacing-lg);
            font-weight: 600;
        }
        
        .question-box {
            background: var(--beige);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-lg);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--primary-brown);
        }
        
        .question-text {
            font-size: var(--font-size-large);
            font-weight: 600;
            color: var(--dark-brown);
            margin-bottom: var(--spacing-md);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form fade-in">
            <div class="text-center mb-lg">
                <div class="security-icon">
                    🔒
                </div>
                <h1 style="color: var(--primary-brown); margin-bottom: var(--spacing-sm);">
                    Verificação de Segurança
                </h1>
                <p style="color: var(--light-brown);">
                    Para sua segurança, responda à pergunta abaixo
                </p>
            </div>
            
            <?php if ($remaining_attempts > 0): ?>
                <div class="attempts-counter">
                    Tentativas restantes: <?php echo $remaining_attempts; ?>
                </div>
                
                <div class="question-box">
                    <div class="question-text">
                        <?php echo htmlspecialchars($question_text); ?>
                    </div>
                </div>
                
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group">
                        <label for="resposta" class="form-label">Sua Resposta</label>
                        <input 
                            type="text" 
                            id="resposta" 
                            name="resposta" 
                            class="form-input" 
                            required
                            placeholder="Digite sua resposta"
                            autocomplete="off"
                        >
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Enviar
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-error">
                    <strong>3 tentativas sem sucesso!</strong><br>
                    Favor realizar Login novamente.
                </div>
                
                <div class="text-center">
                    <a href="login.php" class="btn btn-primary">
                        Voltar ao Login
                    </a>
                </div>
            <?php endif; ?>
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
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $remaining_attempts > 0) {
        $resposta = trim($_POST['resposta'] ?? '');
        
        if (empty($resposta)) {
            echo "<script>showAlert('Por favor, digite uma resposta', 'error');</script>";
        } else {
            $question_key = $_SESSION['2fa_question'];
            $resposta_correta = '';
            
            // Define a resposta correta baseada na pergunta
            switch ($question_key) {
                case 'nome_materno':
                    $resposta_correta = strtolower(trim($user_data['nome_materno']));
                    break;
                case 'data_nascimento':
                    // Aceita formato DD/MM/AAAA
                    $data = DateTime::createFromFormat('Y-m-d', $user_data['data_nascimento']);
                    $resposta_correta = $data->format('d/m/Y');
                    break;
                case 'cep':
                    $resposta_correta = $user_data['cep'];
                    break;
            }
            
            $resposta_usuario = strtolower(trim($resposta));
            $resposta_correta = strtolower($resposta_correta);
            
            if ($resposta_usuario === $resposta_correta) {
                // 2FA bem-sucedido
                $_SESSION['2fa_verified'] = true;
                
                // Registra log de autenticação (simulado)
                $log_entry = [
                    'user_id' => $_SESSION['user_id'],
                    'user_name' => $_SESSION['user_name'],
                    'login_time' => date('Y-m-d H:i:s'),
                    '2fa_method' => $question_key,
                    'status' => 'success'
                ];
                
                // Em um sistema real, salvaria no banco de dados
                if (!isset($_SESSION['auth_logs'])) {
                    $_SESSION['auth_logs'] = [];
                }
                $_SESSION['auth_logs'][] = $log_entry;
                
                // Limpa dados do 2FA
                unset($_SESSION['2fa_question']);
                unset($_SESSION['2fa_question_text']);
                unset($_SESSION['2fa_attempts']);
                
                echo "<script>
                    showAlert('Autenticação realizada com sucesso!', 'success');
                    setTimeout(() => {
                        window.location.href = 'principal.php';
                    }, 2000);
                </script>";
            } else {
                // Resposta incorreta
                $_SESSION['2fa_attempts']++;
                $remaining_attempts = 3 - $_SESSION['2fa_attempts'];
                
                if ($remaining_attempts > 0) {
                    echo "<script>showAlert('Resposta incorreta. Tentativas restantes: $remaining_attempts', 'error');</script>";
                    echo "<script>setTimeout(() => { window.location.reload(); }, 2000);</script>";
                } else {
                    // Esgotou tentativas
                    session_destroy();
                    echo "<script>
                        showAlert('3 tentativas sem sucesso! Redirecionando para login...', 'error');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 3000);
                    </script>";
                }
            }
        }
    }
    ?>
</body>
</html>

