<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../controllers/ComprovanteController.php';

$db = Database::getInstance()->getConnection();
$controller = new ComprovanteController();

$clienteId = $_GET['cliente_id'] ?? null;
$clienteNome = '';

if ($clienteId) {
    $stmt = $db->prepare("SELECT nome FROM clientes WHERE id = ?");
    $stmt->execute([$clienteId]);
    $cliente = $stmt->fetch();
    $clienteNome = $cliente['nome'] ?? '';
    $comprovantes = $controller->getByCliente($clienteId);
} else {
    $comprovantes = $controller->getAll();
}

$pageTitle = $clienteId ? 'Comprovantes de ' . htmlspecialchars($clienteNome) : 'Comprovantes de Pagamento';
$moduleName = 'Gerencie os comprovantes de pagamento';

include '../layout_base.php';
?>

<script>
function filtrarTabela() {
    var input = document.getElementById('busca');
    var filter = input.value.toLowerCase();
    var table = document.querySelector('.table');
    var rows = table.getElementsByTagName('tr');
    
    for (var i = 1; i < rows.length; i++) {
        var text = rows[i].textContent || rows[i].innerText;
        if (text.toLowerCase().indexOf(filter) > -1) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

function excluirComprovante(id) {
    if (confirm('Tem certeza que deseja excluir este comprovante?')) {
        fetch('excluir.php?id=' + id, { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
    }
}
</script>

<div class="card">
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success mb-4" style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 10px; border: 1px solid #bbf7d0;">
        <i class="fas fa-check-circle mr-2"></i>
        <?php 
            if($_GET['success'] == 'upload') echo "Comprovante enviado com sucesso!";
            elseif($_GET['success'] == 'excluido') echo "Comprovante excluído com sucesso!";
            else echo "Operação realizada com sucesso!";
        ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error mb-4" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 10px; border: 1px solid #fecaca;">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?php echo $_GET['error']; ?>
    </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-4">
        <h2 class="card-title">
            <i class="fas fa-file-invoice-dollar"></i>
            <?php echo $pageTitle; ?>
        </h2>
        <?php if ($clienteId): ?>
        <a href="novo.php?cliente_id=<?php echo $clienteId; ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Novo Comprovante
        </a>
        <?php endif; ?>
    </div>

    <?php if ($clienteId): ?>
    <div class="mb-4">
        <a href="../clientes/visualizar.php?id=<?php echo $clienteId; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Cliente
        </a>
    </div>
    <?php endif; ?>

    <div class="mb-4">
        <input type="text" id="busca" class="input" placeholder="Buscar comprovante..." onkeyup="filtrarTabela()">
    </div>
    
    <?php if (empty($comprovantes)): ?>
    <div class="empty-state">
        <i class="fas fa-file-invoice-dollar"></i>
        <h3>Nenhum comprovante encontrado</h3>
        <p>Comece enviando o primeiro comprovante de pagamento.</p>
        <?php if ($clienteId): ?>
        <a href="novo.php?cliente_id=<?php echo $clienteId; ?>" class="btn btn-primary mt-4">
            <i class="fas fa-plus"></i>
            Enviar Comprovante
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <?php if (!$clienteId): ?>
                    <th>Cliente</th>
                    <?php endif; ?>
                    <th>Arquivo</th>
                    <th>Valor</th>
                    <th>Descrição</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comprovantes as $comp): ?>
                <tr>
                    <td><?php echo $comp['id']; ?></td>
                    <?php if (!$clienteId && isset($comp['cliente_nome'])): ?>
                    <td><?php echo htmlspecialchars($comp['cliente_nome']); ?></td>
                    <?php endif; ?>
                    <td>
                        <a href="../../uploads/comprovantes/<?php echo $comp['nome_arquivo']; ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-file-<?php echo strpos($comp['tipo_arquivo'], 'pdf') !== false ? 'pdf' : 'image'; ?>"></i>
                            <?php echo htmlspecialchars($comp['nome_original']); ?>
                        </a>
                    </td>
                    <td><?php echo $comp['valor_pagamento'] ? 'R$ ' . number_format($comp['valor_pagamento'], 2, ',', '.') : '-'; ?></td>
                    <td><?php echo htmlspecialchars($comp['descricao'] ?? '-'); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($comp['criado_em'])); ?></td>
                    <td>
                        <div class="actions">
                            <a href="visualizar.php?id=<?php echo $comp['id']; ?>" class="btn-icon" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="../../uploads/comprovantes/<?php echo $comp['nome_arquivo']; ?>" download="<?php echo $comp['nome_original']; ?>" class="btn-icon" title="Baixar">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="#" onclick="excluirComprovante(<?php echo $comp['id']; ?>); return false;" class="btn-icon danger" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include '../layout_end.php'; ?>