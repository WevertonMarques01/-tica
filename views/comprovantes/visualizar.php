<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/ComprovanteController.php';

$db = Database::getInstance()->getConnection();
$controller = new ComprovanteController();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php?error=id_obrigatorio');
    exit;
}

$stmt = $db->prepare("
    SELECT c.*, cli.nome as cliente_nome, v.total as venda_total, v.data_venda as venda_data
    FROM comprovantes_pagamento c
    LEFT JOIN clientes cli ON c.cliente_id = cli.id
    LEFT JOIN vendas v ON c.venda_id = v.id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$comprovante = $stmt->fetch();

if (!$comprovante) {
    header('Location: index.php?error=comprovante_nao_encontrado');
    exit;
}

$extensao = strtolower(pathinfo($comprovante['nome_original'], PATHINFO_EXTENSION));
$isImage = in_array($extensao, ['jpg', 'jpeg', 'png', 'gif']);
$isPdf = $extensao === 'pdf';

$pageTitle = 'Visualizar Comprovante';
include __DIR__ . '/../layout_base.php';
?>

<div class="card">
    <div class="mb-4 flex justify-between items-center">
        <a href="index.php<?php echo $comprovante['cliente_id'] ? '?cliente_id=' . $comprovante['cliente_id'] : ''; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Voltar
        </a>
        <div class="flex gap-2">
            <a href="../../uploads/comprovantes/<?php echo $comprovante['nome_arquivo']; ?>" download="<?php echo $comprovante['nome_original']; ?>" class="btn btn-primary">
                <i class="fas fa-download mr-2"></i>
                Baixar
            </a>
            <a href="#" onclick="excluirComprovante(<?php echo $comprovante['id']; ?>); return false;" class="btn btn-danger">
                <i class="fas fa-trash mr-2"></i>
                Excluir
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="text-lg font-semibold mb-4">VisualizaÃ§Ã£o do Comprovante</h2>
            
            <div class="bg-gray-100 rounded-lg p-4 min-h-[400px] flex items-center justify-center">
                <?php if ($isImage): ?>
                <img src="../../uploads/comprovantes/<?php echo $comprovante['nome_arquivo']; ?>" 
                     alt="<?php echo htmlspecialchars($comprovante['nome_original']); ?>"
                     class="max-w-full max-h-[500px] rounded-lg shadow-lg">
                <?php elseif ($isPdf): ?>
                <iframe src="../../uploads/comprovantes/<?php echo $comprovante['nome_arquivo']; ?>" 
                        class="w-full h-[500px] rounded-lg"
                        style="border: none;"></iframe>
                <?php else: ?>
                <div class="text-center">
                    <i class="fas fa-file text-6xl text-gray-400"></i>
                    <p class="mt-2 text-gray-500">VisualizaÃ§Ã£o nÃ£o disponÃ­vel</p>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($isImage): ?>
            <div class="mt-4 text-center">
                <a href="../../uploads/comprovantes/<?php echo $comprovante['nome_arquivo']; ?>" target="_blank" class="btn btn-secondary">
                    <i class="fas fa-expand mr-2"></i>
                    Visualizar em Tela Cheia
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <div>
            <h2 class="text-lg font-semibold mb-4">InformaÃ§Ãµes do Comprovante</h2>
            
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-500">Arquivo Original</label>
                    <p class="mt-1 font-medium"><?php echo htmlspecialchars($comprovante['nome_original']); ?></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500">Tipo</label>
                        <p class="mt-1 font-medium"><?php echo htmlspecialchars($comprovante['tipo_arquivo']); ?></p>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-500">Tamanho</label>
                        <p class="mt-1 font-medium"><?php echo number_format($comprovante['tamanho_arquivo'] / 1024, 1); ?> KB</p>
                    </div>
                </div>
                
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <label class="block text-sm font-medium text-green-700">Valor do Pagamento</label>
                    <p class="mt-1 text-2xl font-bold text-green-800">
                        <?php echo $comprovante['valor_pagamento'] ? 'R$ ' . number_format($comprovante['valor_pagamento'], 2, ',', '.') : 'NÃ£o informado'; ?>
                    </p>
                </div>
                
                <?php if (!empty($comprovante['descricao'])): ?>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-500">DescriÃ§Ã£o</label>
                    <p class="mt-1"><?php echo nl2br(htmlspecialchars($comprovante['descricao'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($comprovante['venda_id']): ?>
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <label class="block text-sm font-medium text-blue-700">Venda Associada</label>
                    <p class="mt-1 font-medium">
                        <a href="../vendas/visualizar.php?id=<?php echo $comprovante['venda_id']; ?>" class="text-blue-600 hover:underline">
                            #<?php echo $comprovante['venda_id']; ?> - R$ <?php echo number_format($comprovante['venda_total'], 2, ',', '.'); ?>
                        </a>
                    </p>
                    <p class="text-sm text-gray-500"><?php echo date('d/m/Y', strtotime($comprovante['venda_data'])); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="p-4 bg-gray-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-500">Cliente</label>
                    <p class="mt-1 font-medium">
                        <a href="../clientes/visualizar.php?id=<?php echo $comprovante['cliente_id']; ?>" class="text-blue-600 hover:underline">
                            <?php echo htmlspecialchars($comprovante['cliente_nome']); ?>
                        </a>
                    </p>
                </div>
                
                <div class="p-4 bg-gray-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-500">Data de Envio</label>
                    <p class="mt-1 font-medium"><?php echo date('d/m/Y H:i:s', strtotime($comprovante['criado_em'])); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function excluirComprovante(id) {
    if (confirm('Tem certeza que deseja excluir este comprovante? Esta aÃ§Ã£o nÃ£o pode ser desfeita.')) {
        fetch('excluir.php?id=' + id, { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php?success=excluido';
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                alert('Erro ao excluir comprovante');
            });
    }
}
</script>

<?php include __DIR__ . '/../layout_end.php'; ?>
