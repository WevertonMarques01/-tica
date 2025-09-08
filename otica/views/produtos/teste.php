<?php
// Teste simples para verificar se as páginas de produtos estão funcionando
echo "<!DOCTYPE html>";
echo "<html><head><title>Teste Produtos</title></head><body>";
echo "<h1>Teste do Sistema de Produtos</h1>";

// Verificar se os arquivos existem
$arquivos = [
    'index.php',
    'novo.php', 
    'excluir.php',
    'visualizar.php',
    'verificar_codigo.php'
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
    
    // Verificar se a tabela produtos existe
    $stmt = $db->query("SHOW TABLES LIKE 'produtos'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Tabela 'produtos': OK</p>";
        
        // Verificar estrutura da tabela
        $stmt = $db->query("DESCRIBE produtos");
        $campos = $stmt->fetchAll();
        echo "<p style='color: green;'>✓ Campos da tabela produtos: " . count($campos) . " campos encontrados</p>";
        echo "<p style='color: blue;'>ℹ Campos: " . implode(', ', array_column($campos, 'Field')) . "</p>";
        
        // Contar registros
        $stmt = $db->query("SELECT COUNT(*) FROM produtos");
        $count = $stmt->fetchColumn();
        echo "<p style='color: blue;'>ℹ Total de produtos cadastrados: $count</p>";
        
        // Verificar campo de estoque
        $temEstoque = in_array('estoque', array_column($campos, 'Field'));
        $temEstoqueAtual = in_array('estoque_atual', array_column($campos, 'Field'));
        echo "<p style='color: " . ($temEstoque ? 'green' : 'orange') . "'>Campo 'estoque': " . ($temEstoque ? 'SIM' : 'NÃO') . "</p>";
        echo "<p style='color: " . ($temEstoqueAtual ? 'green' : 'orange') . "'>Campo 'estoque_atual': " . ($temEstoqueAtual ? 'SIM' : 'NÃO') . "</p>";
        
    } else {
        echo "<p style='color: orange;'>⚠ Tabela 'produtos': NÃO ENCONTRADA</p>";
    }
    
    // Verificar dependências (tabelas relacionadas)
    $tabelas_relacionadas = ['itens_venda', 'movimentacao_estoque', 'marcas', 'categorias_produtos'];
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

// Testar roteamento
echo "<h2>Teste de Roteamento:</h2>";
echo "<p><a href='../../produtos.php?action=index'>→ Controller Route (Index)</a></p>";
echo "<p><a href='index.php'>→ Direct View (Index)</a></p>";
echo "<p><a href='../../produtos.php?action=novo'>→ Controller Route (Novo)</a></p>";
echo "<p><a href='novo.php'>→ Direct View (Novo)</a></p>";

echo "<h2>Links de Teste:</h2>";
echo "<p><a href='index.php'>→ Lista de Produtos</a></p>";
echo "<p><a href='novo.php'>→ Cadastrar Novo Produto</a></p>";

echo "<h2>Teste de URLs de Ação:</h2>";
echo "<p>Para testar, substitua {ID} por um ID válido:</p>";
echo "<ul>";
echo "<li><a href='visualizar.php?id=1'>→ Visualizar Produto (ID: 1)</a></li>";
echo "<li><a href='../../produtos.php?action=editar&id=1'>→ Editar Produto (ID: 1)</a></li>";
echo "<li style='color: orange;'>→ Excluir Produto (ID: 1) - Use com cuidado!</li>";
echo "</ul>";

echo "</body></html>";
?>