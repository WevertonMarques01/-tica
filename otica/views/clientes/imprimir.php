<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=id_invalido');
    exit;
}

$clienteId = (int)$_GET['id'];
$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->execute([$clienteId]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header('Location: index.php?error=cliente_nao_encontrado');
    exit;
}

$stmtVendas = $db->prepare("SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as valor_total FROM vendas WHERE cliente_id = ?");
$stmtVendas->execute([$clienteId]);
$vendas = $stmtVendas->fetch();

$stmtReceitas = $db->prepare("SELECT COUNT(*) as total FROM receitas WHERE cliente_id = ?");
$stmtReceitas->execute([$clienteId]);
$receitas = $stmtReceitas->fetch();

$stmtOrdens = $db->prepare("SELECT COUNT(*) as total FROM ordens_servico WHERE cliente_id = ?");
$stmtOrdens->execute([$clienteId]);
$ordens = $stmtOrdens->fetch();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cliente - <?php echo htmlspecialchars($cliente['nome']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', Arial, sans-serif; font-size: 13px; padding: 40px; background: white; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e40af; padding-bottom: 20px; }
        .header h1 { color: #1e40af; font-size: 28px; margin-bottom: 5px; }
        .header p { color: #666; font-size: 12px; }
        .btn-print { 
            position: fixed; top: 20px; right: 20px; 
            padding: 12px 24px; background: #1e40af; color: white;
            border: none; border-radius: 8px; cursor: pointer;
            font-size: 14px; font-weight: 600;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
        }
        .btn-print:hover { background: #1e3a8a; }
        .btn-back { 
            position: fixed; top: 20px; left: 20px; 
            padding: 12px 24px; background: #6b7280; color: white;
            border: none; border-radius: 8px; cursor: pointer;
            font-size: 14px; font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .btn-back:hover { background: #4b5563; }
        .section { margin-bottom: 20px; }
        .section h3 { background: #1e40af; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .info-grid { display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; padding: 8px; background: #f8f9fa; font-weight: bold; width: 30%; }
        .info-value { display: table-cell; padding: 8px; border-bottom: 1px solid #eee; }
        .stats { display: table; width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .stats td { padding: 15px; text-align: center; border: 1px solid #ddd; }
        .stats .stat-label { background: #f8f9fa; font-weight: bold; }
        .stats .stat-value { font-size: 20px; font-weight: bold; color: #1e40af; }
        .footer { margin-top: 40px; text-align: center; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <a href="visualizar.php?id=<?php echo $clienteId; ?>" class="btn-back no-print">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
    <button class="btn-print no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir / Salvar PDF
    </button>
    
    <div class="header">
        <h1>ÓTICA</h1>
        <p>Ficha do Cliente</p>
    </div>
    
    <table class="stats">
        <tr>
            <td class="stat-label">Vendas</td>
            <td class="stat-label">Valor Total</td>
            <td class="stat-label">Receitas</td>
            <td class="stat-label">Ordens de Serviço</td>
        </tr>
        <tr>
            <td class="stat-value"><?php echo $vendas['total']; ?></td>
            <td class="stat-value">R$ <?php echo number_format($vendas['valor_total'], 2, ',', '.'); ?></td>
            <td class="stat-value"><?php echo $receitas['total']; ?></td>
            <td class="stat-value"><?php echo $ordens['total']; ?></td>
        </tr>
    </table>
    
    <div class="section">
        <h3>Informações Pessoais</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nome Completo</div>
                <div class="info-value"><?php echo htmlspecialchars($cliente['nome']); ?></div>
            </div>
            <?php if (!empty($cliente['cpf'])): ?>
            <div class="info-row">
                <div class="info-label">CPF</div>
                <div class="info-value"><?php echo htmlspecialchars($cliente['cpf']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($cliente['telefone'])): ?>
            <div class="info-row">
                <div class="info-label">Telefone</div>
                <div class="info-value"><?php echo htmlspecialchars($cliente['telefone']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($cliente['celular'])): ?>
            <div class="info-row">
                <div class="info-label">Celular</div>
                <div class="info-value"><?php echo htmlspecialchars($cliente['celular']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($cliente['email'])): ?>
            <div class="info-row">
                <div class="info-label">E-mail</div>
                <div class="info-value"><?php echo htmlspecialchars($cliente['email']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($cliente['data_nascimento'])): ?>
            <div class="info-row">
                <div class="info-label">Data de Nascimento</div>
                <div class="info-value"><?php echo date('d/m/Y', strtotime($cliente['data_nascimento'])); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($cliente['endereco']) || !empty($cliente['bairro'])): ?>
    <div class="section">
        <h3>Endereço</h3>
        <div class="info-grid">
            <?php if (!empty($cliente['endereco'])): ?>
            <div class="info-row">
                <div class="info-label">Endereço</div>
                <div class="info-value"><?php echo htmlspecialchars($cliente['endereco']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($cliente['numero'])): ?>
            <div class="info-row">
                <div class="info-label">Número</div>
                <div class="info-value"><?php echo htmlspecialchars($cliente['numero']); ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($cliente['bairro'])): ?>
            <div class="info-row">
                <div class="info-label">Bairro</div>
                <div class="info-value"><?php echo htmlspecialchars($cliente['bairro']); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($cliente['observacoes'])): ?>
    <div class="section">
        <h3>Observações</h3>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <?php echo nl2br(htmlspecialchars($cliente['observacoes'])); ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="footer">
        <p>Gerado em <?php echo date('d/m/Y H:i'); ?></p>
    </div>
</body>
</html>