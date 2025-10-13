<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Verificar se foi passado um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=id_invalido');
    exit;
}

$id = (int)$_GET['id'];

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

try {
    // Verificar se o cliente existe
    $stmt = $db->prepare("SELECT id, nome, documento FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) {
        header('Location: index.php?error=cliente_nao_encontrado');
        exit;
    }
    
    // Verificar se há vendas relacionadas ao cliente
    $stmtVendas = $db->prepare("SELECT COUNT(*) as total FROM vendas WHERE cliente_id = ?");
    $stmtVendas->execute([$id]);
    $vendasRelacionadas = $stmtVendas->fetch();
    
    if ($vendasRelacionadas['total'] > 0) {
        header('Location: index.php?error=cliente_tem_vendas');
        exit;
    }
    
    // Verificar se há receitas relacionadas ao cliente
    try {
        $stmtReceitas = $db->prepare("SELECT COUNT(*) as total FROM receitas WHERE cliente_id = ?");
        $stmtReceitas->execute([$id]);
        $receitasRelacionadas = $stmtReceitas->fetch();
        
        if ($receitasRelacionadas['total'] > 0) {
            header('Location: index.php?error=cliente_tem_receitas');
            exit;
        }
    } catch (PDOException $e) {
        // Tabela receitas pode não existir
        error_log("Aviso: Tabela receitas não encontrada: " . $e->getMessage());
    }
    
    // Verificar se há ordens de serviço relacionadas ao cliente
    try {
        $stmtOrdens = $db->prepare("SELECT COUNT(*) as total FROM ordens_servico WHERE cliente_id = ?");
        $stmtOrdens->execute([$id]);
        $ordensRelacionadas = $stmtOrdens->fetch();
        
        if ($ordensRelacionadas['total'] > 0) {
            header('Location: index.php?error=cliente_tem_ordens');
            exit;
        }
    } catch (PDOException $e) {
        // Tabela ordens_servico pode não existir
        error_log("Aviso: Tabela ordens_servico não encontrada: " . $e->getMessage());
    }
    
    // Excluir o cliente (após verificar que não há dados relacionados)
    $stmt = $db->prepare("DELETE FROM clientes WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        // Registrar log (se a tabela logs_sistema existir)
        try {
            $logStmt = $db->prepare("INSERT INTO logs_sistema (usuario_id, acao, detalhes) VALUES (?, ?, ?)");
            $logStmt->execute([$_SESSION['usuario_id'], 'cliente_excluido', "Cliente ID: $id ({$cliente['nome']}) excluído"]);
        } catch (PDOException $e) {
            // Se a tabela de logs não existir, continua sem registrar
            error_log("Aviso: Não foi possível registrar log: " . $e->getMessage());
        }
        
        header('Location: index.php?success=excluido');
    } else {
        header('Location: index.php?error=erro_exclusao');
    }
    
} catch (PDOException $e) {
    error_log("Erro ao excluir cliente: " . $e->getMessage());
    header('Location: index.php?error=erro_sistema');
}
exit;
?>