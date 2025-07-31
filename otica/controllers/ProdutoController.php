<?php
/**
 * Controller Produto - Gerenciamento de produtos
 */
class ProdutoController extends Controller
{
    private $produtoModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->produtoModel = $this->loadModel('Produto');
    }
    
    /**
     * Lista todos os produtos
     */
    public function indexAction()
    {
        $produtos = $this->produtoModel->getAll();
        
        $data = [
            'title' => 'Gerenciar Produtos',
            'produtos' => $produtos
        ];
        
        $this->render('produtos/index', $data);
    }
    
    /**
     * Exibe formulário para criar produto
     */
    public function novoAction()
    {
        if ($this->isPost()) {
            $produtoData = [
                'nome' => $this->getPost('nome'),
                'codigo' => $this->getPost('codigo'),
                'descricao' => $this->getPost('descricao'),
                'preco' => $this->getPost('preco'),
                'estoque' => $this->getPost('estoque'),
                'categoria' => $this->getPost('categoria')
            ];
            
            // Validar dados
            $errors = $this->produtoModel->validate($produtoData);
            
            if (empty($errors)) {
                if ($this->produtoModel->create($produtoData)) {
                    $this->redirect('produtos');
                } else {
                    $data = [
                        'title' => 'Novo Produto',
                        'error' => 'Erro ao criar produto',
                        'produto' => $produtoData
                    ];
                    $this->render('produtos/novo', $data);
                }
            } else {
                $data = [
                    'title' => 'Novo Produto',
                    'errors' => $errors,
                    'produto' => $produtoData
                ];
                $this->render('produtos/novo', $data);
            }
        } else {
            $data = [
                'title' => 'Novo Produto'
            ];
            $this->render('produtos/novo', $data);
        }
    }
    
    /**
     * Exibe formulário para editar produto
     */
    public function editarAction()
    {
        $id = $this->getGet('id');
        
        if ($this->isPost()) {
            $produtoData = [
                'id' => $id,
                'nome' => $this->getPost('nome'),
                'codigo' => $this->getPost('codigo'),
                'descricao' => $this->getPost('descricao'),
                'preco' => $this->getPost('preco'),
                'estoque' => $this->getPost('estoque'),
                'categoria' => $this->getPost('categoria')
            ];
            
            // Validar dados
            $errors = $this->produtoModel->validate($produtoData, $id);
            
            if (empty($errors)) {
                if ($this->produtoModel->update($produtoData)) {
                    $this->redirect('produtos');
                } else {
                    $data = [
                        'title' => 'Editar Produto',
                        'error' => 'Erro ao atualizar produto',
                        'produto' => $produtoData
                    ];
                    $this->render('produtos/editar', $data);
                }
            } else {
                $data = [
                    'title' => 'Editar Produto',
                    'errors' => $errors,
                    'produto' => $produtoData
                ];
                $this->render('produtos/editar', $data);
            }
        } else {
            $produto = $this->produtoModel->getById($id);
            
            $data = [
                'title' => 'Editar Produto',
                'produto' => $produto
            ];
            $this->render('produtos/editar', $data);
        }
    }
    
    /**
     * Remove um produto
     */
    public function excluirAction()
    {
        $id = $this->getGet('id');
        
        if ($this->produtoModel->delete($id)) {
            $this->redirect('produtos');
        } else {
            $this->redirect('produtos');
        }
    }
    
    /**
     * Busca produtos por nome (AJAX)
     */
    public function buscarAction()
    {
        $nome = $this->getGet('nome');
        $produtos = $this->produtoModel->searchByNome($nome);
        
        $this->json($produtos);
    }
    
    /**
     * Lista produtos em estoque
     */
    public function estoqueAction()
    {
        $produtos = $this->produtoModel->getEmEstoque();
        
        $data = [
            'title' => 'Produtos em Estoque',
            'produtos' => $produtos
        ];
        
        $this->render('produtos/estoque', $data);
    }
    
    /**
     * Atualiza estoque do produto
     */
    public function atualizarEstoqueAction()
    {
        if ($this->isPost()) {
            $id = $this->getPost('id');
            $quantidade = $this->getPost('quantidade');
            
            if ($this->produtoModel->updateEstoque($id, $quantidade)) {
                $this->json(['success' => true]);
            } else {
                $this->json(['success' => false, 'error' => 'Erro ao atualizar estoque']);
            }
        }
    }
}
?> 