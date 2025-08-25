<?php
/**
 * Teste de Conexão com o Banco de Dados
 * Sistema Ótica
 */

// Incluir arquivo de configuração
require_once 'otica/config/database.php';

echo "<h2>Teste de Conexão com o Banco de Dados</h2>";
echo "<hr>";

try {
    // Obter instância da conexão
    $db = Database::getInstance();
    
    // Testar conexão
    if ($db->testConnection()) {
        echo "<p style='color: green;'>✅ <strong>Conexão bem-sucedida!</strong></p>";
        echo "<p>Banco de dados: " . DB_NAME . "</p>";
        echo "<p>Host: " . DB_HOST . "</p>";
        echo "<p>Usuário: " . DB_USER . "</p>";
        
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
echo "<p><a href='index.php'>Voltar ao sistema</a></p>";
?> 