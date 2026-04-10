<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Novo Produto';
$moduleName = 'Cadastre um novo produto';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $cor = trim($_POST['cor'] ?? '');
    $estoque = (int)($_POST['estoque'] ?? 0);
    $preco = (float)($_POST['preco'] ?? 0);
    
    if (empty($nome)) {
        $errors['geral'] = 'Nome do produto é obrigatório';
    } elseif ($preco <= 0) {
        $errors['geral'] = 'Preço deve ser maior que zero';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO produtos (nome, descricao, tipo, marca, modelo, cor, estoque, preco) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $descricao, $tipo, $marca, $modelo, $cor, $estoque, $preco]);
            $success = true;
        } catch (PDOException $e) {
            $errors['geral'] = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

include '../layout_base.php';
?>

<div class="card">
    <form method="POST" class="space-y-6">
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            Produto cadastrado com sucesso!
        </div>
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i>
            Voltar para Lista
        </a>
        <?php endif; ?>

        <?php if (!empty($errors['geral'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <?php echo htmlspecialchars($errors['geral']); ?>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="nome" class="form-label">Nome do Produto *</label>
            <input type="text" name="nome" id="nome" required class="form-input" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" placeholder="Digite o nome do produto">
        </div>

        <div class="form-group">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3" class="form-input" placeholder="Descrição detalhada do produto"><?php echo htmlspecialchars($_POST['descricao'] ?? ''); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" name="tipo" id="tipo" class="form-input" value="<?php echo htmlspecialchars($_POST['tipo'] ?? ''); ?>" placeholder="Ex: Óculos de Grau">
            </div>
            <div class="form-group">
                <label for="marca" class="form-label">Marca</label>
                <input type="text" name="marca" id="marca" class="form-input" value="<?php echo htmlspecialchars($_POST['marca'] ?? ''); ?>" placeholder="Ex: Ray-Ban">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="modelo" class="form-label">Modelo</label>
                <input type="text" name="modelo" id="modelo" class="form-input" value="<?php echo htmlspecialchars($_POST['modelo'] ?? ''); ?>" placeholder="Ex: Aviador">
            </div>
            <div class="form-group">
                <label for="cor" class="form-label">Cor</label>
                <input type="text" name="cor" id="cor" class="form-input" value="<?php echo htmlspecialchars($_POST['cor'] ?? ''); ?>" placeholder="Ex: Preto">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="estoque" class="form-label">Quantidade em Estoque</label>
                <input type="number" name="estoque" id="estoque" class="form-input" value="<?php echo htmlspecialchars($_POST['estoque'] ?? '0'); ?>" min="0">
            </div>
            <div class="form-group">
                <label for="preco" class="form-label">Preço de Venda *</label>
                <input type="number" name="preco" id="preco" class="form-input" value="<?php echo htmlspecialchars($_POST['preco'] ?? ''); ?>" step="0.01" min="0" required placeholder="0,00">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Salvar Produto
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </form>
</div>

<?php include '../layout_end.php'; ?>
