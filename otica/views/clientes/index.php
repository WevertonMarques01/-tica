<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

// Buscar clientes
try {
    $stmt = $db->prepare("SELECT * FROM clientes ORDER BY nome");
    $stmt->execute();
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar clientes: " . $e->getMessage());
    $clientes = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Clientes - Ótica</title>
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
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gerenciar Clientes</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <button onclick="toggleTheme()" class="bg-white dark:bg-gray-700 p-3 rounded-lg shadow-md border border-gray-200 dark:border-gray-600 theme-toggle">
                            <i class="fas fa-moon text-gray-600 dark:text-yellow-400" id="theme-icon"></i>
                        </button>
                        <a href="novo.php" class="px-4 py-2 bg-otica-primary hover:bg-otica-secondary text-white rounded-md font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>Novo Cliente
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <?php if (isset($_GET['success'])): ?>
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>Cliente cadastrado com sucesso!
                </div>
            <?php endif; ?>

            <?php if (!empty($clientes)): ?>
                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="card p-6 bg-blue-600 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium">Total de Clientes</h3>
                                <p class="text-2xl font-bold"><?php echo count($clientes); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="card p-6 bg-green-600 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-plus text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium">Novos Este Mês</h3>
                                <p class="text-2xl font-bold">
                                    <?php 
                                    $novosEsteMes = 0;
                                    foreach ($clientes as $cliente) {
                                        if (date('Y-m') === date('Y-m', strtotime($cliente['created_at']))) {
                                            $novosEsteMes++;
                                        }
                                    }
                                    echo $novosEsteMes;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card p-6 bg-purple-600 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium">Último Cadastro</h3>
                                <p class="text-sm font-bold">
                                    <?php 
                                    if (!empty($clientes)) {
                                        echo date('d/m/Y', strtotime($clientes[0]['created_at']));
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Clientes -->
                <div class="card overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Todos os Clientes</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nome
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Documento
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Telefone
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Data de Cadastro
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr class="table-row">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo $cliente['id']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($cliente['nome']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($cliente['documento']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($cliente['email'] ?? '-'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo htmlspecialchars($cliente['telefone'] ?? '-'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?php echo date('d/m/Y H:i', strtotime($cliente['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="visualizar.php?id=<?php echo $cliente['id']; ?>" 
                                                   class="text-otica-primary hover:text-otica-secondary transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="editar.php?id=<?php echo $cliente['id']; ?>" 
                                                   class="text-otica-blue hover:text-blue-600 transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="excluir.php?id=<?php echo $cliente['id']; ?>" 
                                                   class="text-red-600 hover:text-red-900 transition-colors"
                                                   onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-12">
                    <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nenhum cliente encontrado</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Comece cadastrando o primeiro cliente.</p>
                    <a href="novo.php" class="px-6 py-3 bg-otica-primary hover:bg-otica-secondary text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Cadastrar Primeiro Cliente
                    </a>
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