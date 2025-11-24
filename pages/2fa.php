<?php
require_once '../config/config.php';

// Se não fez login preliminar, manda voltar
if (!isset($_SESSION['temp_user_id'])) { 
    header('Location: login.php'); 
    exit(); 
}

if (!isset($_SESSION['tentativas_2fa'])) { $_SESSION['tentativas_2fa'] = 0; }
$bloqueado = false;
$erro = '';

// Verifica bloqueio imediato por excesso de tentativas
if ($_SESSION['tentativas_2fa'] >= 3) {
    $bloqueado = true;
    session_destroy(); 
} else {
    $id_usuario = $_SESSION['temp_user_id'];
    
    // Busca a resposta correta no banco
    $stmt = $pdo->prepare("SELECT dp.nome_materno, dp.data_nascimento, e.cep 
                           FROM dados_pessoais dp 
                           LEFT JOIN enderecos e ON dp.id_usuario = e.id_usuario 
                           WHERE dp.id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $dados = $stmt->fetch();

    // Se for usuário Master (que não tem dados pessoais cadastrados), libera direto
    if (!$dados && $_SESSION['temp_user_type'] === 'master') {
        $_SESSION['user_id'] = $_SESSION['temp_user_id'];
        $_SESSION['user_type'] = $_SESSION['temp_user_type'];
        $_SESSION['user_nome'] = $_SESSION['temp_user_nome'];
        $_SESSION['2fa_verified'] = true; // <--- O CARIMBO QUE FALTAVA!
        header('Location: principal.php'); 
        exit();
    }

    // Escolhe a pergunta aleatória se ainda não tiver escolhido
    if (!isset($_SESSION['2fa_pergunta'])) {
        $opcoes = ['nome_materno', 'data_nascimento', 'cep'];
        $_SESSION['2fa_pergunta'] = $opcoes[array_rand($opcoes)];
    }
    $chave = $_SESSION['2fa_pergunta'];
    $rotulos = [
        'nome_materno' => 'Nome da Mãe', 
        'data_nascimento' => 'Data Nasc. (AAAA-MM-DD)', 
        'cep' => 'CEP (Somente números)'
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $resposta = trim($_POST['resposta']);
        
        // Verifica a resposta
        if (strtolower($resposta) == strtolower($dados[$chave])) {
            // SUCESSO! Transfere da sessão temporária para a real
            $_SESSION['user_id'] = $_SESSION['temp_user_id'];
            $_SESSION['user_type'] = $_SESSION['temp_user_type'];
            $_SESSION['user_nome'] = $_SESSION['temp_user_nome'];
            
            // --- A CORREÇÃO ESTÁ AQUI ---
            $_SESSION['2fa_verified'] = true; // Define que o 2FA foi passado com sucesso
            // ----------------------------

            // Registra log de sucesso
            $pdo->prepare("INSERT INTO logs_autenticacao (id_usuario, status_login, tipo_2fa, ip_origem) VALUES (?, 'sucesso', ?, ?)")
                ->execute([$id_usuario, $chave, $_SERVER['REMOTE_ADDR']]);
            
            header('Location: principal.php'); 
            exit();
        } else {
            // FALHA
            $_SESSION['tentativas_2fa']++;
            
            // Registra log de falha
            $pdo->prepare("INSERT INTO logs_autenticacao (id_usuario, status_login, tipo_2fa, ip_origem) VALUES (?, 'falha', ?, ?)")
                ->execute([$id_usuario, $chave, $_SERVER['REMOTE_ADDR']]);
            
            if ($_SESSION['tentativas_2fa'] >= 3) {
                $bloqueado = true;
            } else {
                $erro = "Resposta incorreta. Tentativa " . $_SESSION['tentativas_2fa'] . "/3.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>2FA - Segurança</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="login-body-wrapper">
        <div class="login-box-fixed">
            <div class="login-header">
                <div class="login-icon-anim">🔒</div>
                <h2>Verificação</h2>
                <p style="color: #666;">Confirme sua identidade</p>
            </div>
            
            <?php if ($bloqueado): ?>
                <div class="alert alert-danger" style="text-align: center;">
                    <strong>3 tentativas sem sucesso!</strong><br>
                    Favor realizar Login novamente.
                </div>
                <a href="login.php" class="btn-login" style="display:block; text-align:center; text-decoration:none;">Voltar ao Login</a>
            
            <?php else: ?>
                <?php if($erro): ?>
                    <div class="alert alert-danger" style="text-align:center; animation: shake 0.4s;">
                        <?= $erro ?>
                    </div>
                    <style>@keyframes shake {0%,100%{transform:translateX(0)} 25%{transform:translateX(-5px)} 75%{transform:translateX(5px)}}</style>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="input-animated-group">
                        <input type="text" name="resposta" class="input-animated" placeholder=" " required autofocus>
                        <label class="label-animated"><?= $rotulos[$chave] ?></label>
                    </div>
                    
                    <div class="login-actions">
                        <a href="login.php" class="btn-clear" style="text-decoration:none; text-align:center; padding-top:14px;">Voltar</a>
                        <button type="submit" class="btn-login">Verificar</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>