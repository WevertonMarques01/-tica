<?php
/**
 * Ponto de entrada principal da aplicação MVC
 */

// Configurações básicas
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoloader simples
spl_autoload_register(function ($class) {
    $paths = [
        'controller/',
        'model/',
        'view/',
        'config/',
        'helpers/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Incluir arquivo de configuração
require_once 'config/config.php';

// Incluir router
require_once 'core/Router.php';

// Iniciar aplicação
$router = new Router();
$router->dispatch();
?> 