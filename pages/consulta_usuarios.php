<?php
require_once '../config/config.php';
if ($_SESSION['user_type'] !== 'master') { die('Acesso Negado'); }

// Exclusão
if (isset($_GET['excluir'])) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ? AND tipo_usuario = 'comum'");
    $stmt->execute([$_GET['excluir']]);
}

// Pesquisa
$busca = $_GET['busca'] ?? '';
$sql = "SELECT * FROM usuarios WHERE tipo_usuario = 'comum' AND nome LIKE ?";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$busca%"]);
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consulta Usuários</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <a href="principal.php" class="btn btn-secondary">Voltar</a>
        <h2>Consulta de Usuários Comuns</h2>
        
        <div class="card">
            <form method="GET">
                <div class="form-group">
                    <input type="text" name="busca" placeholder="Pesquisar por parte do nome..." value="<?= htmlspecialchars($busca) ?>">
                </div>
                <button type="submit" class="btn">Pesquisar</button>
            </form>
        </div>

        <button onclick="window.print()" class="btn btn-secondary">Baixar Lista (PDF)</button> <table>
            <thead><tr><th>ID</th><th>Nome</th><th>Login</th><th>Email</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id_usuario'] ?></td>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['login']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <a href="?excluir=<?= $u['id_usuario'] ?>" class="btn btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>