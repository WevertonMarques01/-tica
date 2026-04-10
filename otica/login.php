<?php
session_start();

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Por favor, preencha todos os campos.';
    } else {
        try {
            require_once 'config/database.php';
            require_once 'config/database_compatibility.php';
            $db = Database::getInstance()->getConnection();
            
            $query = DatabaseCompatibility::buildUserQuery($email);
            $stmt = $db->prepare($query);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($password, $usuario['senha'])) {
                if (isset($usuario['ativo']) && $usuario['ativo'] == 0) {
                    $_SESSION['login_error'] = 'Usuário inativo. Contate o administrador.';
                } else {
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    $_SESSION['usuario_permissao'] = $usuario['perfil'];
                    header('Location: views/admin/index.php');
                    exit();
                }
            } else {
                $_SESSION['login_error'] = 'E-mail ou senha inválidos.';
            }
        } catch (PDOException $e) {
            $_SESSION['login_error'] = 'Erro ao conectar ao banco de dados.';
        }
    }
}

$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Wiz Óptica</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0ea5e9',
                        primarydark: '#0284c7',
                        secondary: '#6366f1',
                        success: '#10b981',
                        error: '#ef4444',
                        warning: '#f59e0b'
                    },
                    fontFamily: {
                        nunito: ['Nunito', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="assets/css/clean-ui.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0f9ff 100%);
            min-height: 100vh;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        
        .input-field {
            transition: all 0.2s ease;
        }
        
        .input-field:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
        }
        
        .btn-login {
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        .error-shake {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    
    <div class="w-full max-w-md">
        <!-- Logo Section -->
        <div class="text-center mb-8 fade-in">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-primary to-primarydark rounded-2xl mb-4 floating">
                <i class="fas fa-glasses text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-800">Wiz Óptica</h1>
            <p class="text-gray-500 mt-2">Sistema de Gestão</p>
        </div>
        
        <!-- Login Card -->
        <div class="login-card rounded-2xl p-8 fade-in" style="animation-delay: 0.1s;">
            <h2 class="text-xl font-bold text-gray-800 mb-6">
                <i class="fas fa-sign-in-alt text-primary mr-2"></i>
                Acesse sua conta
            </h2>
            
            <!-- Error Message -->
            <?php if ($login_error): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3 error-shake" id="errorMessage">
                <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-700"><?php echo htmlspecialchars($login_error); ?></p>
                </div>
                <button onclick="document.getElementById('errorMessage').remove()" class="text-red-400 hover:text-red-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="POST" action="" class="space-y-5">
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope mr-2 text-gray-400"></i>
                        E-mail
                    </label>
                    <div class="relative">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            placeholder="seu@email.com"
                            class="form-input pl-12"
                            autocomplete="email"
                        >
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock mr-2 text-gray-400"></i>
                        Senha
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="••••••••"
                            class="form-input pl-12"
                            autocomplete="current-password"
                        >
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </div>
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary">
                        <span class="text-gray-600">Lembrar-me</span>
                    </label>
                    <a href="#" class="text-primary hover:text-primarydark font-medium">
                        Esqueceu a senha?
                    </a>
                </div>
                
                <button 
                    type="submit" 
                    class="btn btn-primary w-full py-4 text-base font-bold rounded-xl btn-login"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Entrar
                </button>
            </form>
        </div>
        
        <!-- Footer -->
        <p class="text-center text-gray-400 text-sm mt-8">
            &copy; <?php echo date('Y'); ?> Wiz Óptica. Todos os direitos reservados.
        </p>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Auto-focus first input
        document.getElementById('email').focus();
    </script>
</body>
</html>
