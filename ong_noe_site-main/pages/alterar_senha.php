<?php
require_once '../config/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'comum') { die('Acesso Negado'); }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha = $_POST['senha'];
    $confirma = $_POST['confirma'];
    
    if (strlen($senha) < 8) {
        $msg = "<div class='alert alert-danger'>Senha deve ter no mínimo 8 caracteres.</div>";
    } elseif ($senha !== $confirma) {
        $msg = "<div class='alert alert-danger'>Senhas não conferem.</div>";
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id_usuario = ?");
        $stmt->execute([$hash, $_SESSION['user_id']]);
        $msg = "<div class='alert alert-success'>Senha alterada com sucesso!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><title>Alterar Senha</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
    <div class="container" style="max-width: 500px;">
        <a href="principal.php" class="btn btn-secondary">Voltar</a>
        <div class="card" style="margin-top: 20px;">
            <h2>Alterar Minha Senha</h2>
            <?= $msg ?>
            <form method="POST">
                <div class="form-group">
                    <label>Nova Senha (Mín 8 caracteres)</label>
                    <input type="password" name="senha" minlength="8" required>
                </div>
                <div class="form-group">
                    <label>Confirmar Nova Senha</label>
                    <input type="password" name="confirma" required>
                </div>
                <button type="submit" class="btn">Salvar</button>
            </form>
        </div>
    </div>
</body>
</html>