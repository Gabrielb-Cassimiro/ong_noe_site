<?php
require_once '../config/config.php';
if (!isset($_SESSION['temp_user_id'])) { header('Location: login.php'); exit(); }

if (!isset($_SESSION['tentativas_2fa'])) { $_SESSION['tentativas_2fa'] = 0; }
$bloqueado = false;
$erro = '';

// Verifica bloqueio imediato
if ($_SESSION['tentativas_2fa'] >= 3) {
    $bloqueado = true;
    // Destrói sessão após mostrar a mensagem
    session_destroy(); 
} else {
    // Lógica normal de pergunta (código igual ao anterior)
    $id_usuario = $_SESSION['temp_user_id'];
    $stmt = $pdo->prepare("SELECT dp.nome_materno, dp.data_nascimento, e.cep FROM dados_pessoais dp LEFT JOIN enderecos e ON dp.id_usuario = e.id_usuario WHERE dp.id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $dados = $stmt->fetch();

    if (!$dados && $_SESSION['temp_user_type'] === 'master') {
        $_SESSION['user_id'] = $_SESSION['temp_user_id'];
        $_SESSION['user_type'] = $_SESSION['temp_user_type'];
        $_SESSION['user_nome'] = $_SESSION['temp_user_nome'];
        header('Location: principal.php'); exit();
    }

    if (!isset($_SESSION['2fa_pergunta'])) {
        $opcoes = ['nome_materno', 'data_nascimento', 'cep'];
        $_SESSION['2fa_pergunta'] = $opcoes[array_rand($opcoes)];
    }
    $chave = $_SESSION['2fa_pergunta'];
    $rotulos = ['nome_materno' => 'Nome da Mãe', 'data_nascimento' => 'Data Nasc. (AAAA-MM-DD)', 'cep' => 'CEP'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $resposta = trim($_POST['resposta']);
        if (strtolower($resposta) == strtolower($dados[$chave])) {
            $_SESSION['user_id'] = $_SESSION['temp_user_id'];
            $_SESSION['user_type'] = $_SESSION['temp_user_type'];
            $_SESSION['user_nome'] = $_SESSION['temp_user_nome'];
            $pdo->prepare("INSERT INTO logs_autenticacao (id_usuario, status_login, tipo_2fa, ip_origem) VALUES (?, 'sucesso', ?, ?)")->execute([$id_usuario, $chave, $_SERVER['REMOTE_ADDR']]);
            header('Location: principal.php'); exit();
        } else {
            $_SESSION['tentativas_2fa']++;
            $pdo->prepare("INSERT INTO logs_autenticacao (id_usuario, status_login, tipo_2fa, ip_origem) VALUES (?, 'falha', ?, ?)")->execute([$id_usuario, $chave, $_SERVER['REMOTE_ADDR']]);
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
<head><title>2FA</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
    <div class="container" style="max-width: 400px;">
        <div class="card">
            <h3>Segundo Fator de Autenticação</h3>
            
            <?php if ($bloqueado): ?>
                <div class="alert alert-danger">
                    <strong>3 tentativas sem sucesso!</strong><br>
                    Favor realizar Login novamente.
                </div>
                <a href="login.php" class="btn" style="width:100%; text-align:center; display:block;">Voltar ao Login</a>
            
            <?php else: ?>
                <?php if($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label><?= $rotulos[$chave] ?></label>
                        <input type="text" name="resposta" required autofocus>
                    </div>
                    <button type="submit" class="btn">Verificar</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>