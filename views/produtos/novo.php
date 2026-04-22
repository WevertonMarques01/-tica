<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance()->getConnection();

// Determinar se é criação ou edição
$id = $_GET['id'] ?? null;
$editing = false;
$produto = [
    'nome' => '',
    'descricao' => '',
    'tipo' => '',
    'marca' => '',
    'modelo' => '',
    'cor' => '',
    'estoque' => 0,
    'preco' => 0
];

if ($id) {
    $editing = true;
    $pageTitle = 'Editar Produto';
    $moduleName = 'Atualize os dados do produto';
    try {
        $stmt = $db->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        $produto = $stmt->fetch();
        if (!$produto) {
            header('Location: index.php?error=produto_nao_encontrado');
            exit;
        }
    } catch (PDOException $e) {
        error_log("Erro ao buscar produto: " . $e->getMessage());
        header('Location: index.php?error=erro_sistema');
        exit;
    }
} else {
    $pageTitle = 'Novo Produto';
    $moduleName = 'Cadastre um novo produto';
}

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
            if ($editing) {
                $stmt = $db->prepare("UPDATE produtos SET nome = ?, descricao = ?, tipo = ?, marca = ?, modelo = ?, cor = ?, estoque = ?, preco = ? WHERE id = ?");
                $stmt->execute([$nome, $descricao, $tipo, $marca, $modelo, $cor, $estoque, $preco, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO produtos (nome, descricao, tipo, marca, modelo, cor, estoque, preco) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nome, $descricao, $tipo, $marca, $modelo, $cor, $estoque, $preco]);
            }
            $success = true;
        } catch (PDOException $e) {
            $errors['geral'] = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../layout_base.php';
?>

<div class="card">
    <form method="POST" class="space-y-6">
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            Produto <?php echo $editing ? 'atualizado' : 'cadastrado'; ?> com sucesso!
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
            <input type="text" name="nome" id="nome" required class="form-input" value="<?php echo htmlspecialchars($produto['nome'] ?? ($_POST['nome'] ?? '')); ?>" placeholder="Digite o nome do produto">
        </div>

        <div class="form-group">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" rows="3" class="form-input" placeholder="Descrição detalhada do produto"><?php echo htmlspecialchars($produto['descricao'] ?? ($_POST['descricao'] ?? '')); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" name="tipo" id="tipo" class="form-input" value="<?php echo htmlspecialchars($produto['tipo'] ?? ($_POST['tipo'] ?? '')); ?>" placeholder="Ex: Óculos de Grau">
            </div>
            <div class="form-group">
                <label for="marca" class="form-label">Marca</label>
                <input type="text" name="marca" id="marca" class="form-input" value="<?php echo htmlspecialchars($produto['marca'] ?? ($_POST['marca'] ?? '')); ?>" placeholder="Ex: Ray-Ban">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="modelo" class="form-label">Modelo</label>
                <input type="text" name="modelo" id="modelo" class="form-input" value="<?php echo htmlspecialchars($produto['modelo'] ?? ($_POST['modelo'] ?? '')); ?>" placeholder="Ex: Aviador">
            </div>
            <div class="form-group">
                <label for="cor" class="form-label">Cor</label>
                <input type="text" name="cor" id="cor" class="form-input" value="<?php echo htmlspecialchars($produto['cor'] ?? ($_POST['cor'] ?? '')); ?>" placeholder="Ex: Preto">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="estoque" class="form-label">Quantidade em Estoque</label>
                <input type="number" name="estoque" id="estoque" class="form-input" value="<?php echo htmlspecialchars($produto['estoque'] ?? ($_POST['estoque'] ?? '0')); ?>" min="0">
            </div>
            <div class="form-group">
                <label for="preco" class="form-label">Preço de Venda *</label>
                <input type="number" name="preco" id="preco" class="form-input" value="<?php echo htmlspecialchars($produto['preco'] ?? ($_POST['preco'] ?? '')); ?>" step="0.01" min="0" required placeholder="0,00">
            </div>
        </div>

        <?php if ($editing): ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php endif; ?>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                <?php echo $editing ? 'Atualizar' : 'Salvar'; ?> Produto
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../layout_end.php'; ?>
