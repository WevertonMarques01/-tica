<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$db = Database::getInstance()->getConnection();

$pageTitle = 'Agendamentos Concluídos';
$moduleName = 'Histórico de consultas';

$stmt = $db->query("SELECT a.*, c.nome as cliente_nome, c.telefone as cliente_telefone 
                    FROM agendamentos a 
                    LEFT JOIN clientes c ON a.cliente_id = c.id 
                    WHERE a.status = 'concluido'
                    ORDER BY a.data_consulta DESC, a.hora_consulta DESC");
$agendamentos = $stmt->fetchAll();

include '../layout_base.php';
?>

<style>
    .agendamento-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-left: 4px solid #10b981;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .agendamento-info {
        flex: 1;
    }
    
    .agendamento-info h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }
    
    .agendamento-info p {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }
    
    .agendamento-data {
        text-align: right;
    }
    
    .agendamento-data .data {
        font-weight: 600;
        color: #0f172a;
    }
    
    .agendamento-data .hora {
        font-size: 0.875rem;
        color: #64748b;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
    }
</style>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="card-title">
            <i class="fas fa-check-circle"></i>
            Agendamentos Concluídos
        </h2>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Voltar ao Calendário
        </a>
    </div>
    
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        Agendamento marcado como concluído!
    </div>
    <?php endif; ?>
    
    <?php if (empty($agendamentos)): ?>
    <div class="empty-state">
        <i class="fas fa-calendar-check"></i>
        <h3>Nenhum agendamento concluído</h3>
        <p>Os agendamentos concluídos aparecerão aqui.</p>
    </div>
    <?php else: ?>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Telefone</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agendamentos as $ag): ?>
                <tr>
                    <td class="font-semibold"><?php echo htmlspecialchars($ag['cliente_nome']); ?></td>
                    <td><?php echo htmlspecialchars($ag['cliente_telefone'] ?? '-'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($ag['data_consulta'])); ?></td>
                    <td><?php echo date('H:i', strtotime($ag['hora_consulta'])); ?></td>
                    <td><?php echo htmlspecialchars($ag['tipo_consulta'] ?? 'Consulta'); ?></td>
                    <td>
                        <div class="actions">
                            <button onclick="showAppointmentDetails(<?php echo $ag['id']; ?>)" class="btn-icon" title="Ver detalhes">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="excluir.php?id=<?php echo $ag['id']; ?>" class="btn-icon danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este agendamento?');">
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

<div id="appointmentModal" class="modal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a;">
                <i class="fas fa-calendar-check"></i> Detalhes do Agendamento
            </h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="appointmentDetails"></div>
    </div>
</div>

<script>
function showAppointmentDetails(id) {
    fetch('get_agendamento.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert(data.erro);
                return;
            }
            
            const detalhes = document.getElementById('appointmentDetails');
            
            let html = `
            <div class="agendamento-card" style="border-left-color: #10b981;">
                <div class="agendamento-info">
                    <h4>${data.cliente_nome}</h4>
                    <p><i class="fas fa-phone"></i> ${data.cliente_telefone || 'Não informado'}</p>
                </div>
                <div class="agendamento-data">
                    <div class="data">${data.data_consulta}</div>
                    <div class="hora">${data.hora_consulta.substring(0, 5)}</div>
                </div>
            </div>
            
            <div style="margin-top: 1rem;">
                <p><strong>Tipo:</strong> ${data.tipo_consulta || 'Consulta'}</p>
                ${data.observacoes ? '<p><strong>Observações:</strong> ' + data.observacoes + '</p>' : ''}
                <p><strong>Status:</strong> <span class="badge badge-success">Concluído</span></p>
            </div>
            
            <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
                <a href="excluir.php?id=${data.id}" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este agendamento?')">
                    <i class="fas fa-trash"></i> Excluir
                </a>
            </div>`;
            
            detalhes.innerHTML = html;
            document.getElementById('appointmentModal').classList.add('active');
        });
}

function closeModal() {
    document.getElementById('appointmentModal').classList.remove('active');
}

document.getElementById('appointmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php include '../layout_end.php'; ?> 