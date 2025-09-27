<?php
// Test deletion with detailed error reporting
echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'><head><meta charset='UTF-8'><title>Teste de Exclusão - Debug</title>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border:1px solid #ddd;}</style>";
echo "</head><body>";
echo "<h1>🧪 Teste de Exclusão com Debug Detalhado</h1>";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

try {
    require_once '../../config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✅ Conexão com banco: OK</p>";
    
    // Check if user is logged in
    if (!isset($_SESSION['usuario_id'])) {
        echo "<p class='error'>❌ Usuário não está logado. <a href='../../login.php'>Fazer login</a></p>";
        echo "</body></html>";
        exit;
    }
    
    echo "<p class='success'>✅ Usuário logado: ID {$_SESSION['usuario_id']}</p>";
    
    // Get product ID from URL or use first available
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    if (!$id) {
        // Get first product for testing
        $stmt = $db->query("SELECT id, nome FROM produtos LIMIT 1");
        $produto = $stmt->fetch();
        if ($produto) {
            $id = $produto['id'];
            echo "<p class='info'>ℹ️ Usando produto de teste: ID $id - " . htmlspecialchars($produto['nome']) . "</p>";
        } else {
            echo "<p class='error'>❌ Nenhum produto encontrado para teste</p>";
            echo "</body></html>";
            exit;
        }
    }
    
    echo "<h2>🔍 Simulação de Exclusão - Produto ID: $id</h2>";
    
    // Step 1: Check if product exists
    echo "<h3>Passo 1: Verificar se produto existe</h3>";
    $stmt = $db->prepare("SELECT id, nome, codigo FROM produtos WHERE id = ?");
    $stmt->execute([$id]);
    $produto = $stmt->fetch();
    
    if (!$produto) {
        echo "<p class='error'>❌ Produto ID $id não encontrado</p>";
        echo "</body></html>";
        exit;
    }
    
    echo "<p class='success'>✅ Produto encontrado: " . htmlspecialchars($produto['nome']) . "</p>";
    
    // Step 2: Check related records
    echo "<h3>Passo 2: Verificar registros relacionados</h3>";
    
    // Check itens_venda
    $vendasCount = 0;
    try {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM itens_venda WHERE produto_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        $vendasCount = $result['total'];
        echo "<p class='info'>ℹ️ Itens de venda relacionados: $vendasCount</p>";
    } catch (PDOException $e) {
        echo "<p class='warning'>⚠️ Erro ao verificar itens_venda: " . $e->getMessage() . "</p>";
    }
    
    // Check movimentacao_estoque
    $movimentacoesCount = 0;
    try {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM movimentacao_estoque WHERE produto_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        $movimentacoesCount = $result['total'];
        echo "<p class='info'>ℹ️ Movimentações de estoque relacionadas: $movimentacoesCount</p>";
    } catch (PDOException $e) {
        echo "<p class='warning'>⚠️ Erro ao verificar movimentacao_estoque: " . $e->getMessage() . "</p>";
    }
    
    // Check if can delete
    $podeExcluir = ($vendasCount == 0 && $movimentacoesCount == 0);
    
    if (!$podeExcluir) {
        echo "<p class='error'>❌ NÃO PODE EXCLUIR: Produto tem registros relacionados</p>";
        if ($vendasCount > 0) {
            echo "<p class='error'>→ $vendasCount vendas associadas</p>";
        }
        if ($movimentacoesCount > 0) {
            echo "<p class='error'>→ $movimentacoesCount movimentações de estoque associadas</p>";
        }
        echo "</body></html>";
        exit;
    }
    
    echo "<p class='success'>✅ Produto pode ser excluído (sem registros relacionados)</p>";
    
    // Step 3: Test deletion (only if requested)
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
        echo "<h3>Passo 3: EXECUTANDO EXCLUSÃO REAL</h3>";
        
        try {
            $db->beginTransaction();
            
            // Delete product
            $stmt = $db->prepare("DELETE FROM produtos WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if (!$result) {
                throw new Exception("Comando DELETE retornou false");
            }
            
            $rowsAffected = $stmt->rowCount();
            echo "<p class='success'>✅ DELETE executado. Linhas afetadas: $rowsAffected</p>";
            
            if ($rowsAffected === 0) {
                throw new Exception("Nenhuma linha foi afetada");
            }
            
            // Try to log
            try {
                $logStmt = $db->prepare("INSERT INTO logs_sistema (usuario_id, acao, detalhes, created_at) VALUES (?, ?, ?, NOW())");
                $logResult = $logStmt->execute([
                    $_SESSION['usuario_id'], 
                    'produto_excluido', 
                    "Produto ID: $id ({$produto['nome']}) excluído via teste"
                ]);
                echo "<p class='success'>✅ Log registrado</p>";
            } catch (Exception $e) {
                echo "<p class='warning'>⚠️ Não foi possível registrar log: " . $e->getMessage() . "</p>";
            }
            
            $db->commit();
            echo "<p class='success'>✅ PRODUTO EXCLUÍDO COM SUCESSO!</p>";
            echo "<p><a href='index.php'>→ Voltar para lista de produtos</a></p>";
            
        } catch (Exception $e) {
            $db->rollback();
            echo "<p class='error'>❌ ERRO NA EXCLUSÃO: " . $e->getMessage() . "</p>";
            echo "<p class='info'>ℹ️ Transação revertida</p>";
        }
        
    } else {
        echo "<h3>Passo 3: Confirmar Exclusão</h3>";
        echo "<p class='warning'>⚠️ <a href='?id=$id&confirm=yes' onclick='return confirm(\"ATENÇÃO: Isso irá REALMENTE excluir o produto! Continuar?\")'>EXCLUIR PRODUTO AGORA</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro geral: " . $e->getMessage() . "</p>";
    echo "<p class='info'>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
?>