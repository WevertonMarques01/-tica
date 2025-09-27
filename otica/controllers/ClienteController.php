<?php
/**
 * Controller Cliente - Gerenciamento de clientes
 */

require_once '../config/database.php';
session_start();

class ClienteController {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Busca dados de um cliente específico
     */
    public function getCliente($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, nome, documento, telefone, celular, email, 
                       endereco, numero, complemento, bairro, cidade, estado, cep
                FROM clientes 
                WHERE id = ? AND ativo = 1
            ");
            $stmt->execute([$id]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cliente) {
                return [
                    'success' => true,
                    'cliente' => $cliente
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Cliente não encontrado'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro ao buscar cliente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }
    
    /**
     * Lista todos os clientes ativos
     */
    public function listarClientes() {
        try {
            $stmt = $this->db->prepare("
                SELECT id, nome, documento, telefone, email, cidade, estado
                FROM clientes 
                WHERE ativo = 1 
                ORDER BY nome
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar clientes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cria um novo cliente
     */
    public function criarCliente($dados) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO clientes (
                    nome, documento, tipo_documento, email, telefone, celular,
                    data_nascimento, sexo, endereco, numero, complemento, bairro,
                    cidade, estado, cep, observacoes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $dados['nome'],
                $dados['documento'],
                $dados['tipo_documento'] ?? 'cpf',
                $dados['email'] ?? null,
                $dados['telefone'] ?? null,
                $dados['celular'] ?? null,
                $dados['data_nascimento'] ?? null,
                $dados['sexo'] ?? null,
                $dados['endereco'] ?? null,
                $dados['numero'] ?? null,
                $dados['complemento'] ?? null,
                $dados['bairro'] ?? null,
                $dados['cidade'] ?? null,
                $dados['estado'] ?? null,
                $dados['cep'] ?? null,
                $dados['observacoes'] ?? null
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'id' => $this->db->lastInsertId(),
                    'message' => 'Cliente criado com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao criar cliente'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro ao criar cliente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }
    
    /**
     * Atualiza dados de um cliente
     */
    public function atualizarCliente($id, $dados) {
        try {
            $stmt = $this->db->prepare("
                UPDATE clientes SET
                    nome = ?, documento = ?, tipo_documento = ?, email = ?,
                    telefone = ?, celular = ?, data_nascimento = ?, sexo = ?,
                    endereco = ?, numero = ?, complemento = ?, bairro = ?,
                    cidade = ?, estado = ?, cep = ?, observacoes = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $dados['nome'],
                $dados['documento'],
                $dados['tipo_documento'] ?? 'cpf',
                $dados['email'] ?? null,
                $dados['telefone'] ?? null,
                $dados['celular'] ?? null,
                $dados['data_nascimento'] ?? null,
                $dados['sexo'] ?? null,
                $dados['endereco'] ?? null,
                $dados['numero'] ?? null,
                $dados['complemento'] ?? null,
                $dados['bairro'] ?? null,
                $dados['cidade'] ?? null,
                $dados['estado'] ?? null,
                $dados['cep'] ?? null,
                $dados['observacoes'] ?? null,
                $id
            ]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Cliente atualizado com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao atualizar cliente'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro ao atualizar cliente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }
    
    /**
     * Desativa um cliente (soft delete)
     */
    public function desativarCliente($id) {
        try {
            $stmt = $this->db->prepare("UPDATE clientes SET ativo = 0 WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Cliente desativado com sucesso'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao desativar cliente'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erro ao desativar cliente: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }
}

// Processar requisições AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $controller = new ClienteController();
    
    switch ($_GET['action']) {
        case 'getCliente':
            if (isset($_GET['id'])) {
                $result = $controller->getCliente($_GET['id']);
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID do cliente não fornecido']);
            }
            break;
            
        case 'listar':
            $clientes = $controller->listarClientes();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'clientes' => $clientes]);
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
    $controller = new ClienteController();
    
    switch ($_POST['action']) {
        case 'criar':
            $result = $controller->criarCliente($_POST);
            header('Content-Type: application/json');
            echo json_encode($result);
            break;
            
        case 'atualizar':
            if (isset($_POST['id'])) {
                $result = $controller->atualizarCliente($_POST['id'], $_POST);
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID do cliente não fornecido']);
            }
            break;
            
        case 'desativar':
            if (isset($_POST['id'])) {
                $result = $controller->desativarCliente($_POST['id']);
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID do cliente não fornecido']);
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