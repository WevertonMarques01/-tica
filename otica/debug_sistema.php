<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();

echo "<h2>Verificação do Sistema</h2>";

try {
    $stmt = $db->query("SHOW TABLES LIKE 'vendas'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>✓ Tabela 'vendas' existe</p>";
        
        $stmt = $db->query("SELECT COUNT(*) as total FROM vendas");
        $result = $stmt->fetch();
        echo "<p>Vendas no banco: " . $result['total'] . "</p>";
        
        if ($result['total'] > 0) {
            $stmt = $db->query("SELECT * FROM vendas ORDER BY id DESC LIMIT 5");
            echo "<h3>Últimas vendas:</h3>";
            echo "<pre>";
            print_r($stmt->fetchAll());
            echo "</pre>";
        }
    } else {
        echo "<p style='color:red'>✗ Tabela 'vendas' NÃO existe</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Erro: " . $e->getMessage() . "</p>";
}

try {
    $stmt = $db->query("SHOW TABLES LIKE 'comprovantes_pagamento'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>✓ Tabela 'comprovantes_pagamento' existe</p>";
    } else {
        echo "<p style='color:red'>✗ Tabela 'comprovantes_pagamento' NÃO existe</p>";
        echo "<p>Execute: <code>install_comprovantes.php</code></p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Erro: " . $e->getMessage() . "</p>";
}

try {
    $stmt = $db->query("SHOW TABLES LIKE 'clientes'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>✓ Tabela 'clientes' existe</p>";
        
        $stmt = $db->query("SELECT id, nome FROM clientes");
        $clientes = $stmt->fetchAll();
        echo "<p>Clientes: " . count($clientes) . "</p>";
        foreach ($clientes as $c) {
            echo "- ID " . $c['id'] . ": " . $c['nome'] . "<br>";
        }
    } else {
        echo "<p style='color:red'>✗ Tabela 'clientes' NÃO existe</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Erro: " . $e->getMessage() . "</p>";
}
?>