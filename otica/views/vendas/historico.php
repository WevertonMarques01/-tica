<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Histórico de Vendas';
$moduleName = 'Veja todas as vendas realizadas';

try {
    $stmt = $db->query("SELECT v.*, c.nome as cliente_nome FROM vendas v LEFT JOIN clientes c ON v.cliente_id = c.id ORDER BY v.id DESC");
    $vendas = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro: " . $e->getMessage());
    $vendas = [];
}

include '../layout_base.php';
?>

<div class="card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="card-title">
            <i class="fas fa-history"></i>
            Todas as Vendas
        </h2>
        <a href="nova.php" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nova Venda
        </a>
    </div>
    
    <?php if (empty($vendas)): ?>
    <div class="empty-state">
        <i class="fas fa-shopping-cart"></i>
        <h3>Nenhuma venda realizada</h3>
        <p>Comece criando sua primeira venda.</p>
        <a href="nova.php" class="btn btn-primary mt-4">
            <i class="fas fa-plus"></i>
            Criar Venda
        </a>
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
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $v): ?>
                <tr>
                    <td>#<?php echo $v['id']; ?></td>
                    <td class="font-semibold"><?php echo htmlspecialchars($v['cliente_nome'] ?? 'Sem cliente'); ?></td>
                    <td class="font-bold text-green-600">R$ <?php echo number_format($v['total'] ?? 0, 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($v['forma_pagamento'] ?? '-')); ?></td>
                    <td><?php echo $v['data_venda'] ? date('d/m/Y H:i', strtotime($v['data_venda'])) : '-'; ?></td>
                    <td>
                        <div class="actions">
                            <a href="visualizar.php?id=<?php echo $v['id']; ?>" class="btn-icon" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="excluir.php?id=<?php echo $v['id']; ?>" class="btn-icon danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta venda?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include '../layout_end.php'; ?>
