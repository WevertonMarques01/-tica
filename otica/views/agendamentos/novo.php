<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../config/database_compatibility.php';

$db = Database::getInstance()->getConnection();
$pageTitle = 'Novo Agendamento';
$moduleName = 'Agende uma nova consulta';

$stmt = $db->query("SELECT id, nome, telefone FROM clientes ORDER BY nome");
$clientes = $stmt->fetchAll();

$tiposConsulta = ['Avaliação', 'Retorno', 'Adaptação', 'Revisão', 'Emergência', 'Exame de Vista'];

$horarios = [];
for ($h = 8; $h <= 18; $h++) {
    $horarios[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
    $horarios[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':30';
}

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'] ?? '';
    $data_consulta = $_POST['data_consulta'] ?? '';
    $hora_consulta = $_POST['hora_consulta'] ?? '';
    $tipo_consulta = $_POST['tipo_consulta'] ?? '';
    $observacoes = $_POST['observacoes'] ?? '';
    
    if (empty($cliente_id) || empty($data_consulta) || empty($hora_consulta)) {
        $erro = 'Preencha todos os campos obrigatórios';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO agendamentos (cliente_id, data_consulta, hora_consulta, tipo_consulta, observacoes) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$cliente_id, $data_consulta, $hora_consulta, $tipo_consulta, $observacoes]);
            
            header('Location: index.php?success=1');
            exit;
        } catch (PDOException $e) {
            $erro = 'Erro ao criar agendamento: ' . $e->getMessage();
        }
    }
}

include '../layout_base.php';
?>

<style>
    .form-container {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .cliente-suggestion {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        max-height: 300px;
        overflow-y: auto;
        z-index: 100;
        display: none;
    }
    
    .cliente-suggestion.active {
        display: block;
    }
    
    .cliente-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.2s;
    }
    
    .cliente-item:hover {
        background: #f8fafc;
    }
    
    .cliente-item:last-child {
        border-bottom: none;
    }
    
    .cliente-item strong {
        color: #0f172a;
    }
    
    .cliente-item small {
        color: #64748b;
    }
</style>

<div class="form-container">
    <div class="card">
        <h2 class="card-title">
            <i class="fas fa-calendar-plus"></i>
            Novo Agendamento
        </h2>
        
        <?php if ($erro): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($erro); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group" style="position: relative;">
                <label class="form-label">Cliente *</label>
                <div style="position: relative;">
                    <input type="text" id="buscaCliente" class="form-input" placeholder="Digite o nome do cliente..." autocomplete="off">
                    <input type="hidden" name="cliente_id" id="clienteId">
                    <div id="clienteSuggestions" class="cliente-suggestion"></div>
                </div>
                <div id="clienteSelecionado" style="margin-top: 0.5rem; padding: 0.75rem; background: #f0f9ff; border-radius: 8px; display: none;">
                    <i class="fas fa-user-check" style="color: #0ea5e9;"></i>
                    <span id="clienteNome" style="color: #0f172a; font-weight: 500;"></span>
                    <span id="clienteTelefone" style="color: #64748b; font-size: 0.875rem;"></span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Data da Consulta *</label>
                <input type="date" name="data_consulta" class="form-input" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Horário *</label>
                <select name="hora_consulta" class="form-select" required>
                    <option value="">Selecione um horário</option>
                    <?php foreach ($horarios as $horario): ?>
                    <option value="<?php echo $horario; ?>"><?php echo $horario; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tipo de Consulta</label>
                <select name="tipo_consulta" class="form-select">
                    <option value="">Selecione...</option>
                    <?php foreach ($tiposConsulta as $tipo): ?>
                    <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-input" rows="3" placeholder="Observações adicionais..."></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Agendar
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const clientes = <?php echo json_encode($clientes); ?>;
const buscaCliente = document.getElementById('buscaCliente');
const clienteSuggestions = document.getElementById('clienteSuggestions');
const clienteId = document.getElementById('clienteId');
const clienteSelecionado = document.getElementById('clienteSelecionado');
const clienteNome = document.getElementById('clienteNome');
const clienteTelefone = document.getElementById('clienteTelefone');

buscaCliente.addEventListener('input', function() {
    const termo = this.value.toLowerCase();
    
    if (termo.length < 2) {
        clienteSuggestions.classList.remove('active');
        return;
    }
    
    const filtrados = clientes.filter(c => c.nome.toLowerCase().includes(termo));
    
    if (filtrados.length === 0) {
        clienteSuggestions.innerHTML = '<div class="cliente-item"><small>Nenhum cliente encontrado</small></div>';
    } else {
        clienteSuggestions.innerHTML = filtrados.map(c => `
            <div class="cliente-item" onclick="selecionarCliente(${c.id}, '${c.nome}', '${c.telefone}')">
                <strong>${c.nome}</strong><br>
                <small>${c.telefone || 'Sem telefone'}</small>
            </div>
        `).join('');
    }
    
    clienteSuggestions.classList.add('active');
});

function selecionarCliente(id, nome, telefone) {
    clienteId.value = id;
    buscaCliente.value = nome;
    clienteNome.textContent = nome;
    clienteTelefone.textContent = telefone || '';
    clienteSelecionado.style.display = 'block';
    clienteSuggestions.classList.remove('active');
}

document.addEventListener('click', function(e) {
    if (!buscaCliente.contains(e.target) && !clienteSuggestions.contains(e.target)) {
        clienteSuggestions.classList.remove('active');
    }
});

buscaCliente.addEventListener('focus', function() {
    if (this.value.length >= 2) {
        clienteSuggestions.classList.add('active');
    }
});
</script>

<?php include '../layout_end.php'; ?> 