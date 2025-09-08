<?php
/**
 * Controller Usuario - Gerenciamento de usuários/funcionários
 */

require_once '../config/database.php';
require_once '../includes/auth_check.php';
session_start();

class UsuarioController {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Lista todos os usuários (apenas para dono)
     */
    public function listarUsuarios() {
        if (!verificarSeDono()) {
            return [
                'success' => false,
                'message' => 'Acesso negado'
            ];
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT id, nome, email, perfil, ativo, ultimo_login, created_at
                FROM usuarios 
                ORDER BY nome
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar usuários: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cria um novo usuário/funcionário (apenas para dono)
     */
    public function criarUsuario($dados) {
        if (!verificarSeDono()) {
            return [
                'success' => false,
                'message' => 'Acesso negado'
            ];
        }
        
        try {
            // Verificar se o email já existe
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$dados['email']]);
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Email já cadastrado'
                ];
            }
            
            // Hash da senha
            $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                INSERT INTO usuarios (
                    nome, email, senha, perfil, ativo
                ) VALUES (?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $dados['nome'],
                $dados['email'],
                $senhaHash,
                $dados['perfil'] ?? 'vendedor',
                $dados['ativo'] ?? 1
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'id' => $this->db->lastInsertId(),
                    'message' => 'Funcionário criado com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao criar usuário'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }
    
    /**
     * Atualiza dados de um usuário (apenas para dono)
     */
    public function atualizarUsuario($id, $dados) {
        if (!verificarSeDono()) {
            return [
                'success' => false,
                'message' => 'Acesso negado'
            ];
        }
        
        try {
            // Verificar se o email já existe (exceto para o próprio usuário)
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt->execute([$dados['email'], $id]);
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Email já cadastrado'
                ];
            }
            
            $campos = [
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'perfil' => $dados['perfil'] ?? 'vendedor',
                'ativo' => $dados['ativo'] ?? 1
            ];
            
            // Se uma nova senha foi fornecida, atualizar
            if (!empty($dados['senha'])) {
                $campos['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            }
            
            $sql = "UPDATE usuarios SET ";
            $valores = [];
            foreach ($campos as $campo => $valor) {
                $sql .= "$campo = ?, ";
                $valores[] = $valor;
            }
            $sql = rtrim($sql, ', ');
            $sql .= " WHERE id = ?";
            $valores[] = $id;
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($valores);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Usuário atualizado com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao atualizar usuário'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }
    
    /**
     * Desativa um usuário (apenas para dono)
     */
    public function desativarUsuario($id) {
        if (!verificarSeDono()) {
            return [
                'success' => false,
                'message' => 'Acesso negado'
            ];
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Usuário desativado com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao desativar usuário'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro ao desativar usuário: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }
    
    /**
     * Busca dados de um usuário específico
     */
    public function getUsuario($id) {
        if (!verificarSeDono()) {
            return [
                'success' => false,
                'message' => 'Acesso negado'
            ];
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT id, nome, email, perfil, ativo, ultimo_login, created_at
                FROM usuarios 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                return [
                    'success' => true,
                    'usuario' => $usuario
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }
}

// Processar requisições AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $controller = new UsuarioController();
    
    switch ($_GET['action']) {
        case 'getUsuario':
            if (isset($_GET['id'])) {
                $result = $controller->getUsuario($_GET['id']);
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID do usuário não fornecido']);
            }
            break;
            
        case 'listar':
            $usuarios = $controller->listarUsuarios();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'usuarios' => $usuarios]);
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
            break;
    }
    exit;
}

// Processar requisições POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controller = new UsuarioController();
    
    switch ($_POST['action']) {
        case 'criar':
            $result = $controller->criarUsuario($_POST);
            header('Content-Type: application/json');
            echo json_encode($result);
            break;
            
        case 'atualizar':
            if (isset($_POST['id'])) {
                $result = $controller->atualizarUsuario($_POST['id'], $_POST);
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID do usuário não fornecido']);
            }
            break;
            
        case 'desativar':
            if (isset($_POST['id'])) {
                $result = $controller->desativarUsuario($_POST['id']);
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID do usuário não fornecido']);
            }
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
            break;
    }
    exit;
}
?> 