<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

// Buscar dados financeiros
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-01');
$data_fim = $_GET['data_fim'] ?? date('Y-m-t');

try {
    $stmt = $db->prepare("
        SELECT v.*, c.nome as cliente_nome 
        FROM vendas v 
        LEFT JOIN clientes c ON v.cliente_id = c.id 
        WHERE DATE(v.created_at) BETWEEN ? AND ?
        ORDER BY v.created_at DESC
    ");
    $stmt->execute([$data_inicio, $data_fim]);
    $vendas = $stmt->fetchAll();
    
    $total = array_sum(array_column($vendas, 'valor_total'));
} catch (PDOException $e) {
    error_log("Erro ao buscar vendas: " . $e->getMessage());
    $vendas = [];
    $total = 0;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro - Ótica</title>
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

        .light .card {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        /* Dark Mode */
        .dark {
            background-color: #0f172a;
            color: #f1f5f9;
        }

        .dark .card {
            background: #1e293b;
            border: 1px solid #334155;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
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
        .card {
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
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

        .theme-toggle {
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: rotate(180deg);
        }

        .table-row {
            transition: all 0.3s ease;
        }

        .table-row:hover {
            background-color: #f0fdfa;
        }

        .dark .table-row:hover {
            background-color: #0f172a;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <a href="../admin/index.php" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Relatório Financeiro</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <button onclick="toggleTheme()" class="bg-white dark:bg-gray-700 p-3 rounded-lg shadow-md border border-gray-200 dark:border-gray-600 theme-toggle">
                            <i class="fas fa-moon text-gray-600 dark:text-yellow-400" id="theme-icon"></i>
                        </button>
                        <a href="exportarPdf.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                        </a>
                        <a href="exportarExcel.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Filtros -->
            <div class="card mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Filtros</h2>
                </div>
                <form method="GET" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="data_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Data Início
                            </label>
                            <input type="date" name="data_inicio" id="data_inicio" 
                                   value="<?php echo htmlspecialchars($data_inicio); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label for="data_fim" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Data Fim
                            </label>
                            <input type="date" name="data_fim" id="data_fim" 
                                   value="<?php echo htmlspecialchars($data_fim); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-otica-primary hover:bg-otica-secondary text-white px-4 py-2 rounded-md font-medium transition-colors">
                                <i class="fas fa-search mr-2"></i>Filtrar
                            </button>
                        </div>
                        <div class="flex items-end">
                            <a href="relatorio.php" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md font-medium transition-colors text-center">
                                <i class="fas fa-refresh mr-2"></i>Limpar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Resumo -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="card p-6 bg-blue-600 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Total de Vendas</h3>
                            <p class="text-2xl font-bold"><?php echo count($vendas); ?></p>
                        </div>
                    </div>
                </div>
                <div class="card p-6 bg-green-600 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-dollar-sign text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Valor Total</h3>
                            <p class="text-2xl font-bold">R$ <?php echo number_format($total, 2, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="card p-6 bg-purple-600 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Ticket Médio</h3>
                            <p class="text-2xl font-bold">
                                R$ <?php echo count($vendas) > 0 ? number_format($total / count($vendas), 2, ',', '.') : '0,00'; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card p-6 bg-yellow-600 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Período</h3>
                            <p class="text-sm font-bold">
                                <?php echo date('d/m/Y', strtotime($data_inicio)); ?> - <?php echo date('d/m/Y', strtotime($data_fim)); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Vendas -->
            <?php if (empty($vendas)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-chart-bar text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhuma venda encontrada</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Não há vendas no período selecionado.</p>
                </div>
            <?php else: ?>
                <div class="card overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Vendas do Período</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Valor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Forma de Pagamento
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Data
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                <?php foreach ($vendas as $venda): ?>
                                    <tr class="table-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($venda['cliente_nome'] ?? 'Cliente não encontrado'); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            R$ <?php echo number_format($venda['valor_total'], 2, ',', '.'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo date('d/m/Y H:i', strtotime($venda['created_at'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Theme management
        function toggleTheme() {
            const html = document.documentElement;
            const themeIcon = document.getElementById('theme-icon');
            
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                html.classList.add('light');
                themeIcon.className = 'fas fa-moon text-gray-600';
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.remove('light');
                html.classList.add('dark');
                themeIcon.className = 'fas fa-sun text-yellow-400';
                localStorage.setItem('theme', 'dark');
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

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
        });
    </script>
</body>
</html> 