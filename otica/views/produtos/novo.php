<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$db = Database::getInstance()->getConnection();

$errors = [];
$success = false;
$produto = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo_barras'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $cor = trim($_POST['cor'] ?? '');
    $estoque = (int)($_POST['estoque'] ?? 0);
    $preco_venda = (float)($_POST['preco'] ?? 0);
    
    // Validações
    if (empty($codigo)) {
        $errors['codigo_barras'] = 'Código de barras é obrigatório';
    } elseif (!preg_match('/^\d+$/', $codigo)) {
        $errors['codigo_barras'] = 'Código deve conter apenas números';
    } else {
        // Verificar se código já existe
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM produtos WHERE codigo_barras = ?");
        $stmt->execute([$codigo]);
        $result = $stmt->fetch();

        if ($result && $result['count'] > 0) {
            $errors['codigo_barras'] = 'Este código de barras já está cadastrado';
        }
    }
    
    if (empty($nome)) {
        $errors['nome'] = 'Nome do produto é obrigatório';
    }
    
    if ($estoque < 0) {
        $errors['estoque'] = 'Estoque não pode ser negativo';
    }
    
    if ($preco_venda <= 0) {
        $errors['preco_venda'] = 'Preço deve ser maior que zero';
    }
    
    // Se não há erros, salvar produto
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO produtos (codigo_barras, nome, descricao, estoque, preco_venda) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $codigo, $nome, $descricao, $estoque, $preco_venda
            ]);
            
            if ($result) {
                $success = true;
                $produto = [
                    'codigo_barras' => $codigo,
                    'nome' => $nome,
                    'descricao' => $descricao,
                    'tipo' => $tipo,
                    'modelo' => $modelo,
                    'cor' => $cor,
                    'estoque' => $estoque,
                    'preco' => $preco_venda
                ];
                
                // Limpar formulário após sucesso
                $codigo = $nome = $descricao = $tipo = $marca = $modelo = $cor = '';
                $estoque = $preco = 0;
            } else {
                $errors['geral'] = 'Erro ao salvar produto';
            }
        } catch (Exception $e) {
            $errors['geral'] = 'Erro interno do sistema: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Produto - Wiz Ótica</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .barcode-input:focus { outline: 2px solid #3b82f6; outline-offset: 2px; }
        .success-animation { animation: successPulse 0.6s ease-in-out; }
        @keyframes successPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">Novo Produto</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="index.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <?php if ($success): ?>
                <!-- Success Message -->
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 success-animation">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Produto cadastrado com sucesso!</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p><strong>Código:</strong> <?= htmlspecialchars($produto['codigo_barras']) ?></p>
                                <p><strong>Nome:</strong> <?= htmlspecialchars($produto['nome']) ?></p>
                                <p><strong>Preço:</strong> R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                                <p><strong>Estoque:</strong> <?= $produto['estoque'] ?> unidades</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Cadastrar Novo Produto</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Use o leitor de código de barras ou digite manualmente o código
                    </p>
                </div>
                
                <form method="POST" class="p-6 space-y-6">
                    <?php if (isset($errors['geral'])): ?>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800"><?= htmlspecialchars($errors['geral']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Código de Barras -->
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">
                            Código de Barras <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="codigo" 
                                   name="codigo_barras" 
                                   value="<?= htmlspecialchars($codigo ?? '') ?>"
                                   class="barcode-input w-full px-4 py-3 border border-gray-300 rounded-lg text-lg font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Passe o produto no leitor ou digite o código"
                                   maxlength="50"
                                   autocomplete="off"
                                   required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-barcode text-gray-400"></i>
                            </div>
                        </div>
                        <?php if (isset($errors['codigo'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['codigo']) ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Apenas números são aceitos. O código será preenchido automaticamente pelo leitor.
                        </p>
                    </div>

                    <!-- Nome do Produto -->
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do Produto <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="nome" 
                               name="nome" 
                               value="<?= htmlspecialchars($nome ?? '') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Digite o nome do produto"
                               required>
                        <?php if (isset($errors['nome'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['nome']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Descrição -->
                    <div>
                        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">
                            Descrição
                        </label>
                        <textarea id="descricao" 
                                  name="descricao" 
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Descrição detalhada do produto (opcional)"><?= htmlspecialchars($descricao ?? '') ?></textarea>
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo
                        </label>
                        <input type="text" 
                               id="tipo" 
                               name="tipo" 
                               value="<?= htmlspecialchars($tipo ?? '') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ex: Óculos de Grau, Óculos de Sol, etc.">
                    </div>

                    <!-- Modelo -->
                    <div>
                        <label for="modelo" class="block text-sm font-medium text-gray-700 mb-2">
                            Modelo
                        </label>
                        <input type="text" 
                               id="modelo" 
                               name="modelo" 
                               value="<?= htmlspecialchars($modelo ?? '') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ex: Aviador, Wayfarer, etc.">
                    </div>

                    <!-- Cor -->
                    <div>
                        <label for="cor" class="block text-sm font-medium text-gray-700 mb-2">
                            Cor
                        </label>
                        <input type="text" 
                               id="cor" 
                               name="cor" 
                               value="<?= htmlspecialchars($cor ?? '') ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ex: Preto, Dourado, Prateado, etc.">
                    </div>

                    <!-- Estoque e Preço -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="estoque" class="block text-sm font-medium text-gray-700 mb-2">
                                Quantidade em Estoque <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="estoque" 
                                   name="estoque" 
                                   value="<?= $estoque ?? 0 ?>"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0"
                                   required>
                            <?php if (isset($errors['estoque'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['estoque']) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="preco" class="block text-sm font-medium text-gray-700 mb-2">
                                Preço (R$) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                                    R$
                                </span>
                                <input type="number" 
                                       id="preco" 
                                       name="preco" 
                                       value="<?= $preco ?? '' ?>"
                                       min="0.01"
                                       step="0.01"
                                       class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0,00"
                                       required>
                            </div>
                            <?php if (isset($errors['preco'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['preco']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="../produtos.php" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>
                            Salvar Produto
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const codigoInput = document.getElementById('codigo');
            const nomeInput = document.getElementById('nome');
            
            // Focar no campo de código ao carregar a página
            codigoInput.focus();
            
            // Permitir apenas números no campo de código
            codigoInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
            
            // Prevenir teclas não numéricas
            codigoInput.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
            
            // Auto-focus no nome após preencher código
            codigoInput.addEventListener('input', function() {
                if (this.value.length >= 8) {
                    nomeInput.focus();
                }
            });
            
            // Formatação automática do preço
            const precoInput = document.getElementById('preco');
            precoInput.addEventListener('blur', function() {
                if (this.value) {
                    this.value = parseFloat(this.value).toFixed(2);
                }
            });
            
            // Detectar se é um leitor de código de barras (digitação rápida)
            let lastKeyTime = 0;
            codigoInput.addEventListener('keydown', function(e) {
                const currentTime = new Date().getTime();
                const timeDiff = currentTime - lastKeyTime;
                lastKeyTime = currentTime;
                
                // Se a digitação for muito rápida (menos de 50ms entre teclas), 
                // provavelmente é um leitor de código de barras
                if (timeDiff < 50 && this.value.length > 0) {
                    this.classList.add('barcode-scanned');
                    setTimeout(() => {
                        this.classList.remove('barcode-scanned');
                        nomeInput.focus();
                    }, 100);
                }
            });
            
            // Limpar formulário após sucesso
            <?php if ($success): ?>
            setTimeout(() => {
                codigoInput.focus();
            }, 2000);
            <?php endif; ?>
        });
    </script>
</body>
</html>
