<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--beige) 0%, var(--light-brown) 100%);
        }
        
        .error-card {
            background: var(--white);
            padding: var(--spacing-xxl);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        
        .error-icon {
            width: 100px;
            height: 100px;
            background: var(--error-red);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--spacing-lg);
            font-size: 3rem;
            color: var(--white);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .error-code {
            font-size: var(--font-size-xxl);
            font-weight: bold;
            color: var(--error-red);
            margin-bottom: var(--spacing-md);
        }
        
        .error-title {
            font-size: var(--font-size-xl);
            color: var(--dark-brown);
            margin-bottom: var(--spacing-lg);
        }
        
        .error-description {
            color: var(--light-brown);
            margin-bottom: var(--spacing-xl);
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: var(--spacing-md);
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .error-details {
            background: var(--beige);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius);
            margin-top: var(--spacing-lg);
            text-align: left;
            font-size: var(--font-size-small);
            color: var(--dark-brown);
        }
        
        .error-details summary {
            cursor: pointer;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
        }
        
        .error-details code {
            background: var(--white);
            padding: 2px 4px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        
        @media (max-width: 480px) {
            .error-actions {
                flex-direction: column;
            }
            
            .error-card {
                padding: var(--spacing-lg);
            }
            
            .error-icon {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card fade-in">
            <div class="error-icon">
                ⚠️
            </div>
            
            <div class="error-code">
                <?php 
                $error_code = $_GET['code'] ?? '500';
                echo htmlspecialchars($error_code);
                ?>
            </div>
            
            <h1 class="error-title">
                <?php
                $error_titles = [
                    '400' => 'Requisição Inválida',
                    '401' => 'Não Autorizado',
                    '403' => 'Acesso Negado',
                    '404' => 'Página Não Encontrada',
                    '500' => 'Erro Interno do Servidor',
                    '503' => 'Serviço Indisponível'
                ];
                
                echo $error_titles[$error_code] ?? 'Erro Desconhecido';
                ?>
            </h1>
            
            <p class="error-description">
                <?php
                $error_descriptions = [
                    '400' => 'A requisição enviada contém dados inválidos ou malformados.',
                    '401' => 'Você precisa fazer login para acessar esta página.',
                    '403' => 'Você não tem permissão para acessar este recurso.',
                    '404' => 'A página que você está procurando não foi encontrada.',
                    '500' => 'Ocorreu um erro interno no servidor. Tente novamente mais tarde.',
                    '503' => 'O serviço está temporariamente indisponível.'
                ];
                
                echo $error_descriptions[$error_code] ?? 'Ocorreu um erro inesperado no sistema.';
                ?>
            </p>
            
            <div class="error-actions">
                <button onclick="history.back()" class="btn btn-outline">
                    ← Voltar
                </button>
                <button onclick="window.location.reload()" class="btn btn-secondary">
                    🔄 Tentar Novamente
                </button>
                <a href="../pages/principal.php" class="btn btn-primary">
                    🏠 Página Inicial
                </a>
            </div>
            
            <!-- Detalhes técnicos (apenas para desenvolvimento) -->
            <details class="error-details">
                <summary>Detalhes Técnicos</summary>
                <p><strong>Código do Erro:</strong> <code><?php echo htmlspecialchars($error_code); ?></code></p>
                <p><strong>Timestamp:</strong> <code><?php echo date('Y-m-d H:i:s'); ?></code></p>
                <p><strong>URL Solicitada:</strong> <code><?php echo htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A'); ?></code></p>
                <p><strong>Método HTTP:</strong> <code><?php echo htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'N/A'); ?></code></p>
                <p><strong>User Agent:</strong> <code><?php echo htmlspecialchars(substr($_SERVER['HTTP_USER_AGENT'] ?? 'N/A', 0, 100)); ?>...</code></p>
                
                <?php if (isset($_GET['message'])): ?>
                    <p><strong>Mensagem:</strong> <code><?php echo htmlspecialchars($_GET['message']); ?></code></p>
                <?php endif; ?>
                
                <hr style="margin: var(--spacing-md) 0;">
                <p><strong>Sugestões:</strong></p>
                <ul style="margin-left: var(--spacing-lg);">
                    <li>Verifique se a URL está correta</li>
                    <li>Tente fazer login novamente</li>
                    <li>Limpe o cache do navegador</li>
                    <li>Entre em contato com o suporte se o problema persistir</li>
                </ul>
            </details>
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
        // Log do erro para análise (em produção, enviaria para um serviço de monitoramento)
        console.error('Erro da aplicação:', {
            code: '<?php echo $error_code; ?>',
            url: window.location.href,
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent
        });
        
        // Redireciona automaticamente após 30 segundos se for erro 404
        <?php if ($error_code === '404'): ?>
        setTimeout(() => {
            if (confirm('Página não encontrada. Deseja ser redirecionado para a página inicial?')) {
                window.location.href = '../pages/principal.php';
            }
        }, 30000);
        <?php endif; ?>
        
        // Função para reportar erro (simulada)
        function reportError() {
            showAlert('Erro reportado com sucesso! Nossa equipe foi notificada.', 'success');
        }
        
        // Adiciona botão de reportar erro para erros 500
        <?php if ($error_code === '500'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const actions = document.querySelector('.error-actions');
            const reportBtn = document.createElement('button');
            reportBtn.className = 'btn btn-error';
            reportBtn.textContent = '📧 Reportar Erro';
            reportBtn.onclick = reportError;
            actions.appendChild(reportBtn);
        });
        <?php endif; ?>
    </script>
</body>
</html>

