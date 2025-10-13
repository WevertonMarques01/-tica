<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Verificar se foi passado um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=id_invalido');
    exit;
}

$id = (int)$_GET['id'];

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

try {
    // Buscar cliente
    $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) {
        header('Location: index.php?error=cliente_nao_encontrado');
        exit;
    }
    
    // Buscar estatísticas relacionadas ao cliente
    $estatisticas = [
        'vendas' => 0,
        'receitas' => 0,
        'ordens_servico' => 0,
        'valor_total_vendas' => 0
    ];
    
    // Contar vendas
    try {
        $stmtVendas = $db->prepare("SELECT COUNT(*) as total, COALESCE(SUM(valor_total), 0) as valor_total FROM vendas WHERE cliente_id = ?");
        $stmtVendas->execute([$id]);
        $vendas = $stmtVendas->fetch();
        $estatisticas['vendas'] = $vendas['total'];
        $estatisticas['valor_total_vendas'] = $vendas['valor_total'];
    } catch (PDOException $e) {
        error_log("Aviso: Erro ao buscar vendas: " . $e->getMessage());
    }
    
    // Contar receitas
    try {
        $stmtReceitas = $db->prepare("SELECT COUNT(*) as total FROM receitas WHERE cliente_id = ?");
        $stmtReceitas->execute([$id]);
        $receitas = $stmtReceitas->fetch();
        $estatisticas['receitas'] = $receitas['total'];
    } catch (PDOException $e) {
        error_log("Aviso: Tabela receitas não encontrada: " . $e->getMessage());
    }
    
    // Contar ordens de serviço
    try {
        $stmtOrdens = $db->prepare("SELECT COUNT(*) as total FROM ordens_servico WHERE cliente_id = ?");
        $stmtOrdens->execute([$id]);
        $ordens = $stmtOrdens->fetch();
        $estatisticas['ordens_servico'] = $ordens['total'];
    } catch (PDOException $e) {
        error_log("Aviso: Tabela ordens_servico não encontrada: " . $e->getMessage());
    }
    
} catch (PDOException $e) {
    error_log("Erro ao buscar cliente: " . $e->getMessage());
    header('Location: index.php?error=erro_sistema');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente: <?php echo htmlspecialchars($cliente['nome']); ?> - Ótica</title>
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
                        'otica-secondary': '#20b8a9'
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

        .light {
            background-color: #f8fafc;
            color: #1f2937;
        }

        .light .card {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .dark {
            background-color: #0f172a;
            color: #f1f5f9;
        }

        .dark .card {
            background: #1e293b;
            border: 1px solid #334155;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .card {
            border-radius: 16px;
            transition: all 0.3s ease;
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
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Cliente: <?php echo htmlspecialchars($cliente['nome']); ?>
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="editar.php?id=<?php echo $cliente['id']; ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition-colors">
                            <i class="fas fa-edit mr-2"></i>Editar
                        </a>
                        <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-medium transition-colors">
                            <i class="fas fa-print mr-2"></i>Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="card p-6 bg-blue-600 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shopping-cart text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Vendas</h3>
                            <p class="text-2xl font-bold"><?php echo $estatisticas['vendas']; ?></p>
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
                            <p class="text-lg font-bold">R$ <?php echo number_format($estatisticas['valor_total_vendas'], 2, ',', '.'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="card p-6 bg-purple-600 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-prescription text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Receitas</h3>
                            <p class="text-2xl font-bold"><?php echo $estatisticas['receitas']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="card p-6 bg-orange-600 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-cogs text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Ordens de Serviço</h3>
                            <p class="text-2xl font-bold"><?php echo $estatisticas['ordens_servico']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Informações Pessoais -->
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informações Pessoais</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome Completo</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($cliente['nome']); ?></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Documento</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($cliente['documento']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <?php echo strtoupper($cliente['tipo_documento'] ?? 'CPF'); ?>
                                </p>
                            </div>
                        </div>
                        <?php if (!empty($cliente['data_nascimento'])): ?>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de Nascimento</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <?php echo date('d/m/Y', strtotime($cliente['data_nascimento'])); ?>
                                </p>
                            </div>
                            <?php if (!empty($cliente['sexo'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sexo</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <?php 
                                    $sexos = ['M' => 'Masculino', 'F' => 'Feminino', 'O' => 'Outro'];
                                    echo $sexos[$cliente['sexo']] ?? $cliente['sexo'];
                                    ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <p class="mt-1">
                                <?php if ($cliente['ativo']): ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Ativo</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inativo</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contato -->
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informações de Contato</h2>
                    <div class="space-y-4">
                        <?php if (!empty($cliente['email'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="mailto:<?php echo $cliente['email']; ?>" class="text-otica-primary hover:text-otica-secondary">
                                    <?php echo htmlspecialchars($cliente['email']); ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($cliente['telefone'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="tel:<?php echo $cliente['telefone']; ?>" class="text-otica-primary hover:text-otica-secondary">
                                    <?php echo htmlspecialchars($cliente['telefone']); ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($cliente['celular'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Celular</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <a href="tel:<?php echo $cliente['celular']; ?>" class="text-otica-primary hover:text-otica-secondary">
                                    <?php echo htmlspecialchars($cliente['celular']); ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Endereço -->
                <?php if (!empty($cliente['endereco']) || !empty($cliente['cidade'])): ?>
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Endereço</h2>
                    <div class="space-y-4">
                        <?php if (!empty($cliente['endereco'])): ?>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Endereço</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($cliente['endereco']); ?></p>
                            </div>
                            <?php if (!empty($cliente['numero'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($cliente['numero']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($cliente['complemento'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Complemento</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($cliente['complemento']); ?></p>
                        </div>
                        <?php endif; ?>
                        <div class="grid grid-cols-2 gap-4">
                            <?php if (!empty($cliente['bairro'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($cliente['bairro']); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($cliente['cep'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($cliente['cep']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <?php if (!empty($cliente['cidade'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($cliente['cidade']); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($cliente['estado'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo htmlspecialchars($cliente['estado']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Observações -->
                <?php if (!empty($cliente['observacoes'])): ?>
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Observações</h2>
                    <p class="text-sm text-gray-700 dark:text-gray-300"><?php echo nl2br(htmlspecialchars($cliente['observacoes'])); ?></p>
                </div>
                <?php endif; ?>

                <!-- Informações do Sistema -->
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informações do Sistema</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ID do Cliente</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo $cliente['id']; ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de Cadastro</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <?php echo date('d/m/Y H:i', strtotime($cliente['created_at'])); ?>
                            </p>
                        </div>
                        <?php if (!empty($cliente['updated_at']) && $cliente['updated_at'] !== $cliente['created_at']): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Última Atualização</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <?php echo date('d/m/Y H:i', strtotime($cliente['updated_at'])); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>