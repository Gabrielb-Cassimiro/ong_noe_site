<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .form {
            max-width: 800px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-md);
        }
        
        .form-section {
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-lg);
            border-bottom: 2px solid var(--beige);
        }
        
        .form-section:last-child {
            border-bottom: none;
        }
        
        .form-section-title {
            font-size: var(--font-size-large);
            font-weight: 600;
            color: var(--primary-brown);
            margin-bottom: var(--spacing-lg);
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form fade-in">
            <div class="text-center mb-lg">
                <div class="logo">
                    <img src="../images/logo.png" alt="Logo ONG de Noé" onerror="this.style.display='none'">
                    <h1>Cadastro de Usuário</h1>
                </div>
                <p style="color: var(--light-brown);">Preencha todos os campos obrigatórios</p>
            </div>
            
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <!-- Dados Pessoais -->
                <div class="form-section">
                    <h2 class="form-section-title">Dados Pessoais</h2>
                    
                    <div class="form-group">
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input 
                            type="text" 
                            id="nome" 
                            name="nome" 
                            class="form-input" 
                            required 
                            minlength="15"
                            maxlength="80"
                            placeholder="Digite seu nome completo (mín. 15 caracteres)"
                        >
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_nascimento" class="form-label">Data de Nascimento *</label>
                            <input 
                                type="date" 
                                id="data_nascimento" 
                                name="data_nascimento" 
                                class="form-input" 
                                required
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="sexo" class="form-label">Sexo *</label>
                            <select id="sexo" name="sexo" class="form-input" required>
                                <option value="">Selecione</option>
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                                <option value="O">Outro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome_materno" class="form-label">Nome Materno *</label>
                            <input 
                                type="text" 
                                id="nome_materno" 
                                name="nome_materno" 
                                class="form-input" 
                                required
                                placeholder="Nome completo da sua mãe"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="cpf" class="form-label">CPF *</label>
                            <input 
                                type="text" 
                                id="cpf" 
                                name="cpf" 
                                class="form-input" 
                                required
                                placeholder="000.000.000-00"
                                maxlength="14"
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Contato -->
                <div class="form-section">
                    <h2 class="form-section-title">Contato</h2>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">E-mail *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            required
                            placeholder="seu@email.com"
                        >
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefone_celular" class="form-label">Telefone Celular *</label>
                            <input 
                                type="tel" 
                                id="telefone_celular" 
                                name="telefone_celular" 
                                class="form-input" 
                                required
                                placeholder="(11) 99999-9999"
                                maxlength="15"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="telefone_fixo" class="form-label">Telefone Fixo *</label>
                            <input 
                                type="tel" 
                                id="telefone_fixo" 
                                name="telefone_fixo" 
                                class="form-input" 
                                required
                                placeholder="(11) 3333-3333"
                                maxlength="14"
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Endereço -->
                <div class="form-section">
                    <h2 class="form-section-title">Endereço</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cep" class="form-label">CEP *</label>
                            <input 
                                type="text" 
                                id="cep" 
                                name="cep" 
                                class="form-input" 
                                required
                                placeholder="00000-000"
                                maxlength="9"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="logradouro" class="form-label">Logradouro *</label>
                            <input 
                                type="text" 
                                id="logradouro" 
                                name="logradouro" 
                                class="form-input" 
                                required
                                placeholder="Rua, Avenida, etc."
                            >
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="numero" class="form-label">Número *</label>
                            <input 
                                type="text" 
                                id="numero" 
                                name="numero" 
                                class="form-input" 
                                required
                                placeholder="123"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input 
                                type="text" 
                                id="complemento" 
                                name="complemento" 
                                class="form-input"
                                placeholder="Apto, Casa, etc."
                            >
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bairro" class="form-label">Bairro *</label>
                            <input 
                                type="text" 
                                id="bairro" 
                                name="bairro" 
                                class="form-input" 
                                required
                                placeholder="Nome do bairro"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="cidade" class="form-label">Cidade *</label>
                            <input 
                                type="text" 
                                id="cidade" 
                                name="cidade" 
                                class="form-input" 
                                required
                                placeholder="Nome da cidade"
                            >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="estado" class="form-label">Estado *</label>
                        <select id="estado" name="estado" class="form-input" required>
                            <option value="">Selecione</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                    </div>
                </div>
                
                <!-- Credenciais ->
                <div class="form-section">
                    <h2 class="form-section-title">Credenciais de Acesso</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="login" class="form-label">Login *</label>
                            <input 
                                type="text" 
                                id="login" 
                                name="login" 
                                class="form-input" 
                                required
                                maxlength="6"
                                placeholder="6 caracteres alfabéticos"
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="senha" class="form-label">Senha *</label>
                            <input 
                                type="password" 
                                id="senha" 
                                name="senha" 
                                class="form-input" 
                                required
                                minlength="8"
                                placeholder="Mínimo 8 caracteres"
                            >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirma_senha" class="form-label">Confirmação da Senha *</label>
                        <input 
                            type="password" 
                            id="confirma_senha" 
                            name="confirma_senha" 
                            class="form-input" 
                            required
                            placeholder="Digite a senha novamente"
                        >
                    </div>
                </div>
                
                <div class="form-group" style="display: flex; gap: var(--spacing-md);">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Enviar
                    </button>
                    <button type="reset" class="btn btn-outline" style="flex: 1;">
                        Limpar Tela
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-lg">
                <a href="login.php" style="color: var(--primary-brown); text-decoration: none;">
                    ← Voltar para Login
                </a>
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
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Coleta e sanitiza os dados
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
            $erros[] = 'Nome deve ter entre 15 e 80 caracteres';
        }
        
        if (!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $dados['nome'])) {
            $erros[] = 'Nome deve conter apenas letras';
        }
        
        if (strlen($dados['login']) !== 6 || !ctype_alpha($dados['login'])) {
            $erros[] = 'Login deve ter exatamente 6 caracteres alfabéticos';
        }
        
        if (strlen($dados['senha']) < 8) {
            $erros[] = 'Senha deve ter pelo menos 8 caracteres';
        }
        
        if ($dados['senha'] !== $dados['confirma_senha']) {
            $erros[] = 'Senhas não coincidem';
        }
        
        if (empty($erros)) {
            // Em um sistema real, aqui seria feita a inserção no banco de dados
            // Por enquanto, apenas simula o sucesso
            echo "<script>
                showAlert('Cadastro realizado com sucesso! Redirecionando para login...', 'success');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 3000);
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

