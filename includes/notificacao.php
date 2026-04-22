<?php
/**
 * Sistema de Notificações (Toast Alerts)
 *Wiz Óptica
 */

class Notificacao {
    const SUCESSO = 'sucesso';
    const ERRO = 'erro';
    const AVISO = 'aviso';
    const INFO = 'info';

    public static function set($mensagem, $tipo = self::INFO) {
        if (!isset($_SESSION['notificacoes'])) {
            $_SESSION['notificacoes'] = [];
        }
        $_SESSION['notificacoes'][] = [
            'mensagem' => $mensagem,
            'tipo' => $tipo,
            'timestamp' => time()
        ];
    }

    public static function get() {
        $notificacoes = $_SESSION['notificacoes'] ?? [];
        $_SESSION['notificacoes'] = [];
        return $notificacoes;
    }

    public static function has() {
        return !empty($_SESSION['notificacoes']);
    }

    public static function sucesso($mensagem) {
        self::set($mensagem, self::SUCESSO);
    }

    public static function erro($mensagem) {
        self::set($mensagem, self::ERRO);
    }

    public static function aviso($mensagem) {
        self::set($mensagem, self::AVISO);
    }

    public static function info($mensagem) {
        self::set($mensagem, self::INFO);
    }
}

function exibirNotificacoes() {
    $notificacoes = Notificacao::get();
    
    if (empty($notificacoes)) return;

    foreach ($notificacoes as $n) {
        $tipo = $n['tipo'];
        $mensagem = htmlspecialchars($n['mensagem'], ENT_QUOTES, 'UTF-8');
        
        $icon = '';
        $bgClass = '';
        
        switch($tipo) {
            case 'sucesso':
                $icon = '<i class="fas fa-check-circle"></i>';
                $bgClass = 'bg-emerald-500';
                break;
            case 'erro':
                $icon = '<i class="fas fa-exclamation-circle"></i>';
                $bgClass = 'bg-red-500';
                break;
            case 'aviso':
                $icon = '<i class="fas fa-exclamation-triangle"></i>';
                $bgClass = 'bg-amber-500';
                break;
            default:
                $icon = '<i class="fas fa-info-circle"></i>';
                $bgClass = 'bg-blue-500';
        }
        
        echo "<div class='toast-notificacao {$tipo}' data-tipo='{$tipo}'>";
        echo "  <div class='toast-icon {$bgClass}'>{$icon}</div>";
        echo "  <div class='toast-mensagem'>{$mensagem}</div>";
        echo "  <button class='toast-fechar' onclick='this.parentElement.remove()'><i class='fas fa-times'></i></button>";
        echo "</div>";
    }
}
?>
<style>
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 400px;
}

.toast-notificacao {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    animation: slideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    font-family: 'Nunito', sans-serif;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.toast-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 18px;
    flex-shrink: 0;
}

.toast-mensagem {
    flex: 1;
    font-size: 14px;
    color: #1e293b;
    font-weight: 500;
    line-height: 1.4;
}

.toast-fechar {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    font-size: 14px;
    transition: color 0.2s;
}

.toast-fechar:hover {
    color: #475569;
}

.toast-notificacao.erro {
    border-left: 4px solid #ef4444;
}

.toast-notificacao.sucesso {
    border-left: 4px solid #10b981;
}

.toast-notificacao.aviso {
    border-left: 4px solid #f59e0b;
}

.toast-notificacao.info {
    border-left: 4px solid #3b82f6;
}

@media (max-width: 480px) {
    .toast-container {
        left: 10px;
        right: 10px;
        max-width: none;
    }
    
    .toast-notificacao {
        padding: 12px 16px;
    }
}
</style>

<div class="toast-container">
<?php exibirNotificacoes(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toasts = document.querySelectorAll('.toast-notificacao');
    
    toasts.forEach(toast => {
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-in forwards';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    });
});

const style = document.createElement('style');
style.textContent = `
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100px); }
    }
`;
document.head.appendChild(style);
</script>
