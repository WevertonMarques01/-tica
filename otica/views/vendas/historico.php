<?php include 'otica/views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-history"></i> Histórico de Vendas</h2>
            <a href="/vendas/nova" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Venda
            </a>
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
                                    <div class="btn-group" role="group">
                                        <a href="/vendas/visualizar?id=<?= $venda['id'] ?>" 
                                           class="btn btn-sm btn-outline-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/vendas/editar?id=<?= $venda['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/vendas/excluir?id=<?= $venda['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           data-bs-toggle="tooltip" 
                                           title="Excluir"
                                           onclick="return confirm('Tem certeza que deseja excluir esta venda?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Estatísticas -->
            <div class="row mt-4">
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
                            <h3>
                                R$ <?= number_format(array_sum(array_column($vendas, 'valor_total')), 2, ',', '.') ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Ticket Médio</h5>
                            <h3>
                                R$ <?= count($vendas) > 0 ? number_format(array_sum(array_column($vendas, 'valor_total')) / count($vendas), 2, ',', '.') : '0,00' ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Última Venda</h5>
                            <h6><?= date('d/m/Y', strtotime($vendas[0]['created_at'])) ?></h6>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Nenhuma venda encontrada. 
                <a href="/vendas/nova" class="alert-link">Realizar primeira venda</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'otica/views/layout/footer.php'; ?> 