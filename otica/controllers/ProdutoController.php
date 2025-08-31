<?php
/**
 * Controller Produto - Gerenciamento de produtos
 */
require_once __DIR__ . '/../models/Produto.php';

class ProdutoController
{
    private $produtoModel;
    
    public function __construct()
    {
        $this->produtoModel = new Produto();
    }
    
    /**
     * Lista todos os produtos
     */
    public function index()
    {
        $produtos = $this->produtoModel->getAllWithDetails();
        
        // Incluir a view
        include __DIR__ . '/../views/produtos/index.php';
    }
    
    /**
     * Exibe formulário para criar produto
     */
    public function novo()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoData = [
                'nome' => $_POST['nome'] ?? '',
                'codigo' => $_POST['codigo'] ?? '',
                'descricao' => $_POST['descricao'] ?? '',
                'preco' => $_POST['preco'] ?? 0,
                'estoque' => $_POST['estoque'] ?? 0,
                'tipo' => $_POST['tipo'] ?? '',
                'marca' => $_POST['marca'] ?? '',
                'modelo' => $_POST['modelo'] ?? '',
                'cor' => $_POST['cor'] ?? ''
            ];
            
            // Validar dados
            $errors = $this->produtoModel->validate($produtoData);
            
            if (empty($errors)) {
                if ($this->produtoModel->create($produtoData)) {
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Erro ao criar produto';
                }
            }
        } else {
            $errors = [];
            $produtoData = [];
        }
        
        // Incluir a view
        include __DIR__ . '/../views/produtos/novo.php';
    }
    
    /**
     * Exibe formulário para editar produto
     */
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            header('Location: index.php');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoData = [
                'id' => $id,
                'nome' => $_POST['nome'] ?? '',
                'codigo' => $_POST['codigo'] ?? '',
                'descricao' => $_POST['descricao'] ?? '',
                'preco' => $_POST['preco'] ?? 0,
                'estoque' => $_POST['estoque'] ?? 0,
                'tipo' => $_POST['tipo'] ?? '',
                'marca' => $_POST['marca'] ?? '',
                'modelo' => $_POST['modelo'] ?? '',
                'cor' => $_POST['cor'] ?? ''
            ];
            
            // Validar dados
            $errors = $this->produtoModel->validate($produtoData, $id);
            
            if (empty($errors)) {
                if ($this->produtoModel->update($produtoData)) {
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Erro ao atualizar produto';
                }
            }
        } else {
            $produtoData = $this->produtoModel->getById($id);
            $errors = [];
        }
        
        // Incluir a view
        include __DIR__ . '/../views/produtos/editar.php';
    }
    
    /**
     * Remove um produto
     */
    public function excluir()
    {
        $id = $_GET['id'] ?? null;
        
        if ($id && $this->produtoModel->delete($id)) {
            header('Location: index.php');
            exit;
        } else {
            header('Location: index.php');
            exit;
        }
    }
    
    /**
     * Busca produtos por nome (AJAX)
     */
    public function buscar()
    {
        $nome = $_GET['nome'] ?? '';
        $produtos = $this->produtoModel->searchByNome($nome);
        
        header('Content-Type: application/json');
        echo json_encode($produtos);
        exit;
    }
    
    /**
     * Verifica se código existe (AJAX)
     */
    public function verificarCodigo()
    {
        $codigo = $_GET['codigo'] ?? '';
        $excludeId = $_GET['exclude_id'] ?? null;
        
        $exists = $this->produtoModel->codigoExists($codigo, $excludeId);
        
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
        exit;
    }
    
    /**
     * Atualiza estoque do produto
     */
    public function atualizarEstoque()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $quantidade = $_POST['quantidade'] ?? 0;
            
            if ($id && $this->produtoModel->updateEstoque($id, $quantidade)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Erro ao atualizar estoque']);
            }
            exit;
        }
    }
}
?> 