<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once realpath(__DIR__ . '/config.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome' => trim($_POST['nome'] ?? ''),
        'data_nascimento' => trim($_POST['data_nascimento'] ?? ''),
        'sexo' => trim($_POST['sexo'] ?? ''),
        'nome_materno' => trim($_POST['nome_materno'] ?? ''),
        'cpf' => trim($_POST['cpf'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telefone_celular' => trim($_POST['telefone_celular'] ?? ''),
        'telefone_fixo' => trim($_POST['telefone_fixo'] ?? ''),
        'cep' => trim($_POST['cep'] ?? ''),
        'logradouro' => trim($_POST['logradouro'] ?? ''),
        'numero' => trim($_POST['numero'] ?? ''),
        'complemento' => trim($_POST['complemento'] ?? ''),
        'bairro' => trim($_POST['bairro'] ?? ''),
        'cidade' => trim($_POST['cidade'] ?? ''),
        'estado' => trim($_POST['estado'] ?? ''),
        'login' => trim($_POST['login'] ?? ''),
        'senha' => trim($_POST['senha'] ?? ''),
        'confirma_senha' => trim($_POST['confirma_senha'] ?? '')
    ];

    $erros = [];

    // Validações
    if (strlen($dados['nome']) < 15 || strlen($dados['nome']) > 80) {
        $erros[] = 'Nome deve ter entre 15 e 80 caracteres.';
    }

    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail inválido.';
    }

    if (strlen($dados['senha']) < 8) {
        $erros[] = 'Senha deve ter pelo menos 8 caracteres.';
    }

    if ($dados['senha'] !== $dados['confirma_senha']) {
        $erros[] = 'As senhas não coincidem.';
    }

    if (!empty($erros)) {
        echo json_encode(['status' => 'erro', 'mensagens' => $erros]);
        exit;
    }

    try {
        $conn->beginTransaction();

        // Criptografa a senha
        $hash = password_hash($dados['senha'], PASSWORD_ARGON2ID);

        // Insere usuário
        $stmt = $conn->prepare("
            INSERT INTO usuarios (nome, email, login, senha)
            VALUES (:nome, :email, :login, :senha)
        ");
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':email' => $dados['email'],
            ':login' => $dados['login'],
            ':senha' => $hash
        ]);

        $id_usuario = $conn->lastInsertId();

        // Inserir dados pessoais
        $stmt = $conn->prepare("
            INSERT INTO dados_pessoais 
            (id_usuario, data_nascimento, sexo, nome_materno, cpf, telefone_celular, telefone_fixo)
            VALUES 
            (:id_usuario, :data_nascimento, :sexo, :nome_materno, :cpf, :telefone_celular, :telefone_fixo)
        ");
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':data_nascimento' => $dados['data_nascimento'],
            ':sexo' => $dados['sexo'],
            ':nome_materno' => $dados['nome_materno'],
            ':cpf' => $dados['cpf'],
            ':telefone_celular' => $dados['telefone_celular'],
            ':telefone_fixo' => $dados['telefone_fixo']
        ]);

        // Inserir endereço
        $stmt = $conn->prepare("
            INSERT INTO enderecos 
            (id_usuario, cep, logradouro, numero, complemento, bairro, cidade, estado)
            VALUES 
            (:id_usuario, :cep, :logradouro, :numero, :complemento, :bairro, :cidade, :estado)
        ");
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':cep' => $dados['cep'],
            ':logradouro' => $dados['logradouro'],
            ':numero' => $dados['numero'],
            ':complemento' => $dados['complemento'],
            ':bairro' => $dados['bairro'],
            ':cidade' => $dados['cidade'],
            ':estado' => $dados['estado']
        ]);

        $conn->commit();
        echo json_encode(['status' => 'sucesso']);
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'erro', 'mensagens' => [$e->getMessage()]]);
    }
}
?>
