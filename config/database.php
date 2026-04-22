<?php
/**
 * Configuração do Banco de Dados
 * Sistema Ótica
 * 
 * IMPORTANTE: Para produção no InfinityFree, configure as credenciais abaixo
 * com os dados fornecidos no painel de controle (banco de dados MySQL).
 */

// Configurações do banco de dados
// Para InfinityFree, use os dados do seu banco criado no painel
if (file_exists(__DIR__ . '/database_local.php')) {
    // Carrega configurações locais se existir (não versionado)
    require_once __DIR__ . '/database_local.php';
} else {
    // Configurações padrão - ALTERE PARA PRODUÇÃO
    define('DB_HOST', 'localhost'); // Geralmente localhost no InfinityFree
    define('DB_NAME', 'otica_db');   // Nome do banco no InfinityFree
    define('DB_USER', 'root');       // Usuário do banco (fornecido pelo InfinityFree)
    define('DB_PASS', '');           // Senha do banco (fornecida pelo InfinityFree)
}
define('DB_CHARSET', 'utf8mb4');

/**
 * Classe para conexão com o banco de dados
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
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
    
    public function testConnection() {
        try {
            $stmt = $this->connection->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?> 