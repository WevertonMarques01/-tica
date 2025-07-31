<?php
/**
 * Controller Venda - Gerenciamento de vendas
 */
class VendaController extends Controller
{
    private $vendaModel;
    private $clienteModel;
    private $produtoModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->vendaModel = $this->loadModel('Venda');
        $this->clienteModel = $this->loadModel('Cliente');
        $this->produtoModel = $this->loadModel('Produto');
    }
    
    /**
     * Lista todas as vendas
     */
    public function indexAction()
    {
        $vendas = $this->vendaModel->getAllWithDetails();
        
        $data = [
            'title' => 'Histórico de Vendas',
            'vendas' => $vendas
        ];
        
        $this->render('vendas/historico', $data);
    }
    
    /**
     * Exibe formulário para criar nova venda
     */
    public function novaAction()
    {
        if ($this->isPost()) {
            $vendaData = [
                'cliente_id' => $this->getPost('cliente_id'),
                'valor_total' => $this->getPost('valor_total'),
                'forma_pagamento' => $this->getPost('forma_pagamento'),
                'observacoes' => $this->getPost('observacoes')
            ];
            
            // Validar dados
            $errors = $this->vendaModel->validate($vendaData);
            
            if (empty($errors)) {
                if ($this->vendaModel->create($vendaData)) {
                    $this->redirect('vendas');
                } else {
                    $data = [
                        'title' => 'Nova Venda',
                        'error' => 'Erro ao criar venda',
                        'venda' => $vendaData,
                        'clientes' => $this->clienteModel->getAll(),
                        'produtos' => $this->produtoModel->getEmEstoque()
                    ];
                    $this->render('vendas/nova', $data);
                }
            } else {
                $data = [
                    'title' => 'Nova Venda',
                    'errors' => $errors,
                    'venda' => $vendaData,
                    'clientes' => $this->clienteModel->getAll(),
                    'produtos' => $this->produtoModel->getEmEstoque()
                ];
                $this->render('vendas/nova', $data);
            }
        } else {
            $data = [
                'title' => 'Nova Venda',
                'clientes' => $this->clienteModel->getAll(),
                'produtos' => $this->produtoModel->getEmEstoque()
            ];
            $this->render('vendas/nova', $data);
        }
    }
    
    /**
     * Exibe detalhes da venda
     */
    public function visualizarAction()
    {
        $id = $this->getGet('id');
        $venda = $this->vendaModel->getWithDetails($id);
        
        $data = [
            'title' => 'Detalhes da Venda',
            'venda' => $venda
        ];
        
        $this->render('vendas/visualizar', $data);
    }
    
    /**
     * Remove uma venda
     */
    public function excluirAction()
    {
        $id = $this->getGet('id');
        
        if ($this->vendaModel->delete($id)) {
            $this->redirect('vendas');
        } else {
            $this->redirect('vendas');
        }
    }
    
    /**
     * Busca vendas por período
     */
    public function relatorioAction()
    {
        $dataInicio = $this->getGet('data_inicio', date('Y-m-01'));
        $dataFim = $this->getGet('data_fim', date('Y-m-t'));
        
        $vendas = $this->vendaModel->getByPeriodo($dataInicio, $dataFim);
        $total = $this->vendaModel->getTotalByPeriodo($dataInicio, $dataFim);
        
        $data = [
            'title' => 'Relatório de Vendas',
            'vendas' => $vendas,
            'total' => $total,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ];
        
        $this->render('vendas/relatorio', $data);
    }
    
    /**
     * Busca vendas por cliente
     */
    public function porClienteAction()
    {
        $clienteId = $this->getGet('cliente_id');
        $vendas = $this->vendaModel->getByCliente($clienteId);
        $cliente = $this->clienteModel->getById($clienteId);
        
        $data = [
            'title' => 'Vendas do Cliente',
            'vendas' => $vendas,
            'cliente' => $cliente
        ];
        
        $this->render('vendas/por_cliente', $data);
    }
    
    /**
     * Calcula total da venda (AJAX)
     */
    public function calcularTotalAction()
    {
        if ($this->isPost()) {
            $produtos = $this->getPost('produtos');
            $total = 0;
            
            foreach ($produtos as $produto) {
                $total += $produto['preco'] * $produto['quantidade'];
            }
            
            $this->json(['total' => $total]);
        }
    }
}
?> 