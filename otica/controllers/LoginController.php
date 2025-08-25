<?php
/**
 * Controller Login - Gerenciamento de autenticação
 */

// Incluir configuração do banco de dados
require_once '../config/database.php';
session_start();

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destruir a sessão
    session_unset();
    session_destroy();

    // Redirecionar para a página de login
    header('Location: ../login.php');
    exit();
}
class LoginController {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Processa o login
     */
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $this->redirectWithError('Por favor, preencha todos os campos.');
            }
            
            // Verificar credenciais no banco de dados
            $usuario = $this->authenticateUser($email, $password);
            
            if ($usuario) {
                // Iniciar sessão
                session_start();
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_permissao'] = $usuario['perfil'];
                $_SESSION['logado'] = true;
                
                // Redirecionar para dashboard
                header('Location: ../views/admin/index.php');
                exit;
            } else {
                $this->redirectWithError('Usuário ou senha inválidos.');
            }
        }
    }
    
    /**
     * Autentica o usuário
     */
    private function authenticateUser($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($password, $usuario['senha'])) {
                return $usuario;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Erro na autenticação: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Redireciona com erro
     */
    private function redirectWithError($message) {
        session_start();
        $_SESSION['login_error'] = $message;
        header('Location: login.php');
        exit;
    }
    
    /**
     * Logout
     */
    public function logout() {
        session_start();
        
        // Registrar log de logout
        if (isset($_SESSION['usuario_id'])) {
            try {
                require_once '../config/database.php';
                $db = Database::getInstance()->getConnection();
                $logStmt = $db->prepare("INSERT INTO logs (usuario_id, acao, detalhes) VALUES (?, ?, ?)");
                $logStmt->execute([$_SESSION['usuario_id'], 'logout', 'Logout realizado']);
            } catch (Exception $e) {
                error_log("Erro ao registrar log de logout: " . $e->getMessage());
            }
        }
        
        session_destroy();
        header('Location: ../login.php');
        exit;
    }
}

// Processar login se for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginController = new LoginController();
    $loginController->processLogin();
}

// Processar logout se for GET com action=logout
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    $loginController = new LoginController();
    $loginController->logout();
}
?> 