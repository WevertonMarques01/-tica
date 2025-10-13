<?php
// URL path testing tool
echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'><head><meta charset='UTF-8'><title>Debug de Caminhos</title></head><body>";
echo "<h1>🔗 Debug de Caminhos - Exclusão de Produtos</h1>";

echo "<h2>Informações de Localização</h2>";
echo "<p><strong>Arquivo atual:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Diretório atual:</strong> " . __DIR__ . "</p>";

if (isset($_SERVER['REQUEST_URI'])) {
    echo "<p><strong>URL atual:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
}

echo "<h2>Teste de Caminhos</h2>";
echo "<p>Testando diferentes caminhos para produtos.php:</p>";

// Test different path combinations
$paths_to_test = [
    '../../produtos.php',
    '../produtos.php', 
    '/otica/produtos.php',
    'http://localhost/otica/produtos.php'
];

foreach ($paths_to_test as $path) {
    echo "<p><strong>Caminho:</strong> <code>$path</code></p>";
    echo "<ul>";
    echo "<li><a href='{$path}' target='_blank'>Testar: {$path}</a></li>";
    echo "<li><a href='{$path}?action=index' target='_blank'>Testar: {$path}?action=index</a></li>";
    echo "<li><a href='{$path}?action=excluir&id=1' target='_blank'>Testar: {$path}?action=excluir&id=1</a></li>";
    echo "</ul>";
    echo "<hr>";
}

echo "<h2>Teste do Produto Router</h2>";
echo "<p>Verificando se o arquivo produtos.php existe:</p>";

$router_paths = [
    __DIR__ . '/../../produtos.php',
    __DIR__ . '/../produtos.php',
    __DIR__ . '/produtos.php'
];

foreach ($router_paths as $router_path) {
    $exists = file_exists($router_path);
    echo "<p><strong>$router_path:</strong> " . ($exists ? "✅ EXISTS" : "❌ NOT FOUND") . "</p>";
    
    if ($exists) {
        echo "<p>→ Tamanho: " . filesize($router_path) . " bytes</p>";
        echo "<p>→ Modificado: " . date('Y-m-d H:i:s', filemtime($router_path)) . "</p>";
    }
}

echo "</body></html>";
?>