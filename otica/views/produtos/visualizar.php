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
    // Buscar produto
    $stmt = $db->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();
    
    if (!$produto) {
        header('Location: index.php?error=produto_nao_encontrado');
        exit;
    }
    
    // Buscar estatísticas relacionadas ao produto
    $estatisticas = [
        'vendas' => 0,
        'movimentacoes' => 0,
        'valor_total_vendas' => 0
    ];
    
    // Contar vendas (se existir tabela itens_venda)
    try {
        $stmtVendas = $db->prepare("
            SELECT COUNT(*) as total, COALESCE(SUM(iv.quantidade * iv.preco), 0) as valor_total 
            FROM itens_venda iv 
            WHERE iv.produto_id = ?
        ");
        $stmtVendas->execute([$id]);
        $vendas = $stmtVendas->fetch();
        $estatisticas['vendas'] = $vendas['total'];
        $estatisticas['valor_total_vendas'] = $vendas['valor_total'];
    } catch (PDOException $e) {
        error_log("Aviso: Erro ao buscar vendas: " . $e->getMessage());
    }
    
    // Contar movimentações de estoque
    try {
        $stmtMovimentacoes = $db->prepare("SELECT COUNT(*) as total FROM movimentacao_estoque WHERE produto_id = ?");
        $stmtMovimentacoes->execute([$id]);
        $movimentacoes = $stmtMovimentacoes->fetch();
        $estatisticas['movimentacoes'] = $movimentacoes['total'];
    } catch (PDOException $e) {
        error_log("Aviso: Tabela movimentacao_estoque não encontrada: " . $e->getMessage());
    }
    
} catch (PDOException $e) {
    error_log("Erro ao buscar produto: " . $e->getMessage());
    header('Location: index.php?error=erro_sistema');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produto: <?php echo htmlspecialchars($produto['nome']); ?> - Ótica</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
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

        .card {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 16px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <a href="index.php" class="text-gray-400 hover:text-gray-600 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">
                            <?php echo htmlspecialchars($produto['nome']); ?>
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="../produtos.php?action=editar&id=<?php echo $produto['id']; ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition-colors">
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                            <i class="fas fa-exchange-alt text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium">Movimentações</h3>
                            <p class="text-2xl font-bold"><?php echo $estatisticas['movimentacoes']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Informações Básicas -->
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações Básicas</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($produto['nome']); ?></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Código</label>
                                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($produto['codigo'] ?? $produto['codigo_barras'] ?? 'N/A'); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($produto['tipo'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                        <?php if (!empty($produto['descricao'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Descrição</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1">
                                <?php if ($produto['ativo'] ?? true): ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Ativo</span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inativo</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Preços e Estoque -->
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Preços e Estoque</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <?php if (isset($produto['preco_custo'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Preço de Custo</label>
                                <p class="mt-1 text-sm text-gray-900">R$ <?php echo number_format($produto['preco_custo'], 2, ',', '.'); ?></p>
                            </div>
                            <?php endif; ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Preço de Venda</label>
                                <p class="mt-1 text-lg font-semibold text-green-600">
                                    R$ <?php echo number_format($produto['preco_venda'], 2, ',', '.'); ?>
                                </p>
                            </div>
                        </div>
                        <?php if (!empty($produto['preco_promocional'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Preço Promocional</label>
                            <p class="mt-1 text-lg font-semibold text-orange-600">
                                R$ <?php echo number_format($produto['preco_promocional'], 2, ',', '.'); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estoque Atual</label>
                                <?php 
                                $estoque = $produto['estoque'] ?? $produto['estoque_atual'] ?? 0;
                                ?>
                                <p class="mt-1">
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?= $estoque > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?php echo $estoque; ?> unidades
                                    </span>
                                </p>
                            </div>
                            <?php if (isset($produto['estoque_minimo'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estoque Mínimo</label>
                                <p class="mt-1 text-sm text-gray-900"><?php echo $produto['estoque_minimo']; ?> unidades</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Detalhes Técnicos -->
                <?php if (!empty($produto['modelo']) || !empty($produto['cor']) || !empty($produto['material'])): ?>
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalhes Técnicos</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <?php if (!empty($produto['modelo'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Modelo</label>
                                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($produto['modelo']); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($produto['cor'])): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cor</label>
                                <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($produto['cor']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($produto['material'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Material</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($produto['material']); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($produto['tamanho'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tamanho</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($produto['tamanho']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Informações do Sistema -->
                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações do Sistema</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID do Produto</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo $produto['id']; ?></p>
                        </div>
                        <?php if (!empty($produto['codigo_barras'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Código de Barras</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($produto['codigo_barras']); ?></p>
                        </div>
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data de Cadastro</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <?php echo date('d/m/Y H:i', strtotime($produto['created_at'] ?? $produto['criado_em'] ?? 'now')); ?>
                            </p>
                        </div>
                        <?php if (!empty($produto['updated_at']) && $produto['updated_at'] !== ($produto['created_at'] ?? $produto['criado_em'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Última Atualização</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <?php echo date('d/m/Y H:i', strtotime($produto['updated_at'])); ?>
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