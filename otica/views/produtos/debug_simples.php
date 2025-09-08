<?php
// Simple debug tool for deletion issues
session_start();

echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'><head><meta charset='UTF-8'><title>Debug Simples - Exclusão</title>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";
echo "</head><body>";
echo "<h1>🔧 Debug Simples - Exclusão de Produtos</h1>";

// Check if running via web or CLI
echo "<h2>1. Ambiente de Execução</h2>";
if (isset($_SERVER['HTTP_HOST'])) {
    echo "<p class='success'>✅ Executando via navegador (OK)</p>";
} else {
    echo "<p class='warning'>⚠️ Executando via linha de comando</p>";
}

// Check session
echo "<h2>2. Verificação de Sessão</h2>";
if (isset($_SESSION['usuario_id'])) {
    echo "<p class='success'>✅ Usuário logado: ID {$_SESSION['usuario_id']}</p>";
    if (isset($_SESSION['usuario_permissao'])) {
        echo "<p class='info'>ℹ️ Permissão: {$_SESSION['usuario_permissao']}</p>";
    }
} else {
    echo "<p class='error'>❌ Usuário não está logado</p>";
    echo "<p><a href='../../login.php'>→ Fazer login</a></p>";
}

// Check database connection
echo "<h2>3. Conexão com Banco</h2>";
try {
    require_once '../../config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✅ Conexão com banco: OK</p>";
    
    // Test simple query
    $stmt = $db->query("SELECT COUNT(*) as total FROM produtos");
    $result = $stmt->fetch();
    echo "<p class='info'>ℹ️ Total de produtos: {$result['total']}</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro de banco: " . $e->getMessage() . "</p>";
}

// Check if we can access the first product
echo "<h2>4. Teste com Primeiro Produto</h2>";
if (isset($db)) {
    try {
        $stmt = $db->query("SELECT id, nome, codigo_barras FROM produtos LIMIT 1");
        $produto = $stmt->fetch();
        
        if ($produto) {
            echo "<p class='info'>ℹ️ Produto de teste: ID {$produto['id']} - " . htmlspecialchars($produto['nome']) . "</p>";
            echo "<p><a href='excluir.php?id={$produto['id']}' onclick='return confirm(\"Testar exclusão?\")'>🗑️ Testar exclusão deste produto</a></p>";
        } else {
            echo "<p class='warning'>⚠️ Nenhum produto encontrado</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erro ao buscar produto: " . $e->getMessage() . "</p>";
    }
}

// Check PHP error log
echo "<h2>5. Últimos Erros PHP</h2>";
$errorLog = ini_get('error_log');
if (empty($errorLog)) {
    $errorLog = 'C:\\xampp\\php\\logs\\php_error_log';
}

echo "<p class='info'>ℹ️ Log de erros: $errorLog</p>";

if (file_exists($errorLog)) {
    $lines = file($errorLog);
    $recentLines = array_slice($lines, -10); // Últimas 10 linhas
    echo "<h3>Últimas 10 linhas do log de erros:</h3>";
    echo "<pre style='background:#f5f5f5;padding:10px;border:1px solid #ddd;'>";
    foreach ($recentLines as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
} else {
    echo "<p class='warning'>⚠️ Arquivo de log não encontrado</p>";
}

echo "<hr>";
echo "<p><strong>PRÓXIMOS PASSOS:</strong></p>";
echo "<ol>";
echo "<li>Se você estiver logado, teste o link de exclusão acima</li>";
echo "<li>Verifique os logs de erro após o teste</li>";
echo "<li>Se aparecer erro, copie a mensagem do log</li>";
echo "</ol>";

echo "</body></html>";
?>