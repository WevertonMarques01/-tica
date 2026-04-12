<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../config/database_compatibility.php';
require_once '../../models/AgendamentoModel.php';

$db = Database::getInstance()->getConnection();
$agendamentoModel = new AgendamentoModel();

$pageTitle = 'Agendamentos';
$moduleName = 'Gerencie suas consultas';

$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');

if ($mes < 1) { $mes = 12; $ano--; }
if ($mes > 12) { $mes = 1; $ano++; }

$dataInicio = date('Y-m-01', strtotime("{$ano}-{$mes}-01"));
$dataFim = date('Y-m-t', strtotime("{$ano}-{$mes}-01"));

$agendamentos = $agendamentoModel->getAllByDateRange($dataInicio, $dataFim);

$agendamentosPorDia = [];
foreach ($agendamentos as $ag) {
    $dia = date('j', strtotime($ag['data_consulta']));
    if (!isset($agendamentosPorDia[$dia])) {
        $agendamentosPorDia[$dia] = [];
    }
    $agendamentosPorDia[$dia][] = $ag;
}

$nomeMes = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

$primeiroDia = date('w', strtotime($dataInicio));
$diasNoMes = date('t', strtotime($dataInicio));

$stmtCli = $db->query("SELECT id, nome, telefone FROM clientes ORDER BY nome");
$clientes = $stmtCli->fetchAll();

$tiposConsulta = ['Avaliação', 'Retorno', 'Adaptação', 'Revisão', 'Emergência'];

include '../layout_base.php';
?>

<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .calendar-nav {
        display: flex;
        gap: 0.5rem;
    }
    
    .calendar-nav a {
        padding: 0.5rem 1rem;
        background: #f1f5f9;
        color: #0f172a;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .calendar-nav a:hover {
        background: #0ea5e9;
        color: white;
    }
    
    .calendar-day-header {
        text-align: center;
        font-weight: 600;
        color: #64748b;
        padding: 0.75rem;
        font-size: 0.875rem;
    }
    
    .calendar-day {
        min-height: 100px;
        background: #f8fafc;
        border-radius: 10px;
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }
    
    .calendar-day:hover {
        border-color: #0ea5e9;
    }
    
    .calendar-day.empty {
        background: transparent;
        border: 1px dashed #e2e8f0;
    }
    
    .day-number {
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .day-today .day-number {
        background: #0ea5e9;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .appointment-item {
        background: #dbeafe;
        color: #1e40af;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .appointment-item.completed {
        background: #d1fae5;
        color: #065f46;
    }
    
    .appointment-item:hover {
        background: #0ea5e9;
        color: white;
    }
    
    .agendamento-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-left: 4px solid #0ea5e9;
    }
    
    .agendamento-card.concluido {
        border-left-color: #10b981;
    }
    
    .agendamento-card.cancelado {
        border-left-color: #ef4444;
    }
    
    .btn-whatsapp {
        background: #25d366;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn-whatsapp:hover {
        background: #128c7e;
        transform: translateY(-2px);
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
        max-height: 80vh;
        overflow-y: auto;
    }
</style>

<div class="card">
    <div class="calendar-header">
        <h2 class="card-title">
            <i class="fas fa-calendar-alt"></i>
            Calendário de Agendamentos
        </h2>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <span style="font-size: 1.25rem; font-weight: 600; color: #0f172a;">
                <?php echo $nomeMes[$mes] . ' ' . $ano; ?>
            </span>
            <div class="calendar-nav">
                <a href="index.php?mes=<?php echo $mes-1; ?>&ano=<?php echo $ano; ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="index.php?mes=<?php echo $mes+1; ?>&ano=<?php echo $ano; ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            <a href="novo.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Novo Agendamento
            </a>
        </div>
    </div>
    
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        Operação realizada com sucesso!
    </div>
    <?php endif; ?>
    
    <div class="calendar-grid">
        <div class="calendar-day-header">Dom</div>
        <div class="calendar-day-header">Seg</div>
        <div class="calendar-day-header">Ter</div>
        <div class="calendar-day-header">Qua</div>
        <div class="calendar-day-header">Qui</div>
        <div class="calendar-day-header">Sex</div>
        <div class="calendar-day-header">Sáb</div>
        
        <?php for ($i = 0; $i < $primeiroDia; $i++): ?>
        <div class="calendar-day empty"></div>
        <?php endfor; ?>
        
        <?php for ($dia = 1; $dia <= $diasNoMes; $dia++): ?>
        <?php 
            $dataAtual = date('Y-m-d', strtotime("{$ano}-{$mes}-{$dia}"));
            $isToday = $dataAtual === date('Y-m-d');
            $agendamentosDia = $agendamentosPorDia[$dia] ?? [];
        ?>
        <div class="calendar-day <?php echo $isToday ? 'day-today' : ''; ?>" onclick="showDayAppointments(<?php echo $dia; ?>, '<?php echo $dataAtual; ?>')">
            <div class="day-number"><?php echo $dia; ?></div>
            <?php foreach (array_slice($agendamentosDia, 0, 3) as $ag): ?>
            <div class="appointment-item <?php echo $ag['status']; ?>" onclick="event.stopPropagation(); showAppointmentDetails(<?php echo $ag['id']; ?>)">
                <?php echo date('H:i', strtotime($ag['hora_consulta'])) . ' - ' . $ag['cliente_nome']; ?>
            </div>
            <?php endforeach; ?>
            <?php if (count($agendamentosDia) > 3): ?>
            <div class="appointment-item" style="background: #f1f5f9; color: #64748b;">
                +<?php echo count($agendamentosDia) - 3; ?> mais
            </div>
            <?php endif; ?>
        </div>
        <?php endfor; ?>
    </div>
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
function showDayAppointments(dia, data) {
    const detalhes = document.getElementById('appointmentDetails');
    const agendamentos = <?php echo json_encode($agendamentosPorDia); ?>;
    
    let html = '<p style="color: #64748b; margin-bottom: 1rem;">Clique em um agendamento para ver os detalhes.</p>';
    
    if (agendamentos[dia] && agendamentos[dia].length > 0) {
        agendamentos[dia].forEach(function(ag) {
            html += `
            <div class="agendamento-card ${ag.status}">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <strong>${ag.cliente_nome}</strong><br>
                        <small style="color: #64748b;">
                            <i class="fas fa-clock"></i> ${ag.hora_consulta.substring(0, 5)}
                        </small><br>
                        <small style="color: #64748b;">
                            <i class="fas fa-tag"></i> ${ag.tipo_consulta || 'Consulta'}
                        </small>
                    </div>
                    <span class="badge badge-${ag.status === 'agendado' ? 'info' : (ag.status === 'concluido' ? 'success' : 'danger')}">
                        ${ag.status}
                    </span>
                </div>
                ${ag.observacoes ? '<p style="margin-top: 0.5rem; font-size: 0.875rem; color: #64748b;">' + ag.observacoes + '</p>' : ''}
                <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <button onclick="showAppointmentDetails(${ag.id})" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                        <i class="fas fa-eye"></i> Ver
                    </button>
                    ${ag.status === 'agendado' ? `
                    <a href="concluir.php?id=${ag.id}" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                        <i class="fas fa-check"></i> Concluir
                    </a>
                    <button onclick="shareWhatsapp(${ag.id})" class="btn-whatsapp" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                        <i class="fab fa-whatsapp"></i> Enviar
                    </button>
                    ` : ''}
                </div>
            </div>`;
        });
    } else {
        html += '<p style="text-align: center; color: #64748b; padding: 2rem;">Nenhum agendamento neste dia.</p>';
    }
    
    detalhes.innerHTML = html;
    document.getElementById('appointmentModal').classList.add('active');
}

function showAppointmentDetails(id) {
    fetch('get_agendamento.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert(data.erro);
                return;
            }
            
            const detalhes = document.getElementById('appointmentDetails');
            const telefone = data.cliente_telefone ? data.cliente_telefone.replace(/\D/g, '') : '';
            
            let html = `
            <div class="agendamento-card ${data.status}">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h4 style="font-size: 1.125rem; font-weight: 700; color: #0f172a;">${data.cliente_nome}</h4>
                        <p style="color: #64748b; font-size: 0.875rem;">
                            <i class="fas fa-phone"></i> ${data.cliente_telefone || 'Não informado'}
                        </p>
                    </div>
                    <span class="badge badge-${data.status === 'agendado' ? 'info' : (data.status === 'concluido' ? 'success' : 'danger')}">
                        ${data.status === 'agendado' ? 'Agendado' : (data.status === 'concluido' ? 'Concluído' : 'Cancelado')}
                    </span>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <strong style="font-size: 0.875rem; color: #64748b;">Data</strong>
                        <p style="color: #0f172a;">${data.data_consulta}</p>
                    </div>
                    <div>
                        <strong style="font-size: 0.875rem; color: #64748b;">Hora</strong>
                        <p style="color: #0f172a;">${data.hora_consulta.substring(0, 5)}</p>
                    </div>
                    <div>
                        <strong style="font-size: 0.875rem; color: #64748b;">Tipo</strong>
                        <p style="color: #0f172a;">${data.tipo_consulta || 'Consulta'}</p>
                    </div>
                </div>
                
                ${data.observacoes ? `
                <div style="margin-bottom: 1rem;">
                    <strong style="font-size: 0.875rem; color: #64748b;">Observações</strong>
                    <p style="color: #0f172a;">${data.observacoes}</p>
                </div>
                ` : ''}
                
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                    ${data.status === 'agendado' ? `
                    <a href="concluir.php?id=${data.id}" class="btn btn-success">
                        <i class="fas fa-check"></i> Concluir
                    </a>
                    <a href="cancelar.php?id=${data.id}" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button onclick="shareWhatsapp(${data.id})" class="btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> Compartilhar
                    </button>
                    ` : ''}
                    ${data.status === 'concluido' ? `
                    <a href="excluir.php?id=${data.id}" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este agendamento?')">
                        <i class="fas fa-trash"></i> Excluir
                    </a>
                    ` : ''}
                </div>
            </div>`;
            
            detalhes.innerHTML = html;
            document.getElementById('appointmentModal').classList.add('active');
        });
}

function shareWhatsapp(id) {
    fetch('compartilhar_whatsapp.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert(data.erro);
                return;
            }
            window.open(data.url, '_blank');
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