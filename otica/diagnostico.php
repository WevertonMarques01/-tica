<?php
// Simple diagnostic page to check authentication and database

session_start();

echo "<!DOCTYPE html>";
echo "<html><head><title>Sistema Ótica - Diagnóstico</title></head><body>";
echo "<h1>Diagnóstico do Sistema</h1>";

echo "<h2>1. Sessão</h2>";
if (isset($_SESSION['usuario_id'])) {
    echo "<p style='color: green'>✅ Usuário logado: ID " . $_SESSION['usuario_id'] . "</p>";
    if (isset($_SESSION['usuario_nome'])) {
        echo "<p>Nome: " . htmlspecialchars($_SESSION['usuario_nome']) . "</p>";
    }
    if (isset($_SESSION['usuario_permissao'])) {
        echo "<p>Permissão: " . htmlspecialchars($_SESSION['usuario_permissao']) . "</p>";
    }
} else {
    echo "<p style='color: red'>❌ Usuário não está logado</p>";
    echo "<p><a href='login.php'>Fazer login</a></p>";
}

echo "<h2>2. Dados da Sessão</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

echo "<h2>3. Conexão com Banco de Dados</h2>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green'>✅ Conexão com banco OK</p>";
    
    // Test query
    $stmt = $db->query("SELECT COUNT(*) as total FROM clientes");
    $result = $stmt->fetch();
    echo "<p>Total de clientes: " . $result['total'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red'>❌ Erro na conexão: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>4. Estrutura de Arquivos</h2>";
$files_to_check = [
    'config/database.php',
    'includes/auth_check.php',
    'views/clientes/novo.php',
    'views/clientes/index.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green'>✅ $file existe</p>";
    } else {
        echo "<p style='color: red'>❌ $file não encontrado</p>";
    }
}

echo "<h2>5. Links de Teste</h2>";
echo "<ul>";
echo "<li><a href='views/clientes/index.php'>Lista de Clientes</a></li>";
echo "<li><a href='views/clientes/novo.php'>Novo Cliente</a></li>";
echo "<li><a href='login.php'>Login</a></li>";
echo "</ul>";

echo "</body></html>";
?>