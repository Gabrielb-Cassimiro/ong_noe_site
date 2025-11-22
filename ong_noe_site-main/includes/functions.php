<?php
/*
 * Funções Auxiliares do Sistema
 * ONG de Noé - Sistema de Gestão
 */

require_once __DIR__ . '/../config/database.php';

/*
 * Inicia sessão segura se não estiver ativa
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
        
        // Regenera ID da sessão periodicamente para segurança
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutos
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
}

/**
 * Verifica se o usuário está logado
 */
function isLoggedIn() {
    startSecureSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['user_login']);
}

/**
 * Verifica se o usuário passou pelo 2FA (não mexe mais aqui q eu não sei refazer,só funcionou...)
 */
function is2FAVerified() {
    startSecureSession();
    return isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] === true;
}

/**
 * Verifica se o usuário é Master
 */
function isMasterUser() {
    startSecureSession();
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'master';
}

/**
 * Redireciona para página de login se não estiver autenticado
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Redireciona para 2FA se não estiver verificado (não mexe)
 */
function require2FA() {
    requireLogin();
    if (!is2FAVerified()) {
        header('Location: 2fa.php');
        exit();
    }
}

/**
 * Requer permissão de usuário Master
 */
function requireMasterUser() {
    require2FA();
    if (!isMasterUser()) {
        header('Location: erro.php?code=403&message=Acesso negado');
        exit();
    }
}

/**
 * Requer permissão de usuário Comum
 */
function requireCommonUser() {
    require2FA();
    if (isMasterUser()) {
        header('Location: erro.php?code=403&message=Acesso negado');
        exit();
    }
}

/**
 * Formata CPF para exibição
 */
function formatCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) === 11) {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    return $cpf;
}

/**
 * Formata telefone para exibição
 */
function formatPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 11) {
        return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
    } elseif (strlen($phone) === 10) {
        return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6, 4);
    }
    return $phone;
}

/**
 * Formata CEP para exibição
 */
function formatCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    if (strlen($cep) === 8) {
        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }
    return $cep;
}

/**
 * Formata data para exibição brasileira
 */
function formatDate($date, $includeTime = false) {
    if (empty($date)) return '';
    
    $timestamp = is_string($date) ? strtotime($date) : $date;
    
    if ($includeTime) {
        return date('d/m/Y H:i:s', $timestamp);
    } else {
        return date('d/m/Y', $timestamp);
    }
}

/**
 * Calcula idade a partir da data de nascimento
 */
function calculateAge($birthDate) {
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    return $birth->diff($today)->y;
}

/**
 * Valida dados de entrada
 */
function validateInput($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = isset($data[$field]) ? trim($data[$field]) : '';
        
        // Campo obrigatório
        if (isset($rule['required']) && $rule['required'] && empty($value)) {
            $errors[$field] = "Campo {$field} é obrigatório";
            continue;
        }
        
        // Se campo não é obrigatório e está vazio, pula validação
        if (empty($value) && (!isset($rule['required']) || !$rule['required'])) {
            continue;
        }
        
        // Validação de tamanho mínimo
        if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
            $errors[$field] = "Campo {$field} deve ter pelo menos {$rule['min_length']} caracteres";
        }
        
        // Validação de tamanho máximo
        if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
            $errors[$field] = "Campo {$field} deve ter no máximo {$rule['max_length']} caracteres";
        }
        
        // Validação de email
        if (isset($rule['email']) && $rule['email'] && !Security::validateEmail($value)) {
            $errors[$field] = "Campo {$field} deve ser um email válido";
        }
        
        // Validação de CPF
        if (isset($rule['cpf']) && $rule['cpf'] && !Security::validateCPF($value)) {
            $errors[$field] = "Campo {$field} deve ser um CPF válido";
        }
        
        // Validação de padrão regex
        if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
            $message = isset($rule['pattern_message']) ? $rule['pattern_message'] : "Campo {$field} não atende ao padrão exigido";
            $errors[$field] = $message;
        }
        
        // Validação customizada
        if (isset($rule['custom']) && is_callable($rule['custom'])) {
            $customResult = $rule['custom']($value);
            if ($customResult !== true) {
                $errors[$field] = $customResult;
            }
        }
    }
    
    return $errors;
}

/**
 * Gera pergunta aleatória para 2FA (não mexe)
 */
function generate2FAQuestion($userData) {
    $questions = [
        'nome_materno' => [
            'question' => 'Qual o nome de solteira da sua mãe?',
            'answer' => strtolower(trim($userData['nome_materno'] ?? ''))
        ],
        'data_nascimento' => [
            'question' => 'Qual a sua data de nascimento (DD/MM/AAAA)?',
            'answer' => isset($userData['data_nascimento']) ? 
                       date('d/m/Y', strtotime($userData['data_nascimento'])) : ''
        ],
        'cep' => [
            'question' => 'Qual o seu CEP?',
            'answer' => $userData['cep'] ?? ''
        ]
    ];
    
    $questionKey = array_rand($questions);
    return [
        'key' => $questionKey,
        'question' => $questions[$questionKey]['question'],
        'answer' => $questions[$questionKey]['answer']
    ];
}

/**
 * Verifica resposta do 2FA (não mexe)
 */
function verify2FAAnswer($questionKey, $userAnswer, $correctAnswer) {
    $userAnswer = strtolower(trim($userAnswer));
    $correctAnswer = strtolower(trim($correctAnswer));
    
    // Para data de nascimento, aceita diferentes formatos
    if ($questionKey === 'data_nascimento') {
        // Remove separadores e compara apenas números
        $userAnswer = preg_replace('/[^0-9]/', '', $userAnswer);
        $correctAnswer = preg_replace('/[^0-9]/', '', $correctAnswer);
    }
    
    return $userAnswer === $correctAnswer;
}

/**
 * Envia email (simulado em produção usaria um serviço real)
 */
function sendEmail($to, $subject, $message, $from = 'noreply@ongdenoe.org') {
    // Em produção, implementaria envio real de email
    // Por enquanto, apenas registra em log
    error_log("Email enviado para {$to}: {$subject}");
    return true;
}

/**
 * Gera relatório em PDF (simulado)
 */
function generatePDFReport($data, $title, $filename) {
    // Em produção, usaria uma biblioteca como TCPDF ou FPDF
    // Por enquanto, apenas simula a geração
    $content = "Relatório: {$title}\n";
    $content .= "Gerado em: " . date('d/m/Y H:i:s') . "\n\n";
    
    foreach ($data as $item) {
        $content .= print_r($item, true) . "\n";
    }
    
    // Simula salvamento do arquivo
    file_put_contents("/tmp/{$filename}", $content);
    return "/tmp/{$filename}";
}

/**
 * Limpa dados antigos do sistema
 */
function cleanupOldData() {
    try {
        $db = Database::getInstance();
        
        // Remove logs de autenticação mais antigos que 90 dias
        $db->query(
            "DELETE FROM logs_autenticacao WHERE data_hora < DATE_SUB(NOW(), INTERVAL 90 DAY)"
        );
        
        // Remove logs de ações mais antigos que 30 dias
        $db->query(
            "DELETE FROM logs_acoes WHERE data_hora < DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
        
        return true;
    } catch (Exception $e) {
        error_log("Erro na limpeza de dados antigos: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtém estatísticas do sistema
 */
function getSystemStats() {
    try {
        $db = Database::getInstance();
        
        $stats = [];
        
        // Total de usuários
        $result = $db->fetchOne("SELECT COUNT(*) as total FROM usuarios WHERE status = 'ativo'");
        $stats['total_usuarios'] = $result['total'] ?? 0;
        
        // Total de animais
        $result = $db->fetchOne("SELECT COUNT(*) as total FROM animais");
        $stats['total_animais'] = $result['total'] ?? 0;
        
        // Total de adoções
        $result = $db->fetchOne("SELECT COUNT(*) as total FROM adocoes WHERE status = 'ativo'");
        $stats['total_adocoes'] = $result['total'] ?? 0;
        
        // Logins hoje
        $result = $db->fetchOne(
            "SELECT COUNT(*) as total FROM logs_autenticacao 
             WHERE DATE(data_hora) = CURDATE() AND status_login = 'sucesso'"
        );
        $stats['logins_hoje'] = $result['total'] ?? 0;
        
        return $stats;
    } catch (Exception $e) {
        error_log("Erro ao obter estatísticas: " . $e->getMessage());
        return [];
    }
}

/**
 * Registra atividade do usuário
 */
function logActivity($action, $details = null) {
    if (isLoggedIn()) {
        Logger::logUserAction($_SESSION['user_id'], $action, $details);
    }
}

/**
 * Obtém informações do usuário atual
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $db = Database::getInstance();
        return $db->fetchOne(
            "SELECT u.*, dp.cpf, dp.nome_materno, dp.data_nascimento, e.cep 
             FROM usuarios u 
             LEFT JOIN dados_pessoais dp ON u.id_usuario = dp.id_usuario 
             LEFT JOIN enderecos e ON u.id_usuario = e.id_usuario 
             WHERE u.id_usuario = ?",
            [$_SESSION['user_id']]
        );
    } catch (Exception $e) {
        error_log("Erro ao obter dados do usuário: " . $e->getMessage());
        return null;
    }
}

/**
 * Verifica se login já existe
 */
function loginExists($login, $excludeUserId = null) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM usuarios WHERE login = ?";
        $params = [$login];
        
        if ($excludeUserId) {
            $sql .= " AND id_usuario != ?";
            $params[] = $excludeUserId;
        }
        
        $result = $db->fetchOne($sql, $params);
        return $result['count'] > 0;
    } catch (Exception $e) {
        error_log("Erro ao verificar login: " . $e->getMessage());
        return false;
    }
}

/**
 * Verifica se email já existe
 */
function emailExists($email, $excludeUserId = null) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM usuarios WHERE email = ?";
        $params = [$email];
        
        if ($excludeUserId) {
            $sql .= " AND id_usuario != ?";
            $params[] = $excludeUserId;
        }
        
        $result = $db->fetchOne($sql, $params);
        return $result['count'] > 0;
    } catch (Exception $e) {
        error_log("Erro ao verificar email: " . $e->getMessage());
        return false;
    }
}

/**
 * Verifica se CPF já existe
 */
function cpfExists($cpf, $excludeUserId = null) {
    try {
        $db = Database::getInstance();
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        $sql = "SELECT COUNT(*) as count FROM dados_pessoais dp 
                JOIN usuarios u ON dp.id_usuario = u.id_usuario 
                WHERE dp.cpf = ?";
        $params = [$cpf];
        
        if ($excludeUserId) {
            $sql .= " AND u.id_usuario != ?";
            $params[] = $excludeUserId;
        }
        
        $result = $db->fetchOne($sql, $params);
        return $result['count'] > 0;
    } catch (Exception $e) {
        error_log("Erro ao verificar CPF: " . $e->getMessage());
        return false;
    }
}

/**
 * Função para debug (apenas em desenv)
 */
function debug($data, $die = false) {
    if (ENVIRONMENT === 'development') {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        
        if ($die) {
            die();
        }
    }
}

/**
 * Obtém IP real do cliente
 */
function getRealIP() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            $ip = trim($ips[0]);
            
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}
?>

