<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Painel Administrativo';
$moduleName = 'Bem-vindo ao Wiz Óptica';

try {
    $stmt = $db->prepare("SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as valor FROM vendas WHERE DATE(data_venda) = CURDATE()");
    $stmt->execute();
    $vendasHoje = $stmt->fetch();

    $stmt = $db->prepare("SELECT COUNT(*) as total FROM clientes WHERE DATE(criado_em) = CURDATE()");
    $stmt->execute();
    $novosClientes = $stmt->fetch();

    $stmt = $db->prepare("SELECT COUNT(*) as total FROM produtos WHERE estoque > 0");
    $stmt->execute();
    $produtosEstoque = $stmt->fetch();

    $stmt = $db->prepare("SELECT COALESCE(SUM(total), 0) as valor FROM vendas WHERE MONTH(data_venda) = MONTH(CURDATE()) AND YEAR(data_venda) = YEAR(CURDATE())");
    $stmt->execute();
    $receitaMes = $stmt->fetch();

    $stmt = $db->query("SELECT * FROM logs ORDER BY id DESC LIMIT 10");
    $atividades = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Erro: " . $e->getMessage());
    $vendasHoje = ['total' => 0, 'valor' => 0];
    $novosClientes = ['total' => 0];
    $produtosEstoque = ['total' => 0];
    $receitaMes = ['valor' => 0];
    $atividades = [];
}

$vendasHoje['total'] = $vendasHoje['total'] ?? 0;
$vendasHoje['valor'] = $vendasHoje['valor'] ?? 0;
$novosClientes['total'] = $novosClientes['total'] ?? 0;
$produtosEstoque['total'] = $produtosEstoque['total'] ?? 0;
$receitaMes['valor'] = $receitaMes['valor'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Wiz Óptica</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        darkblue: '#0f172a',
                        darkblue2: '#1e293b',
                        darkblue3: '#334155',
                        accent: '#0ea5e9',
                        accent2: '#0284c7'
                    },
                    fontFamily: {
                        nunito: ['Nunito', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Nunito', sans-serif; background: #f1f5f9; }
        
        .sidebar {
            background: linear-gradient(180deg, #0f172a 0%, #1e3a5f 100%);
            width: 260px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0,0,0,0.3);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .sidebar-logo i { font-size: 1.75rem; color: #0ea5e9; }
        .sidebar-logo h1 { font-size: 1.25rem; font-weight: 700; color: #f8fafc; }
        
        .menu-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            margin: 1rem 0 0.5rem 0.75rem;
            font-weight: 600;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .menu-item:hover {
            background: rgba(14, 165, 233, 0.15);
            color: #f8fafc;
            transform: translateX(4px);
        }
        
        .menu-item.active {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
        }
        
        .menu-item i { width: 20px; text-align: center; }
        
        .main-content {
            margin-left: 260px;
            padding: 2rem;
            min-height: 100vh;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title-page {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0f172a;
        }
        
        .page-subtitle {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.875rem;
        }
        
        .user-name { font-weight: 600; color: #0f172a; font-size: 0.875rem; }
        
        .card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        .card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-title i { color: #0ea5e9; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            border-left: 4px solid;
        }
        
        .stat-card.blue { border-color: #0ea5e9; }
        .stat-card.green { border-color: #10b981; }
        .stat-card.purple { border-color: #8b5cf6; }
        .stat-card.orange { border-color: #f59e0b; }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-icon.blue { background: linear-gradient(135deg, #0ea5e9, #0284c7); }
        .stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        
        .stat-info { flex: 1; }
        
        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }
        
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .activity-item:hover {
            background: #f8fafc;
        }
        
        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
        }
        
        .activity-icon.info { background: #dbeafe; color: #2563eb; }
        
        .activity-content { flex: 1; }
        
        .activity-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #0f172a;
        }
        
        .activity-desc {
            font-size: 0.75rem;
            color: #64748b;
        }
        
        .activity-time {
            font-size: 0.7rem;
            color: #94a3b8;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }
        
        .quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.25rem;
            background: #f8fafc;
            border-radius: 12px;
            text-decoration: none;
            color: #0f172a;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .quick-action:hover {
            background: white;
            border-color: #0ea5e9;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.2);
        }
        
        .quick-action i {
            font-size: 1.5rem;
            color: #0ea5e9;
            margin-bottom: 0.5rem;
        }
        
        .quick-action span {
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.75rem;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-family: inherit;
            transition: all 0.2s ease;
            margin-top: auto;
        }
        
        .btn-logout:hover {
            background: #ef4444;
            color: white;
        }
        
        .menu-scroll {
            flex: 1;
            overflow-y: auto;
            padding-right: 0.5rem;
        }
        
        .menu-scroll::-webkit-scrollbar { width: 4px; }
        .menu-scroll::-webkit-scrollbar-track { background: transparent; }
        .menu-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        
        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
            .quick-actions { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-glasses"></i>
            <h1>Wiz Óptica</h1>
        </div>
        
        <div class="menu-scroll">
            <div class="menu-group">
                <p class="menu-title">Principal</p>
                <a href="index.php" class="menu-item active">
                    <i class="fas fa-home"></i>
                    <span>Início</span>
                </a>
                <a href="../vendas/nova.php" class="menu-item">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nova Venda</span>
                </a>
                <a href="../vendas/historico.php" class="menu-item">
                    <i class="fas fa-history"></i>
                    <span>Histórico Vendas</span>
                </a>
            </div>
            
            <div class="menu-group">
                <p class="menu-title">Cadastros</p>
                <a href="../clientes/index.php" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Clientes</span>
                </a>
                <a href="../clientes/novo.php" class="menu-item">
                    <i class="fas fa-user-plus"></i>
                    <span>Novo Cliente</span>
                </a>
                <a href="../produtos/index.php" class="menu-item">
                    <i class="fas fa-box"></i>
                    <span>Produtos</span>
                </a>
                <a href="../produtos/novo.php" class="menu-item">
                    <i class="fas fa-plus"></i>
                    <span>Novo Produto</span>
                </a>
            </div>
            
            <div class="menu-group">
                <p class="menu-title">Serviços</p>
                <a href="../receitas/index.php" class="menu-item">
                    <i class="fas fa-glasses"></i>
                    <span>Receitas</span>
                </a>
                <a href="../receitas/nova.php" class="menu-item">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nova Receita</span>
                </a>
                <a href="../agendamentos/index.php" class="menu-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Agendamentos</span>
                </a>
                <a href="../agendamentos/novo.php" class="menu-item">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Novo Agendamento</span>
                </a>
            </div>
            
            <div class="menu-group">
                <p class="menu-title">Financeiro</p>
                <a href="../financeiro/relatorio.php" class="menu-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Relatórios</span>
                </a>
            </div>
        </div>
        
        <button onclick="logout()" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair</span>
        </button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="page-header">
            <div>
                <h1 class="page-title-page"><?php echo $pageTitle; ?></h1>
                <p class="page-subtitle"><?php echo $moduleName; ?></p>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?></div>
            </div>
        </header>
        
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon blue">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $vendasHoje['total']; ?></div>
                    <div class="stat-label">Vendas hoje</div>
                </div>
            </div>
            
            <div class="stat-card green">
                <div class="stat-icon green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">R$ <?php echo number_format($vendasHoje['valor'], 2, ',', '.'); ?></div>
                    <div class="stat-label">Valor hoje</div>
                </div>
            </div>
            
            <div class="stat-card purple">
                <div class="stat-icon purple">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $novosClientes['total']; ?></div>
                    <div class="stat-label">Novos clientes hoje</div>
                </div>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-icon orange">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $produtosEstoque['total']; ?></div>
                    <div class="stat-label">Produtos em estoque</div>
                </div>
            </div>
        </div>
        
        <!-- Content Grid -->
        <div class="grid-2">
            <!-- Atividade Recente -->
            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-clock"></i>
                    Atividade Recente
                </h2>
                <div class="activity-list">
                    <?php if (empty($atividades)): ?>
                        <p class="text-gray-500 text-center py-4">Nenhuma atividade recente</p>
                    <?php else: ?>
                        <?php foreach ($atividades as $atividade): ?>
                            <div class="activity-item">
                                <div class="activity-icon info">
                                    <i class="fas fa-info"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo htmlspecialchars(ucfirst($atividade['acao'])); ?></div>
                                    <div class="activity-desc"><?php echo htmlspecialchars($atividade['detalhes']); ?></div>
                                </div>
                                <div class="activity-time"><?php echo date('H:i', strtotime($atividade['data'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Receita Mensal -->
            <div class="card">
                <h2 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Receita do Mês
                </h2>
                <div class="text-center py-4">
                    <div class="text-4xl font-extrabold text-darkblue mb-2">
                        R$ <?php echo number_format($receitaMes['valor'], 2, ',', '.'); ?>
                    </div>
                    <p class="text-gray-500">Total de vendas este mês</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-bolt"></i>
                Ações Rápidas
            </h2>
            <div class="quick-actions">
                <a href="../vendas/nova.php" class="quick-action">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Nova Venda</span>
                </a>
                <a href="../clientes/novo.php" class="quick-action">
                    <i class="fas fa-user-plus"></i>
                    <span>Novo Cliente</span>
                </a>
                <a href="../produtos/novo.php" class="quick-action">
                    <i class="fas fa-plus-square"></i>
                    <span>Novo Produto</span>
                </a>
                <a href="../receitas/nova.php" class="quick-action">
                    <i class="fas fa-prescription-bottle"></i>
                    <span>Nova Receita</span>
                </a>
            </div>
        </div>
    </main>
    
    <script>
        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = '../login.php?action=logout';
            }
        }
    </script>
</body>
</html>
