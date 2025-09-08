<?php
// Enhanced diagnostic tool for product deletion issues
echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'><head><meta charset='UTF-8'><title>Diagnóstico Avançado - Exclusão de Produtos</title>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;border:1px solid #ddd;}</style>";
echo "</head><body>";
echo "<h1>🔍 Diagnóstico Avançado - Exclusão de Produtos</h1>";

// Test database connection and table structure
echo "<h2>1️⃣ Análise da Estrutura do Banco</h2>";
try {
    require_once '../../config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✅ Conexão com banco: OK</p>";
    
    // Check produtos table structure
    $stmt = $db->query("DESCRIBE produtos");
    $campos = $stmt->fetchAll();
    echo "<h3>Estrutura da tabela 'produtos':</h3>";
    echo "<pre>";
    foreach ($campos as $campo) {
        echo "Campo: {$campo['Field']} | Tipo: {$campo['Type']} | Null: {$campo['Null']} | Key: {$campo['Key']}\n";
    }
    echo "</pre>";
    
    // Check for foreign key constraints
    echo "<h3>Verificação de Foreign Keys:</h3>";
    try {
        $stmt = $db->query("
            SELECT 
                TABLE_NAME,
                COLUMN_NAME,
                CONSTRAINT_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_SCHEMA = DATABASE() 
            AND REFERENCED_TABLE_NAME = 'produtos'
        ");
        $fks = $stmt->fetchAll();
        
        if (count($fks) > 0) {
            echo "<p class='warning'>⚠️ Encontradas " . count($fks) . " foreign keys apontando para produtos:</p>";
            echo "<pre>";
            foreach ($fks as $fk) {
                echo "Tabela: {$fk['TABLE_NAME']}.{$fk['COLUMN_NAME']} -> produtos.{$fk['REFERENCED_COLUMN_NAME']}\n";
            }
            echo "</pre>";
        } else {
            echo "<p class='info'>ℹ️ Nenhuma foreign key encontrada apontando para produtos</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erro ao verificar foreign keys: " . $e->getMessage() . "</p>";
    }
    
    // Check related tables
    echo "<h3>Verificação de Tabelas Relacionadas:</h3>";
    $related_tables = ['itens_venda', 'movimentacao_estoque', 'categorias_produtos', 'marcas'];
    foreach ($related_tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p class='success'>✅ Tabela '$table': EXISTS</p>";
            
            // Check if table has produto_id column
            try {
                $stmt = $db->query("DESCRIBE $table");
                $columns = $stmt->fetchAll();
                $has_produto_id = false;
                foreach ($columns as $col) {
                    if ($col['Field'] === 'produto_id') {
                        $has_produto_id = true;
                        break;
                    }
                }
                if ($has_produto_id) {
                    echo "<p class='info'>ℹ️ → Tem coluna 'produto_id'</p>";
                } else {
                    echo "<p class='warning'>⚠️ → Não tem coluna 'produto_id'</p>";
                }
            } catch (Exception $e) {
                echo "<p class='error'>❌ → Erro ao verificar estrutura: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='warning'>⚠️ Tabela '$table': NOT EXISTS</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro de banco: " . $e->getMessage() . "</p>";
}

// Test with a real product
echo "<h2>2️⃣ Teste com Produto Real</h2>";
try {
    $stmt = $db->query("SELECT id, nome, codigo FROM produtos LIMIT 1");
    $produto = $stmt->fetch();
    
    if ($produto) {
        echo "<p class='info'>ℹ️ Produto de teste: ID {$produto['id']} - " . htmlspecialchars($produto['nome']) . "</p>";
        
        // Check related records
        $relacionados = [];
        
        // Check itens_venda
        try {
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM itens_venda WHERE produto_id = ?");
            $stmt->execute([$produto['id']]);
            $count = $stmt->fetchColumn();
            $relacionados['itens_venda'] = $count;
            echo "<p class='info'>ℹ️ → Itens de venda: $count</p>";
        } catch (Exception $e) {
            echo "<p class='warning'>⚠️ → Erro ao verificar itens_venda: " . $e->getMessage() . "</p>";
        }
        
        // Check movimentacao_estoque
        try {
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM movimentacao_estoque WHERE produto_id = ?");
            $stmt->execute([$produto['id']]);
            $count = $stmt->fetchColumn();
            $relacionados['movimentacao_estoque'] = $count;
            echo "<p class='info'>ℹ️ → Movimentações de estoque: $count</p>";
        } catch (Exception $e) {
            echo "<p class='warning'>⚠️ → Erro ao verificar movimentacao_estoque: " . $e->getMessage() . "</p>";
        }
        
        // Simulate deletion check
        $pode_excluir = true;
        foreach ($relacionados as $tabela => $count) {
            if ($count > 0) {
                $pode_excluir = false;
                echo "<p class='warning'>⚠️ → Não pode excluir: tem $count registros em $tabela</p>";
            }
        }
        
        if ($pode_excluir) {
            echo "<p class='success'>✅ → Produto pode ser excluído (sem registros relacionados)</p>";
            echo "<p class='warning'>⚠️ <a href='teste_exclusao.php?id={$produto['id']}' onclick='return confirm(\"ATENÇÃO: Isso irá tentar excluir o produto! Continuar?\")'>Testar exclusão real</a></p>";
        }
        
    } else {
        echo "<p class='warning'>⚠️ Nenhum produto encontrado para teste</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro no teste: " . $e->getMessage() . "</p>";
}

// Test session
echo "<h2>3️⃣ Verificação de Sessão</h2>";
if (isset($_SESSION['usuario_id'])) {
    echo "<p class='success'>✅ Usuário logado: ID {$_SESSION['usuario_id']}</p>";
    if (isset($_SESSION['usuario_permissao'])) {
        echo "<p class='info'>ℹ️ → Permissão: {$_SESSION['usuario_permissao']}</p>";
    }
} else {
    echo "<p class='error'>❌ Usuário não está logado</p>";
}

// Test logs table
echo "<h2>4️⃣ Verificação de Logs</h2>";
try {
    $stmt = $db->query("SHOW TABLES LIKE 'logs_sistema'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='success'>✅ Tabela 'logs_sistema': EXISTS</p>";
        $stmt = $db->query("DESCRIBE logs_sistema");
        $campos = $stmt->fetchAll();
        echo "<p class='info'>ℹ️ → Campos: " . implode(', ', array_column($campos, 'Field')) . "</p>";
    } else {
        echo "<p class='warning'>⚠️ Tabela 'logs_sistema': NOT EXISTS</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro ao verificar logs: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>