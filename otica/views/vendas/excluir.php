<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Verificar se foi passado um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: historico.php?error=id_invalido');
    exit;
}

$id = (int)$_GET['id'];

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

try {
    // Verificar se a venda existe
    $stmt = $db->prepare("SELECT id, cliente_id, valor_total FROM vendas WHERE id = ?");
    $stmt->execute([$id]);
    $venda = $stmt->fetch();
    
    if (!$venda) {
        header('Location: historico.php?error=venda_nao_encontrada');
        exit;
    }
    
    // Verificar se há itens de venda relacionados e excluí-los primeiro
    $stmtItens = $db->prepare("DELETE FROM itens_venda WHERE venda_id = ?");
    $stmtItens->execute([$id]);
    
    // Excluir a venda
    $stmt = $db->prepare("DELETE FROM vendas WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        // Registrar log (se a tabela logs_sistema existir)
        try {
            $logStmt = $db->prepare("INSERT INTO logs_sistema (usuario_id, acao, detalhes) VALUES (?, ?, ?)");
            $logStmt->execute([$_SESSION['usuario_id'], 'venda_excluida', "Venda ID: $id excluída"]);
        } catch (PDOException $e) {
            // Se a tabela de logs não existir, continua sem registrar
            error_log("Aviso: Não foi possível registrar log: " . $e->getMessage());
        }
        
        header('Location: historico.php?success=excluida');
    } else {
        header('Location: historico.php?error=erro_exclusao');
    }
    
} catch (PDOException $e) {
    error_log("Erro ao excluir venda: " . $e->getMessage());
    header('Location: historico.php?error=erro_sistema');
}
exit;
?>