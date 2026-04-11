<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Produtos';
$moduleName = 'Gerencie seus produtos';

try {
    $stmt = $db->query("SELECT * FROM produtos ORDER BY nome");
    $produtos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro: " . $e->getMessage());
    $produtos = [];
}

include '../layout_base.php';
?>

<div class="card">
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success mb-4" style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 10px; border: 1px solid #bbf7d0;">
        <i class="fas fa-check-circle mr-2"></i>
        <?php 
            if($_GET['success'] == 'excluido') echo "Produto excluído com sucesso!";
            else echo "Operação realizada com sucesso!";
        ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error mb-4" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 10px; border: 1px solid #fecaca;">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?php 
            if($_GET['error'] == 'id_invalido') echo "ID de produto inválido.";
            elseif($_GET['error'] == 'produto_nao_encontrado') echo "Produto não encontrado.";
            elseif($_GET['error'] == 'produto_tem_vendas') echo "Não é possível excluir este produto pois ele possui vendas registradas.";
            elseif($_GET['error'] == 'produto_tem_movimentacoes') echo "Não é possível excluir este produto pois ele possui movimentações de estoque registradas.";
            elseif($_GET['error'] == 'erro_exclusao') echo "Erro ao excluir o produto.";
            else echo "Ocorreu um erro no sistema. Tente novamente.";
        ?>
    </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-4">
        <h2 class="card-title">
            <i class="fas fa-box"></i>
            Lista de Produtos
        </h2>
        <a href="novo.php" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Novo Produto
        </a>
    </div>
    
    <?php if (empty($produtos)): ?>
    <div class="empty-state">
        <i class="fas fa-box"></i>
        <h3>Nenhum produto cadastrado</h3>
        <p>Comece adicionando seu primeiro produto.</p>
        <a href="novo.php" class="btn btn-primary mt-4">
            <i class="fas fa-plus"></i>
            Cadastrar Produto
        </a>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Marca</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?php echo $produto['id']; ?></td>
                    <td class="font-semibold"><?php echo htmlspecialchars($produto['nome']); ?></td>
                    <td><?php echo htmlspecialchars($produto['tipo'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($produto['marca'] ?? '-'); ?></td>
                    <td>R$ <?php echo number_format($produto['preco'] ?? 0, 2, ',', '.'); ?></td>
                    <td>
                        <?php if (($produto['estoque'] ?? 0) > 0): ?>
                        <span class="badge badge-success"><?php echo $produto['estoque']; ?> un</span>
                        <?php else: ?>
                        <span class="badge badge-danger">Sem estoque</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="visualizar.php?id=<?php echo $produto['id']; ?>" class="btn-icon" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="novo.php?id=<?php echo $produto['id']; ?>" class="btn-icon" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="excluir.php?id=<?php echo $produto['id']; ?>" class="btn-icon danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?');">
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
