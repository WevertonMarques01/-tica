/**
 * Auto-fill client data functionality for prescription form
 * Sistema Ótica - Preenchimento automático de dados do cliente
 */

// Função principal para inicializar o auto-preenchimento
function initAutoFillClient() {
    const clienteSelect = document.getElementById('cliente_id');
    
    if (!clienteSelect) {
        console.warn('Campo cliente_id não encontrado');
        return;
    }
    
    clienteSelect.addEventListener('change', function() {
        const clienteId = this.value;
        console.log('Cliente selecionado:', clienteId);
        
        if (clienteId && clienteId !== '') {
            loadClientData(clienteId);
        } else {
            clearClientFields();
        }
    });
}

// Função para carregar dados do cliente via AJAX
function loadClientData(clienteId) {
    // Elementos dos campos a serem preenchidos
    const fields = {
        nome: document.getElementById('nome'),
        telefone: document.getElementById('telefone'),
        cpf: document.getElementById('cpf'),
        endereco: document.getElementById('endereco'),
        bairro: document.getElementById('bairro'),
        numero: document.getElementById('numero')
    };
    
    // Mostrar indicador de carregamento
    showLoadingState(fields);
    
    // Fazer requisição AJAX
    fetch(`nova.php?action=get_cliente&cliente_id=${clienteId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Resposta do servidor:', data);
            
            if (data.success && data.cliente) {
                fillClientFields(fields, data.cliente);
                showSuccessNotification('Dados do cliente carregados automaticamente!');
            } else {
                hideLoadingState(fields);
                showErrorNotification('Erro ao carregar dados do cliente: ' + (data.message || 'Cliente não encontrado'));
            }
        })
        .catch(error => {
            console.error('Erro na requisição AJAX:', error);
            hideLoadingState(fields);
            showErrorNotification('Erro de conexão ao carregar dados do cliente');
        });
}

// Função para mostrar estado de carregamento
function showLoadingState(fields) {
    if (fields.nome) {
        fields.nome.value = 'Carregando dados do cliente...';
        fields.nome.disabled = true;
        fields.nome.style.fontStyle = 'italic';
        fields.nome.style.color = '#6c757d';
    }
    
    // Limpar outros campos
    Object.keys(fields).forEach(key => {
        if (key !== 'nome' && fields[key]) {
            fields[key].value = '';
            fields[key].style.backgroundColor = '#f8f9fa';
        }
    });
}

// Função para esconder estado de carregamento
function hideLoadingState(fields) {
    if (fields.nome) {
        fields.nome.disabled = false;
        fields.nome.style.fontStyle = 'normal';
        fields.nome.style.color = '';
        fields.nome.value = '';
    }
    
    // Restaurar cor de fundo dos campos
    Object.keys(fields).forEach(key => {
        if (fields[key]) {
            fields[key].style.backgroundColor = '';
        }
    });
}

// Função para preencher os campos com dados do cliente
function fillClientFields(fields, clienteData) {
    // Mapear dados do cliente para os campos
    const fieldMapping = {
        nome: clienteData.nome || '',
        telefone: clienteData.telefone || '',
        cpf: clienteData.cpf || '',
        endereco: clienteData.endereco || '',
        bairro: clienteData.bairro || '',
        numero: clienteData.numero || ''
    };
    
    console.log('Preenchendo campos com dados:', fieldMapping);
    
    // Preencher cada campo com animação sutil
    Object.keys(fieldMapping).forEach(fieldName => {
        const field = fields[fieldName];
        if (field) {
            // Animação de preenchimento
            field.style.transition = 'all 0.3s ease';
            field.style.backgroundColor = '#d4edda';
            field.value = fieldMapping[fieldName];
            field.disabled = false;
            field.style.fontStyle = 'normal';
            field.style.color = '';
            
            // Restaurar cor normal após animação
            setTimeout(() => {
                field.style.backgroundColor = '';
            }, 1000);
        }
    });
}

// Função para limpar todos os campos do cliente
function clearClientFields() {
    const fieldIds = ['nome', 'telefone', 'cpf', 'endereco', 'bairro', 'numero'];
    
    fieldIds.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.value = '';
            field.style.backgroundColor = '';
            field.disabled = false;
            field.style.fontStyle = 'normal';
            field.style.color = '';
        }
    });
}

// Função para mostrar notificação de sucesso
function showSuccessNotification(message) {
    showNotification(message, 'success');
}

// Função para mostrar notificação de erro
function showErrorNotification(message) {
    showNotification(message, 'error');
}

// Função genérica para mostrar notificações
function showNotification(message, type = 'info') {
    // Remover notificação existente
    const existingNotification = document.querySelector('.auto-fill-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = 'auto-fill-notification';
    
    // Definir estilos baseados no tipo
    const styles = {
        success: {
            background: 'linear-gradient(135deg, #28a745 0%, #20c997 100%)',
            color: 'white',
            icon: 'fas fa-check-circle'
        },
        error: {
            background: 'linear-gradient(135deg, #dc3545 0%, #fd7e14 100%)',
            color: 'white',
            icon: 'fas fa-exclamation-circle'
        },
        info: {
            background: 'linear-gradient(135deg, #007bff 0%, #6f42c1 100%)',
            color: 'white',
            icon: 'fas fa-info-circle'
        }
    };
    
    const style = styles[type] || styles.info;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${style.background};
        color: ${style.color};
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        z-index: 9999;
        font-weight: 500;
        max-width: 350px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        line-height: 1.4;
        transform: translateX(100%);
        transition: transform 0.3s ease-out;
    `;
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center;">
            <i class="${style.icon}" style="margin-right: 10px; font-size: 16px;"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Adicionar ao DOM
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remover após 4 segundos
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Função para debug - verificar se os campos existem
function debugFields() {
    const fieldIds = ['cliente_id', 'nome', 'telefone', 'cpf', 'endereco', 'bairro', 'numero'];
    const report = {};
    
    fieldIds.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        report[fieldId] = field ? 'Encontrado' : 'NÃO ENCONTRADO';
    });
    
    console.log('Estado dos campos na página:', report);
    return report;
}

// Inicializar quando o DOM estiver carregado
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Inicializando auto-preenchimento de cliente...');
        debugFields();
        initAutoFillClient();
    });
} else {
    // DOM já carregado
    console.log('DOM já carregado, inicializando auto-preenchimento...');
    debugFields();
    initAutoFillClient();
}