<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

try {
    // Vendas de hoje (use o campo correto de valor, geralmente valor_total)
    $stmt = $db->prepare("SELECT COUNT(*) as total, SUM(valor_total) as valor FROM vendas WHERE DATE(data_venda) = CURDATE()");
    $stmt->execute();
    $vendasHoje = $stmt->fetch();

    // Novos clientes hoje (confirme se o campo é criado_em)
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM clientes WHERE DATE(criado_em) = CURDATE()");
    $stmt->execute();
    $novosClientes = $stmt->fetch();

    // Total de produtos diferentes em estoque (produtos com estoque > 0)
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM produtos WHERE estoque > 0");
    $stmt->execute();
    $produtosEstoque = $stmt->fetch();

    // Receita do mês atual (use o campo correto de valor, geralmente valor_total)
    $stmt = $db->prepare("SELECT SUM(valor_total) as valor FROM vendas WHERE MONTH(data_venda) = MONTH(CURDATE()) AND YEAR(data_venda) = YEAR(CURDATE())");
    $stmt->execute();
    $receitaMes = $stmt->fetch();

    // Atividade recente (últimas 10 ações)
    $stmt = $db->prepare("
        SELECT l.acao, l.detalhes, l.data, u.nome as usuario 
        FROM logs l 
        LEFT JOIN usuarios u ON l.usuario_id = u.id 
        ORDER BY l.data DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $atividades = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Erro ao buscar estatísticas: " . $e->getMessage());
    // Valores padrão em caso de erro
    $vendasHoje = ['total' => 0, 'valor' => 0];
    $novosClientes = ['total' => 0];
    $produtosEstoque = ['total' => 0];
    $receitaMes = ['valor' => 0];
    $atividades = [];
}

// Garantir valores padrão para evitar null
$vendasHoje['total'] = $vendasHoje['total'] ?? 0;
$vendasHoje['valor'] = $vendasHoje['valor'] ?? 0;
$novosClientes['total'] = $novosClientes['total'] ?? 0;
$produtosEstoque['total'] = $produtosEstoque['total'] ?? 0;
$receitaMes['valor'] = $receitaMes['valor'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Wiz Ótica</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'otica-primary': '#28d2c3',
                        'otica-secondary': '#20b8a9',
                        'otica-accent': '#f4a261',
                        'otica-warm': '#e76f51',
                        'otica-sage': '#a4c3a2',
                        'otica-cream': '#f7f3e9',
                        'otica-mist': '#e8f4f8',
                        'otica-forest': '#2d5016',
                        'otica-gold': '#f1c40f',
                        'otica-coral': '#ff6b6b',
                        'otica-blue': '#3b82f6',
                        'otica-indigo': '#6366f1',
                        'otica-purple': '#8b5cf6',
                        'otica-pink': '#ec4899',
                        'otica-red': '#ef4444',
                        'otica-orange': '#f97316',
                        'otica-yellow': '#eab308',
                        'otica-lime': '#84cc16',
                        'otica-green': '#22c55e',
                        'otica-emerald': '#10b981',
                        'otica-teal': '#14b8a6',
                        'otica-cyan': '#06b6d4',
                        'otica-sky': '#0ea5e9'
                    }
                }
            }
        }
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        /* Light Mode */
        .light {
            background-color: #f8fafc;
            color: #1f2937;
        }

        .light .sidebar {
            background-color: #1e293b;
        }

        .light .sidebar-item:hover {
            background-color: #334155;
        }

        .light .sidebar-item.active {
            background-color: #28d2c3;
        }

        .light .card {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .light .chart-container {
            background: white;
            border: 1px solid #e2e8f0;
        }

        .light .activity-item {
            background-color: #f0fdfa;
        }

        .light .activity-item:hover {
            background-color: #ccfbf1;
        }

        /* Light Mode - Stat Cards */
        .light .stat-card {
            background-color: #28d2c3 !important;
            color: white !important;
        }

        .light .stat-card.sales {
            background-color: #3b82f6 !important;
        }

        .light .stat-card.clients {
            background-color: #6366f1 !important;
        }

        .light .stat-card.revenue {
            background-color: #10b981 !important;
        }

        /* Dark Mode */
        .dark {
            background-color: #0f172a;
            color: #f1f5f9;
        }

        .dark .sidebar {
            background-color: #1e293b;
        }

        .dark .sidebar-item:hover {
            background-color: #334155;
        }

        .dark .sidebar-item.active {
            background-color: #28d2c3;
        }

        .dark .card {
            background: #1e293b;
            border: 1px solid #334155;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .dark .chart-container {
            background: #1e293b;
            border: 1px solid #334155;
        }

        .dark .activity-item {
            background-color: #0f172a;
        }

        .dark .activity-item:hover {
            background-color: #1e293b;
        }

        /* Dark Mode - Stat Cards */
        .dark .stat-card {
            background-color: #28d2c3 !important;
            color: white !important;
        }

        .dark .stat-card.sales {
            background-color: #3b82f6 !important;
        }

        .dark .stat-card.clients {
            background-color: #6366f1 !important;
        }

        .dark .stat-card.revenue {
            background-color: #10b981 !important;
        }

        .dark .text-gray-800 {
            color: #f1f5f9;
        }

        .dark .text-gray-600 {
            color: #cbd5e1;
        }

        .dark .text-gray-500 {
            color: #94a3b8;
        }

        .dark .text-gray-400 {
            color: #64748b;
        }

        .dark .bg-gray-50 {
            background-color: #0f172a;
        }

        .dark .bg-gray-100 {
            background-color: #1e293b;
        }

        .dark .border-gray-200 {
            border-color: #334155;
        }

        /* Common Styles */
        .sidebar {
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }

        .sidebar-item {
            transition: all 0.3s ease;
            border-radius: 12px;
            margin: 4px 0;
        }

        .sidebar-item:hover {
            transform: translateX(5px);
        }

        .sidebar-item.active {
            box-shadow: 0 4px 15px rgba(40, 210, 195, 0.3);
        }

        .card {
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .stat-card {
            background-color: #28d2c3;
            color: white;
        }

        .stat-card.sales {
            background-color: #3b82f6;
        }

        .stat-card.clients {
            background-color: #6366f1;
        }

        .stat-card.revenue {
            background-color: #10b981;
        }

        .btn-primary {
            background-color: #28d2c3;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #20b8a9;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(40, 210, 195, 0.3);
        }

        .btn-danger {
            background-color: #ef4444;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
        }

        .notification {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .recent-activity {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 12px;
            border-left: 3px solid #28d2c3;
            margin-bottom: 8px;
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            transform: translateX(5px);
        }

        .quick-action-card {
            transition: all 0.3s ease;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
        }

        .quick-action-card:hover .icon {
            transform: scale(1.1);
        }

        .icon {
            transition: transform 0.3s ease;
        }

        .theme-toggle {
            transition: all 0.3s ease;
        }

        .theme-toggle.rotated {
            transform: rotate(180deg);
        }

        /* Estilos para a barra de rolagem do sidebar */
        .sidebar nav::-webkit-scrollbar {
            width: 0px;
            display: none;
        }

        .sidebar nav::-webkit-scrollbar-track {
            display: none;
        }

        .sidebar nav::-webkit-scrollbar-thumb {
            display: none;
        }

        .sidebar nav::-webkit-scrollbar-thumb:hover {
            display: none;
        }

        /* Para Firefox */
        .sidebar nav {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="sidebar fixed left-0 top-0 h-full w-64 z-50">
        <div class="p-6 h-full flex flex-col">
            <!-- Logo -->
            <div class="flex items-center mb-8 flex-shrink-0">
                <i class="fas fa-glasses text-3xl text-white mr-3"></i>
                <h1 class="text-xl font-bold text-white">Wiz Admin</h1>
            </div>

            <!-- Menu Items -->
            <nav class="space-y-2 flex-1 overflow-y-auto">
                <a href="#" class="sidebar-item active flex items-center p-3 text-white">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                    <span>Painel</span>
                </a>
                <a href="../vendas/nova.php" class="sidebar-item flex items-center p-3 text-white">
                    <i class="fas fa-shopping-cart w-5 mr-3"></i>
                    <span>Nova Venda</span>
                </a>
                <a href="../vendas/historico.php" class="sidebar-item flex items-center p-3 text-white">
                    <i class="fas fa-history w-5 mr-3"></i>
                    <span>Histórico</span>
                </a>
                <a href="../clientes/index.php" class="sidebar-item flex items-center p-3 text-white">
                    <i class="fas fa-users w-5 mr-3"></i>
                    <span>Clientes</span>
                </a>
                <a href="../clientes/novo.php" class="sidebar-item flex items-center p-3 text-white">
                    <i class="fas fa-user-plus w-5 mr-3"></i>
                    <span>Novo Cliente</span>
                </a>
                <a href="../receitas/index.php" class="sidebar-item flex items-center p-3 text-white">
                    <i class="fas fa-eye w-5 mr-3"></i>
                    <span>Receitas</span>
                </a>
                <a href="../receitas/nova.php" class="sidebar-item flex items-center p-3 text-white">
                    <i class="fas fa-plus-circle w-5 mr-3"></i>
                    <span>Nova Receita</span>
                </a>
                <a href="../financeiro/relatorio.php" class="sidebar-item flex items-center p-3 text-white">
                    <i class="fas fa-chart-line w-5 mr-3"></i>
                    <span>Financeiro</span>
                </a>
                <a href="../produtos/index.php" class="sidebar-item flex items-center p-3 text-white">
                    <i class="fas fa-box w-5 mr-3"></i>
                    <span>Produtos</span>
                </a>
                <a href="../produtos/novo.php" class="sidebar-item flex items-center p-3 text-white">
                    <i class="fas fa-plus-circle w-5 mr-3"></i>
                    <span>Novo Produto</span>
                </a>
            </nav>
            
            <!-- Logout Button -->

            <div class="absolute bottom-6 left-6 right-6">
                <button onclick="showConfirmModal('Tem certeza que deseja sair?', function() { window.location.href = '../../controllers/LoginController.php?action=logout'; })" class="btn-danger w-full py-3 px-4 text-white rounded-lg font-semibold">
            
                
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Sair
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button onclick="toggleSidebar()" class="bg-white dark:bg-gray-800 p-2 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600">
            <i class="fas fa-bars text-gray-700 dark:text-gray-300"></i>
        </button>
    </div>

    <!-- Main Content -->
    <div class="main-content ml-0 lg:ml-64 p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-300">Bem-vindo ao painel administrativo da Wiz</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Theme Toggle -->
                <button onclick="toggleTheme()" class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-md border border-gray-200 dark:border-gray-600 theme-toggle">
                    <i class="fas fa-moon text-gray-600 dark:text-yellow-400" id="theme-icon"></i>
                </button>
                
                <div class="relative">
                    <button class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-md border border-gray-200 dark:border-gray-600">
                        <i class="fas fa-bell text-gray-600 dark:text-gray-300"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </button>
                </div>
                <div class="flex items-center space-x-3 bg-white dark:bg-gray-800 p-3 rounded-lg shadow-md border border-gray-200 dark:border-gray-600">
                    <div class="w-8 h-8 bg-otica-primary rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin'); ?></span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card stat-card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-black/90 text-sm font-medium">Vendas Hoje</p>
                        <p class="text-3xl font-bold text-black"><?php echo $vendasHoje['total'] ?? 0; ?></p>
                        <p class="text-black/80 text-sm mt-1">
                            R$ <?php echo number_format($vendasHoje['valor'] ?? 0, 2, ',', '.'); ?>
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-black/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="card stat-card sales p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/90 text-sm font-medium">Novos Clientes</p>
                        <p class="text-3xl font-bold text-white"><?php echo $novosClientes['total'] ?? 0; ?></p>
                        <p class="text-white/80 text-sm mt-1">
                            Hoje
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-plus text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="card stat-card clients p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/90 text-sm font-medium">Produtos Estoque</p>
                        <p class="text-3xl font-bold text-white"><?php echo $produtosEstoque['total'] ?? 0; ?></p>
                        <p class="text-white/80 text-sm mt-1">
                            Disponível
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-boxes text-white text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="card stat-card revenue p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/90 text-sm font-medium">Receita Mensal</p>
                        <p class="text-3xl font-bold text-white">R$ <?php echo number_format($receitaMes['valor'] ?? 0, 2, ',', '.'); ?></p>
                        <p class="text-white/80 text-sm mt-1">
                            Este mês
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sales Chart -->
            <div class="lg:col-span-2 chart-container rounded-lg">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Vendas dos Últimos 7 Dias</h3>
                <div class="h-64 bg-gray-50 dark:bg-gray-800 rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-600">
                    <div class="text-center">
                        <i class="fas fa-chart-bar text-4xl text-gray-400 dark:text-gray-500 mb-2"></i>
                        <p class="text-gray-500 dark:text-gray-400">Gráfico de vendas será implementado</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="chart-container rounded-lg">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Atividade Recente</h3>
                <div class="recent-activity">
                    <?php if (empty($atividades)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-info-circle text-2xl text-gray-400 dark:text-gray-500 mb-2"></i>
                            <p class="text-gray-500 dark:text-gray-400">Nenhuma atividade recente</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($atividades as $atividade): ?>
                            <div class="activity-item">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-otica-primary rounded-full mt-2 mr-3"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white"><?php echo htmlspecialchars(ucfirst($atividade['acao'])); ?></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($atividade['detalhes']); ?></p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500"><?php echo date('d/m/Y H:i', strtotime($atividade['data'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Ações Rápidas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="../vendas/nova.php" class="card quick-action-card p-6 text-center hover:bg-otica-primary hover:text-white transition-all duration-300">
                    <i class="fas fa-plus-circle text-3xl text-otica-primary mb-3 icon"></i>
                    <h4 class="font-semibold">Nova Venda</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Registrar nova venda</p>
                </a>

                <a href="../clientes/novo.php" class="card quick-action-card p-6 text-center hover:bg-otica-blue hover:text-white transition-all duration-300">
                    <i class="fas fa-user-plus text-3xl text-otica-blue mb-3 icon"></i>
                    <h4 class="font-semibold">Novo Cliente</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Cadastrar cliente</p>
                </a>

                <a href="../produtos.php?action=novo" class="card quick-action-card p-6 text-center hover:bg-otica-indigo hover:text-white transition-all duration-300">
                    <i class="fas fa-box text-3xl text-otica-indigo mb-3 icon"></i>
                    <h4 class="font-semibold">Novo Produto</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Cadastrar produto</p>
                </a>

                <a href="../financeiro/relatorio.php" class="card quick-action-card p-6 text-center hover:bg-otica-emerald hover:text-white transition-all duration-300">
                    <i class="fas fa-chart-pie text-3xl text-otica-emerald mb-3 icon"></i>
                    <h4 class="font-semibold">Relatórios</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerar relatórios</p>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg w-full max-w-sm">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Confirmação</h2>
        <p class="mb-6 text-gray-600 dark:text-gray-300" id="confirmModalMessage">Tem certeza que deseja sair?</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeConfirmModal()" class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-white">Cancelar</button>
            <button onclick="confirmModalAction()" class="px-4 py-2 rounded bg-otica-primary text-white font-semibold">Confirmar</button>
        </div>
    </div>
</div>

    <script>
        // Theme management
        function toggleTheme() {
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            const themeButton = document.querySelector('.theme-toggle');
            
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                html.classList.add('light');
                themeIcon.className = 'fas fa-moon text-gray-600';
                localStorage.setItem('theme', 'light');
                // Remover rotação quando voltar para o tema claro
                themeButton.classList.remove('rotated');
            } else {
                html.classList.remove('light');
                html.classList.add('dark');
                themeIcon.className = 'fas fa-sun text-yellow-400';
                localStorage.setItem('theme', 'dark');
                // Adicionar rotação quando ativar o tema escuro
                themeButton.classList.add('rotated');
            }
        }

        // Load saved theme
        function loadTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            
            html.classList.remove('light', 'dark');
            html.classList.add(savedTheme);
            
            if (savedTheme === 'dark') {
                themeIcon.className = 'fas fa-sun text-yellow-400';
            } else {
                themeIcon.className = 'fas fa-moon text-gray-600';
            }
        }

        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('open');
        }

        // Logout function
        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = '../../controllers/LoginController.php?action=logout';
            }
        }

        // Confirm modal functions
        let confirmModalCallback = null;

        function showConfirmModal(message, callback) {
            document.getElementById('confirmModalMessage').textContent = message;
            document.getElementById('confirmModal').classList.remove('hidden');
            confirmModalCallback = callback;
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            confirmModalCallback = null;
        }

        function confirmModalAction() {
            if (typeof confirmModalCallback === 'function') {
                confirmModalCallback();
            }
            closeConfirmModal();
        }

        // Add active class to current menu item
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
            
            const currentPath = window.location.pathname;
            const menuItems = document.querySelectorAll('.sidebar-item');
            
            menuItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href && currentPath.includes(href)) {
                    item.classList.add('active');
                }
            });
        });

        // Auto-refresh stats every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);

        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>
</html>
