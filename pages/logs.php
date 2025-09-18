<?php
session_start();

// Verifica se o usuário está logado e é Master
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'master') {
    header('Location: login.php');
    exit();
}

// Verifica se passou pelo 2FA
if (!isset($_SESSION['2fa_verified']) || $_SESSION['2fa_verified'] !== true) {
    header('Location: 2fa.php');
    exit();
}

// Dados simulados de logs de autenticação
$logs = [
    [
        'id' => 1,
        'data_hora' => '2024-08-27 14:30:25',
        'nome' => 'João Silva Santos',
        'cpf' => '123.456.789-01',
        'login' => 'joao01',
        'tipo_2fa' => 'nome_materno',
        'status' => 'sucesso'
    ],
    [
        'id' => 2,
        'data_hora' => '2024-08-27 13:15:42',
        'nome' => 'Maria Oliveira Costa',
        'cpf' => '987.654.321-02',
        'login' => 'maria02',
        'tipo_2fa' => 'data_nascimento',
        'status' => 'sucesso'
    ],
    [
        'id' => 3,
        'data_hora' => '2024-08-27 12:45:18',
        'nome' => 'Pedro Santos Almeida',
        'cpf' => '456.789.123-03',
        'login' => 'pedro03',
        'tipo_2fa' => 'cep',
        'status' => 'falha'
    ],
    [
        'id' => 4,
        'data_hora' => '2024-08-27 11:20:33',
        'nome' => 'Ana Carolina Ferreira',
        'cpf' => '789.123.456-04',
        'login' => 'ana004',
        'tipo_2fa' => 'nome_materno',
        'status' => 'sucesso'
    ],
    [
        'id' => 5,
        'data_hora' => '2024-08-27 10:55:07',
        'nome' => 'Carlos Eduardo Lima',
        'cpf' => '321.654.987-05',
        'login' => 'carlos',
        'tipo_2fa' => 'data_nascimento',
        'status' => 'sucesso'
    ],
    [
        'id' => 6,
        'data_hora' => '2024-08-26 16:40:12',
        'nome' => 'João Silva Santos',
        'cpf' => '123.456.789-01',
        'login' => 'joao01',
        'tipo_2fa' => 'cep',
        'status' => 'falha'
    ]
];

// Filtros
$filtro_nome = trim($_GET['nome'] ?? '');
$filtro_cpf = trim($_GET['cpf'] ?? '');
$filtro_tipo = $_GET['tipo'] ?? 'todos';

$logs_filtrados = $logs;

// Aplica filtros
if (!empty($filtro_nome)) {
    $logs_filtrados = array_filter($logs_filtrados, function($log) use ($filtro_nome) {
        return stripos($log['nome'], $filtro_nome) !== false;
    });
}

if (!empty($filtro_cpf)) {
    $logs_filtrados = array_filter($logs_filtrados, function($log) use ($filtro_cpf) {
        return stripos($log['cpf'], $filtro_cpf) !== false;
    });
}

if ($filtro_tipo !== 'todos') {
    $logs_filtrados = array_filter($logs_filtrados, function($log) use ($filtro_tipo) {
        return $log['status'] === $filtro_tipo;
    });
}

// Ordena por data/hora mais recente
usort($logs_filtrados, function($a, $b) {
    return strtotime($b['data_hora']) - strtotime($a['data_hora']);
});

function formatarTipo2FA($tipo) {
    switch($tipo) {
        case 'nome_materno':
            return 'Nome da Mãe';
        case 'data_nascimento':
            return 'Data de Nascimento';
        case 'cep':
            return 'CEP';
        default:
            return ucfirst($tipo);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Autenticação - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .logs-header {
            background: var(--primary-brown);
            color: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .filters-box {
            background: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: var(--spacing-lg);
        }
        
        .filters-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: var(--spacing-md);
            align-items: end;
        }
        
        .logs-table-container {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .status-badge {
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--border-radius);
            font-size: var(--font-size-small);
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-sucesso {
            background: rgba(139, 195, 74, 0.2);
            color: #2E7D32;
        }
        
        .status-falha {
            background: rgba(229, 115, 115, 0.2);
            color: #C62828;
        }
        
        .export-section {
            background: var(--beige);
            padding: var(--spacing-lg);
            text-align: center;
            border-top: 1px solid var(--light-brown);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }
        
        .stat-card {
            background: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-lg);
            text-align: center;
            box-shadow: var(--shadow-sm);
        }
        
        .stat-number {
            font-size: var(--font-size-xxl);
            font-weight: bold;
            color: var(--primary-brown);
        }
        
        .stat-label {
            color: var(--light-brown);
            font-size: var(--font-size-small);
            margin-top: var(--spacing-xs);
        }
        
        @media (max-width: 768px) {
            .filters-form {
                grid-template-columns: 1fr;
            }
            
            .table {
                font-size: var(--font-size-small);
            }
            
            .table th,
            .table td {
                padding: var(--spacing-sm);
            }
            
            .stats-grid {
                grid-template-columns: 1fr 1fr;
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
                <a href="consulta_usuarios.php">Usuários</a>
                <a href="logs.php">Logs</a>
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
        <div class="logs-header fade-in">
            <h1 style="margin-bottom: var(--spacing-sm);">Logs de Autenticação</h1>
            <p>Consulte o histórico de acessos e tentativas de autenticação no sistema</p>
        </div>
        
        <!-- Estatísticas -->
        <div class="stats-grid fade-in">
            <?php
            $total_logs = count($logs_filtrados);
            $sucessos = count(array_filter($logs_filtrados, function($log) { return $log['status'] === 'sucesso'; }));
            $falhas = count(array_filter($logs_filtrados, function($log) { return $log['status'] === 'falha'; }));
            $taxa_sucesso = $total_logs > 0 ? round(($sucessos / $total_logs) * 100, 1) : 0;
            ?>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_logs; ?></div>
                <div class="stat-label">Total de Logs</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number" style="color: var(--success-green);"><?php echo $sucessos; ?></div>
                <div class="stat-label">Sucessos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number" style="color: var(--error-red);"><?php echo $falhas; ?></div>
                <div class="stat-label">Falhas</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number" style="color: var(--primary-brown);"><?php echo $taxa_sucesso; ?>%</div>
                <div class="stat-label">Taxa de Sucesso</div>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="filters-box fade-in">
            <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="filters-form">
                <div>
                    <label for="nome" class="form-label">Nome do Usuário</label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        class="form-input" 
                        placeholder="Digite o nome..."
                        value="<?php echo htmlspecialchars($filtro_nome); ?>"
                    >
                </div>
                
                <div>
                    <label for="cpf" class="form-label">CPF</label>
                    <input 
                        type="text" 
                        id="cpf" 
                        name="cpf" 
                        class="form-input" 
                        placeholder="Digite o CPF..."
                        value="<?php echo htmlspecialchars($filtro_cpf); ?>"
                    >
                </div>
                
                <div>
                    <label for="tipo" class="form-label">Status</label>
                    <select id="tipo" name="tipo" class="form-input">
                        <option value="todos" <?php echo $filtro_tipo === 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="sucesso" <?php echo $filtro_tipo === 'sucesso' ? 'selected' : ''; ?>>Sucesso</option>
                        <option value="falha" <?php echo $filtro_tipo === 'falha' ? 'selected' : ''; ?>>Falha</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: var(--spacing-sm);">
                    <button type="submit" class="btn btn-primary">
                        🔍 Filtrar
                    </button>
                    <a href="logs.php" class="btn btn-outline">
                        Limpar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Tabela de Logs -->
        <div class="logs-table-container fade-in">
            <?php if (!empty($logs_filtrados)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Nome do Usuário</th>
                            <th>CPF</th>
                            <th>Login</th>
                            <th>Tipo 2FA</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs_filtrados as $log): ?>
                            <tr>
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($log['data_hora'])); ?></strong><br>
                                    <small style="color: var(--light-brown);">
                                        <?php echo date('H:i:s', strtotime($log['data_hora'])); ?>
                                    </small>
                                </td>
                                <td><?php echo htmlspecialchars($log['nome']); ?></td>
                                <td><?php echo htmlspecialchars($log['cpf']); ?></td>
                                <td><code><?php echo htmlspecialchars($log['login']); ?></code></td>
                                <td><?php echo formatarTipo2FA($log['tipo_2fa']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $log['status']; ?>">
                                        <?php echo $log['status'] === 'sucesso' ? '✅ Sucesso' : '❌ Falha'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Seção de Exportação -->
                <div class="export-section">
                    <p style="margin-bottom: var(--spacing-md);">
                        <strong>Exportar dados dos logs filtrados</strong>
                    </p>
                    <button onclick="exportarPDF()" class="btn btn-success">
                        📄 Baixar PDF
                    </button>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: var(--spacing-xxl); color: var(--light-brown);">
                    <h3>Nenhum log encontrado</h3>
                    <p>Não foram encontrados logs com os filtros aplicados</p>
                </div>
            <?php endif; ?>
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
        function exportarPDF() {
            // Em um sistema real, faria uma requisição para gerar o PDF
            showAlert('Funcionalidade de exportação em desenvolvimento. PDF seria gerado com os logs filtrados.', 'info');
        }
        
        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = 'logout.php';
            }
        }
        
        // Aplica máscara no campo CPF
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });
    </script>
</body>
</html>

