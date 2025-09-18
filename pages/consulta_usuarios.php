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

// Dados simulados de usuários comuns
$usuarios = [
    [
        'id' => 1,
        'nome' => 'João Silva Santos',
        'email' => 'joao.silva@email.com',
        'login' => 'joao01',
        'data_cadastro' => '2024-01-15',
        'status' => 'ativo'
    ],
    [
        'id' => 2,
        'nome' => 'Maria Oliveira Costa',
        'email' => 'maria.oliveira@email.com',
        'login' => 'maria02',
        'data_cadastro' => '2024-02-20',
        'status' => 'ativo'
    ],
    [
        'id' => 3,
        'nome' => 'Pedro Santos Almeida',
        'email' => 'pedro.santos@email.com',
        'login' => 'pedro03',
        'data_cadastro' => '2024-03-10',
        'status' => 'ativo'
    ],
    [
        'id' => 4,
        'nome' => 'Ana Carolina Ferreira',
        'email' => 'ana.ferreira@email.com',
        'login' => 'ana004',
        'data_cadastro' => '2024-03-25',
        'status' => 'ativo'
    ],
    [
        'id' => 5,
        'nome' => 'Carlos Eduardo Lima',
        'email' => 'carlos.lima@email.com',
        'login' => 'carlos',
        'data_cadastro' => '2024-04-05',
        'status' => 'ativo'
    ]
];

// Filtro de pesquisa
$search = trim($_GET['search'] ?? '');
$usuarios_filtrados = $usuarios;

if (!empty($search)) {
    $usuarios_filtrados = array_filter($usuarios, function($usuario) use ($search) {
        return stripos($usuario['nome'], $search) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Usuários - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-header {
            background: var(--primary-brown);
            color: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-lg);
            margin-bottom: var(--spacing-xl);
        }
        
        .search-box {
            background: var(--white);
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: var(--spacing-lg);
        }
        
        .search-form {
            display: flex;
            gap: var(--spacing-md);
            align-items: end;
        }
        
        .search-input {
            flex: 1;
        }
        
        .users-table-container {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .action-btn {
            padding: var(--spacing-xs) var(--spacing-sm);
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: var(--font-size-small);
            transition: all 0.3s ease;
        }
        
        .btn-delete {
            background: var(--error-red);
            color: var(--white);
        }
        
        .btn-delete:hover {
            background: #C62828;
            transform: translateY(-1px);
        }
        
        .no-results {
            text-align: center;
            padding: var(--spacing-xxl);
            color: var(--light-brown);
        }
        
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }
            
            .table {
                font-size: var(--font-size-small);
            }
            
            .table th,
            .table td {
                padding: var(--spacing-sm);
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
        <div class="admin-header fade-in">
            <h1 style="margin-bottom: var(--spacing-sm);">Gerenciamento de Usuários</h1>
            <p>Consulte, pesquise e gerencie usuários comuns do sistema</p>
        </div>
        
        <!-- Caixa de Pesquisa -->
        <div class="search-box fade-in">
            <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="search-form">
                <div class="search-input">
                    <label for="search" class="form-label">Pesquisar por nome</label>
                    <input 
                        type="text" 
                        id="search" 
                        name="search" 
                        class="form-input" 
                        placeholder="Digite parte do nome do usuário..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >
                </div>
                <button type="submit" class="btn btn-primary">
                    🔍 Pesquisar
                </button>
                <?php if (!empty($search)): ?>
                    <a href="consulta_usuarios.php" class="btn btn-outline">
                        Limpar
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Tabela de Usuários -->
        <div class="users-table-container fade-in">
            <?php if (!empty($usuarios_filtrados)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Login</th>
                            <th>Data de Cadastro</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios_filtrados as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['login']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?></td>
                                <td>
                                    <span style="color: var(--success-green); font-weight: 600;">
                                        ✅ <?php echo ucfirst($usuario['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button 
                                        class="action-btn btn-delete" 
                                        onclick="confirmarExclusao(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nome']); ?>')"
                                        title="Excluir usuário"
                                    >
                                        🗑️ Excluir
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="padding: var(--spacing-lg); text-align: center; background: var(--beige); color: var(--dark-brown);">
                    <strong>Total de usuários encontrados: <?php echo count($usuarios_filtrados); ?></strong>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <h3>Nenhum usuário encontrado</h3>
                    <p>
                        <?php if (!empty($search)): ?>
                            Não foram encontrados usuários com o termo "<?php echo htmlspecialchars($search); ?>"
                        <?php else: ?>
                            Não há usuários cadastrados no sistema
                        <?php endif; ?>
                    </p>
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
    
    <!-- Modal de Confirmação -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 style="color: var(--primary-brown); margin-bottom: var(--spacing-lg);">
                Confirmar Exclusão
            </h3>
            <p id="confirmMessage" style="margin-bottom: var(--spacing-lg);"></p>
            <div style="display: flex; gap: var(--spacing-md); justify-content: flex-end;">
                <button class="btn btn-outline" data-modal-close>
                    Cancelar
                </button>
                <button id="confirmDelete" class="btn btn-error">
                    Confirmar Exclusão
                </button>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
    
    <script>
        let userToDelete = null;
        
        function confirmarExclusao(userId, userName) {
            userToDelete = userId;
            document.getElementById('confirmMessage').textContent = 
                `Tem certeza que deseja excluir o usuário "${userName}"? Esta ação não pode ser desfeita.`;
            openModal('confirmModal');
        }
        
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (userToDelete) {
                // Em um sistema real, faria uma requisição AJAX para excluir
                showAlert(`Usuário ID ${userToDelete} excluído com sucesso!`, 'success');
                
                // Remove a linha da tabela
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    if (row.cells[0].textContent == userToDelete) {
                        row.remove();
                    }
                });
                
                closeModal('confirmModal');
                userToDelete = null;
                
                // Atualiza contador
                const remainingRows = document.querySelectorAll('tbody tr').length;
                const totalElement = document.querySelector('div[style*="Total de usuários"] strong');
                if (totalElement) {
                    totalElement.textContent = `Total de usuários encontrados: ${remainingRows}`;
                }
            }
        });
        
        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>

