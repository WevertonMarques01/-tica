<?php
/**
 * Layout Base para Views - Estilo Azul Escuro
 * Uso: incluir este arquivo no início de cada view
 */

$currentPage = $pageTitle ?? 'Wiz Óptica';
$currentModule = $moduleName ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $currentPage; ?> - Wiz Óptica</title>
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
        
        .page-title {
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
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-family: inherit;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);
        }
        
        .btn-secondary {
            background: #f1f5f9;
            color: #0f172a;
        }
        
        .btn-secondary:hover {
            background: #e2e8f0;
        }
        
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        
        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #10b981;
            color: white;
        }
        
        .btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
        }
        
        .form-group { margin-bottom: 1.25rem; }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }
        
        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: white;
            color: #0f172a;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }
        
        .form-input::placeholder { color: #94a3b8; }
        
        .table-container {
            overflow-x: auto;
            border-radius: 12px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
        }
        
        .table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            color: #0f172a;
        }
        
        .table tbody tr:hover {
            background: #f8fafc;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
        }
        
        .badge-success { background: #d1fae5; color: #059669; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
        }
        
        .btn-icon:hover {
            background: #0ea5e9;
            color: white;
        }
        
        .btn-icon.danger:hover {
            background: #ef4444;
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
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
                <a href="../admin/index.php" class="menu-item">
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

    <style>
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
    </style>
    
    <main class="main-content">
        <header class="page-header">
            <div>
                <h1 class="page-title"><?php echo $currentPage; ?></h1>
                <?php if (!empty($currentModule)): ?>
                <p class="page-subtitle"><?php echo $currentModule; ?></p>
                <?php endif; ?>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['usuario_nome'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?></div>
            </div>
        </header>
        
        <?php if (!empty($erro)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($erro); ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            Operation completed successfully!
        </div>
        <?php endif; ?>
