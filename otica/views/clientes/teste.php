<?php
// Teste simples para verificar se as páginas de clientes estão funcionando
echo "<!DOCTYPE html>";
echo "<html><head><title>Teste Clientes</title></head><body>";
echo "<h1>Teste do Sistema de Clientes</h1>";

// Verificar se os arquivos existem
$arquivos = [
    'index.php',
    'novo.php', 
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
    
    // Verificar se a tabela clientes existe
    $stmt = $db->query("SHOW TABLES LIKE 'clientes'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Tabela 'clientes': OK</p>";
        
        // Verificar estrutura da tabela
        $stmt = $db->query("DESCRIBE clientes");
        $campos = $stmt->fetchAll();
        echo "<p style='color: green;'>✓ Campos da tabela clientes: " . count($campos) . " campos encontrados</p>";
        
        // Contar registros
        $stmt = $db->query("SELECT COUNT(*) FROM clientes");
        $count = $stmt->fetchColumn();
        echo "<p style='color: blue;'>ℹ Total de clientes cadastrados: $count</p>";
        
    } else {
        echo "<p style='color: orange;'>⚠ Tabela 'clientes': NÃO ENCONTRADA</p>";
    }
    
    // Verificar dependências (tabelas relacionadas)
    $tabelas_relacionadas = ['vendas', 'receitas', 'ordens_servico'];
    echo "<h3>Tabelas Relacionadas:</h3>";
    foreach ($tabelas_relacionadas as $tabela) {
        $stmt = $db->query("SHOW TABLES LIKE '$tabela'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✓ Tabela '$tabela': OK</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Tabela '$tabela': NÃO ENCONTRADA</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erro de conexão: " . $e->getMessage() . "</p>";
}

echo "<h2>Links de Teste:</h2>";
echo "<p><a href='index.php'>→ Lista de Clientes</a></p>";
echo "<p><a href='novo.php'>→ Cadastrar Novo Cliente</a></p>";

echo "<h2>Teste de URLs de Ação:</h2>";
echo "<p>Para testar, substitua {ID} por um ID válido:</p>";
echo "<ul>";
echo "<li><a href='visualizar.php?id=1'>→ Visualizar Cliente (ID: 1)</a></li>";
echo "<li><a href='editar.php?id=1'>→ Editar Cliente (ID: 1)</a></li>";
echo "<li style='color: orange;'>→ Excluir Cliente (ID: 1) - Use com cuidado!</li>";
echo "</ul>";

echo "</body></html>";
?>