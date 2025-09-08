<?php
// Simple URL test for product deletion
echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'><head><meta charset='UTF-8'><title>Teste de URL - Exclusão</title></head><body>";
echo "<h1>🔗 Teste de URL - Exclusão de Produtos</h1>";

echo "<h2>Informações do Ambiente</h2>";
echo "<p><strong>Caminho do arquivo atual:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Diretório atual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>URL atual:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>Método HTTP:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>";

echo "<h2>Verificação de Arquivos</h2>";
$excluir_path = __DIR__ . '/excluir.php';
echo "<p><strong>Arquivo excluir.php:</strong> " . ($excluir_path) . "</p>";
echo "<p><strong>Existe:</strong> " . (file_exists($excluir_path) ? "✅ SIM" : "❌ NÃO") . "</p>";

if (file_exists($excluir_path)) {
    echo "<p><strong>Permissões:</strong> " . substr(sprintf('%o', fileperms($excluir_path)), -4) . "</p>";
    echo "<p><strong>Tamanho:</strong> " . filesize($excluir_path) . " bytes</p>";
}

echo "<h2>Teste de URLs</h2>";
echo "<p>Se algum dos links abaixo der erro 404, há um problema de configuração:</p>";
echo "<ul>";
echo "<li><a href='excluir.php' target='_blank'>excluir.php (sem parâmetros)</a> - Deve redirecionar com erro</li>";
echo "<li><a href='excluir.php?id=999' target='_blank'>excluir.php?id=999</a> - Deve mostrar erro de produto não encontrado</li>";
echo "<li><a href='index.php' target='_blank'>index.php</a> - Deve mostrar a lista de produtos</li>";
echo "</ul>";

echo "<h2>Informações do Servidor</h2>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";

echo "<h2>Debugging de Caminhos</h2>";
$base_path = dirname(dirname(__DIR__));
echo "<p><strong>Base do projeto:</strong> " . $base_path . "</p>";
echo "<p><strong>Config existe:</strong> " . (file_exists($base_path . '/config/database.php') ? "✅ SIM" : "❌ NÃO") . "</p>";
echo "<p><strong>Auth existe:</strong> " . (file_exists($base_path . '/includes/auth_check.php') ? "✅ SIM" : "❌ NÃO") . "</p>";

echo "</body></html>";
?>