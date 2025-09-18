<?php
/**
 * Configurações do Banco de Dados
 * ONG de Noé - Sistema de Gestão
 */

// Configurações de conexão com o banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'ong_noe_db');
define('DB_USER', 'ong_user');
define('DB_PASS', 'ong_password');
define('DB_CHARSET', 'utf8mb4');

// Configurações de sessão
define('SESSION_TIMEOUT', 3600); // 1 hora em segundos
define('SESSION_NAME', 'ONG_NOE_SESSION');

// Configurações de segurança
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_MAX_ATTEMPTS', 3);
define('2FA_MAX_ATTEMPTS', 3);

// Configurações de criptografia
define('ENCRYPTION_KEY', 'sua_chave_secreta_aqui_32_caracteres');
define('HASH_ALGORITHM', 'sha256');

/**
 * Classe para gerenciar conexão com banco de dados
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Em produção, registrar erro em log ao invés de exibir
            error_log("Erro de conexão com banco de dados: " . $e->getMessage());
            throw new Exception("Erro de conexão com banco de dados");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Executa uma query preparada
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erro na query: " . $e->getMessage());
            throw new Exception("Erro na execução da query");
        }
    }
    
    /**
     * Busca um único registro
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Busca múltiplos registros
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Insere um registro e retorna o ID
     */
    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES ({$placeholders})";
        
        $this->query($sql, $data);
        return $this->connection->lastInsertId();
    }
    
    /**
     * Atualiza registros
     */
    public function update($table, $data, $where, $whereParams = []) {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "{$field} = :{$field}";
        }
        
        $sql = "UPDATE {$table} SET " . implode(', ', $fields) . " WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        return $this->query($sql, $params);
    }
    
    /**
     * Deleta registros
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params);
    }
}

/**
 * Funções utilitárias para segurança
 */
class Security {
    
    /**
     * Gera hash seguro da senha
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
    
    /**
     * Verifica senha contra hash
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Sanitiza entrada do usuário
     */
    public static function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Valida CPF
     */
    public static function validateCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Valida email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Gera token CSRF
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verifica token CSRF
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

/**
 * Classe para gerenciar logs do sistema
 */
class Logger {
    
    /**
     * Registra log de autenticação
     */
    public static function logAuthentication($userId, $userName, $type2FA, $status, $ipAddress = null) {
        try {
            $db = Database::getInstance();
            
            $data = [
                'id_usuario' => $userId,
                'nome_usuario' => $userName,
                'data_hora' => date('Y-m-d H:i:s'),
                'tipo_2fa' => $type2FA,
                'status_login' => $status,
                'ip_origem' => $ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ];
            
            $db->insert('logs_autenticacao', $data);
        } catch (Exception $e) {
            error_log("Erro ao registrar log de autenticação: " . $e->getMessage());
        }
    }
    
    /**
     * Registra log de ação do usuário
     */
    public static function logUserAction($userId, $action, $details = null) {
        try {
            $db = Database::getInstance();
            
            $data = [
                'id_usuario' => $userId,
                'acao' => $action,
                'detalhes' => $details,
                'data_hora' => date('Y-m-d H:i:s'),
                'ip_origem' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ];
            
            $db->insert('logs_acoes', $data);
        } catch (Exception $e) {
            error_log("Erro ao registrar log de ação: " . $e->getMessage());
        }
    }
}

/**
 * Configurações de ambiente
 */
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development'); // development, production
}

// Configurações específicas do ambiente
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Configurações de sessão segura
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

/**
 * Script SQL para criação das tabelas (para referência)
 */
/*
CREATE DATABASE IF NOT EXISTS ong_noe_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ong_noe_db;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(80) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    login VARCHAR(6) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('master', 'comum') NOT NULL DEFAULT 'comum',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo'
);

CREATE TABLE dados_pessoais (
    id_dados INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    data_nascimento DATE NOT NULL,
    sexo ENUM('M', 'F', 'O') NOT NULL,
    nome_materno VARCHAR(80) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    telefone_celular VARCHAR(15) NOT NULL,
    telefone_fixo VARCHAR(14) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

CREATE TABLE enderecos (
    id_endereco INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    cep VARCHAR(9) NOT NULL,
    logradouro VARCHAR(100) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    complemento VARCHAR(50),
    bairro VARCHAR(50) NOT NULL,
    cidade VARCHAR(50) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

CREATE TABLE logs_autenticacao (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nome_usuario VARCHAR(80) NOT NULL,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo_2fa ENUM('nome_materno', 'data_nascimento', 'cep') NOT NULL,
    status_login ENUM('sucesso', 'falha') NOT NULL,
    ip_origem VARCHAR(45),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

CREATE TABLE logs_acoes (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    acao VARCHAR(100) NOT NULL,
    detalhes TEXT,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_origem VARCHAR(45),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

CREATE TABLE animais (
    id_animal INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    especie ENUM('cao', 'gato', 'outro') NOT NULL,
    raca VARCHAR(50),
    idade INT,
    sexo ENUM('M', 'F') NOT NULL,
    status_adocao ENUM('disponivel', 'adotado', 'em_processo') DEFAULT 'disponivel',
    data_resgate DATE NOT NULL,
    descricao TEXT,
    foto VARCHAR(255)
);

CREATE TABLE adocoes (
    id_adocao INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_animal INT NOT NULL,
    data_adocao DATE NOT NULL,
    status ENUM('ativo', 'cancelado') DEFAULT 'ativo',
    observacoes TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_animal) REFERENCES animais(id_animal) ON DELETE CASCADE
);

-- Inserir usuário master padrão
INSERT INTO usuarios (nome, email, login, senha, tipo_usuario) 
VALUES ('Administrador', 'admin@ongdenoe.org', 'admin', '$argon2id$v=19$m=65536,t=4,p=3$...', 'master');
*/
?>

