<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../config/database_compatibility.php';

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

include '../layout_base.php';
?>

<div class="card">
<<<<<<< Updated upstream
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success mb-4" style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 10px; border: 1px solid #bbf7d0;">
        <i class="fas fa-check-circle mr-2"></i>
        <?php 
            if($_GET['success'] == 'excluido') echo "Cliente excluído com sucesso!";
            else echo "Operação realizada com sucesso!";
        ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error mb-4" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 10px; border: 1px solid #fecaca;">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?php 
            if($_GET['error'] == 'id_invalido') echo "ID de cliente inválido.";
            elseif($_GET['error'] == 'cliente_nao_encontrado') echo "Cliente não encontrado.";
            elseif($_GET['error'] == 'cliente_tem_vendas') echo "Não é possível excluir este cliente pois ele possui vendas registradas.";
            elseif($_GET['error'] == 'cliente_tem_receitas') echo "Não é possível excluir este cliente pois ele possui receitas registradas.";
            elseif($_GET['error'] == 'cliente_tem_ordens') echo "Não é possível excluir este cliente pois ele possui ordens de serviço registradas.";
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
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
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

=======
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
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
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

>>>>>>> Stashed changes
<?php include '../layout_end.php'; ?>
