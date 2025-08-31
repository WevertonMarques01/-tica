<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

// Buscar clientes para o select
try {
    $stmt = $db->prepare("SELECT id, nome FROM clientes ORDER BY nome");
    $stmt->execute();
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar clientes: " . $e->getMessage());
    $clientes = [];
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? '';
    $indicacao = $_POST['indicacao'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    
    // Dados do fiador
    $fiador_nome = $_POST['fiador_nome'] ?? '';
    $fiador_endereco = $_POST['fiador_endereco'] ?? '';
    $fiador_cpf = $_POST['fiador_cpf'] ?? '';
    
    // Dados da receita
    $od_esf = $_POST['od_esf'] ?? '';
    $od_cil = $_POST['od_cil'] ?? '';
    $od_eixo = $_POST['od_eixo'] ?? '';
    $od_dnp = $_POST['od_dnp'] ?? '';
    
    $oe_esf = $_POST['oe_esf'] ?? '';
    $oe_cil = $_POST['oe_cil'] ?? '';
    $oe_eixo = $_POST['oe_eixo'] ?? '';
    $oe_dnp = $_POST['oe_dnp'] ?? '';
    
    $adicao = $_POST['adicao'] ?? '';
    $co = $_POST['co'] ?? '';
    
    // Armações (múltipla seleção)
    $armacoes = $_POST['armacoes'] ?? [];
    
    // Lentes (múltipla seleção)
    $lentes = $_POST['lentes'] ?? [];
    
    // Marca da lente
    $marca_lente = $_POST['marca_lente'] ?? '';
    
    // Tipos de lentes (múltipla seleção)
    $tipos_lentes = $_POST['tipos_lentes'] ?? [];
    
    $observacoes = $_POST['observacoes'] ?? '';
    $data_receita = $_POST['data_receita'] ?? date('Y-m-d');
    
    if (empty($cliente_id)) {
        $erro = 'Selecione um cliente.';
    } else {
        try {
            $stmt = $db->prepare("
                INSERT INTO receitas (
                    cliente_id, indicacao, nome, endereco, bairro, numero, cpf, telefone,
                    fiador_nome, fiador_endereco, fiador_cpf,
                    od_esf, od_cil, od_eixo, od_dnp,
                    oe_esf, oe_cil, oe_eixo, oe_dnp,
                    adicao, co, armacoes, lentes, marca_lente, tipos_lentes,
                    observacoes, data_receita
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $armacoes_json = json_encode($armacoes);
            $lentes_json = json_encode($lentes);
            $tipos_lentes_json = json_encode($tipos_lentes);
            
            $result = $stmt->execute([
                $cliente_id, $indicacao, $nome, $endereco, $bairro, $numero, $cpf, $telefone,
                $fiador_nome, $fiador_endereco, $fiador_cpf,
                $od_esf, $od_cil, $od_eixo, $od_dnp,
                $oe_esf, $oe_cil, $oe_eixo, $oe_dnp,
                $adicao, $co, $armacoes_json, $lentes_json, $marca_lente, $tipos_lentes_json,
                $observacoes, $data_receita
            ]);
            
            if ($result) {
                // Registrar log
                $logStmt = $db->prepare("INSERT INTO logs (usuario_id, acao, detalhes) VALUES (?, ?, ?)");
                $logStmt->execute([$_SESSION['usuario_id'], 'ficha_oculos_criada', "Nova ficha de óculos criada para cliente ID: $cliente_id"]);
                
                header('Location: index.php?success=1');
                exit;
            } else {
                $erro = 'Erro ao salvar ficha de óculos.';
            }
        } catch (PDOException $e) {
            error_log("Erro ao salvar ficha de óculos: " . $e->getMessage());
            $erro = 'Erro interno do sistema.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FICHA DE ÓCULOS - Ótica</title>
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

        .theme-toggle.rotated {
            transform: rotate(180deg);
        }

        .form-section {
            transition: all 0.3s ease;
        }

        .dark .form-section {
            background-color: #1e293b;
            border: 1px solid #334155;
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
                        <a href="index.php" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">FICHA DE ÓCULOS</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <button onclick="toggleTheme()" class="bg-white dark:bg-gray-700 p-3 rounded-lg shadow-md border border-gray-200 dark:border-gray-600 theme-toggle">
                            <i class="fas fa-moon text-gray-600 dark:text-yellow-400" id="theme-icon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="card">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Dados da Ficha de Óculos</h2>
                </div>
                
                <form method="POST" class="p-6 space-y-6">
                    <?php if (isset($erro)): ?>
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded">
                            <?php echo htmlspecialchars($erro); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Cliente -->
                    <div>
                        <label for="cliente_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cliente *
                        </label>
                        <select name="cliente_id" id="cliente_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Selecione um cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>" <?php echo (isset($_POST['cliente_id']) && $_POST['cliente_id'] == $cliente['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cliente['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Data da Receita -->
                    <div>
                        <label for="data_receita" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Data da Receita
                        </label>
                        <input type="date" name="data_receita" id="data_receita" 
                               value="<?php echo $_POST['data_receita'] ?? date('Y-m-d'); ?>"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <!-- Dados do Paciente -->
                    <div class="form-section bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Dados do Paciente</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="indicacao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Indicação
                                </label>
                                <input type="text" name="indicacao" id="indicacao" 
                                       value="<?php echo htmlspecialchars($_POST['indicacao'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nome
                                </label>
                                <input type="text" name="nome" id="nome" 
                                       value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Endereço
                                </label>
                                <input type="text" name="endereco" id="endereco" 
                                       value="<?php echo htmlspecialchars($_POST['endereco'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Bairro
                                </label>
                                <input type="text" name="bairro" id="bairro" 
                                       value="<?php echo htmlspecialchars($_POST['bairro'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nº
                                </label>
                                <input type="text" name="numero" id="numero" 
                                       value="<?php echo htmlspecialchars($_POST['numero'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="cpf" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    CPF
                                </label>
                                <input type="text" name="cpf" id="cpf" 
                                       value="<?php echo htmlspecialchars($_POST['cpf'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div class="md:col-span-2">
                                <label for="telefone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Telefone
                                </label>
                                <input type="text" name="telefone" id="telefone" 
                                       value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Dados do Fiador -->
                    <div class="form-section bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Fiador</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="fiador_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nome
                                </label>
                                <input type="text" name="fiador_nome" id="fiador_nome" 
                                       value="<?php echo htmlspecialchars($_POST['fiador_nome'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="fiador_endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Endereço
                                </label>
                                <input type="text" name="fiador_endereco" id="fiador_endereco" 
                                       value="<?php echo htmlspecialchars($_POST['fiador_endereco'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="fiador_cpf" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    CPF
                                </label>
                                <input type="text" name="fiador_cpf" id="fiador_cpf" 
                                       value="<?php echo htmlspecialchars($_POST['fiador_cpf'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Dados da Receita -->
                    <div class="form-section bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Ficha</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Olho Direito (OD) -->
                            <div>
                                <h4 class="text-md font-medium text-gray-800 dark:text-white mb-3">OD</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="od_esf" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            ESF
                                        </label>
                                        <input type="text" name="od_esf" id="od_esf" 
                                               value="<?php echo htmlspecialchars($_POST['od_esf'] ?? ''); ?>"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="od_cil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            CIL
                                        </label>
                                        <input type="text" name="od_cil" id="od_cil" 
                                               value="<?php echo htmlspecialchars($_POST['od_cil'] ?? ''); ?>"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="od_eixo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Eixo
                                        </label>
                                        <input type="text" name="od_eixo" id="od_eixo" 
                                               value="<?php echo htmlspecialchars($_POST['od_eixo'] ?? ''); ?>"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="od_dnp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            DNP
                                        </label>
                                        <input type="text" name="od_dnp" id="od_dnp" 
                                               value="<?php echo htmlspecialchars($_POST['od_dnp'] ?? ''); ?>"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                </div>
                            </div>

                            <!-- Olho Esquerdo (OE) -->
                            <div>
                                <h4 class="text-md font-medium text-gray-800 dark:text-white mb-3">OE</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="oe_esf" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            ESF
                                        </label>
                                        <input type="text" name="oe_esf" id="oe_esf" 
                                               value="<?php echo htmlspecialchars($_POST['oe_esf'] ?? ''); ?>"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="oe_cil" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            CIL
                                        </label>
                                        <input type="text" name="oe_cil" id="oe_cil" 
                                               value="<?php echo htmlspecialchars($_POST['oe_cil'] ?? ''); ?>"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="oe_eixo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Eixo
                                        </label>
                                        <input type="text" name="oe_eixo" id="oe_eixo" 
                                               value="<?php echo htmlspecialchars($_POST['oe_eixo'] ?? ''); ?>"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="oe_dnp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            DNP
                                        </label>
                                        <input type="text" name="oe_dnp" id="oe_dnp" 
                                               value="<?php echo htmlspecialchars($_POST['oe_dnp'] ?? ''); ?>"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Adição e C.O -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="adicao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Adição
                                </label>
                                <input type="text" name="adicao" id="adicao" 
                                       value="<?php echo htmlspecialchars($_POST['adicao'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="co" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    C.O
                                </label>
                                <input type="text" name="co" id="co" 
                                       value="<?php echo htmlspecialchars($_POST['co'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Armação -->
                    <div class="form-section bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Armação</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="armacoes[]" value="Regina" 
                                       <?php echo (isset($_POST['armacoes']) && in_array('Regina', $_POST['armacoes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Regina</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="armacoes[]" value="Poly" 
                                       <?php echo (isset($_POST['armacoes']) && in_array('Poly', $_POST['armacoes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Poly</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="armacoes[]" value="Minerva" 
                                       <?php echo (isset($_POST['armacoes']) && in_array('Minerva', $_POST['armacoes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Minerva</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="armacoes[]" value="Cristal" 
                                       <?php echo (isset($_POST['armacoes']) && in_array('Cristal', $_POST['armacoes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Cristal</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="armacoes[]" value="Antirreflexo" 
                                       <?php echo (isset($_POST['armacoes']) && in_array('Antirreflexo', $_POST['armacoes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Antirreflexo</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="armacoes[]" value="Fotossensível" 
                                       <?php echo (isset($_POST['armacoes']) && in_array('Fotossensível', $_POST['armacoes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Fotossensível</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="armacoes[]" value="Transitions" 
                                       <?php echo (isset($_POST['armacoes']) && in_array('Transitions', $_POST['armacoes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Transitions</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="armacoes[]" value="Cristal" 
                                       <?php echo (isset($_POST['armacoes']) && in_array('Cristal', $_POST['armacoes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Cristal</span>
                            </label>
                        </div>
                    </div>

                    <!-- Marca da Lente -->
                    <div class="form-section bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Marca da Lente</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center">
                                <input type="radio" name="marca_lente" value="Hoya" 
                                       <?php echo (isset($_POST['marca_lente']) && $_POST['marca_lente'] == 'Hoya') ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Hoya</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="marca_lente" value="Essilor" 
                                       <?php echo (isset($_POST['marca_lente']) && $_POST['marca_lente'] == 'Essilor') ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Essilor</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tipos de Lentes -->
                    <div class="form-section bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tipos de Lentes</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="tipos_lentes[]" value="Visão Simples" 
                                       <?php echo (isset($_POST['tipos_lentes']) && in_array('Visão Simples', $_POST['tipos_lentes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Visão Simples</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="tipos_lentes[]" value="Progressiva" 
                                       <?php echo (isset($_POST['tipos_lentes']) && in_array('Progressiva', $_POST['tipos_lentes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Progressiva</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="tipos_lentes[]" value="Bifocal (Ultex / Kriptok)" 
                                       <?php echo (isset($_POST['tipos_lentes']) && in_array('Bifocal (Ultex / Kriptok)', $_POST['tipos_lentes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Bifocal (Ultex / Kriptok)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="tipos_lentes[]" value="Gold" 
                                       <?php echo (isset($_POST['tipos_lentes']) && in_array('Gold', $_POST['tipos_lentes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Gold</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="tipos_lentes[]" value="Shynebox" 
                                       <?php echo (isset($_POST['tipos_lentes']) && in_array('Shynebox', $_POST['tipos_lentes'])) ? 'checked' : ''; ?>
                                       class="mr-2 text-otica-primary focus:ring-otica-primary">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Shynebox</span>
                            </label>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div>
                        <label for="observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Observações
                        </label>
                        <textarea name="observacoes" id="observacoes" rows="4" 
                                  placeholder="Observações adicionais..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><?php echo htmlspecialchars($_POST['observacoes'] ?? ''); ?></textarea>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <a href="index.php" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-otica-primary hover:bg-otica-secondary text-white rounded-md font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>Salvar Ficha de Óculos
                        </button>
                    </div>
                </form>
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

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
        });
    </script>
</body>
</html>
<pre>
<?php print_r($produtos); ?>
</pre>
