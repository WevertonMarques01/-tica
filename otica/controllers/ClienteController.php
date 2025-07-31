<?php
/**
 * Controller Cliente - Gerenciamento de clientes
 */
class ClienteController extends Controller
{
    private $clienteModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->clienteModel = $this->loadModel('Cliente');
    }
    
    /**
     * Lista todos os clientes
     */
    public function indexAction()
    {
        $clientes = $this->clienteModel->getAll();
        
        $data = [
            'title' => 'Gerenciar Clientes',
            'clientes' => $clientes
        ];
        
        $this->render('clientes/index', $data);
    }
    
    /**
     * Exibe formulário para criar cliente
     */
    public function novoAction()
    {
        if ($this->isPost()) {
            $clienteData = [
                'nome' => $this->getPost('nome'),
                'documento' => $this->getPost('documento'),
                'email' => $this->getPost('email'),
                'telefone' => $this->getPost('telefone'),
                'endereco' => $this->getPost('endereco')
            ];
            
            // Validar dados
            $errors = $this->clienteModel->validate($clienteData);
            
            if (empty($errors)) {
                if ($this->clienteModel->create($clienteData)) {
                    $this->redirect('clientes');
                } else {
                    $data = [
                        'title' => 'Novo Cliente',
                        'error' => 'Erro ao criar cliente',
                        'cliente' => $clienteData
                    ];
                    $this->render('clientes/novo', $data);
                }
            } else {
                $data = [
                    'title' => 'Novo Cliente',
                    'errors' => $errors,
                    'cliente' => $clienteData
                ];
                $this->render('clientes/novo', $data);
            }
        } else {
            $data = [
                'title' => 'Novo Cliente'
            ];
            $this->render('clientes/novo', $data);
        }
    }
    
    /**
     * Exibe formulário para editar cliente
     */
    public function editarAction()
    {
        $id = $this->getGet('id');
        
        if ($this->isPost()) {
            $clienteData = [
                'id' => $id,
                'nome' => $this->getPost('nome'),
                'documento' => $this->getPost('documento'),
                'email' => $this->getPost('email'),
                'telefone' => $this->getPost('telefone'),
                'endereco' => $this->getPost('endereco')
            ];
            
            // Validar dados
            $errors = $this->clienteModel->validate($clienteData, $id);
            
            if (empty($errors)) {
                if ($this->clienteModel->update($clienteData)) {
                    $this->redirect('clientes');
                } else {
                    $data = [
                        'title' => 'Editar Cliente',
                        'error' => 'Erro ao atualizar cliente',
                        'cliente' => $clienteData
                    ];
                    $this->render('clientes/editar', $data);
                }
            } else {
                $data = [
                    'title' => 'Editar Cliente',
                    'errors' => $errors,
                    'cliente' => $clienteData
                ];
                $this->render('clientes/editar', $data);
            }
        } else {
            $cliente = $this->clienteModel->getById($id);
            
            $data = [
                'title' => 'Editar Cliente',
                'cliente' => $cliente
            ];
            $this->render('clientes/editar', $data);
        }
    }
    
    /**
     * Remove um cliente
     */
    public function excluirAction()
    {
        $id = $this->getGet('id');
        
        if ($this->clienteModel->delete($id)) {
            $this->redirect('clientes');
        } else {
            $this->redirect('clientes');
        }
    }
    
    /**
     * Busca clientes por nome (AJAX)
     */
    public function buscarAction()
    {
        $nome = $this->getGet('nome');
        $clientes = $this->clienteModel->searchByNome($nome);
        
        $this->json($clientes);
    }
    
    /**
     * Exibe detalhes do cliente
     */
    public function visualizarAction()
    {
        $id = $this->getGet('id');
        $cliente = $this->clienteModel->getById($id);
        
        $data = [
            'title' => 'Detalhes do Cliente',
            'cliente' => $cliente
        ];
        
        $this->render('clientes/visualizar', $data);
    }
}
?> 