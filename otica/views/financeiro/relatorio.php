<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Relatório Financeiro';
$moduleName = 'Visualize o financeiro do período';

$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');

try {
    $stmt = $db->prepare("SELECT v.*, c.nome as cliente_nome FROM vendas v LEFT JOIN clientes c ON v.cliente_id = c.id WHERE DATE(v.data_venda) BETWEEN ? AND ? ORDER BY v.id DESC");
    $stmt->execute([$data_inicio, $data_fim]);
    $vendas = $stmt->fetchAll();
    
    $total = 0;
    foreach ($vendas as $v) {
        $total += $v['total'] ?? 0;
    }
} catch (PDOException $e) {
    error_log("Erro: " . $e->getMessage());
    $vendas = [];
    $total = 0;
}

include '../layout_base.php';
?>

<div class="card">
<<<<<<< Updated upstream
    <form method="GET" class="flex gap-4 mb-6">
=======
    <form method="GET" class="flex gap-4 mb-4">
>>>>>>> Stashed changes
        <div class="form-group" style="margin-bottom: 0; flex: 1;">
            <label for="data_inicio" class="form-label">Data Início</label>
            <input type="date" name="data_inicio" id="data_inicio" class="form-input" value="<?php echo $data_inicio; ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0; flex: 1;">
            <label for="data_fim" class="form-label">Data Fim</label>
            <input type="date" name="data_fim" id="data_fim" class="form-input" value="<?php echo $data_fim; ?>">
        </div>
<<<<<<< Updated upstream
        <div class="form-group" style="margin-bottom: 0; display: flex; align-items: flex-end;">
=======
        <div class="form-group" style="margin-bottom: 0; display: flex; align-items: flex-end; gap: 8px;">
>>>>>>> Stashed changes
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Filtrar
            </button>
<<<<<<< Updated upstream
        </div>
    </form>
    
=======
            <a href="imprimir.php?data_inicio=<?php echo $data_inicio; ?>&data_fim=<?php echo $data_fim; ?>" target="_blank" class="btn btn-success">
                <i class="fas fa-print"></i>
                Imprimir
            </a>
        </div>
    </form>
    
    <div class="flex gap-2 mb-6">
        <a href="?data_inicio=<?php echo date('Y-m-d'); ?>&data_fim=<?php echo date('Y-m-d'); ?>" class="text-sm text-blue-600 hover:underline">Hoje</a>
        <span class="text-gray-300">|</span>
        <a href="?data_inicio=<?php echo date('Y-m-01'); ?>&data_fim=<?php echo date('Y-m-t'); ?>" class="text-sm text-blue-600 hover:underline">Este Mês</a>
        <span class="text-gray-300">|</span>
        <a href="?data_inicio=<?php echo date('Y-01-01'); ?>&data_fim=<?php echo date('Y-12-31'); ?>" class="text-sm text-blue-600 hover:underline">Este Ano</a>
    </div>
    
>>>>>>> Stashed changes
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="stat-card blue" style="padding: 1.25rem;">
            <div class="flex items-center gap-3">
                <div class="stat-icon blue" style="width: 48px; height: 48px; font-size: 1.25rem;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size: 1.5rem;"><?php echo count($vendas); ?></div>
                    <div class="stat-label">Total de Vendas</div>
                </div>
            </div>
        </div>
        <div class="stat-card green" style="padding: 1.25rem;">
            <div class="flex items-center gap-3">
                <div class="stat-icon green" style="width: 48px; height: 48px; font-size: 1.25rem;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size: 1.5rem;">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
                    <div class="stat-label">Receita Total</div>
                </div>
            </div>
        </div>
        <div class="stat-card purple" style="padding: 1.25rem;">
            <div class="flex items-center gap-3">
                <div class="stat-icon purple" style="width: 48px; height: 48px; font-size: 1.25rem;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size: 1.5rem;">R$ <?php echo number_format($total / max(count($vendas), 1), 2, ',', '.'); ?></div>
                    <div class="stat-label">Média por Venda</div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (empty($vendas)): ?>
    <div class="empty-state">
        <i class="fas fa-chart-line"></i>
        <h3>Nenhuma venda no período</h3>
        <p>Não foram encontradas vendas neste intervalo de datas.</p>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Pagamento</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $v): ?>
                <tr>
                    <td>#<?php echo $v['id']; ?></td>
                    <td><?php echo htmlspecialchars($v['cliente_nome'] ?? 'Sem cliente'); ?></td>
                    <td class="font-bold text-green-600">R$ <?php echo number_format($v['total'] ?? 0, 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($v['forma_pagamento'] ?? '-')); ?></td>
                    <td><?php echo $v['data_venda'] ? date('d/m/Y H:i', strtotime($v['data_venda'])) : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include '../layout_end.php'; ?>
