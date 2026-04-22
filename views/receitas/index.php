<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Receitas';
$moduleName = 'Gerencie as receitas Ã³pticas';

try {
    $stmt = $db->query("SELECT r.*, c.nome as cliente_nome FROM receitas r LEFT JOIN clientes c ON r.cliente_id = c.id ORDER BY r.id DESC");
    $receitas = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro: " . $e->getMessage());
    $receitas = [];
}

include __DIR__ . '/../layout_base.php';
?>

<script>
function filtrarTabela() {
    var input = document.getElementById('busca');
    var filter = input.value.toLowerCase();
    var table = document.querySelector('.table');
    var rows = table.getElementsByTagName('tr');
    
    for (var i = 1; i < rows.length; i++) {
        var nomeCell = rows[i].cells[1];
        if (nomeCell) {
            var nome = nomeCell.textContent || nomeCell.innerText;
            if (nome.toLowerCase().indexOf(filter) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
}
</script>

<div class="card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="card-title">
            <i class="fas fa-glasses"></i>
            Lista de Receitas
        </h2>
        <a href="nova.php" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Nova Receita
        </a>
    </div>

    <div class="mb-4">
        <input type="text" id="busca" class="input" placeholder="Buscar receita por nome do cliente..." onkeyup="filtrarTabela()">
    </div>
    
    <?php if (empty($receitas)): ?>
    <div class="empty-state">
        <i class="fas fa-prescription"></i>
        <h3>Nenhuma receita cadastrada</h3>
        <p>Comece adicionando sua primeira receita.</p>
        <a href="nova.php" class="btn btn-primary mt-4">
            <i class="fas fa-plus"></i>
            Cadastrar Receita
        </a>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Olho Direito (OD)</th>
                    <th>Olho Esquerdo (OE)</th>
                    <th>Data</th>
                    <th>AÃ§Ãµes</th>
                </tr>
            </thead>
            <tbody id="tabela-receitas">
                <?php foreach ($receitas as $r): ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td class="font-semibold"><?php echo htmlspecialchars($r['cliente_nome'] ?? '-'); ?></td>
                    <td>
                        <?php 
                        $od = [];
                        if (!empty($r['esfera_od'])) $od[] = "ESF: {$r['esfera_od']}";
                        if (!empty($r['cilindro_od'])) $od[] = "CIL: {$r['cilindro_od']}";
                        if (!empty($r['eixo_od'])) $od[] = "Eixo: {$r['eixo_od']}";
                        echo !empty($od) ? implode('<br>', $od) : '-';
                        ?>
                    </td>
                    <td>
                        <?php 
                        $oe = [];
                        if (!empty($r['esfera_oe'])) $oe[] = "ESF: {$r['esfera_oe']}";
                        if (!empty($r['cilindro_oe'])) $oe[] = "CIL: {$r['cilindro_oe']}";
                        if (!empty($r['eixo_oe'])) $oe[] = "Eixo: {$r['eixo_oe']}";
                        echo !empty($oe) ? implode('<br>', $oe) : '-';
                        ?>
                    </td>
                    <td><?php echo $r['data_receita'] ? date('d/m/Y', strtotime($r['data_receita'])) : '-'; ?></td>
                    <td>
                        <div class="actions">
                            <a href="compartilhar_whatsapp.php?id=<?php echo $r['id']; ?>" target="_blank" class="btn-icon success" title="Compartilhar no WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="imprimir.php?id=<?php echo $r['id']; ?>" target="_blank" class="btn-icon success" title="Imprimir">
                                <i class="fas fa-print"></i>
                            </a>
                            <a href="excluir.php?id=<?php echo $r['id']; ?>" class="btn-icon danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir esta receita?');">
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

<?php include __DIR__ . '/../layout_end.php'; ?>

