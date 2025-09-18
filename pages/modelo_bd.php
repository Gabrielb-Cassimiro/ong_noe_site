<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verifica se passou pelo 2FA
if (!isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) {
    header('Location: 2fa.php');
    exit();
}

$user_type = $_SESSION['user_type'] ?? 'comum';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modelo do Banco de Dados - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .diagram-container {
            background: var(--white);
            padding: var(--spacing-xl);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-md);
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .diagram-placeholder {
            width: 100%;
            max-width: 800px;
            height: 600px;
            background: var(--beige);
            border: 2px dashed var(--light-brown);
            border-radius: var(--border-radius-lg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-lg);
            position: relative;
        }
        
        .entity-box {
            background: var(--white);
            border: 2px solid var(--primary-brown);
            border-radius: var(--border-radius);
            padding: var(--spacing-md);
            margin: var(--spacing-sm);
            min-width: 150px;
            position: absolute;
        }
        
        .entity-title {
            background: var(--primary-brown);
            color: var(--white);
            padding: var(--spacing-xs) var(--spacing-sm);
            margin: calc(-1 * var(--spacing-md)) calc(-1 * var(--spacing-md)) var(--spacing-sm);
            font-weight: bold;
            text-align: center;
        }
        
        .entity-field {
            font-size: var(--font-size-small);
            padding: 2px 0;
            border-bottom: 1px solid var(--beige);
        }
        
        .entity-field:last-child {
            border-bottom: none;
        }
        
        .primary-key {
            font-weight: bold;
            color: var(--primary-brown);
        }
        
        .foreign-key {
            color: var(--light-brown);
            font-style: italic;
        }
        
        .diagram-controls {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .info-section {
            background: var(--white);
            padding: var(--spacing-xl);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: var(--spacing-lg);
        }
        
        .entities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--spacing-lg);
            margin-top: var(--spacing-lg);
        }
        
        .entity-description {
            background: var(--beige);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-lg);
            border-left: 4px solid var(--primary-brown);
        }
        
        .entity-description h3 {
            color: var(--primary-brown);
            margin-bottom: var(--spacing-md);
        }
        
        .field-list {
            list-style: none;
            padding: 0;
        }
        
        .field-list li {
            padding: var(--spacing-xs) 0;
            border-bottom: 1px solid var(--white);
        }
        
        .field-list li:last-child {
            border-bottom: none;
        }
        
        @media (max-width: 768px) {
            .diagram-placeholder {
                height: 400px;
            }
            
            .entity-box {
                position: static;
                margin: var(--spacing-sm) auto;
            }
            
            .diagram-controls {
                flex-direction: column;
                align-items: center;
            }
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
                <?php if ($user_type === 'master'): ?>
                    <a href="consulta_usuarios.php">Usuários</a>
                    <a href="logs.php">Logs</a>
                <?php endif; ?>
                <?php if ($user_type === 'comum'): ?>
                    <a href="alterar_senha.php">Alterar Senha</a>
                <?php endif; ?>
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
        <!-- Cabeçalho da Página -->
        <div class="info-section fade-in">
            <h1 style="color: var(--primary-brown); margin-bottom: var(--spacing-lg); text-align: center;">
                Modelo do Banco de Dados
            </h1>
            <p style="text-align: center; color: var(--light-brown); margin-bottom: var(--spacing-lg);">
                Diagrama Entidade-Relacionamento (ER) do sistema da ONG de Noé
            </p>
        </div>
        
        <!-- Diagrama ER -->
        <div class="diagram-container fade-in">
            <div class="diagram-placeholder">
                <!-- Entidade Usuários -->
                <div class="entity-box" style="top: 50px; left: 50px;">
                    <div class="entity-title">USUÁRIOS</div>
                    <div class="entity-field primary-key">🔑 id_usuario (PK)</div>
                    <div class="entity-field">nome</div>
                    <div class="entity-field">email</div>
                    <div class="entity-field">login</div>
                    <div class="entity-field">senha</div>
                    <div class="entity-field">tipo_usuario</div>
                    <div class="entity-field">data_cadastro</div>
                    <div class="entity-field">status</div>
                </div>
                
                <!-- Entidade Dados Pessoais -->
                <div class="entity-box" style="top: 50px; right: 50px;">
                    <div class="entity-title">DADOS_PESSOAIS</div>
                    <div class="entity-field primary-key">🔑 id_dados (PK)</div>
                    <div class="entity-field foreign-key">🔗 id_usuario (FK)</div>
                    <div class="entity-field">data_nascimento</div>
                    <div class="entity-field">sexo</div>
                    <div class="entity-field">nome_materno</div>
                    <div class="entity-field">cpf</div>
                    <div class="entity-field">telefone_celular</div>
                    <div class="entity-field">telefone_fixo</div>
                </div>
                
                <!-- Entidade Endereços -->
                <div class="entity-box" style="bottom: 150px; left: 50px;">
                    <div class="entity-title">ENDERECOS</div>
                    <div class="entity-field primary-key">🔑 id_endereco (PK)</div>
                    <div class="entity-field foreign-key">🔗 id_usuario (FK)</div>
                    <div class="entity-field">cep</div>
                    <div class="entity-field">logradouro</div>
                    <div class="entity-field">numero</div>
                    <div class="entity-field">complemento</div>
                    <div class="entity-field">bairro</div>
                    <div class="entity-field">cidade</div>
                    <div class="entity-field">estado</div>
                </div>
                
                <!-- Entidade Logs de Autenticação -->
                <div class="entity-box" style="bottom: 150px; right: 50px;">
                    <div class="entity-title">LOGS_AUTENTICACAO</div>
                    <div class="entity-field primary-key">🔑 id_log (PK)</div>
                    <div class="entity-field foreign-key">🔗 id_usuario (FK)</div>
                    <div class="entity-field">data_hora</div>
                    <div class="entity-field">tipo_2fa</div>
                    <div class="entity-field">status_login</div>
                    <div class="entity-field">ip_origem</div>
                </div>
                
                <!-- Entidade Animais -->
                <div class="entity-box" style="top: 250px; left: 250px;">
                    <div class="entity-title">ANIMAIS</div>
                    <div class="entity-field primary-key">🔑 id_animal (PK)</div>
                    <div class="entity-field">nome</div>
                    <div class="entity-field">especie</div>
                    <div class="entity-field">raca</div>
                    <div class="entity-field">idade</div>
                    <div class="entity-field">sexo</div>
                    <div class="entity-field">status_adocao</div>
                    <div class="entity-field">data_resgate</div>
                </div>
                
                <!-- Entidade Adoções -->
                <div class="entity-box" style="bottom: 50px; left: 250px;">
                    <div class="entity-title">ADOCOES</div>
                    <div class="entity-field primary-key">🔑 id_adocao (PK)</div>
                    <div class="entity-field foreign-key">🔗 id_usuario (FK)</div>
                    <div class="entity-field foreign-key">🔗 id_animal (FK)</div>
                    <div class="entity-field">data_adocao</div>
                    <div class="entity-field">status</div>
                    <div class="entity-field">observacoes</div>
                </div>
            </div>
            
            <div class="diagram-controls">
                <button onclick="zoomIn()" class="btn btn-outline">
                    🔍+ Zoom In
                </button>
                <button onclick="zoomOut()" class="btn btn-outline">
                    🔍- Zoom Out
                </button>
                <button onclick="downloadDiagram()" class="btn btn-primary">
                    📥 Baixar Diagrama
                </button>
            </div>
        </div>
        
        <!-- Descrição das Entidades -->
        <div class="info-section fade-in">
            <h2 style="color: var(--primary-brown); margin-bottom: var(--spacing-lg);">
                Descrição das Entidades
            </h2>
            
            <div class="entities-grid">
                <div class="entity-description">
                    <h3>👤 USUÁRIOS</h3>
                    <p>Armazena informações básicas dos usuários do sistema (Master e Comum).</p>
                    <ul class="field-list">
                        <li><strong>id_usuario:</strong> Chave primária</li>
                        <li><strong>tipo_usuario:</strong> 'master' ou 'comum'</li>
                        <li><strong>login:</strong> 6 caracteres alfabéticos únicos</li>
                        <li><strong>senha:</strong> Criptografada, mínimo 8 caracteres</li>
                    </ul>
                </div>
                
                <div class="entity-description">
                    <h3>📋 DADOS_PESSOAIS</h3>
                    <p>Informações pessoais detalhadas dos usuários comuns.</p>
                    <ul class="field-list">
                        <li><strong>cpf:</strong> Único, formato XXX.XXX.XXX-XX</li>
                        <li><strong>nome_materno:</strong> Para autenticação 2FA</li>
                        <li><strong>data_nascimento:</strong> Para autenticação 2FA</li>
                    </ul>
                </div>
                
                <div class="entity-description">
                    <h3>🏠 ENDERECOS</h3>
                    <p>Endereços completos dos usuários.</p>
                    <ul class="field-list">
                        <li><strong>cep:</strong> Para autenticação 2FA</li>
                        <li><strong>logradouro:</strong> Rua, avenida, etc.</li>
                        <li><strong>estado:</strong> Sigla de 2 caracteres</li>
                    </ul>
                </div>
                
                <div class="entity-description">
                    <h3>📊 LOGS_AUTENTICACAO</h3>
                    <p>Registra todas as tentativas de login e 2FA.</p>
                    <ul class="field-list">
                        <li><strong>tipo_2fa:</strong> nome_materno, data_nascimento ou cep</li>
                        <li><strong>status_login:</strong> sucesso ou falha</li>
                        <li><strong>data_hora:</strong> Timestamp do acesso</li>
                    </ul>
                </div>
                
                <div class="entity-description">
                    <h3>🐕 ANIMAIS</h3>
                    <p>Cadastro dos animais disponíveis para adoção.</p>
                    <ul class="field-list">
                        <li><strong>especie:</strong> Cão, gato, etc.</li>
                        <li><strong>status_adocao:</strong> disponível, adotado, em_processo</li>
                        <li><strong>data_resgate:</strong> Quando foi resgatado</li>
                    </ul>
                </div>
                
                <div class="entity-description">
                    <h3>❤️ ADOCOES</h3>
                    <p>Relaciona usuários com animais adotados.</p>
                    <ul class="field-list">
                        <li><strong>data_adocao:</strong> Data da adoção</li>
                        <li><strong>status:</strong> ativo, cancelado</li>
                        <li><strong>observacoes:</strong> Notas sobre a adoção</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Relacionamentos -->
        <div class="info-section fade-in">
            <h2 style="color: var(--primary-brown); margin-bottom: var(--spacing-lg);">
                Relacionamentos
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--spacing-lg);">
                <div style="background: var(--beige); padding: var(--spacing-lg); border-radius: var(--border-radius-lg);">
                    <h4 style="color: var(--primary-brown);">USUÁRIOS ↔ DADOS_PESSOAIS</h4>
                    <p>Relacionamento 1:1 - Cada usuário comum possui um registro de dados pessoais.</p>
                </div>
                
                <div style="background: var(--beige); padding: var(--spacing-lg); border-radius: var(--border-radius-lg);">
                    <h4 style="color: var(--primary-brown);">USUÁRIOS ↔ ENDERECOS</h4>
                    <p>Relacionamento 1:1 - Cada usuário possui um endereço cadastrado.</p>
                </div>
                
                <div style="background: var(--beige); padding: var(--spacing-lg); border-radius: var(--border-radius-lg);">
                    <h4 style="color: var(--primary-brown);">USUÁRIOS ↔ LOGS_AUTENTICACAO</h4>
                    <p>Relacionamento 1:N - Um usuário pode ter múltiplos logs de acesso.</p>
                </div>
                
                <div style="background: var(--beige); padding: var(--spacing-lg); border-radius: var(--border-radius-lg);">
                    <h4 style="color: var(--primary-brown);">USUÁRIOS ↔ ADOCOES ↔ ANIMAIS</h4>
                    <p>Relacionamento N:M - Usuários podem adotar múltiplos animais ao longo do tempo.</p>
                </div>
            </div>
        </div>
        
        <!-- Botão para voltar -->
        <div class="text-center mt-lg">
            <a href="principal.php" class="btn btn-outline">
                ← Voltar à Página Principal
            </a>
        </div>
    </div>

    <script src="../js/main.js"></script>
    
    <script>
        let currentZoom = 1;
        
        function zoomIn() {
            currentZoom += 0.1;
            applyZoom();
        }
        
        function zoomOut() {
            if (currentZoom > 0.5) {
                currentZoom -= 0.1;
                applyZoom();
            }
        }
        
        function applyZoom() {
            const diagram = document.querySelector('.diagram-placeholder');
            diagram.style.transform = `scale(${currentZoom})`;
            diagram.style.transformOrigin = 'center center';
        }
        
        function downloadDiagram() {
            // Em um sistema real, geraria uma imagem do diagrama
            showAlert('Funcionalidade de download em desenvolvimento. O diagrama seria exportado como PNG ou PDF.', 'info');
        }
        
        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>

