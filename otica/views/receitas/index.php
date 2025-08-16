<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

// Buscar receitas
try {
    $stmt = $db->prepare("
        SELECT r.*, c.nome as cliente_nome 
        FROM receitas r 
        LEFT JOIN clientes c ON r.cliente_id = c.id 
        ORDER BY r.data_receita DESC
    ");
    $stmt->execute();
    $receitas = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar receitas: " . $e->getMessage());
    $receitas = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receitas - Ótica</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
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
                        'otica-coral': '#ff6b6b'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <a href="../admin/index.php" class="text-gray-400 hover:text-gray-600 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">Receitas</h1>
                    </div>
                    <a href="nova.php" class="bg-otica-primary hover:bg-otica-secondary text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nova Receita
                    </a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Mensagens de Sucesso/Erro -->
            <?php if (isset($_GET['success'])): ?>
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                    <?php if ($_GET['success'] == '1'): ?>
                        Receita cadastrada com sucesso!
                    <?php elseif ($_GET['success'] == 'excluida'): ?>
                        Receita excluída com sucesso!
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <?php 
                    switch ($_GET['error']) {
                        case 'id_invalido':
                            echo 'ID inválido.';
                            break;
                        case 'receita_nao_encontrada':
                            echo 'Receita não encontrada.';
                            break;
                        case 'erro_exclusao':
                            echo 'Erro ao excluir receita.';
                            break;
                        case 'erro_sistema':
                            echo 'Erro interno do sistema.';
                            break;
                        default:
                            echo 'Erro desconhecido.';
                    }
                    ?>
                </div>
            <?php endif; ?>
            <?php if (empty($receitas)): ?>
                <!-- Empty State -->
                <div class="text-center py-12">
                    <i class="fas fa-eye text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma receita cadastrada</h3>
                    <p class="text-gray-500 mb-6">Comece cadastrando a primeira receita de um cliente.</p>
                    <a href="nova.php" class="bg-otica-primary hover:bg-otica-secondary text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Cadastrar Receita
                    </a>
                </div>
            <?php else: ?>
                <!-- Receitas List -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Todas as Receitas</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Olho Direito
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Olho Esquerdo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($receitas as $receita): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($receita['cliente_nome'] ?? 'Cliente não encontrado'); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($receita['olho_direito'] ?? '-'); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($receita['olho_esquerdo'] ?? '-'); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo date('d/m/Y', strtotime($receita['data_receita'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="visualizar.php?id=<?php echo $receita['id']; ?>" class="text-otica-primary hover:text-otica-secondary mr-3">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar.php?id=<?php echo $receita['id']; ?>" class="text-otica-accent hover:text-otica-warm mr-3">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="excluirReceita(<?php echo $receita['id']; ?>)" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
        function excluirReceita(id) {
            if (confirm('Tem certeza que deseja excluir esta receita?')) {
                window.location.href = `excluir.php?id=${id}`;
            }
        }
    </script>
</body>
</html> 