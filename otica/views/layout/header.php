<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Sistema Ótica' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .main-content {
            min-height: calc(100vh - 200px);
            padding: 2rem 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 1rem 0;
            margin-top: auto;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link:hover {
            color: #fff;
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-glasses"></i> Sistema Ótica
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/clientes">
                            <i class="fas fa-users"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/produtos">
                            <i class="fas fa-box"></i> Produtos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/vendas">
                            <i class="fas fa-shopping-cart"></i> Vendas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/ordens_servico">
                            <i class="fas fa-tools"></i> Ordens de Serviço
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/financeiro">
                            <i class="fas fa-chart-line"></i> Financeiro
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?= $_SESSION['usuario_nome'] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/perfil"><i class="fas fa-user-cog"></i> Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/login/logout"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container main-content"> 