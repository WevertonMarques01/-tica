<?php
// Teste simples para verificar se as páginas de vendas estão funcionando
echo "<!DOCTYPE html>";
echo "<html><head><title>Teste Vendas</title></head><body>";
echo "<h1>Teste do Sistema de Vendas</h1>";

// Verificar se os arquivos existem
$arquivos = [
    'historico.php',
    'nova.php', 
    'excluir.php',
    'visualizar.php',
    'editar.php'
];

echo "<h2>Status dos Arquivos:</h2>";
echo "<ul>";
foreach ($arquivos as $arquivo) {
    $caminho = __DIR__ . '/' . $arquivo;
    if (file_exists($caminho)) {
        echo "<li style='color: green;'>✓ $arquivo - OK</li>";
    } else {
        echo "<li style='color: red;'>✗ $arquivo - NÃO ENCONTRADO</li>";
    }
}
echo "</ul>";

// Testar conexão com banco
echo "<h2>Teste de Conexão:</h2>";
try {
    require_once '../../config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✓ Conexão com banco de dados: OK</p>";
    
    // Verificar se a tabela vendas existe
    $stmt = $db->query("SHOW TABLES LIKE 'vendas'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Tabela 'vendas': OK</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Tabela 'vendas': NÃO ENCONTRADA</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro de conexão: " . $e->getMessage() . "</p>";
}

echo "<h2>Links de Teste:</h2>";
echo "<p><a href='historico.php'>→ Histórico de Vendas</a></p>";
echo "<p><a href='nova.php'>→ Nova Venda</a></p>";

echo "</body></html>";
?>