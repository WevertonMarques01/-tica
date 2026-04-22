<?php
// Verificar autenticaÃ§Ã£o
require_once __DIR__ . '/../../includes/auth_check.php';

// Verificar se foi passado um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: historico.php?error=id_invalido');
    exit;
}

$id = (int)$_GET['id'];

// Conectar ao banco de dados
require_once __DIR__ . '/../../config/database.php';
$db = Database::getInstance()->getConnection();

try {
    // Verificar se a venda existe
    $stmt = $db->prepare("SELECT id, cliente_id, total FROM vendas WHERE id = ?");
    $stmt->execute([$id]);
    $venda = $stmt->fetch();
    
    if (!$venda) {
        header('Location: historico.php?error=venda_nao_encontrada');
        exit;
    }
    
    // Verificar se hÃ¡ itens de venda relacionados e excluÃ­-los primeiro
    $stmtItens = $db->prepare("DELETE FROM venda_produtos WHERE venda_id = ?");
    $stmtItens->execute([$id]);
    
    // Verificar se hÃ¡ registros no financeiro e excluÃ­-los
    try {
        $stmtFinanceiro = $db->prepare("DELETE FROM financeiro WHERE venda_id = ?");
        $stmtFinanceiro->execute([$id]);
    } catch (PDOException $e) {
        // Tabela financeiro pode nÃ£o existir ou nÃ£o ter a coluna venda_id
        error_log("Aviso: Falha ao excluir do financeiro: " . $e->getMessage());
    }
    
    // Excluir a venda
    $stmt = $db->prepare("DELETE FROM vendas WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        // Registrar log (se a tabela logs existir)
        try {
            $logStmt = $db->prepare("INSERT INTO logs (usuario_id, acao, detalhes) VALUES (?, ?, ?)");
            $logStmt->execute([$_SESSION['usuario_id'], 'venda_excluida', "Venda ID: $id excluÃ­da"]);
        } catch (PDOException $e) {
            // Se a tabela de logs nÃ£o existir, continua sem registrar
            error_log("Aviso: NÃ£o foi possÃ­vel registrar log: " . $e->getMessage());
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
