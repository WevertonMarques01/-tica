<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Verificar se foi passado um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: historico.php?error=id_invalido');
    exit;
}

$id = (int)$_GET['id'];

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

try {
    // Buscar venda com detalhes do cliente
    $stmt = $db->prepare("
        SELECT v.*, c.nome as cliente_nome, c.documento as cliente_documento, 
               c.telefone as cliente_telefone, c.email as cliente_email
        FROM vendas v 
        LEFT JOIN clientes c ON v.cliente_id = c.id 
        WHERE v.id = ?
    ");
    $stmt->execute([$id]);
    $venda = $stmt->fetch();
    
    if (!$venda) {
        header('Location: historico.php?error=venda_nao_encontrada');
        exit;
    }
    
    // Buscar itens da venda (se existir tabela itens_venda)
    $itens = [];
    try {
        $stmtItens = $db->prepare("
            SELECT iv.*, p.nome as produto_nome 
            FROM itens_venda iv 
            LEFT JOIN produtos p ON iv.produto_id = p.id 
            WHERE iv.venda_id = ?
        ");
        $stmtItens->execute([$id]);
        $itens = $stmtItens->fetchAll();
    } catch (PDOException $e) {
        // Tabela itens_venda pode não existir
        error_log("Aviso: Tabela itens_venda não encontrada: " . $e->getMessage());
    }
    
} catch (PDOException $e) {
    error_log("Erro ao buscar venda: " . $e->getMessage());
    header('Location: historico.php?error=erro_sistema');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Venda #<?php echo $venda['id']; ?> - Ótica</title>
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
                        <a href="historico.php" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Detalhes da Venda #<?php echo $venda['id']; ?>
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition-colors">
                            <i class="fas fa-print mr-2"></i>Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Informações da Venda -->
            <div class="card p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informações da Venda</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ID da Venda</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo $venda['id']; ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data da Venda</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            <?php echo date('d/m/Y H:i', strtotime($venda['data_venda'] ?? $venda['created_at'] ?? 'now')); ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valor Total</label>
                        <p class="mt-1 text-lg font-semibold text-green-600">
                            R$ <?php echo number_format($venda['valor_total'], 2, ',', '.'); ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Forma de Pagamento</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            <?php
                            $formasPagamento = [
                                'dinheiro' => 'Dinheiro',
                                'cartao_credito' => 'Cartão de Crédito',
                                'cartao_debito' => 'Cartão de Débito',
                                'pix' => 'PIX',
                                'boleto' => 'Boleto'
                            ];
                            echo $formasPagamento[$venda['forma_pagamento']] ?? $venda['forma_pagamento'];
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informações do Cliente -->
            <div class="card p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informações do Cliente</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            <?php echo htmlspecialchars($venda['cliente_nome'] ?? 'Cliente não encontrado'); ?>
                        </p>
                    </div>
                    <?php if (!empty($venda['cliente_documento'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Documento</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo $venda['cliente_documento']; ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($venda['cliente_telefone'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefone</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo $venda['cliente_telefone']; ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($venda['cliente_email'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white"><?php echo $venda['cliente_email']; ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Itens da Venda -->
            <?php if (!empty($itens)): ?>
            <div class="card p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Itens da Venda</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Produto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Quantidade
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Preço Unitário
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            <?php foreach ($itens as $item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?php echo htmlspecialchars($item['produto_nome'] ?? 'Produto não encontrado'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?php echo $item['quantidade']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    R$ <?php echo number_format($item['quantidade'] * $item['subtotal'], 2, ',', '.'); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Observações -->
            <?php if (!empty($venda['observacoes'])): ?>
            <div class="card p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Observações</h2>
                <p class="text-sm text-gray-700 dark:text-gray-300"><?php echo nl2br(htmlspecialchars($venda['observacoes'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>