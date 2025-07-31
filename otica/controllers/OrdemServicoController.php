<?php
/**
 * Controller OrdemServico - Gerenciamento de ordens de serviço
 */
class OrdemServicoController extends Controller
{
    private $ordemServicoModel;
    private $clienteModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->ordemServicoModel = $this->loadModel('OrdemServico');
        $this->clienteModel = $this->loadModel('Cliente');
    }
    
    /**
     * Lista todas as ordens de serviço
     */
    public function indexAction()
    {
        $ordens = $this->ordemServicoModel->getAllWithDetails();
        
        $data = [
            'title' => 'Gerenciar Ordens de Serviço',
            'ordens' => $ordens
        ];
        
        $this->render('ordens_servico/index', $data);
    }
    
    /**
     * Exibe formulário para criar ordem de serviço
     */
    public function novaAction()
    {
        if ($this->isPost()) {
            $ordemData = [
                'cliente_id' => $this->getPost('cliente_id'),
                'descricao' => $this->getPost('descricao'),
                'valor' => $this->getPost('valor'),
                'status' => $this->getPost('status', 'pendente'),
                'observacoes' => $this->getPost('observacoes')
            ];
            
            // Validar dados
            $errors = $this->ordemServicoModel->validate($ordemData);
            
            if (empty($errors)) {
                if ($this->ordemServicoModel->create($ordemData)) {
                    $this->redirect('ordens_servico');
                } else {
                    $data = [
                        'title' => 'Nova Ordem de Serviço',
                        'error' => 'Erro ao criar ordem de serviço',
                        'ordem' => $ordemData,
                        'clientes' => $this->clienteModel->getAll()
                    ];
                    $this->render('ordens_servico/nova', $data);
                }
            } else {
                $data = [
                    'title' => 'Nova Ordem de Serviço',
                    'errors' => $errors,
                    'ordem' => $ordemData,
                    'clientes' => $this->clienteModel->getAll()
                ];
                $this->render('ordens_servico/nova', $data);
            }
        } else {
            $data = [
                'title' => 'Nova Ordem de Serviço',
                'clientes' => $this->clienteModel->getAll()
            ];
            $this->render('ordens_servico/nova', $data);
        }
    }
    
    /**
     * Exibe formulário para editar ordem de serviço
     */
    public function editarAction()
    {
        $id = $this->getGet('id');
        
        if ($this->isPost()) {
            $ordemData = [
                'id' => $id,
                'cliente_id' => $this->getPost('cliente_id'),
                'descricao' => $this->getPost('descricao'),
                'valor' => $this->getPost('valor'),
                'status' => $this->getPost('status'),
                'observacoes' => $this->getPost('observacoes')
            ];
            
            // Validar dados
            $errors = $this->ordemServicoModel->validate($ordemData);
            
            if (empty($errors)) {
                if ($this->ordemServicoModel->update($ordemData)) {
                    $this->redirect('ordens_servico');
                } else {
                    $data = [
                        'title' => 'Editar Ordem de Serviço',
                        'error' => 'Erro ao atualizar ordem de serviço',
                        'ordem' => $ordemData,
                        'clientes' => $this->clienteModel->getAll()
                    ];
                    $this->render('ordens_servico/editar', $data);
                }
            } else {
                $data = [
                    'title' => 'Editar Ordem de Serviço',
                    'errors' => $errors,
                    'ordem' => $ordemData,
                    'clientes' => $this->clienteModel->getAll()
                ];
                $this->render('ordens_servico/editar', $data);
            }
        } else {
            $ordem = $this->ordemServicoModel->getById($id);
            
            $data = [
                'title' => 'Editar Ordem de Serviço',
                'ordem' => $ordem,
                'clientes' => $this->clienteModel->getAll()
            ];
            $this->render('ordens_servico/editar', $data);
        }
    }
    
    /**
     * Remove uma ordem de serviço
     */
    public function excluirAction()
    {
        $id = $this->getGet('id');
        
        if ($this->ordemServicoModel->delete($id)) {
            $this->redirect('ordens_servico');
        } else {
            $this->redirect('ordens_servico');
        }
    }
    
    /**
     * Atualiza status da ordem de serviço
     */
    public function atualizarStatusAction()
    {
        if ($this->isPost()) {
            $id = $this->getPost('id');
            $status = $this->getPost('status');
            
            if ($this->ordemServicoModel->updateStatus($id, $status)) {
                $this->json(['success' => true]);
            } else {
                $this->json(['success' => false, 'error' => 'Erro ao atualizar status']);
            }
        }
    }
    
    /**
     * Lista ordens pendentes
     */
    public function pendentesAction()
    {
        $ordens = $this->ordemServicoModel->getPendentes();
        
        $data = [
            'title' => 'Ordens Pendentes',
            'ordens' => $ordens
        ];
        
        $this->render('ordens_servico/pendentes', $data);
    }
    
    /**
     * Lista ordens em andamento
     */
    public function emAndamentoAction()
    {
        $ordens = $this->ordemServicoModel->getEmAndamento();
        
        $data = [
            'title' => 'Ordens em Andamento',
            'ordens' => $ordens
        ];
        
        $this->render('ordens_servico/em_andamento', $data);
    }
    
    /**
     * Lista ordens concluídas
     */
    public function concluidasAction()
    {
        $ordens = $this->ordemServicoModel->getConcluidas();
        
        $data = [
            'title' => 'Ordens Concluídas',
            'ordens' => $ordens
        ];
        
        $this->render('ordens_servico/concluidas', $data);
    }
    
    /**
     * Exibe detalhes da ordem de serviço
     */
    public function visualizarAction()
    {
        $id = $this->getGet('id');
        $ordem = $this->ordemServicoModel->getWithDetails($id);
        
        $data = [
            'title' => 'Detalhes da Ordem de Serviço',
            'ordem' => $ordem
        ];
        
        $this->render('ordens_servico/visualizar', $data);
    }
}
?> 