<?php
// Test file to debug product deletion routing
echo "<!DOCTYPE html>";
echo "<html><head><title>Test Product Deletion</title></head><body>";
echo "<h1>Product Deletion Route Test</h1>";

// Test basic includes
echo "<h2>1. Testing Includes:</h2>";
try {
    echo "<p>✓ Testing auth_check...</p>";
    require_once 'includes/auth_check.php';
    echo "<p style='color: green;'>✓ Auth check OK</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Auth check error: " . $e->getMessage() . "</p>";
}

try {
    echo "<p>✓ Testing ProdutoController...</p>";
    require_once 'controllers/ProdutoController.php';
    echo "<p style='color: green;'>✓ ProdutoController OK</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ ProdutoController error: " . $e->getMessage() . "</p>";
}

// Test controller instantiation
echo "<h2>2. Testing Controller:</h2>";
try {
    $controller = new ProdutoController();
    echo "<p style='color: green;'>✓ Controller instantiated successfully</p>";
    
    // Test if excluir method exists
    if (method_exists($controller, 'excluir')) {
        echo "<p style='color: green;'>✓ excluir() method exists</p>";
    } else {
        echo "<p style='color: red;'>✗ excluir() method does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Controller error: " . $e->getMessage() . "</p>";
}

// Test database connection
echo "<h2>3. Testing Database:</h2>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✓ Database connection OK</p>";
    
    // Check produtos table
    $stmt = $db->query("SHOW TABLES LIKE 'produtos'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ produtos table exists</p>";
        
        // Check table structure
        $stmt = $db->query("DESCRIBE produtos");
        $fields = $stmt->fetchAll();
        echo "<p style='color: blue;'>ℹ Table fields: " . implode(', ', array_column($fields, 'Field')) . "</p>";
        
        // Count products
        $stmt = $db->query("SELECT COUNT(*) FROM produtos");
        $count = $stmt->fetchColumn();
        echo "<p style='color: blue;'>ℹ Total products: $count</p>";
        
    } else {
        echo "<p style='color: red;'>✗ produtos table does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test routing simulation
echo "<h2>4. Testing Routing:</h2>";
echo "<p>Current GET parameters:</p>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<p>Testing action parameter:</p>";
$action = $_GET['action'] ?? 'none';
echo "<p>Action: $action</p>";

echo "<h2>5. Test Links:</h2>";
echo "<p><a href='produtos.php?action=index'>→ Test Index Action</a></p>";
echo "<p><a href='produtos.php?action=novo'>→ Test Novo Action</a></p>";
echo "<p><a href='produtos.php?action=excluir&id=1'>→ Test Excluir Action (ID: 1)</a></p>";
echo "<p><a href='views/produtos/index.php'>→ Direct View Access</a></p>";

echo "</body></html>";
?>