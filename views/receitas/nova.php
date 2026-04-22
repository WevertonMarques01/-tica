<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Nova Receita';
$moduleName = 'Cadastre uma nova receita Ã³ptica';

$success = false;
$erro = '';

try {
    $stmt = $db->query("SELECT id, nome, cpf FROM clientes ORDER BY nome");
    $clientes = $stmt->fetchAll();
} catch (PDOException $e) {
    $erro = 'Erro ao carregar clientes: ' . $e->getMessage();
    $clientes = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? '';
    $data_receita = $_POST['data_receita'] ?? date('Y-m-d');
    $validade = $_POST['validade'] ?? '';
    
    // OD - Olho Direito
    $esfera_od = $_POST['esfera_od'] ?? '';
    $cilindro_od = $_POST['cilindro_od'] ?? '';
    $eixo_od = $_POST['eixo_od'] ?? '';
    $adicao_od = $_POST['adicao_od'] ?? '';
    
    // OE - Olho Esquerdo
    $esfera_oe = $_POST['esfera_oe'] ?? '';
    $cilindro_oe = $_POST['cilindro_oe'] ?? '';
    $eixo_oe = $_POST['eixo_oe'] ?? '';
    $adicao_oe = $_POST['adicao_oe'] ?? '';
    
    $obs = $_POST['observacoes'] ?? '';
    
    if (empty($cliente_id)) {
        $erro = 'Selecione um cliente.';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO receitas (cliente_id, esfera_od, cilindro_od, eixo_od, adicao_od, esfera_oe, cilindro_oe, eixo_oe, adicao_oe, obs, data_receita, validade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$cliente_id, $esfera_od, $cilindro_od, $eixo_od, $adicao_od, $esfera_oe, $cilindro_oe, $eixo_oe, $adicao_oe, $obs, $data_receita, $validade]);
            $success = true;
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../layout_base.php';
?>

<style>
    .receita-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
    
    .olho-section {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 12px;
    }
    
    .olho-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .olho-title.od { color: #0ea5e9; }
    .olho-title.oe { color: #8b5cf6; }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .form-group-full {
        grid-column: 1 / -1;
    }
    
    @media (max-width: 768px) {
        .receita-grid { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
    }
</style>

<div class="card">
    <form method="POST" class="space-y-6">
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            Receita cadastrada com sucesso!
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

        <!-- Dados BÃ¡sicos -->
        <div class="card" style="margin-bottom: 0;">
            <h3 class="card-title">
                <i class="fas fa-user"></i>
                Dados do Cliente
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group">
                    <label for="cliente_id" class="form-label">Cliente *</label>
                    <select name="cliente_id" id="cliente_id" class="form-select" required>
                        <option value="">Selecione</option>
                        <?php foreach ($clientes as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="data_receita" class="form-label">Data da Receita</label>
                    <input type="date" name="data_receita" id="data_receita" class="form-input" value="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="validade" class="form-label">Validade</label>
                    <input type="date" name="validade" id="validade" class="form-input">
                </div>
            </div>
        </div>

        <!-- GraduaÃ§Ãµes -->
        <div class="receita-grid">
            <!-- Olho Direito -->
            <div class="olho-section">
                <h4 class="olho-title od">
                    <i class="fas fa-eye"></i>
                    Olho Direito (OD)
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="esfera_od" class="form-label">Esfera</label>
                        <input type="text" name="esfera_od" id="esfera_od" class="form-input" placeholder="ex: -2.50">
                    </div>
                    <div class="form-group">
                        <label for="cilindro_od" class="form-label">Cilindro</label>
                        <input type="text" name="cilindro_od" id="cilindro_od" class="form-input" placeholder="ex: -0.75">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="eixo_od" class="form-label">Eixo</label>
                        <input type="text" name="eixo_od" id="eixo_od" class="form-input" placeholder="ex: 180">
                    </div>
                    <div class="form-group">
                        <label for="adicao_od" class="form-label">AdiÃ§Ã£o</label>
                        <input type="text" name="adicao_od" id="adicao_od" class="form-input" placeholder="ex: +2.50">
                    </div>
                </div>
            </div>
            
            <!-- Olho Esquerdo -->
            <div class="olho-section">
                <h4 class="olho-title oe">
                    <i class="fas fa-eye"></i>
                    Olho Esquerdo (OE)
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="esfera_oe" class="form-label">Esfera</label>
                        <input type="text" name="esfera_oe" id="esfera_oe" class="form-input" placeholder="ex: -2.50">
                    </div>
                    <div class="form-group">
                        <label for="cilindro_oe" class="form-label">Cilindro</label>
                        <input type="text" name="cilindro_oe" id="cilindro_oe" class="form-input" placeholder="ex: -0.75">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="eixo_oe" class="form-label">Eixo</label>
                        <input type="text" name="eixo_oe" id="eixo_oe" class="form-input" placeholder="ex: 180">
                    </div>
                    <div class="form-group">
                        <label for="adicao_oe" class="form-label">AdiÃ§Ã£o</label>
                        <input type="text" name="adicao_oe" id="adicao_oe" class="form-input" placeholder="ex: +2.50">
                    </div>
                </div>
            </div>
        </div>

        <!-- ObservaÃ§Ãµes -->
        <div class="form-group">
            <label for="observacoes" class="form-label">ObservaÃ§Ãµes</label>
            <textarea name="observacoes" id="observacoes" rows="3" class="form-input" placeholder="ObservaÃ§Ãµes adicionais..."></textarea>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Salvar Receita
            </button>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../layout_end.php'; ?>

