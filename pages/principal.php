<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user_name'] ?? 'Usuário';
$user_type = $_SESSION['user_type'] ?? 'comum';
$user_login = $_SESSION['user_login'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal - ONG de Noé</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                <a href="#sobre">Sobre Nós</a>
                <a href="#animais">Nossos Animais</a>
                <a href="#loja">Loja</a>
                <a href="#contato">Contato</a>
                
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
                    Olá, <?php echo htmlspecialchars($user_name); ?> (<?php echo htmlspecialchars($user_login); ?>)
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

    <!-- Conteúdo Principal -->
    <div class="layout layout-with-sidebar">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title">Informações do Usuário</h3>
            <div style="text-align: center; margin-bottom: var(--spacing-lg);">
                <div style="width: 60px; height: 60px; background: var(--primary-brown); border-radius: 50%; margin: 0 auto var(--spacing-md); display: flex; align-items: center; justify-content: center; color: var(--white); font-size: var(--font-size-xl); font-weight: bold;">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <strong><?php echo htmlspecialchars($user_name); ?></strong>
                <br>
                <small style="color: var(--light-brown);">
                    <?php echo $user_type === 'master' ? 'Usuário Master' : 'Usuário Comum'; ?>
                </small>
            </div>
            
            <a href="principal.php" class="sidebar-item active">Meu Perfil</a>
            <a href="#" class="sidebar-item">Minhas Adoções</a>
            <a href="#" class="sidebar-item">Configurações</a>
            
            <?php if ($user_type === 'master'): ?>
                <hr style="margin: var(--spacing-lg) 0; border: 1px solid var(--beige);">
                <a href="consulta_usuarios.php" class="sidebar-item">Gerenciar Usuários</a>
                <a href="logs.php" class="sidebar-item">Logs do Sistema</a>
            <?php endif; ?>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="container">
            <h1 style="color: var(--primary-brown); margin-bottom: var(--spacing-xl);">
                Produtos para Animais
            </h1>
            
            <!-- Grid de Produtos -->
            <div class="products-grid">
                <div class="product-card fade-in">
                    <img src="../images/produto1.jpg" alt="Brinquedo para Cães" class="product-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjVGNURDIi8+Cjx0ZXh0IHg9IjE1MCIgeT0iMTAwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjNUM0MDMzIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiPkJyaW5xdWVkbyBwYXJhIEPDo2VzPC90ZXh0Pgo8L3N2Zz4K'">
                    <div class="product-info">
                        <h3 class="product-title">Corda Colorida para Cães</h3>
                        <p class="product-description">Brinquedo resistente feito com materiais naturais, perfeito para brincadeiras e exercícios.</p>
                        <button class="btn btn-primary">Ver Produto</button>
                    </div>
                </div>
                
                <div class="product-card fade-in">
                    <img src="../images/produto2.jpg" alt="Ração Premium" class="product-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjVGNURDIi8+Cjx0ZXh0IHg9IjE1MCIgeT0iMTAwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjNUM0MDMzIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiPlJhw6fDo28gUHJlbWl1bTwvdGV4dD4KPC9zdmc+Cg=='">
                    <div class="product-info">
                        <h3 class="product-title">Ração Premium para Cães</h3>
                        <p class="product-description">Alimento completo e balanceado, rico em nutrientes essenciais para a saúde do seu pet.</p>
                        <button class="btn btn-primary">Ver Produto</button>
                    </div>
                </div>
                
                <div class="product-card fade-in">
                    <img src="../images/produto3.jpg" alt="Casinha para Gatos" class="product-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjVGNURDIi8+Cjx0ZXh0IHg9IjE1MCIgeT0iMTAwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjNUM0MDMzIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiPkNhc2luaGEgcGFyYSBHYXRvczwvdGV4dD4KPC9zdmc+Cg=='">
                    <div class="product-info">
                        <h3 class="product-title">Casinha Aconchegante</h3>
                        <p class="product-description">Abrigo confortável e seguro, ideal para gatos que gostam de se esconder e descansar.</p>
                        <button class="btn btn-primary">Ver Produto</button>
                    </div>
                </div>
                
                <div class="product-card fade-in">
                    <img src="../images/produto4.jpg" alt="Coleira Personalizada" class="product-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjVGNURDIi8+Cjx0ZXh0IHg9IjE1MCIgeT0iMTAwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjNUM0MDMzIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiPkNvbGVpcmEgUGVyc29uYWxpemFkYTwvdGV4dD4KPC9zdmc+Cg=='">
                    <div class="product-info">
                        <h3 class="product-title">Coleira Personalizada</h3>
                        <p class="product-description">Coleira ajustável com placa de identificação personalizada para a segurança do seu pet.</p>
                        <button class="btn btn-primary">Ver Produto</button>
                    </div>
                </div>
                
                <div class="product-card fade-in">
                    <img src="../images/produto5.jpg" alt="Kit Higiene" class="product-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjVGNURDIi8+Cjx0ZXh0IHg9IjE1MCIgeT0iMTAwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjNUM0MDMzIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiPktpdCBIaWdpZW5lPC90ZXh0Pgo8L3N2Zz4K'">
                    <div class="product-info">
                        <h3 class="product-title">Kit Higiene Completo</h3>
                        <p class="product-description">Conjunto completo para cuidados de higiene: shampoo, escova, cortador de unhas e mais.</p>
                        <button class="btn btn-primary">Ver Produto</button>
                    </div>
                </div>
                
                <div class="product-card fade-in">
                    <img src="../images/produto6.jpg" alt="Petisco Natural" class="product-image" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDMwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjVGNURDIi8+Cjx0ZXh0IHg9IjE1MCIgeT0iMTAwIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmaWxsPSIjNUM0MDMzIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTYiPlBldGlzY28gTmF0dXJhbDwvdGV4dD4KPC9zdmc+Cg=='">
                    <div class="product-info">
                        <h3 class="product-title">Petisco Natural</h3>
                        <p class="product-description">Snacks saudáveis e naturais, sem conservantes artificiais, perfeitos para recompensar seu pet.</p>
                        <button class="btn btn-primary">Ver Produto</button>
                    </div>
                </div>
            </div>
            
            <!-- Seção Sobre a ONG -->
            <section id="sobre" class="card" style="margin-top: var(--spacing-xxl);">
                <h2 style="color: var(--primary-brown); margin-bottom: var(--spacing-lg);">Sobre a ONG de Noé</h2>
                <p style="margin-bottom: var(--spacing-md);">
                    A ONG de Noé é uma organização dedicada ao resgate, cuidado e adoção responsável de animais em situação de vulnerabilidade. 
                    Localizada em Campo Grande, RJ, nossa missão é proporcionar uma segunda chance para cães e gatos abandonados.
                </p>
                <p style="margin-bottom: var(--spacing-md);">
                    Além do trabalho de resgate e adoção, desenvolvemos uma loja online com produtos especialmente selecionados 
                    para o bem-estar dos animais. Todos os recursos arrecadados são reinvestidos em nossos projetos de proteção animal.
                </p>
                <div style="display: flex; gap: var(--spacing-md); flex-wrap: wrap;">
                    <div class="btn btn-success">✅ Mais de 500 animais resgatados</div>
                    <div class="btn btn-success">✅ 300+ adoções realizadas</div>
                    <div class="btn btn-success">✅ Atendimento veterinário gratuito</div>
                </div>
            </section>
        </main>
    </div>

    <script src="../js/main.js"></script>
    
    <script>
        // Função específica para logout
        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>

