<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance()->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    die('ID da receita nÃ£o fornecido');
}

try {
    $stmt = $db->prepare("SELECT r.*, c.nome as cliente_nome, c.telefone as cliente_telefone 
                         FROM receitas r 
                         LEFT JOIN clientes c ON r.cliente_id = c.id 
                         WHERE r.id = ?");
    $stmt->execute([$id]);
    $receita = $stmt->fetch();
    
    if (!$receita) {
        die('Receita nÃ£o encontrada');
    }
    
    $mensagem = "ðŸ”¬ *RECETA Ã“PTICA*\n\n";
    $mensagem .= "ðŸ‘¤ *Cliente:* {$receita['cliente_nome']}\n";
    $mensagem .= "ðŸ“… *Data:* " . ($receita['data_receita'] ? date('d/m/Y', strtotime($receita['data_receita'])) : '-') . "\n\n";
    
    $mensagem .= "ðŸ‘ï¸ *OLHO DIREITO (OD):*\n";
    if (!empty($receita['esfera_od'])) $mensagem .= "  ESF: {$receita['esfera_od']}\n";
    if (!empty($receita['cilindro_od'])) $mensagem .= "  CIL: {$receita['cilindro_od']}\n";
    if (!empty($receita['eixo_od'])) $mensagem .= "  Eixo: {$receita['eixo_od']}\n";
    if (!empty($receita['adicao_od'])) $mensagem .= "  AdiÃ§Ã£o: {$receita['adicao_od']}\n";
    if (!empty($receita['dnp_od'])) $mensagem .= "  DNP: {$receita['dnp_od']}\n";
    if (!empty($receita['altura_od'])) $mensagem .= "  Altura: {$receita['altura_od']}\n";
    
    $mensagem .= "\nðŸ‘ï¸ *OLHO ESQUERDO (OE):*\n";
    if (!empty($receita['esfera_oe'])) $mensagem .= "  ESF: {$receita['esfera_oe']}\n";
    if (!empty($receita['cilindro_oe'])) $mensagem .= "  CIL: {$receita['cilindro_oe']}\n";
    if (!empty($receita['eixo_oe'])) $mensagem .= "  Eixo: {$receita['eixo_oe']}\n";
    if (!empty($receita['adicao_oe'])) $mensagem .= "  AdiÃ§Ã£o: {$receita['adicao_oe']}\n";
    if (!empty($receita['dnp_oe'])) $mensagem .= "  DNP: {$receita['dnp_oe']}\n";
    if (!empty($receita['altura_oe'])) $mensagem .= "  Altura: {$receita['altura_oe']}\n";
    
    if (!empty($receita['observacoes'])) {
        $mensagem .= "\nðŸ“ *ObservaÃ§Ãµes:*\n{$receita['observacoes']}\n";
    }
    
    $telefone = preg_replace('/[^0-9]/', '', $receita['cliente_telefone'] ?? '');
    
    if (!empty($telefone) && strlen($telefone) >= 10) {
        if (strlen($telefone) == 11 && $telefone[0] == '0') {
            $telefone = substr($telefone, 1);
        }
        if (strlen($telefone) == 10) {
            $telefone = '55' . $telefone;
        } elseif (strlen($telefone) == 11) {
            $telefone = '55' . $telefone;
        }
        $urlWhatsapp = 'https://wa.me/' . $telefone . '?text=' . urlencode($mensagem);
    } else {
        $urlWhatsapp = 'https://wa.me/?text=' . urlencode($mensagem);
    }
    
    header('Location: ' . $urlWhatsapp);
    exit;
    
} catch (PDOException $e) {
    error_log("Erro: " . $e->getMessage());
    die('Erro ao buscar receita');
}
