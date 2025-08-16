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
    $olho_direito = $_POST['olho_direito'] ?? '';
    $olho_esquerdo = $_POST['olho_esquerdo'] ?? '';
    $observacoes = $_POST['observacoes'] ?? '';
    $data_receita = $_POST['data_receita'] ?? date('Y-m-d');
    
    if (empty($cliente_id)) {
        $erro = 'Selecione um cliente.';
    } else {
        try {
            $stmt = $db->prepare("
                INSERT INTO receitas (cliente_id, olho_direito, olho_esquerdo, observacoes, data_receita) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $result = $stmt->execute([$cliente_id, $olho_direito, $olho_esquerdo, $observacoes, $data_receita]);
            
            if ($result) {
                // Registrar log
                $logStmt = $db->prepare("INSERT INTO logs (usuario_id, acao, detalhes) VALUES (?, ?, ?)");
                $logStmt->execute([$_SESSION['usuario_id'], 'receita_criada', "Nova receita criada para cliente ID: $cliente_id"]);
                
                header('Location: index.php?success=1');
                exit;
            } else {
                $erro = 'Erro ao salvar receita.';
            }
        } catch (PDOException $e) {
            error_log("Erro ao salvar receita: " . $e->getMessage());
            $erro = 'Erro interno do sistema.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Receita - Ótica</title>
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
                        <a href="index.php" class="text-gray-400 hover:text-gray-600 mr-4">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">Nova Receita</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Dados da Receita</h2>
                </div>
                
                <form method="POST" class="p-6 space-y-6">
                    <?php if (isset($erro)): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                            <?php echo htmlspecialchars($erro); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Cliente -->
                    <div>
                        <label for="cliente_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Cliente *
                        </label>
                        <select name="cliente_id" id="cliente_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent">
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
                        <label for="data_receita" class="block text-sm font-medium text-gray-700 mb-2">
                            Data da Receita
                        </label>
                        <input type="date" name="data_receita" id="data_receita" 
                               value="<?php echo $_POST['data_receita'] ?? date('Y-m-d'); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent">
                    </div>

                    <!-- Olho Direito -->
                    <div>
                        <label for="olho_direito" class="block text-sm font-medium text-gray-700 mb-2">
                            Olho Direito (OD)
                        </label>
                        <textarea name="olho_direito" id="olho_direito" rows="3" 
                                  placeholder="Ex: Esférico: -2.50, Cilíndrico: -0.75, Eixo: 90°"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent"><?php echo htmlspecialchars($_POST['olho_direito'] ?? ''); ?></textarea>
                    </div>

                    <!-- Olho Esquerdo -->
                    <div>
                        <label for="olho_esquerdo" class="block text-sm font-medium text-gray-700 mb-2">
                            Olho Esquerdo (OE)
                        </label>
                        <textarea name="olho_esquerdo" id="olho_esquerdo" rows="3" 
                                  placeholder="Ex: Esférico: -2.25, Cilíndrico: -0.50, Eixo: 85°"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent"><?php echo htmlspecialchars($_POST['olho_esquerdo'] ?? ''); ?></textarea>
                    </div>

                    <!-- Observações -->
                    <div>
                        <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-2">
                            Observações
                        </label>
                        <textarea name="observacoes" id="observacoes" rows="4" 
                                  placeholder="Observações adicionais sobre a receita..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-otica-primary focus:border-transparent"><?php echo htmlspecialchars($_POST['observacoes'] ?? ''); ?></textarea>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="index.php" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-otica-primary hover:bg-otica-secondary text-white rounded-md font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>Salvar Receita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 