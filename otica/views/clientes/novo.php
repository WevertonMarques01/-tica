<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Novo Cliente';
$moduleName = 'Cadastre um novo cliente';

$success = false;
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['documento'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $numero = trim($_POST['numero'] ?? '');

    if (empty($nome) || empty($cpf)) {
        $erro = 'Nome e CPF são obrigatórios.';
    } else {
        try {
            $stmt = $db->prepare("SELECT id FROM clientes WHERE cpf = ?");
            $stmt->execute([$cpf]);
            if ($stmt->fetch()) {
                $erro = 'Este CPF já está cadastrado.';
            } else {
                $stmt = $db->prepare("INSERT INTO clientes (nome, cpf, email, telefone, endereco, bairro, numero) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nome, $cpf, $email, $telefone, $endereco, $bairro, $numero]);
                $success = true;
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar: ' . $e->getMessage();
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
            Cliente cadastrado com sucesso!
        </div>
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i>
            Voltar para Lista
        </a>
        <?php endif; ?>

        <?php if ($erro): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <?php echo htmlspecialchars($erro); ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="nome" class="form-label">Nome Completo *</label>
                <input type="text" name="nome" id="nome" required class="form-input" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" placeholder="Digite o nome completo">
            </div>
            <div class="form-group">
                <label for="documento" class="form-label">CPF *</label>
                <input type="text" name="documento" id="documento" required class="form-input" value="<?php echo htmlspecialchars($_POST['documento'] ?? ''); ?>" placeholder="000.000.000-00">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-input" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="email@exemplo.com">
            </div>
            <div class="form-group">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" name="telefone" id="telefone" class="form-input" value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>" placeholder="(00) 00000-0000">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" name="endereco" id="endereco" class="form-input" value="<?php echo htmlspecialchars($_POST['endereco'] ?? ''); ?>" placeholder="Rua, Avenida...">
            </div>
            <div class="form-group">
                <label for="bairro" class="form-label">Bairro</label>
                <input type="text" name="bairro" id="bairro" class="form-input" value="<?php echo htmlspecialchars($_POST['bairro'] ?? ''); ?>" placeholder="Bairro">
            </div>
            <div class="form-group">
                <label for="numero" class="form-label">Número</label>
                <input type="text" name="numero" id="numero" class="form-input" value="<?php echo htmlspecialchars($_POST['numero'] ?? ''); ?>" placeholder="Nº">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Salvar Cliente
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </form>
</div>

<?php include '../layout_end.php'; ?>
