<?php
// Diagnostic page to test product deletion functionality
echo "<!DOCTYPE html>";
echo "<html lang='pt-BR'><head><meta charset='UTF-8'><title>Diagnóstico - Exclusão de Produtos</title>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style>";
echo "</head><body>";
echo "<h1>🔍 Diagnóstico - Sistema de Exclusão de Produtos</h1>";

// Test 1: Check if files exist
echo "<h2>1️⃣ Verificação de Arquivos</h2>";
$files_to_check = [
    'index.php' => 'Lista de produtos',
    'excluir.php' => 'Página de exclusão',
    'visualizar.php' => 'Visualização de produto',
    '../../includes/auth_check.php' => 'Verificação de autenticação',
    '../../config/database.php' => 'Configuração do banco'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<p class='success'>✅ $file ($description) - EXISTS</p>";
    } else {
        echo "<p class='error'>❌ $file ($description) - NOT FOUND</p>";
    }
}

// Test 2: Database connection
echo "<h2>2️⃣ Teste de Conexão com Banco de Dados</h2>";
try {
    require_once '../../config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✅ Conexão com banco: OK</p>";
    
    // Check produtos table
    $stmt = $db->query("SHOW TABLES LIKE 'produtos'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='success'>✅ Tabela 'produtos': EXISTS</p>";
        
        // Count products
        $stmt = $db->query("SELECT COUNT(*) FROM produtos");
        $count = $stmt->fetchColumn();
        echo "<p class='info'>ℹ️ Total de produtos: $count</p>";
        
        if ($count > 0) {
            // Get first product for testing
            $stmt = $db->query("SELECT id, nome FROM produtos LIMIT 1");
            $product = $stmt->fetch();
            if ($product) {
                echo "<p class='info'>ℹ️ Produto de teste: ID {$product['id']} - {$product['nome']}</p>";
                echo "<p class='warning'>⚠️ <a href='excluir.php?id={$product['id']}' onclick='return confirm(\"ATENÇÃO: Isso irá excluir o produto! Continuar?\")'>Testar exclusão do produto ID {$product['id']}</a></p>";
            }
        }
    } else {
        echo "<p class='error'>❌ Tabela 'produtos': NOT FOUND</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro de banco: " . $e->getMessage() . "</p>";
}

// Test 3: Authentication
echo "<h2>3️⃣ Teste de Autenticação</h2>";
session_start();
if (isset($_SESSION['usuario_id'])) {
    echo "<p class='success'>✅ Usuário logado: ID {$_SESSION['usuario_id']}</p>";
} else {
    echo "<p class='warning'>⚠️ Usuário não está logado (pode causar problemas)</p>";
}

// Test 4: Path testing
echo "<h2>4️⃣ Teste de Caminhos</h2>";
echo "<p class='info'>ℹ️ Diretório atual: " . __DIR__ . "</p>";
echo "<p class='info'>ℹ️ URL atual: " . $_SERVER['REQUEST_URI'] . "</p>";

// Test 5: Direct access test
echo "<h2>5️⃣ Links de Teste</h2>";
echo "<p><a href='index.php'>→ Voltar para lista de produtos</a></p>";
echo "<p><a href='teste.php'>→ Executar teste completo do sistema</a></p>";
echo "<p><a href='excluir.php?id=999' onclick='return confirm(\"Isso irá tentar excluir um produto inexistente. Continuar?\")'>→ Testar com ID inexistente (deve mostrar erro)</a></p>";

// Test 6: GET parameters test
echo "<h2>6️⃣ Parâmetros da URL</h2>";
if (!empty($_GET)) {
    echo "<p class='info'>ℹ️ Parâmetros recebidos:</p>";
    echo "<pre>";
    print_r($_GET);
    echo "</pre>";
} else {
    echo "<p class='warning'>⚠️ Nenhum parâmetro GET recebido</p>";
}

echo "<hr>";
echo "<h2>🏥 Possíveis Soluções para Erro 404</h2>";
echo "<ol>";
echo "<li><strong>Verificar permissões de arquivo:</strong> Certifique-se que o arquivo excluir.php tem permissões de leitura</li>";
echo "<li><strong>Verificar configuração do servidor:</strong> XAMPP/Apache deve estar rodando corretamente</li>";
echo "<li><strong>Verificar .htaccess:</strong> Regras de reescrita podem estar interferindo</li>";
echo "<li><strong>Verificar logs do Apache:</strong> Cheque os logs de erro do servidor</li>";
echo "<li><strong>Limpar cache do navegador:</strong> Às vezes o navegador mantém cache de páginas 404</li>";
echo "</ol>";

echo "</body></html>";
?>