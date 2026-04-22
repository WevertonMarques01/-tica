<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Clientes';
$moduleName = 'Gerencie seus clientes';

try {
    $stmt = $db->query("SELECT * FROM clientes ORDER BY nome");
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro: " . $e->getMessage());
    $clientes = [];
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
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success mb-4" style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 10px; border: 1px solid #bbf7d0;">
        <i class="fas fa-check-circle mr-2"></i>
        <?php 
            if($_GET['success'] == 'excluido') echo "Cliente excluÃ­do com sucesso!";
            else echo "OperaÃ§Ã£o realizada com sucesso!";
        ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error mb-4" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 10px; border: 1px solid #fecaca;">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?php 
            if($_GET['error'] == 'id_invalido') echo "ID de cliente invÃ¡lido.";
            elseif($_GET['error'] == 'cliente_nao_encontrado') echo "Cliente nÃ£o encontrado.";
            elseif($_GET['error'] == 'cliente_tem_vendas') echo "NÃ£o Ã© possÃ­vel excluir este cliente pois ele possui vendas registradas.";
            elseif($_GET['error'] == 'cliente_tem_receitas') echo "NÃ£o Ã© possÃ­vel excluir este cliente pois ele possui receitas registradas.";
            elseif($_GET['error'] == 'cliente_tem_ordens') echo "NÃ£o Ã© possÃ­vel excluir este cliente pois ele possui ordens de serviÃ§o registradas.";
            elseif($_GET['error'] == 'erro_exclusao') echo "Erro ao excluir o cliente.";
            else echo "Ocorreu um erro no sistema. Tente novamente.";
        ?>
    </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-4">
        <h2 class="card-title">
            <i class="fas fa-users"></i>
            Lista de Clientes
        </h2>
        <a href="novo.php" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Novo Cliente
        </a>
    </div>

    <div class="mb-4">
        <input type="text" id="busca" class="input" placeholder="Buscar cliente por nome..." onkeyup="filtrarTabela()">
    </div>
    
    <?php if (empty($clientes)): ?>
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <h3>Nenhum cliente cadastrado</h3>
        <p>Comece adicionando seu primeiro cliente.</p>
        <a href="novo.php" class="btn btn-primary mt-4">
            <i class="fas fa-plus"></i>
            Cadastrar Cliente
        </a>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th>Criado em</th>
                    <th>AÃ§Ãµes</th>
                </tr>
            </thead>
            <tbody id="tabela-clientes">
                <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo $cliente['id']; ?></td>
                    <td class="font-semibold"><?php echo htmlspecialchars($cliente['nome']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['cpf'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($cliente['telefone'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($cliente['email'] ?? '-'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($cliente['criado_em'])); ?></td>
                    <td>
                        <div class="actions">
                            <a href="visualizar.php?id=<?php echo $cliente['id']; ?>" class="btn-icon" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="editar.php?id=<?php echo $cliente['id']; ?>" class="btn-icon" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="excluir.php?id=<?php echo $cliente['id']; ?>" class="btn-icon danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este cliente?');">
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

