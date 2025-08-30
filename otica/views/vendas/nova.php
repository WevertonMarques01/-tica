<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

// Buscar clientes para o select
try {
    $stmt = $db->prepare("SELECT id, nome, documento FROM clientes ORDER BY nome");
    $stmt->execute();
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar clientes: " . $e->getMessage());
    $clientes = [];
}

// Buscar produtos para o select
try {
    $stmt = $db->prepare("SELECT id, nome, preco, estoque FROM produtos WHERE estoque > 0 ORDER BY nome");
    $stmt->execute();
    $produtos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar produtos: " . $e->getMessage());
    $produtos = [];
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? '';
    $forma_pagamento = $_POST['forma_pagamento'] ?? '';
    $produtos_data = $_POST['produtos'] ?? [];
    $observacoes = $_POST['observacoes'] ?? '';
    $valor_total = $_POST['valor_total'] ?? 0;
    
    if (empty($cliente_id) || empty($forma_pagamento) || empty($produtos_data)) {
        $erro = 'Todos os campos obrigatórios devem ser preenchidos.';
    } else {
        try {
            $db->beginTransaction();
            
            // Inserir venda
            $stmt = $db->prepare("
                INSERT INTO vendas (cliente_id, forma_pagamento, valor_total, observacoes, data_venda) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $result = $stmt->execute([$cliente_id, $forma_pagamento, $valor_total, $observacoes]);
            
            if ($result) {
                $venda_id = $db->lastInsertId();
                
                // Inserir itens da venda
                foreach ($produtos_data as $produto) {
                    if (!empty($produto['id']) && !empty($produto['quantidade'])) {
                        $stmt = $db->prepare("
                            INSERT INTO venda_itens (venda_id, produto_id, quantidade, preco_unitario) 
                            VALUES (?, ?, ?, (SELECT preco FROM produtos WHERE id = ?))
                        ");
                        $stmt->execute([$venda_id, $produto['id'], $produto['quantidade'], $produto['id']]);
                        
                        // Atualizar estoque
                        $stmt = $db->prepare("
                            UPDATE produtos SET estoque = estoque - ? WHERE id = ?
                        ");
                        $stmt->execute([$produto['quantidade'], $produto['id']]);
                    }
                }
                
                $db->commit();
                
                // Registrar log
                $logStmt = $db->prepare("INSERT INTO logs (usuario_id, acao, detalhes) VALUES (?, ?, ?)");
                $logStmt->execute([$_SESSION['usuario_id'], 'venda_criada', "Nova venda criada ID: $venda_id"]);
                
                header('Location: historico.php?success=1');
                exit;
            } else {
                $db->rollBack();
                $erro = 'Erro ao salvar venda.';
            }
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Erro ao salvar venda: " . $e->getMessage());
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
    <title>Nova Venda - Ótica</title>
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

        .btn-danger {
            background-color: #ef4444;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
        }

        .btn-success {
            background-color: #10b981;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }

        .theme-toggle {
            transition: all 0.3s ease;
        }

        .theme-toggle.rotated {
            transform: rotate(180deg);
        }

        .produto-item {
            transition: all 0.3s ease;
        }

        .produto-item:hover {
            background-color: #f0fdfa;
        }

        .dark .produto-item:hover {
            background-color: #0f172a;
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
                        <a href="../admin/index.php" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nova Venda</h1>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="card">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Dados da Venda</h2>
                </div>
                
                <form method="POST" class="p-6 space-y-6">
                    <?php if (isset($erro)): ?>
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded">
                            <i class="fas fa-exclamation-triangle mr-2"></i><?php echo htmlspecialchars($erro); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Cliente e Forma de Pagamento -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cliente_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Cliente *
                            </label>
                            <select name="cliente_id" id="cliente_id" required 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Selecione um cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?php echo $cliente['id']; ?>" <?php echo (isset($_POST['cliente_id']) && $_POST['cliente_id'] == $cliente['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cliente['nome']); ?> - <?php echo htmlspecialchars($cliente['documento']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="forma_pagamento" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Forma de Pagamento *
                            </label>
                            <select name="forma_pagamento" id="forma_pagamento" required 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Selecione</option>
                                <option value="dinheiro" <?php echo (isset($_POST['forma_pagamento']) && $_POST['forma_pagamento'] == 'dinheiro') ? 'selected' : ''; ?>>Dinheiro</option>
                                <option value="cartao_credito" <?php echo (isset($_POST['forma_pagamento']) && $_POST['forma_pagamento'] == 'cartao_credito') ? 'selected' : ''; ?>>Cartão de Crédito</option>
                                <option value="cartao_debito" <?php echo (isset($_POST['forma_pagamento']) && $_POST['forma_pagamento'] == 'cartao_debito') ? 'selected' : ''; ?>>Cartão de Débito</option>
                                <option value="pix" <?php echo (isset($_POST['forma_pagamento']) && $_POST['forma_pagamento'] == 'pix') ? 'selected' : ''; ?>>PIX</option>
                                <option value="boleto" <?php echo (isset($_POST['forma_pagamento']) && $_POST['forma_pagamento'] == 'boleto') ? 'selected' : ''; ?>>Boleto</option>
                            </select>
                        </div>
                    </div>

                    <!-- Produtos -->
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            <i class="fas fa-box mr-2"></i>Produtos
                        </h3>
                        <div id="produtos-container" class="space-y-4">
                            <div class="produto-item grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div>
                                    <select class="produto-select w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" name="produtos[0][id]" required>
                                        <option value="">Selecione um produto</option>
                                        <?php foreach ($produtos as $produto): ?>
                                            <option value="<?php echo $produto['id']; ?>" 
                                                    data-preco="<?php echo $produto['preco']; ?>"
                                                    data-estoque="<?php echo $produto['estoque']; ?>">
                                                <?php echo htmlspecialchars($produto['nome']); ?> - R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <input type="number" class="quantidade w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" 
                                           name="produtos[0][quantidade]" placeholder="Qtd" min="1" value="1" required>
                                </div>
                                <div>
                                    <input type="text" class="preco-unitario w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white" 
                                           placeholder="Preço Unit." readonly>
                                </div>
                                <div>
                                    <input type="text" class="subtotal w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white" 
                                           placeholder="Subtotal" readonly>
                                </div>
                                <div>
                                    <button type="button" class="btn-remover-produto w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" id="adicionar-produto" class="mt-4 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>Adicionar Produto
                        </button>
                    </div>

                    <!-- Observações e Resumo -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Observações
                            </label>
                            <textarea name="observacoes" id="observacoes" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                      placeholder="Observações sobre a venda..."><?php echo htmlspecialchars($_POST['observacoes'] ?? ''); ?></textarea>
                        </div>
                        <div class="card p-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Resumo da Venda</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total de Itens:</span>
                                    <span class="font-medium text-gray-900 dark:text-white" id="total-itens">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Valor Total:</span>
                                    <span class="font-bold text-lg text-otica-primary" id="valor-total">R$ 0,00</span>
                                </div>
                            </div>
                            <input type="hidden" id="valor_total" name="valor_total" value="0">
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <a href="../admin/index.php" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Voltar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-otica-primary hover:bg-otica-secondary text-white rounded-md font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>Finalizar Venda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let produtoIndex = 1;

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

        // Adicionar produto
        document.getElementById('adicionar-produto').addEventListener('click', function() {
            const container = document.getElementById('produtos-container');
            const novoProduto = document.querySelector('.produto-item').cloneNode(true);
            
            // Limpar valores
            novoProduto.querySelector('.produto-select').value = '';
            novoProduto.querySelector('.quantidade').value = '1';
            novoProduto.querySelector('.preco-unitario').value = '';
            novoProduto.querySelector('.subtotal').value = '';
            
            // Atualizar nomes dos campos
            novoProduto.querySelector('.produto-select').name = `produtos[${produtoIndex}][id]`;
            novoProduto.querySelector('.quantidade').name = `produtos[${produtoIndex}][quantidade]`;
            
            container.appendChild(novoProduto);
            produtoIndex++;
            
            // Adicionar event listeners
            adicionarEventListeners(novoProduto);
        });

        // Remover produto
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remover-produto')) {
                if (document.querySelectorAll('.produto-item').length > 1) {
                    e.target.closest('.produto-item').remove();
                    calcularTotal();
                }
            }
        });

        // Adicionar event listeners para um item de produto
        function adicionarEventListeners(produtoItem) {
            const produtoSelect = produtoItem.querySelector('.produto-select');
            const quantidadeInput = produtoItem.querySelector('.quantidade');
            const precoUnitarioInput = produtoItem.querySelector('.preco-unitario');
            const subtotalInput = produtoItem.querySelector('.subtotal');
            
            produtoSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const preco = option.dataset.preco || 0;
                precoUnitarioInput.value = `R$ ${parseFloat(preco).toFixed(2).replace('.', ',')}`;
                calcularSubtotal(produtoItem);
            });
            
            quantidadeInput.addEventListener('input', function() {
                calcularSubtotal(produtoItem);
            });
        }

        // Calcular subtotal de um item
        function calcularSubtotal(produtoItem) {
            const produtoSelect = produtoItem.querySelector('.produto-select');
            const quantidadeInput = produtoItem.querySelector('.quantidade');
            const subtotalInput = produtoItem.querySelector('.subtotal');
            
            const option = produtoSelect.options[produtoSelect.selectedIndex];
            const preco = parseFloat(option.dataset.preco || 0);
            const quantidade = parseInt(quantidadeInput.value || 0);
            
            const subtotal = preco * quantidade;
            subtotalInput.value = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
            
            calcularTotal();
        }

        // Calcular total da venda
        function calcularTotal() {
            let total = 0;
            let totalItens = 0;
            
            document.querySelectorAll('.produto-item').forEach(function(item) {
                const subtotalText = item.querySelector('.subtotal').value;
                if (subtotalText) {
                    const subtotal = parseFloat(subtotalText.replace('R$ ', '').replace(',', '.'));
                    total += subtotal;
                }
                
                const quantidade = parseInt(item.querySelector('.quantidade').value || 0);
                totalItens += quantidade;
            });
            
            document.getElementById('total-itens').textContent = totalItens;
            document.getElementById('valor-total').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            document.getElementById('valor_total').value = total;
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadTheme();
            
            // Adicionar event listeners para o primeiro item
            document.querySelectorAll('.produto-item').forEach(function(item) {
                adicionarEventListeners(item);
            });
        });
    </script>
</body>
</html> 