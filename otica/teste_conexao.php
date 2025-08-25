<?php
/**
 * Teste de Conexão com o Banco de Dados
 * Sistema Ótica
 */

// Incluir arquivo de configuração
require_once 'config/database.php';

echo "<h2>🔧 Teste de Conexão com o Banco de Dados</h2>";
echo "<hr>";

try {
    // Obter instância da conexão
    $db = Database::getInstance();
    
    // Testar conexão
    if ($db->testConnection()) {
        echo "<p style='color: green;'>✅ <strong>Conexão bem-sucedida!</strong></p>";
        echo "<p><strong>Banco de dados:</strong> " . DB_NAME . "</p>";
        echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
        echo "<p><strong>Usuário:</strong> " . DB_USER . "</p>";
        
        // Testar uma consulta simples
        $connection = $db->getConnection();
        $stmt = $connection->query("SELECT VERSION() as version");
        $result = $stmt->fetch();
        
        echo "<p><strong>Versão do MySQL:</strong> " . $result['version'] . "</p>";
        
        // Verificar se as tabelas existem
        $stmt = $connection->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        
        if (count($tables) > 0) {
            echo "<p><strong>Tabelas encontradas:</strong></p>";
            echo "<ul>";
            foreach ($tables as $table) {
                $tableName = array_values($table)[0];
                echo "<li>" . $tableName . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: orange;'>⚠️ Nenhuma tabela encontrada no banco de dados.</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ <strong>Falha na conexão!</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Possíveis soluções:</strong></p>";
    echo "<ul>";
    echo "<li>Verifique se o MySQL está rodando</li>";
    echo "<li>Verifique se o banco 'otica_db' existe</li>";
    echo "<li>Verifique as credenciais no arquivo config/database.php</li>";
    echo "<li>Verifique se o XAMPP está ativo</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='login.php'>📝 Ir para o Login</a> | <a href='criar_admin.php'>👑 Criar Administrador</a> | <a href='index.php'>🏠 Página Inicial</a></p>";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Conexão - Sistema de Ótica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        h3 {
            color: #555;
            margin-top: 30px;
        }
        table {
            margin-top: 15px;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .warning {
            color: #ffc107;
            font-weight: bold;
        }
        code {
            background: #f8f9fa;
            padding: 2px 4px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- O conteúdo PHP será exibido aqui -->
    </div>
</body>
</html>
