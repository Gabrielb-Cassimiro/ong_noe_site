<?php
session_start();
require_once '../config/config.php';

//  Verifica login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

//  Se já passou no 2FA, vai direto
if (isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] === true) {
    header('Location: principal.php');
    exit();
}

//  Inicializa tentativas
if (!isset($_SESSION['2fa_attempts'])) {
    $_SESSION['2fa_attempts'] = 0;
}

//  Busca dados do usuário
try {
    $stmt = $conn->prepare("
        SELECT dp.nome_materno, dp.data_nascimento, e.cep
        FROM usuarios u
        LEFT JOIN dados_pessoais dp ON dp.id_usuario = u.id_usuario
        LEFT JOIN enderecos e ON e.id_usuario = u.id_usuario
        WHERE u.id_usuario = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        die('Erro: Dados pessoais ou endereço não encontrados para o usuário.');
    }
} catch (PDOException $e) {
    die('Erro ao buscar dados do usuário: ' . $e->getMessage());
}

// Gera pergunta aleatória para cada sessão de 2FA
$questions = [
    'nome_materno' => 'Qual o nome de solteira da sua mãe?',
    'data_nascimento' => 'Qual a sua data de nascimento (DD/MM/AAAA)?',
    'cep' => 'Qual o seu CEP?'
];

if (!isset($_SESSION['2fa_question'])) {
    $question_key = array_rand($questions);
    $_SESSION['2fa_question'] = $question_key;
    $_SESSION['2fa_question_text'] = $questions[$question_key];
}

$question_text = $_SESSION['2fa_question_text'];
$remaining_attempts = 3 - $_SESSION['2fa_attempts'];
$feedback = '';

//  Processa resposta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $remaining_attempts > 0) {
    $resposta = trim($_POST['resposta'] ?? '');

    if (empty($resposta)) {
        $feedback = "Por favor, digite uma resposta.";
    } else {
        $question_key = $_SESSION['2fa_question'];
        $resposta_correta = '';

        switch ($question_key) {
            case 'nome_materno':
                $resposta_correta = strtolower(trim($user_data['nome_materno']));
                break;
            case 'data_nascimento':
                $data = DateTime::createFromFormat('Y-m-d', $user_data['data_nascimento']);
                $resposta_correta = $data ? $data->format('d/m/Y') : '';
                break;
            case 'cep':
                $resposta_correta = preg_replace('/[^0-9]/', '', $user_data['cep']);
                $resposta = preg_replace('/[^0-9]/', '', $resposta);
                break;
        }

        if (strtolower($resposta) === strtolower($resposta_correta)) {
            $_SESSION['2fa_verified'] = true;

            // Limpa dados do 2FA
            unset($_SESSION['2fa_question'], $_SESSION['2fa_question_text'], $_SESSION['2fa_attempts']);

            // Redireciona para principal.php
            header('Location: principal.php');
            exit();
        } else {
            $_SESSION['2fa_attempts']++;
            $remaining_attempts = 3 - $_SESSION['2fa_attempts'];

            if ($remaining_attempts > 0) {
                $feedback = "Resposta incorreta. Tentativas restantes: $remaining_attempts";
            } else {
                session_destroy();
                header('Location: login.php');
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação 2FA - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="form fade-in">
            <div class="text-center mb-lg">
                <div class="security-icon">🔒</div>
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

                <?php if (!empty($feedback)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($feedback); ?></div>
                <?php endif; ?>

                <form method="POST" action="" class="no-ajax">
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
                    <button type="submit" class="btn btn-primary" style="width:100%;">Enviar</button>
                </form>
            <?php else: ?>
                <div class="alert alert-error">
                    <strong>3 tentativas sem sucesso!</strong><br>
                    Favor realizar login novamente.
                </div>
                <a href="login.php" class="btn btn-primary">Voltar ao Login</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
