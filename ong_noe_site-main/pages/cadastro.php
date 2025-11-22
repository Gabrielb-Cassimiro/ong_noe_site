<?php
require_once '../config/config.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        // 1. Usuário
        $senhaHash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, login, senha, tipo_usuario) VALUES (?, ?, ?, ?, 'comum')");
        $stmt->execute([$_POST['nome'], $_POST['email'], $_POST['login'], $senhaHash]);
        $id_usuario = $pdo->lastInsertId();

        // 2. Dados Pessoais
        $stmt = $pdo->prepare("INSERT INTO dados_pessoais (id_usuario, cpf, nome_materno, data_nascimento, sexo, telefone_celular, telefone_fixo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_usuario, $_POST['cpf'], $_POST['nome_materno'], $_POST['data_nascimento'], $_POST['sexo'], $_POST['celular'], $_POST['fixo']]);

        // 3. Endereço
        $stmt = $pdo->prepare("INSERT INTO enderecos (id_usuario, cep, logradouro, numero, complemento, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_usuario, $_POST['cep'], $_POST['logradouro'], $_POST['numero'], $_POST['complemento'], $_POST['bairro'], $_POST['cidade'], $_POST['estado']]);

        $pdo->commit();
        // Redirecionamento para login conforme PDF [cite: 78]
        header('Location: login.php?cadastro=sucesso');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $msg = "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><title>Cadastro</title><link rel="stylesheet" href="../css/style.css"><script src="../js/main.js"></script></head>
<body>
    <div class="container">
        <div class="card">
            <h2>Cadastro de Usuário</h2>
            <?= $msg ?>
            <div id="msg-js" style="display:none;"></div> <form method="POST" onsubmit="return validarCadastro(event)">
                <div class="form-group"><label>Nome Completo (Min 15 chars)</label><input type="text" name="nome" minlength="15" maxlength="80" required></div>
                <div class="form-group"><label>E-mail</label><input type="email" name="email" required></div>
                <div class="form-group"><label>CPF</label><input type="text" name="cpf" maxlength="14" required></div>
                
                <div class="form-group"><label>Nome da Mãe</label><input type="text" name="nome_materno" required></div>
                <div class="form-group"><label>Data Nascimento</label><input type="date" name="data_nascimento" required></div>
                
                <div class="form-group"><label>CEP</label><input type="text" name="cep" maxlength="9" required></div>
                <div class="form-group"><label>Logradouro</label><input type="text" name="logradouro" required></div>
                <div class="form-group"><label>Número</label><input type="text" name="numero" required></div>
                <div class="form-group"><label>Complemento</label><input type="text" name="complemento"></div>
                <div class="form-group"><label>Bairro</label><input type="text" name="bairro" required></div>
                <div class="form-group"><label>Cidade</label><input type="text" name="cidade" required></div>
                <div class="form-group"><label>Estado</label><input type="text" name="estado" maxlength="2" required></div>

                <div class="form-group"><label>Sexo</label><select name="sexo"><option value="M">M</option><option value="F">F</option><option value="O">Outro</option></select></div>
                <div class="form-group"><label>Celular (+55)XX-XXXXXXXX</label><input type="text" name="celular" placeholder="(+55)XX-XXXXXXXX" required></div>
                <div class="form-group"><label>Fixo</label><input type="text" name="fixo" placeholder="(+55)XX-XXXXXXXX" required></div>

                <div class="form-group"><label>Login (Exatos 6 chars)</label><input type="text" id="login" name="login" pattern="[a-zA-Z]{6}" title="Exatamente 6 letras" required></div>
                <div class="form-group"><label>Senha (Min 8 chars)</label><input type="password" id="senha" name="senha" minlength="8" required></div>
                <div class="form-group"><label>Confirma Senha</label><input type="password" id="confirma_senha" required></div>

                <button type="submit" class="btn">Enviar</button>
                <button type="reset" class="btn btn-secondary">Limpar Tela</button>
            </form>
            <a href="login.php">Voltar</a>
        </div>
    </div>
</body>
</html>