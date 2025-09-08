<?php
// Test the productos.php routing
echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'><head><meta charset='UTF-8'><title>Teste Router - produtos.php</title></head><body>";
echo "<h1>🔧 Teste do Router - produtos.php</h1>";

echo "<h2>1. Verificação de Arquivos</h2>";

// Check if produtos.php exists
$produtos_path = __DIR__ . '/../../produtos.php';
echo "<p><strong>produtos.php:</strong> " . ($produtos_path) . "</p>";
echo "<p><strong>Existe:</strong> " . (file_exists($produtos_path) ? "✅ SIM" : "❌ NÃO") . "</p>";

// Check if controller exists  
$controller_path = __DIR__ . '/../../controllers/ProdutoController.php';
echo "<p><strong>ProdutoController.php:</strong> " . ($controller_path) . "</p>";
echo "<p><strong>Existe:</strong> " . (file_exists($controller_path) ? "✅ SIM" : "❌ NÃO") . "</p>";

echo "<h2>2. Teste de URLs</h2>";
echo "<p>Clique nos links abaixo para testar o roteamento:</p>";

$base_url = '/otica/produtos.php';

$test_urls = [
    'index' => $base_url,
    'index explícito' => $base_url . '?action=index',
    'novo produto' => $base_url . '?action=novo',
    'editar produto (ID 1)' => $base_url . '?action=editar&id=1',
    'excluir produto (ID 999 - deve dar erro)' => $base_url . '?action=excluir&id=999'
];

echo "<ul>";
foreach ($test_urls as $label => $url) {
    echo "<li><a href='$url' target='_blank'>$label</a> - <code>$url</code></li>";
}
echo "</ul>";

echo "<h2>3. Debug de Caminhos</h2>";
echo "<p><strong>__FILE__:</strong> " . __FILE__ . "</p>";
echo "<p><strong>__DIR__:</strong> " . __DIR__ . "</p>";

// Test relative paths from current location
$relative_paths = [
    '../../produtos.php',
    '../produtos.php',
    'produtos.php'
];

echo "<h3>Caminhos relativos testados:</h3>";
foreach ($relative_paths as $path) {
    $full_path = realpath(__DIR__ . '/' . $path);
    $exists = file_exists(__DIR__ . '/' . $path);
    echo "<p><strong>$path:</strong> " . ($exists ? "✅ EXISTS" : "❌ NOT FOUND");
    if ($exists && $full_path) {
        echo " → <em>$full_path</em>";
    }
    echo "</p>";
}

echo "<h2>4. Teste com ID Válido</h2>";
echo "<p>Se você tem produtos cadastrados, teste com um ID real:</p>";

// Try to get a real product ID
try {
    require_once '../../config/database.php';
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, nome FROM produtos LIMIT 1");
    $produto = $stmt->fetch();
    
    if ($produto) {
        echo "<p><strong>Produto encontrado:</strong> ID {$produto['id']} - " . htmlspecialchars($produto['nome']) . "</p>";
        echo "<p><a href='$base_url?action=excluir&id={$produto['id']}' onclick='return confirm(\"Testar exclusão?\")'>🗑️ TESTAR EXCLUSÃO (ID {$produto['id']})</a></p>";
    } else {
        echo "<p><em>Nenhum produto encontrado no banco</em></p>";
    }
} catch (Exception $e) {
    echo "<p><strong>Erro ao conectar ao banco:</strong> " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>