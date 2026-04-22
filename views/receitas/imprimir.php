<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=id_invalido');
    exit;
}

$receitaId = (int)$_GET['id'];
$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("SELECT r.*, c.nome as cliente_nome, c.cpf as cliente_cpf, c.telefone as cliente_telefone 
                      FROM receitas r 
                      LEFT JOIN clientes c ON r.cliente_id = c.id 
                      WHERE r.id = ?");
$stmt->execute([$receitaId]);
$receita = $stmt->fetch();

if (!$receita) {
    header('Location: index.php?error=receita_nao_encontrada');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receita Ã“ptica - #<?php echo $receita['id']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', Arial, sans-serif; font-size: 14px; padding: 40px; background: white; }
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
        .cliente-info { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .cliente-info h3 { color: #333; margin-bottom: 10px; font-size: 16px; }
        .cliente-info p { margin: 5px 0; color: #555; }
        .receita-grid { display: table; width: 100%; border-collapse: collapse; margin-top: 20px; }
        .receita-grid .olho { display: table-cell; width: 50%; padding: 15px; vertical-align: top; }
        .receita-grid .olho h3 { background: #1e40af; color: white; padding: 10px; text-align: center; border-radius: 5px 5px 0 0; margin: -15px -15px 15px -15px; }
        .receita-grid table { width: 100%; border: 1px solid #ddd; }
        .receita-grid th, .receita-grid td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        .receita-grid th { background: #f8f9fa; font-weight: bold; width: 40%; }
        .obs { background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #ffc107; }
        .obs h4 { color: #856404; margin-bottom: 5px; }
        .footer { margin-top: 40px; text-align: center; color: #999; font-size: 12px; }
        .data { text-align: right; margin-bottom: 20px; }
    </style>
</head>
<body>
    <a href="index.php" class="btn-back no-print">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
    <button class="btn-print no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir / Salvar PDF
    </button>
    
    <div class="header">
        <h1>Ã“TICA</h1>
        <p>Receita Ã“ptica</p>
    </div>
    
    <div class="data">
        <strong>Data:</strong> <?php echo $receita['data_receita'] ? date('d/m/Y', strtotime($receita['data_receita'])) : date('d/m/Y'); ?>
    </div>
    
    <div class="cliente-info">
        <h3>Dados do Cliente</h3>
        <p><strong>Nome:</strong> <?php echo htmlspecialchars($receita['cliente_nome']); ?></p>
        <?php if (!empty($receita['cliente_cpf'])): ?>
        <p><strong>CPF:</strong> <?php echo htmlspecialchars($receita['cliente_cpf']); ?></p>
        <?php endif; ?>
        <?php if (!empty($receita['cliente_telefone'])): ?>
        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($receita['cliente_telefone']); ?></p>
        <?php endif; ?>
    </div>
    
    <div class="receita-grid">
        <div class="olho">
            <h3>Olho Direito (OD)</h3>
            <table>
                <tr><th>Esfera</th><td><?php echo !empty($receita['esfera_od']) ? $receita['esfera_od'] : '-'; ?></td></tr>
                <tr><th>Cilindro</th><td><?php echo !empty($receita['cilindro_od']) ? $receita['cilindro_od'] : '-'; ?></td></tr>
                <tr><th>Eixo</th><td><?php echo !empty($receita['eixo_od']) ? $receita['eixo_od'] : '-'; ?></td></tr>
                <tr><th>AdiÃ§Ã£o</th><td><?php echo !empty($receita['adicao_od']) ? $receita['adicao_od'] : '-'; ?></td></tr>
                <tr><th>DP</th><td><?php echo !empty($receita['dp_od']) ? $receita['dp_od'] : '-'; ?></td></tr>
            </table>
        </div>
        <div class="olho">
            <h3>Olho Esquerdo (OE)</h3>
            <table>
                <tr><th>Esfera</th><td><?php echo !empty($receita['esfera_oe']) ? $receita['esfera_oe'] : '-'; ?></td></tr>
                <tr><th>Cilindro</th><td><?php echo !empty($receita['cilindro_oe']) ? $receita['cilindro_oe'] : '-'; ?></td></tr>
                <tr><th>Eixo</th><td><?php echo !empty($receita['eixo_oe']) ? $receita['eixo_oe'] : '-'; ?></td></tr>
                <tr><th>AdiÃ§Ã£o</th><td><?php echo !empty($receita['adicao_oe']) ? $receita['adicao_oe'] : '-'; ?></td></tr>
                <tr><th>DP</th><td><?php echo !empty($receita['dp_oe']) ? $receita['dp_oe'] : '-'; ?></td></tr>
            </table>
        </div>
    </div>
    
    <?php if (!empty($receita['observacoes'])): ?>
    <div class="obs">
        <h4>ObservaÃ§Ãµes</h4>
        <p><?php echo nl2br(htmlspecialchars($receita['observacoes'])); ?></p>
    </div>
    <?php endif; ?>
    
    <div class="footer">
        <p>Receita gerada em <?php echo date('d/m/Y H:i'); ?></p>
    </div>
</body>
</html>
