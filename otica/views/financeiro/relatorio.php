<?php include 'otica/views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-chart-line"></i> Relatório Financeiro</h2>
            <div>
                <a href="/financeiro/exportarPdf" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
                <a href="/financeiro/exportarExcel" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </a>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="/financeiro/relatorio" class="row">
                    <div class="col-md-4">
                        <label for="data_inicio" class="form-label">Data Início</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                               value="<?= $data_inicio ?? date('Y-m-01') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="data_fim" class="form-label">Data Fim</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" 
                               value="<?= $data_fim ?? date('Y-m-t') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="/financeiro/relatorio" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Resumo -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total de Vendas</h5>
                        <h3><?= count($vendas) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Valor Total</h5>
                        <h3>R$ <?= number_format($total, 2, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Ticket Médio</h5>
                        <h3>
                            R$ <?= count($vendas) > 0 ? number_format($total / count($vendas), 2, ',', '.') : '0,00' ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Período</h5>
                        <h6><?= date('d/m/Y', strtotime($data_inicio)) ?> - <?= date('d/m/Y', strtotime($data_fim)) ?></h6>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (isset($vendas) && !empty($vendas)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Valor Total</th>
                            <th>Forma de Pagamento</th>
                            <th>Data da Venda</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vendas as $venda): ?>
                            <tr>
                                <td><?= $venda['id'] ?></td>
                                <td><?= htmlspecialchars($venda['cliente_nome']) ?></td>
                                <td>R$ <?= number_format($venda['valor_total'], 2, ',', '.') ?></td>
                                <td>
                                    <?php
                                    $formasPagamento = [
                                        'dinheiro' => 'Dinheiro',
                                        'cartao_credito' => 'Cartão de Crédito',
                                        'cartao_debito' => 'Cartão de Débito',
                                        'pix' => 'PIX',
                                        'boleto' => 'Boleto'
                                    ];
                                    echo $formasPagamento[$venda['forma_pagamento']] ?? $venda['forma_pagamento'];
                                    ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($venda['created_at'])) ?></td>
                                <td>
                                    <a href="/vendas/visualizar?id=<?= $venda['id'] ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       data-bs-toggle="tooltip" 
                                       title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Gráficos -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Vendas por Forma de Pagamento</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoPagamento"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Vendas por Dia</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoDias"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Nenhuma venda encontrada para o período selecionado.
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dados para os gráficos
const vendas = <?= json_encode($vendas ?? []) ?>;

// Gráfico de formas de pagamento
const ctxPagamento = document.getElementById('graficoPagamento').getContext('2d');
const formasPagamento = {};
vendas.forEach(venda => {
    const forma = venda.forma_pagamento;
    formasPagamento[forma] = (formasPagamento[forma] || 0) + parseFloat(venda.valor_total);
});

new Chart(ctxPagamento, {
    type: 'doughnut',
    data: {
        labels: Object.keys(formasPagamento),
        datasets: [{
            data: Object.values(formasPagamento),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de vendas por dia
const ctxDias = document.getElementById('graficoDias').getContext('2d');
const vendasPorDia = {};
vendas.forEach(venda => {
    const data = venda.created_at.split(' ')[0];
    vendasPorDia[data] = (vendasPorDia[data] || 0) + parseFloat(venda.valor_total);
});

new Chart(ctxDias, {
    type: 'line',
    data: {
        labels: Object.keys(vendasPorDia),
        datasets: [{
            label: 'Valor das Vendas',
            data: Object.values(vendasPorDia),
            borderColor: '#36A2EB',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include 'otica/views/layout/footer.php'; ?> 