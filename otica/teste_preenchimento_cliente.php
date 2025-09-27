<?php
/**
 * Teste do Preenchimento Automático de Cliente
 * Verifica se a funcionalidade de preenchimento automático está funcionando
 */

// Verificar autenticação
require_once '../includes/auth_check.php';

// Conectar ao banco de dados
require_once '../config/database.php';
$db = Database::getInstance()->getConnection();

echo "<h1>🧪 Teste de Preenchimento Automático - Cliente</h1>";
echo "<hr>";

try {
    // Buscar um cliente de exemplo
    $stmt = $db->prepare("SELECT id, nome, telefone, cpf, email, endereco, bairro, numero FROM clientes LIMIT 1");
    $stmt->execute();
    $cliente_exemplo = $stmt->fetch();
    
    if ($cliente_exemplo) {
        echo "<h2>✅ Cliente de Exemplo Encontrado</h2>";
        echo "<p><strong>ID:</strong> " . $cliente_exemplo['id'] . "</p>";
        echo "<p><strong>Nome:</strong> " . $cliente_exemplo['nome'] . "</p>";
        echo "<p><strong>Telefone:</strong> " . ($cliente_exemplo['telefone'] ?? 'Não informado') . "</p>";
        echo "<p><strong>CPF:</strong> " . ($cliente_exemplo['cpf'] ?? 'Não informado') . "</p>";
        echo "<p><strong>Email:</strong> " . ($cliente_exemplo['email'] ?? 'Não informado') . "</p>";
        echo "<p><strong>Endereço:</strong> " . ($cliente_exemplo['endereco'] ?? 'Não informado') . "</p>";
        echo "<p><strong>Bairro:</strong> " . ($cliente_exemplo['bairro'] ?? 'Não informado') . "</p>";
        echo "<p><strong>Número:</strong> " . ($cliente_exemplo['numero'] ?? 'Não informado') . "</p>";
        
        echo "<h2>🔗 Teste da API AJAX</h2>";
        echo "<p>URL de teste: <code>nova.php?action=get_cliente&cliente_id=" . $cliente_exemplo['id'] . "</code></p>";
        
        // Simular requisição AJAX
        $_GET['action'] = 'get_cliente';
        $_GET['cliente_id'] = $cliente_exemplo['id'];
        
        $stmt = $db->prepare("SELECT nome, telefone, cpf, email, endereco, bairro, numero FROM clientes WHERE id = ?");
        $stmt->execute([$_GET['cliente_id']]);
        $cliente = $stmt->fetch();
        
        if ($cliente) {
            $response = [
                'success' => true,
                'cliente' => $cliente
            ];
            echo "<h3>✅ Resposta JSON:</h3>";
            echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        } else {
            echo "<p>❌ Cliente não encontrado na simulação</p>";
        }
        
    } else {
        echo "<h2>❌ Nenhum Cliente Encontrado</h2>";
        echo "<p>Para testar a funcionalidade, é necessário ter pelo menos um cliente cadastrado.</p>";
        echo "<p><a href='../views/clientes/novo.php'>Cadastrar Cliente</a></p>";
    }
    
    echo "<h2>📋 Estrutura da Tabela Clientes</h2>";
    $stmt = $db->query("DESCRIBE clientes");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $required_fields = ['nome', 'telefone', 'cpf', 'email', 'endereco', 'bairro', 'numero'];
    $missing_fields = [];
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
        
        // Remover campo da lista de obrigatórios se ele existir
        if (($key = array_search($column['Field'], $required_fields)) !== false) {
            unset($required_fields[$key]);
        }
    }
    echo "</table>";
    
    if (!empty($required_fields)) {
        echo "<h3>⚠️ Campos Ausentes</h3>";
        echo "<p>Os seguintes campos são necessários para o preenchimento automático mas não existem na tabela:</p>";
        echo "<ul>";
        foreach ($required_fields as $field) {
            echo "<li><code>$field</code></li>";
        }
        echo "</ul>";
        echo "<p><strong>Solução:</strong> Estes campos podem ser adicionados à tabela clientes ou o sistema funcionará apenas com os campos disponíveis.</p>";
    } else {
        echo "<h3>✅ Todos os Campos Necessários Estão Disponíveis</h3>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>🚀 Como Testar</h2>";
echo "<ol>";
echo "<li>Acesse a página: <a href='views/receitas/nova.php'>Nova Receita</a></li>";
echo "<li>Selecione um cliente no dropdown</li>";
echo "<li>Observe os campos sendo preenchidos automaticamente</li>";
echo "<li>Verifique o console do navegador (F12) para logs de debug</li>";
echo "</ol>";

echo "<p style='margin-top: 20px;'>";
echo "<a href='views/receitas/nova.php' style='background: #28d2c3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🧾 Testar Nova Receita</a>";
echo "</p>";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Preenchimento Cliente - Ótica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
            line-height: 1.6;
        }
        h1, h2, h3 {
            color: #28d2c3;
        }
        h1 {
            border-bottom: 3px solid #28d2c3;
            padding-bottom: 10px;
        }
        table {
            margin: 10px 0;
            font-size: 14px;
        }
        th {
            background-color: #28d2c3;
            color: white;
            padding: 8px;
        }
        td {
            padding: 6px 8px;
            background-color: white;
        }
        pre {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 14px;
        }
        code {
            background: #e9ecef;
            padding: 2px 4px;
            border-radius: 3px;
            font-family: monospace;
        }
        hr {
            border: none;
            height: 2px;
            background: #28d2c3;
            margin: 20px 0;
        }
        a {
            color: #20b8a9;
        }
    </style>
</head>
<body>
    <!-- Conteúdo PHP aqui -->
</body>
</html>