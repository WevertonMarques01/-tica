<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../controllers/ComprovanteController.php';

$db = Database::getInstance()->getConnection();
$controller = new ComprovanteController();

$clienteId = $_GET['cliente_id'] ?? null;
$vendaId = $_GET['venda_id'] ?? null;

if (!$clienteId) {
    header('Location: index.php?error=cliente_obrigatorio');
    exit;
}

$stmt = $db->prepare("SELECT id, nome FROM clientes WHERE id = ?");
$stmt->execute([$clienteId]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header('Location: index.php?error=cliente_nao_encontrado');
    exit;
}

$vendas = $controller->getVendas($clienteId);

$pageTitle = 'Enviar Comprovante';
$moduleName = 'Adicione um novo comprovante de pagamento';

include '../layout_base.php';
?>

<script>
function formatarMoeda(input) {
    var value = input.value.replace(/\D/g, '');
    value = (value / 100).toFixed(2);
    value = value.replace('.', ',');
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    input.value = value;
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadForm');
    const fileInput = document.getElementById('comprovante');
    const preview = document.getElementById('preview');
    const fileNameDisplay = document.getElementById('fileName');
    
    fileInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            fileNameDisplay.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 300px; max-height: 200px; border-radius: 8px; margin-top: 10px;">';
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                preview.innerHTML = '<div class="text-gray-500 mt-2"><i class="fas fa-file-pdf text-4xl"></i><br>Arquivo PDF selecionado</div>';
            }
        }
    });
    
    form.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    });
});
</script>

<div class="card">
    <div class="mb-4">
        <a href="index.php?cliente_id=<?php echo $clienteId; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
    </div>

    <h2 class="card-title mb-6">
        <i class="fas fa-file-upload"></i>
        Enviar Comprovante de Pagamento
    </h2>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error mb-4" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 10px; border: 1px solid #fecaca;">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="salvar.php" enctype="multipart/form-data" id="uploadForm" class="space-y-6">
        <input type="hidden" name="cliente_id" value="<?php echo $clienteId; ?>">
        
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <p class="text-sm text-blue-800">
                <i class="fas fa-user mr-2"></i>
                <strong>Cliente:</strong> <?php echo htmlspecialchars($cliente['nome']); ?>
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Arquivo do Comprovante *</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                <input type="file" id="comprovante" name="comprovante" accept="image/*,application/pdf" required class="hidden">
                <label for="comprovante" class="cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-500">Clique para selecionar ou arraste o arquivo aqui</p>
                    <p class="text-xs text-gray-400 mt-1">Formatos: JPG, PNG, GIF, PDF (máx 10MB)</p>
                </label>
                <div id="fileName" class="mt-3 text-sm font-medium text-blue-600"></div>
                <div id="preview" class="mt-3"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Valor do Pagamento</label>
                <input type="text" name="valor_pagamento" class="input" placeholder="0,00" onkeyup="formatarMoeda(this)" pattern="^\d{1,3}(\.\d{3})*,\d{2}$">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Venda Associada</label>
                <?php if (empty($vendas)): ?>
                <p class="text-sm text-gray-500 italic">Nenhuma venda encontrada para este cliente</p>
                <input type="hidden" name="venda_id" value="">
                <?php else: ?>
                <select name="venda_id" class="input">
                    <option value="">Selecione uma venda (opcional)</option>
                    <?php foreach ($vendas as $venda): ?>
                    <option value="<?php echo $venda['id']; ?>" <?php echo $vendaId == $venda['id'] ? 'selected' : ''; ?>>
                        #<?php echo $venda['id']; ?> - R$ <?php echo number_format($venda['total'], 2, ',', '.'); ?> (<?php echo date('d/m/Y', strtotime($venda['data_venda'])); ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
            <textarea name="descricao" class="input" rows="3" placeholder="Ex: Pagamento da parcela 1/3 - Óculos de grau"></textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" id="submitBtn" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i>
                Salvar Comprovante
            </button>
        </div>
    </form>
</div>

<style>
input[type="file"] {
    display: none;
}
</style>

<?php include '../layout_end.php'; ?>