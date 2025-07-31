<?php
/**
 * Controller Financeiro - Gerenciamento de relatórios financeiros
 */
class FinanceiroController extends Controller
{
    private $vendaModel;
    private $ordemServicoModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->vendaModel = $this->loadModel('Venda');
        $this->ordemServicoModel = $this->loadModel('OrdemServico');
    }
    
    /**
     * Dashboard financeiro
     */
    public function indexAction()
    {
        $dataInicio = $this->getGet('data_inicio', date('Y-m-01'));
        $dataFim = $this->getGet('data_fim', date('Y-m-t'));
        
        // Totais de vendas
        $totalVendas = $this->vendaModel->getTotalByPeriodo($dataInicio, $dataFim);
        
        // Vendas por período
        $vendas = $this->vendaModel->getByPeriodo($dataInicio, $dataFim);
        
        // Ordens de serviço concluídas
        $ordensConcluidas = $this->ordemServicoModel->getConcluidas();
        $totalOrdens = 0;
        foreach ($ordensConcluidas as $ordem) {
            $totalOrdens += $ordem['valor'];
        }
        
        $data = [
            'title' => 'Dashboard Financeiro',
            'total_vendas' => $totalVendas,
            'total_ordens' => $totalOrdens,
            'receita_total' => $totalVendas + $totalOrdens,
            'vendas' => $vendas,
            'ordens_concluidas' => $ordensConcluidas,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ];
        
        $this->render('financeiro/dashboard', $data);
    }
    
    /**
     * Relatório de vendas
     */
    public function relatorioAction()
    {
        $dataInicio = $this->getGet('data_inicio', date('Y-m-01'));
        $dataFim = $this->getGet('data_fim', date('Y-m-t'));
        
        $vendas = $this->vendaModel->getByPeriodo($dataInicio, $dataFim);
        $total = $this->vendaModel->getTotalByPeriodo($dataInicio, $dataFim);
        
        $data = [
            'title' => 'Relatório Financeiro',
            'vendas' => $vendas,
            'total' => $total,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ];
        
        $this->render('financeiro/relatorio', $data);
    }
    
    /**
     * Relatório de ordens de serviço
     */
    public function relatorioOrdensAction()
    {
        $status = $this->getGet('status', 'concluida');
        
        if ($status === 'concluida') {
            $ordens = $this->ordemServicoModel->getConcluidas();
        } elseif ($status === 'em_andamento') {
            $ordens = $this->ordemServicoModel->getEmAndamento();
        } elseif ($status === 'pendente') {
            $ordens = $this->ordemServicoModel->getPendentes();
        } else {
            $ordens = $this->ordemServicoModel->getAllWithDetails();
        }
        
        $total = 0;
        foreach ($ordens as $ordem) {
            if ($ordem['status'] === 'concluida') {
                $total += $ordem['valor'];
            }
        }
        
        $data = [
            'title' => 'Relatório de Ordens de Serviço',
            'ordens' => $ordens,
            'total' => $total,
            'status' => $status
        ];
        
        $this->render('financeiro/relatorio_ordens', $data);
    }
    
    /**
     * Relatório consolidado
     */
    public function consolidadoAction()
    {
        $dataInicio = $this->getGet('data_inicio', date('Y-m-01'));
        $dataFim = $this->getGet('data_fim', date('Y-m-t'));
        
        // Vendas do período
        $vendas = $this->vendaModel->getByPeriodo($dataInicio, $dataFim);
        $totalVendas = $this->vendaModel->getTotalByPeriodo($dataInicio, $dataFim);
        
        // Ordens concluídas do período
        $ordensConcluidas = $this->ordemServicoModel->getConcluidas();
        $totalOrdens = 0;
        foreach ($ordensConcluidas as $ordem) {
            $dataOrdem = date('Y-m-d', strtotime($ordem['created_at']));
            if ($dataOrdem >= $dataInicio && $dataOrdem <= $dataFim) {
                $totalOrdens += $ordem['valor'];
            }
        }
        
        $data = [
            'title' => 'Relatório Consolidado',
            'vendas' => $vendas,
            'ordens_concluidas' => $ordensConcluidas,
            'total_vendas' => $totalVendas,
            'total_ordens' => $totalOrdens,
            'receita_total' => $totalVendas + $totalOrdens,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ];
        
        $this->render('financeiro/consolidado', $data);
    }
    
    /**
     * Exporta relatório para PDF
     */
    public function exportarPdfAction()
    {
        $dataInicio = $this->getGet('data_inicio', date('Y-m-01'));
        $dataFim = $this->getGet('data_fim', date('Y-m-t'));
        
        $vendas = $this->vendaModel->getByPeriodo($dataInicio, $dataFim);
        $total = $this->vendaModel->getTotalByPeriodo($dataInicio, $dataFim);
        
        $data = [
            'title' => 'Relatório Financeiro',
            'vendas' => $vendas,
            'total' => $total,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ];
        
        // Aqui você pode implementar a geração do PDF
        // Por exemplo, usando TCPDF ou FPDF
        
        $this->render('financeiro/pdf', $data);
    }
    
    /**
     * Exporta relatório para Excel
     */
    public function exportarExcelAction()
    {
        $dataInicio = $this->getGet('data_inicio', date('Y-m-01'));
        $dataFim = $this->getGet('data_fim', date('Y-m-t'));
        
        $vendas = $this->vendaModel->getByPeriodo($dataInicio, $dataFim);
        
        // Configurar headers para download do Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="relatorio_financeiro.xls"');
        
        $data = [
            'vendas' => $vendas,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ];
        
        $this->render('financeiro/excel', $data);
    }
    
    /**
     * Gráficos financeiros (AJAX)
     */
    public function graficosAction()
    {
        $dataInicio = $this->getGet('data_inicio', date('Y-m-01'));
        $dataFim = $this->getGet('data_fim', date('Y-m-t'));
        
        // Dados para gráficos
        $vendas = $this->vendaModel->getByPeriodo($dataInicio, $dataFim);
        $totalVendas = $this->vendaModel->getTotalByPeriodo($dataInicio, $dataFim);
        
        $ordensConcluidas = $this->ordemServicoModel->getConcluidas();
        $totalOrdens = 0;
        foreach ($ordensConcluidas as $ordem) {
            $totalOrdens += $ordem['valor'];
        }
        
        $dados = [
            'vendas' => $totalVendas,
            'ordens' => $totalOrdens,
            'total' => $totalVendas + $totalOrdens,
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ]
        ];
        
        $this->json($dados);
    }
}
?> 