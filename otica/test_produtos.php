<?php
/**
 * Teste da funcionalidade de produtos
 */
require_once 'config/database.php';
require_once 'models/BaseModel.php';
require_once 'models/Produto.php';

echo "<h1>Teste da Funcionalidade de Produtos</h1>";

try {
    // Testar conexão com banco
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✅ Conexão com banco de dados OK</p>";
    
    // Testar modelo de produto
    $produtoModel = new Produto();
    echo "<p style='color: green;'>✅ Modelo de produto carregado OK</p>";
    
    // Testar busca de produtos
    $produtos = $produtoModel->getAllWithDetails();
    echo "<p style='color: green;'>✅ Busca de produtos OK - Encontrados: " . count($produtos) . " produtos</p>";
    
    // Mostrar produtos existentes
    if (!empty($produtos)) {
        echo "<h2>Produtos Cadastrados:</h2>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Código</th><th>Nome</th><th>Tipo</th><th>Marca</th><th>Preço</th><th>Estoque</th></tr>";
        
        foreach ($produtos as $produto) {
            echo "<tr>";
            echo "<td>" . $produto['id'] . "</td>";
            echo "<td>" . $produto['codigo'] . "</td>";
            echo "<td>" . $produto['nome'] . "</td>";
            echo "<td>" . $produto['tipo'] . "</td>";
            echo "<td>" . $produto['marca'] . "</td>";
            echo "<td>R$ " . number_format($produto['preco'], 2, ',', '.') . "</td>";
            echo "<td>" . $produto['estoque'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ Nenhum produto cadastrado ainda</p>";
    }
    
    // Testar criação de produto
    $testData = [
        'nome' => 'Óculos Teste',
        'codigo' => '123456789',
        'descricao' => 'Óculos de teste para verificação',
        'tipo' => 'Óculos de Grau',
        'marca' => 'Teste',
        'modelo' => 'Teste-001',
        'cor' => 'Preto',
        'preco' => 150.00,
        'estoque' => 10
    ];
    
    // Verificar se código já existe
    if (!$produtoModel->codigoExists($testData['codigo'])) {
        if ($produtoModel->create($testData)) {
            echo "<p style='color: green;'>✅ Produto de teste criado com sucesso</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro ao criar produto de teste</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Produto de teste já existe</p>";
    }
    
    // Testar validação
    $errors = $produtoModel->validate($testData);
    if (empty($errors)) {
        echo "<p style='color: green;'>✅ Validação de dados OK</p>";
    } else {
        echo "<p style='color: red;'>❌ Erros de validação:</p>";
        foreach ($errors as $field => $error) {
            echo "<p style='color: red;'>- $field: $error</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='produtos.php'>Ir para Gerenciamento de Produtos</a></p>";
echo "<p><a href='views/admin/index.php'>Voltar ao Painel</a></p>";
?>

