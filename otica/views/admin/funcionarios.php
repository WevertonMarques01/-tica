<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Verificar se é dono
if (!verificarSeDono()) {
    header('Location: index.php?error=access_denied');
    exit;
}

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

// Buscar funcionários
try {
    $stmt = $db->prepare("
        SELECT id, nome, email, perfil, ativo, ultimo_login, created_at
        FROM usuarios 
        ORDER BY nome
    ");
    $stmt->execute();
    $funcionarios = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar funcionários: " . $e->getMessage());
    $funcionarios = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Funcionários - Wiz Ótica</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../assets/js/notifications.js"></script>
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

        .theme-toggle {
            transition: all 0.3s ease;
        }

        .theme-toggle.rotated {
            transform: rotate(180deg);
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
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center mb-8">
                <i class="fas fa-glasses text-3xl text-white mr-3"></i>
                <h1 class="text-xl font-bold text-white">Wiz Admin</h1>
            </div>

            <!-- Menu Items -->
            <nav class="space-y-2">
                <a href="index.php" class="sidebar-item flex items-center p-3 text-white">
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
                <a href="funcionarios.php" class="sidebar-item active flex items-center p-3 text-white">
                    <i class="fas fa-users-cog w-5 mr-3"></i>
                    <span>Funcionários</span>
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
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Gerenciar Funcionários</h1>
                <p class="text-gray-600 dark:text-gray-300">Adicione e gerencie funcionários do sistema</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Theme Toggle -->
                <button onclick="toggleTheme()" class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-md border border-gray-200 dark:border-gray-600 theme-toggle">
                    <i class="fas fa-moon text-gray-600 dark:text-yellow-400" id="theme-icon"></i>
                </button>
                
                <div class="flex items-center space-x-3 bg-white dark:bg-gray-800 p-3 rounded-lg shadow-md border border-gray-200 dark:border-gray-600">
                    <div class="w-8 h-8 bg-otica-primary rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Admin'); ?></span>
                </div>
            </div>
        </div>

        <!-- Add New Employee Button -->
        <div class="mb-6">
            <button onclick="showAddEmployeeModal()" class="btn-primary px-6 py-3 text-white rounded-lg font-semibold">
                <i class="fas fa-plus mr-2"></i>
                Adicionar Funcionário
            </button>
        </div>

        <!-- Employees List -->
        <div class="card">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">Lista de Funcionários</h2>
            </div>
            
            <div class="p-6">
                <?php if (empty($funcionarios)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">Nenhum funcionário cadastrado</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-600">
                                    <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300">Nome</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300">Email</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300">Perfil</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300">Status</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300">Último Login</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700 dark:text-gray-300">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($funcionarios as $funcionario): ?>
                                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-otica-primary rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-white text-sm"></i>
                                                </div>
                                                <span class="font-medium text-gray-900 dark:text-white"><?php echo htmlspecialchars($funcionario['nome']); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-gray-600 dark:text-gray-300"><?php echo htmlspecialchars($funcionario['email']); ?></td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $funcionario['perfil'] === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'; ?>">
                                                <?php echo ucfirst($funcionario['perfil']); ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo $funcionario['ativo'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'; ?>">
                                                <?php echo $funcionario['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-500 dark:text-gray-400">
                                            <?php echo $funcionario['ultimo_login'] ? date('d/m/Y H:i', strtotime($funcionario['ultimo_login'])) : 'Nunca'; ?>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex space-x-2">
                                                <button onclick="editEmployee(<?php echo $funcionario['id']; ?>)" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($funcionario['ativo']): ?>
                                                    <button onclick="deactivateEmployee(<?php echo $funcionario['id']; ?>)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                        <i class="fas fa-user-times"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button onclick="activateEmployee(<?php echo $funcionario['id']; ?>)" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div id="addEmployeeModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Adicionar Funcionário</h2>
            <form id="addEmployeeForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nome</label>
                    <input type="text" name="nome" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Senha</label>
                    <input type="password" name="senha" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Perfil</label>
                    <select name="perfil" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="vendedor">Vendedor</option>
                        <option value="optico">Óptico</option>
                        <option value="gerente">Gerente</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddEmployeeModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-otica-primary hover:bg-otica-secondary text-white rounded-md font-medium transition-colors">
                        Adicionar
                    </button>
                </div>
            </form>
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
                themeButton.classList.remove('rotated');
            } else {
                html.classList.remove('light');
                html.classList.add('dark');
                themeIcon.className = 'fas fa-sun text-yellow-400';
                localStorage.setItem('theme', 'dark');
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

        // Employee management functions
        function showAddEmployeeModal() {
            document.getElementById('addEmployeeModal').classList.remove('hidden');
        }

        function closeAddEmployeeModal() {
            document.getElementById('addEmployeeModal').classList.add('hidden');
            document.getElementById('addEmployeeForm').reset();
        }

        function editEmployee(id) {
            // Implementar edição de funcionário
            alert('Funcionalidade de edição será implementada');
        }

        function deactivateEmployee(id) {
            if (confirm('Tem certeza que deseja desativar este funcionário?')) {
                // Implementar desativação
                alert('Funcionalidade de desativação será implementada');
            }
        }

        function activateEmployee(id) {
            if (confirm('Tem certeza que deseja ativar este funcionário?')) {
                // Implementar ativação
                alert('Funcionalidade de ativação será implementada');
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

        // Form submission
        document.getElementById('addEmployeeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'criar');
            
            fetch('../../controllers/UsuarioController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Funcionário adicionado com sucesso!');
                    closeAddEmployeeModal();
                    location.reload();
                } else {
                    showError('Erro: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao adicionar funcionário');
            });
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
        });
    </script>
</body>
</html> 