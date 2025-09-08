<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Verificar se o usuário está logado (conforme especificação do sistema)
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../login.php?error=sessao_expirada');
    exit;
}

// Verificar se foi passado um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=id_invalido');
    exit;
}

$id = (int)$_GET['id'];

// Conectar ao banco de dados
try {
    require_once '../../config/database.php';
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    error_log("Erro de conexão com banco: " . $e->getMessage());
    header('Location: index.php?error=erro_sistema');
    exit;
}

try {
    // Verificar se o produto existe
    $stmt = $db->prepare("SELECT id, nome, codigo FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();
    
    if (!$produto) {
        error_log("Tentativa de excluir produto inexistente: ID $id");
        header('Location: index.php?error=produto_nao_encontrado');
        exit;
    }
    
    // Log para debug
    error_log("Iniciando exclusão do produto: ID $id - {$produto['nome']}");
    
    // Verificar se há vendas relacionadas ao produto
    $temVendasRelacionadas = false;
    try {
        $stmtVendas = $db->prepare("SELECT COUNT(*) as total FROM itens_venda WHERE produto_id = ?");
        $stmtVendas->execute([$id]);
        $vendasRelacionadas = $stmtVendas->fetch();
        
        if ($vendasRelacionadas && $vendasRelacionadas['total'] > 0) {
            error_log("Produto ID $id tem {$vendasRelacionadas['total']} vendas associadas");
            $temVendasRelacionadas = true;
        }
    } catch (PDOException $e) {
        // Tabela itens_venda pode não existir
        error_log("Aviso: Tabela itens_venda não encontrada: " . $e->getMessage());
    }
    
    if ($temVendasRelacionadas) {
        header('Location: index.php?error=produto_tem_vendas');
        exit;
    }
    
    // Verificar se há movimentações de estoque relacionadas
    $temMovimentacoesRelacionadas = false;
    try {
        $stmtMovimentacoes = $db->prepare("SELECT COUNT(*) as total FROM movimentacao_estoque WHERE produto_id = ?");
        $stmtMovimentacoes->execute([$id]);
        $movimentacoesRelacionadas = $stmtMovimentacoes->fetch();
        
        if ($movimentacoesRelacionadas && $movimentacoesRelacionadas['total'] > 0) {
            error_log("Produto ID $id tem {$movimentacoesRelacionadas['total']} movimentações de estoque associadas");
            $temMovimentacoesRelacionadas = true;
        }
    } catch (PDOException $e) {
        // Tabela movimentacao_estoque pode não existir
        error_log("Aviso: Tabela movimentacao_estoque não encontrada: " . $e->getMessage());
    }
    
    if ($temMovimentacoesRelacionadas) {
        header('Location: index.php?error=produto_tem_movimentacoes');
        exit;
    }
    
    // Iniciar transação para segurança
    $db->beginTransaction();
    
    try {
        // Excluir o produto (após verificar que não há dados relacionados)
        $stmt = $db->prepare("DELETE FROM produtos WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if (!$result) {
            throw new Exception("Falha ao executar comando DELETE");
        }
        
        $linhasAfetadas = $stmt->rowCount();
        if ($linhasAfetadas === 0) {
            throw new Exception("Nenhuma linha foi afetada pela exclusão");
        }
        
        error_log("Produto ID $id excluído com sucesso. Linhas afetadas: $linhasAfetadas");
        
        // Registrar log (se a tabela logs_sistema existir)
        try {
            $logStmt = $db->prepare("INSERT INTO logs_sistema (usuario_id, acao, detalhes, created_at) VALUES (?, ?, ?, NOW())");
            $logResult = $logStmt->execute([
                $_SESSION['usuario_id'], 
                'produto_excluido', 
                "Produto ID: $id ({$produto['nome']}) excluído"
            ]);
            
            if ($logResult) {
                error_log("Log de exclusão registrado com sucesso para produto ID $id");
            }
        } catch (PDOException $e) {
            // Se a tabela de logs não existir, continua sem registrar
            error_log("Aviso: Não foi possível registrar log: " . $e->getMessage());
        }
        
        // Confirmar transação
        $db->commit();
        
        header('Location: index.php?success=excluido');
        exit;
        
    } catch (Exception $e) {
        // Reverter transação em caso de erro
        $db->rollback();
        error_log("Erro durante exclusão do produto ID $id: " . $e->getMessage());
        header('Location: index.php?error=erro_exclusao');
        exit;
    }
    
} catch (PDOException $e) {
    error_log("Erro de banco ao excluir produto ID $id: " . $e->getMessage());
    header('Location: index.php?error=erro_sistema');
    exit;
} catch (Exception $e) {
    error_log("Erro geral ao excluir produto ID $id: " . $e->getMessage());
    header('Location: index.php?error=erro_sistema');
    exit;
}
exit;
?>