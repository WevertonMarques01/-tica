<?php
/**
 * Configurações Gerais do Sistema
 * Sistema Ótica
 */

// Configurações do sistema
define('SITE_NAME', 'Sistema Ótica');
define('SITE_URL', 'http://localhost/-tica/otica/');
define('SITE_VERSION', '1.0.0');

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Configurações de erro (descomente para desenvolvimento)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Configurações de upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Configurações de paginação
define('ITEMS_PER_PAGE', 10);

// Configurações de segurança
define('PASSWORD_MIN_LENGTH', 6);
define('SESSION_TIMEOUT', 3600); // 1 hora

/**
 * Função para verificar se o usuário está logado
 */
function isLoggedIn() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Função para redirecionar usuário
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Função para sanitizar entrada do usuário
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Função para gerar token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Função para verificar token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>