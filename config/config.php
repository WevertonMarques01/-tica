<?php
/**
 * Arquivo de configuração principal
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'sua_database');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');

// Configurações da aplicação
define('APP_NAME', 'Minha Aplicação MVC');
define('APP_URL', 'http://localhost');
define('APP_ROOT', __DIR__ . '/../');

// Configurações de sessão
define('SESSION_NAME', 'mvc_session');
define('SESSION_LIFETIME', 3600);

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de debug
define('DEBUG_MODE', true);

// Configurações de segurança
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_COST', 12);
?> 