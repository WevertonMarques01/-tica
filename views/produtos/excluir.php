<?php
// Verificar autenticaÃ§Ã£o
require_once __DIR__ . '/../../includes/auth_check.php';

// Verificar se o usuÃ¡rio estÃ¡ logado (conforme especificaÃ§Ã£o do sistema)
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
    require_once __DIR__ . '/../../config/database.php';
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    error_log("Erro de conexÃ£o com banco: " . $e->getMessage());
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
    error_log("Iniciando exclusÃ£o do produto: ID $id - {$produto['nome']}");
    
    // Verificar se hÃ¡ vendas relacionadas ao produto
    $temVendasRelacionadas = false;
    try {
        $stmtVendas = $db->prepare("SELECT COUNT(*) as total FROM venda_produtos WHERE produto_id = ?");
        $stmtVendas->execute([$id]);
        $vendasRelacionadas = $stmtVendas->fetch();
        
        if ($vendasRelacionadas && $vendasRelacionadas['total'] > 0) {
            error_log("Produto ID $id tem {$vendasRelacionadas['total']} vendas associadas");
            $temVendasRelacionadas = true;
        }
    } catch (PDOException $e) {
        error_log("Aviso: Tabela venda_produtos nÃ£o encontrada: " . $e->getMessage());
    }
    
    if ($temVendasRelacionadas) {
        header('Location: index.php?error=produto_tem_vendas');
        exit;
    }
    
    // Verificar se hÃ¡ movimentaÃ§Ãµes de estoque relacionadas
    $temMovimentacoesRelacionadas = false;
    try {
        $stmtMovimentacoes = $db->prepare("SELECT COUNT(*) as total FROM movimentacao_estoque WHERE produto_id = ?");
        $stmtMovimentacoes->execute([$id]);
        $movimentacoesRelacionadas = $stmtMovimentacoes->fetch();
        
        if ($movimentacoesRelacionadas && $movimentacoesRelacionadas['total'] > 0) {
            error_log("Produto ID $id tem {$movimentacoesRelacionadas['total']} movimentaÃ§Ãµes de estoque associadas");
            $temMovimentacoesRelacionadas = true;
        }
    } catch (PDOException $e) {
        // Tabela movimentacao_estoque pode nÃ£o existir
        error_log("Aviso: Tabela movimentacao_estoque nÃ£o encontrada: " . $e->getMessage());
    }
    
    if ($temMovimentacoesRelacionadas) {
        header('Location: index.php?error=produto_tem_movimentacoes');
        exit;
    }
    
    // Iniciar transaÃ§Ã£o para seguranÃ§a
    $db->beginTransaction();
    
    try {
        // Excluir o produto (apÃ³s verificar que nÃ£o hÃ¡ dados relacionados)
        $stmt = $db->prepare("DELETE FROM produtos WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if (!$result) {
            throw new Exception("Falha ao executar comando DELETE");
        }
        
        $linhasAfetadas = $stmt->rowCount();
        if ($linhasAfetadas === 0) {
            throw new Exception("Nenhuma linha foi afetada pela exclusÃ£o");
        }
        
        error_log("Produto ID $id excluÃ­do com sucesso. Linhas afetadas: $linhasAfetadas");
        
        // Registrar log (se a tabela logs existir)
        try {
            $logStmt = $db->prepare("INSERT INTO logs (usuario_id, acao, detalhes, data) VALUES (?, ?, ?, NOW())");
            $logResult = $logStmt->execute([
                $_SESSION['usuario_id'], 
                'produto_excluido', 
                "Produto ID: $id ({$produto['nome']}) excluÃ­do"
            ]);
            
            if ($logResult) {
                error_log("Log de exclusÃ£o registrado com sucesso para produto ID $id");
            }
        } catch (PDOException $e) {
            // Se a tabela de logs nÃ£o existir, continua sem registrar
            error_log("Aviso: NÃ£o foi possÃ­vel registrar log: " . $e->getMessage());
        }
        
        // Confirmar transaÃ§Ã£o
        $db->commit();
        
        header('Location: index.php?success=excluido');
        exit;
        
    } catch (Exception $e) {
        // Reverter transaÃ§Ã£o em caso de erro
        $db->rollback();
        error_log("Erro durante exclusÃ£o do produto ID $id: " . $e->getMessage());
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
