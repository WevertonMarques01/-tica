<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');

$db = Database::getInstance()->getConnection();

try {
    $stmt = $db->prepare("SELECT v.*, c.nome as cliente_nome FROM vendas v LEFT JOIN clientes c ON v.cliente_id = c.id WHERE DATE(v.data_venda) BETWEEN ? AND ? ORDER BY v.id DESC");
    $stmt->execute([$data_inicio, $data_fim]);
    $vendas = $stmt->fetchAll();
    
    $total = 0;
    foreach ($vendas as $v) {
        $total += $v['total'] ?? 0;
    }
} catch (PDOException $e) {
    $vendas = [];
    $total = 0;
}

$periodo = "Período: " . date('d/m/Y', strtotime($data_inicio)) . " a " . date('d/m/Y', strtotime($data_fim));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro - <?php echo $periodo; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', Arial, sans-serif; font-size: 12px; padding: 30px; background: white; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #1e40af; padding-bottom: 15px; }
        .header h1 { color: #1e40af; font-size: 24px; margin-bottom: 5px; }
        .header p { color: #666; font-size: 12px; }
        .btn-print { 
            position: fixed; top: 20px; right: 20px; 
            padding: 12px 24px; background: #1e40af; color: white;
            border: none; border-radius: 8px; cursor: pointer;
            font-size: 14px; font-weight: 600;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
        }
        .btn-back { 
            position: fixed; top: 20px; left: 20px; 
            padding: 12px 24px; background: #6b7280; color: white;
            border: none; border-radius: 8px; cursor: pointer;
            font-size: 14px; font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .btn-back:hover { background: #4b5563; }
        .btn-print:hover { background: #1e3a8a; }
        .periodo { text-align: center; margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .periodo strong { color: #333; }
        .stats { display: table; width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .stats td { padding: 15px; text-align: center; border: 1px solid #ddd; }
        .stats .stat-label { background: #f8f9fa; font-weight: bold; }
        .stats .stat-value { font-size: 18px; font-weight: bold; color: #1e40af; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: #1e40af; color: white; font-weight: 600; }
        .table tr:nth-child(even) { background: #f8f9fa; }
        .text-right { text-align: right; }
        .text-green { color: #28a745; }
        .footer { margin-top: 30px; text-align: center; color: #999; font-size: 11px; }
    </style>
</head>
<body>
    <button class="btn-print no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir / Salvar PDF
    </button>
    <a href="relatorio.php?data_inicio=<?php echo $data_inicio; ?>&data_fim=<?php echo $data_fim; ?>" class="btn-back no-print">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
    
    <div class="header">
        <h1>ÓTICA</h1>
        <p>Relatório Financeiro</p>
    </div>
    
    <div class="periodo">
        <strong><?php echo $periodo; ?></strong>
    </div>
    
    <table class="stats">
        <tr>
            <td class="stat-label">Total de Vendas</td>
            <td class="stat-label">Receita Total</td>
            <td class="stat-label">Média por Venda</td>
        </tr>
        <tr>
            <td class="stat-value"><?php echo count($vendas); ?></td>
            <td class="stat-value">R$ <?php echo number_format($total, 2, ',', '.'); ?></td>
            <td class="stat-value">R$ <?php echo number_format(count($vendas) > 0 ? $total / count($vendas) : 0, 2, ',', '.'); ?></td>
        </tr>
    </table>
    
    <?php if (empty($vendas)): ?>
    <p style="text-align: center; color: #666; padding: 30px;">Nenhuma venda no período informado.</p>
    <?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th class="text-right">Total</th>
                <th>Pagamento</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vendas as $v): ?>
            <tr>
                <td>#<?php echo $v['id']; ?></td>
                <td><?php echo htmlspecialchars($v['cliente_nome'] ?? 'Sem cliente'); ?></td>
                <td class="text-right text-green">R$ <?php echo number_format($v['total'] ?? 0, 2, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($v['forma_pagamento'] ?? '-')); ?></td>
                <td><?php echo $v['data_venda'] ? date('d/m/Y H:i', strtotime($v['data_venda'])) : '-'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    
    <div class="footer">
        <p>Gerado em <?php echo date('d/m/Y H:i'); ?></p>
    </div>
</body>
</html>