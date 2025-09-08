/**
 * Sistema de Notificações Toast
 */

class NotificationSystem {
    constructor() {
        this.createNotificationContainer();
    }

    createNotificationContainer() {
        // Criar container para notificações se não existir
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
        }
    }

    show(message, type = 'success', duration = 5000) {
        const container = document.getElementById('notification-container');
        
        // Criar elemento da notificação
        const notification = document.createElement('div');
        notification.className = `notification transform transition-all duration-300 ease-in-out translate-x-full opacity-0`;
        
        // Definir cores baseadas no tipo
        let bgColor, textColor, icon, iconColor;
        
        switch (type) {
            case 'success':
                bgColor = 'bg-green-500';
                textColor = 'text-white';
                icon = 'fas fa-check-circle';
                iconColor = 'text-green-100';
                break;
            case 'error':
                bgColor = 'bg-red-500';
                textColor = 'text-white';
                icon = 'fas fa-exclamation-circle';
                iconColor = 'text-red-100';
                break;
            case 'warning':
                bgColor = 'bg-yellow-500';
                textColor = 'text-white';
                icon = 'fas fa-exclamation-triangle';
                iconColor = 'text-yellow-100';
                break;
            case 'info':
                bgColor = 'bg-blue-500';
                textColor = 'text-white';
                icon = 'fas fa-info-circle';
                iconColor = 'text-blue-100';
                break;
            default:
                bgColor = 'bg-gray-500';
                textColor = 'text-white';
                icon = 'fas fa-info-circle';
                iconColor = 'text-gray-100';
        }
        
        notification.innerHTML = `
            <div class="${bgColor} ${textColor} px-6 py-4 rounded-lg shadow-lg max-w-sm">
                <div class="flex items-center">
                    <i class="${icon} ${iconColor} mr-3 text-lg"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Adicionar ao container
        container.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
            notification.classList.add('translate-x-0', 'opacity-100');
        }, 100);
        
        // Auto-remover após duração
        setTimeout(() => {
            this.hide(notification);
        }, duration);
        
        return notification;
    }

    hide(notification) {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }

    success(message, duration = 5000) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration = 5000) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration = 5000) {
        return this.show(message, 'info', duration);
    }
}

// Instanciar sistema de notificações
const notifications = new NotificationSystem();

// Função global para mostrar notificações
function showNotification(message, type = 'success', duration = 5000) {
    return notifications.show(message, type, duration);
}

// Função para mostrar notificação de sucesso
function showSuccess(message, duration = 5000) {
    return notifications.success(message, duration);
}

// Função para mostrar notificação de erro
function showError(message, duration = 5000) {
    return notifications.error(message, duration);
}

// Função para mostrar notificação de aviso
function showWarning(message, duration = 5000) {
    return notifications.warning(message, duration);
}

// Função para mostrar notificação de informação
function showInfo(message, duration = 5000) {
    return notifications.info(message, duration);
}

// Verificar se há mensagens de sucesso na URL e mostrar notificação
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('success')) {
        const successType = urlParams.get('success');
        let message = '';
        
        switch (successType) {
            case '1':
                message = 'Operação realizada com sucesso!';
                break;
            case 'cliente_criado':
                message = 'Cliente criado com sucesso!';
                break;
            case 'receita_criada':
                message = 'Receita criada com sucesso!';
                break;
            case 'funcionario_criado':
                message = 'Funcionário criado com sucesso!';
                break;
            default:
                message = 'Operação realizada com sucesso!';
        }
        
        showSuccess(message);
        
        // Limpar parâmetro da URL
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('success');
        window.history.replaceState({}, document.title, newUrl.pathname + newUrl.search);
    }
    
    if (urlParams.has('error')) {
        const errorType = urlParams.get('error');
        let message = '';
        
        switch (errorType) {
            case 'access_denied':
                message = 'Acesso negado. Você não tem permissão para acessar esta área.';
                break;
            case 'financeiro_restrito':
                message = 'Acesso restrito. Apenas o dono pode acessar o financeiro.';
                break;
            default:
                message = 'Ocorreu um erro. Tente novamente.';
        }
        
        showError(message);
        
        // Limpar parâmetro da URL
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('error');
        window.history.replaceState({}, document.title, newUrl.pathname + newUrl.search);
    }
}); 