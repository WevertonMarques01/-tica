<?php
<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$db = Database::getInstance()->getConnection();

// Buscar produtos
$stmt = $db->query("SELECT * FROM produtos ORDER BY nome");
$produtos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos - Wiz Ótica</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Produtos</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="../produtos.php?action=novo" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Novo Produto
                        </a>
                        <a href="../views/admin/index.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Feedback Messages -->
            <?php if (isset($_GET['success'])): ?>
                <?php if ($_GET['success'] == 'excluido'): ?>
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Produto excluído com sucesso!</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Operação realizada com sucesso!</span>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <?php 
                $errorMessages = [
                    'id_invalido' => 'ID do produto inválido.',
                    'produto_nao_encontrado' => 'Produto não encontrado.',
                    'produto_tem_vendas' => 'Não é possível excluir este produto pois ele possui vendas associadas.',
                    'produto_tem_movimentacoes' => 'Não é possível excluir este produto pois ele possui movimentações de estoque associadas.',
                    'erro_exclusao' => 'Erro ao excluir o produto.',
                    'erro_sistema' => 'Erro interno do sistema. Tente novamente.'
                ];
                $errorMessage = $errorMessages[$_GET['error']] ?? 'Erro desconhecido.';
                ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span><?php echo $errorMessage; ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-box text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total de Produtos</p>
                            <p class="text-2xl font-bold text-gray-900"><?= count($produtos) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Em Estoque</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= count(array_filter($produtos, function($p) { 
                                    $estoque = $p['estoque'] ?? $p['estoque_atual'] ?? 0;
                                    return $estoque > 0; 
                                })) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Sem Estoque</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= count(array_filter($produtos, function($p) { 
                                    $estoque = $p['estoque'] ?? $p['estoque_atual'] ?? 0;
                                    return $estoque <= 0; 
                                })) ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="fas fa-tags text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Tipos</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= count(array_unique(array_filter(array_column($produtos, 'tipo')))) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Lista de Produtos</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($produtos)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Nenhum produto cadastrado
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($produtos as $produto): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($produto['codigo_barras']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($produto['nome']) ?>
                                                </div>
                                                <?php if (!empty($produto['descricao'])): ?>
                                                    <div class="text-sm text-gray-500">
                                                        <?= htmlspecialchars(substr($produto['descricao'], 0, 50)) ?>...
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($produto['tipo'] ?? 'Sem tipo') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php 
                                            $estoque = $produto['estoque'] ?? $produto['estoque_atual'] ?? 0;
                                            ?>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                <?= $estoque > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                <?= $estoque ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            R$ <?= number_format($produto['preco_venda'], 2, ',', '.') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="visualizar.php?id=<?= $produto['id'] ?>" 
                                                   class="text-blue-600 hover:text-blue-900" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="../produtos.php?action=editar&id=<?= $produto['id'] ?>" 
                                                   class="text-blue-600 hover:text-blue-900" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="../../produtos.php?action=excluir&id=<?= $produto['id'] ?>" 
                                                   class="text-red-600 hover:text-red-900" title="Excluir"
                                                   onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Exemplo de uso do <option> com dados do produto -->
    <select>
        <?php foreach ($produtos as $produto): ?>
            <option value="<?php echo $produto['id']; ?>" 
                    data-preco="<?php echo $produto['preco_venda']; ?>">
                <?php
                    // Exibe apenas nome e preço
                    echo htmlspecialchars($produto['nome']) .
                         " | Preco: R$ " . number_format($produto['preco_venda'], 2, ',', '.');
                ?>
            </option>
        <?php endforeach; ?>
    </select>
</body>
</html>
