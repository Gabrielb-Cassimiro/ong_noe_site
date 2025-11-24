<?php
require_once '../config/config.php';
if ($_SESSION['user_type'] !== 'master') { die('Acesso Negado'); }

// Filtros
$where = [];
$params = [];
$nome = $_GET['nome'] ?? '';
$cpf = $_GET['cpf'] ?? '';

if ($nome) { $where[] = "u.nome LIKE ?"; $params[] = "%$nome%"; }
if ($cpf)  { $where[] = "dp.cpf LIKE ?";  $params[] = "%$cpf%"; }

$sql = "SELECT l.*, u.nome, dp.cpf 
        FROM logs_autenticacao l 
        JOIN usuarios u ON l.id_usuario = u.id_usuario 
        LEFT JOIN dados_pessoais dp ON u.id_usuario = dp.id_usuario";

if (count($where) > 0) { $sql .= " WHERE " . implode(" AND ", $where); }
$sql .= " ORDER BY l.data_hora DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><title>Logs</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
    <div class="container">
        <a href="principal.php" class="btn btn-secondary">Voltar</a>
        <h2>Logs de Autenticação</h2>
        
        <div class="card">
            <form method="GET" style="display:flex; gap:10px;">
                <input type="text" name="nome" placeholder="Filtrar por Nome" value="<?= htmlspecialchars($nome) ?>">
                <input type="text" name="cpf" placeholder="Filtrar por CPF" value="<?= htmlspecialchars($cpf) ?>">
                <button type="submit" class="btn">Filtrar</button>
                <a href="logs.php" class="btn btn-secondary">Limpar</a>
            </form>
        </div>

        <table>
            <tr><th>Data</th><th>Usuário</th><th>CPF</th><th>Status</th><th>2FA</th></tr>
            <?php foreach($logs as $log): ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($log['data_hora'])) ?></td>
                <td><?= htmlspecialchars($log['nome']) ?></td>
                <td><?= htmlspecialchars($log['cpf'] ?? '-') ?></td>
                <td><?= $log['status_login'] ?></td>
                <td><?= $log['tipo_2fa'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>