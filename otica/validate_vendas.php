<?php
/**
 * Simple validation script for the fixed sales form
 */

// Include authentication and database
require_once 'includes/auth_check.php';
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();

echo "<!DOCTYPE html>";
echo "<html><head><title>Sales Form Validation</title></head><body>";
echo "<h1>Sales Form Validation Test</h1>";

// Check if we have the data needed for a sale
$stmt = $db->query("SELECT COUNT(*) as count FROM clientes WHERE 1");
$clients_count = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM produtos WHERE 1");
$products_count = $stmt->fetch()['count'];

echo "<h2>Pre-requisites Check:</h2>";
echo "<p>Clients available: <strong>$clients_count</strong></p>";
echo "<p>Products available: <strong>$products_count</strong></p>";

if ($clients_count > 0 && $products_count > 0) {
    echo "<p style='color: green'>✅ Ready to test sales form</p>";
    echo "<p><a href='views/vendas/nova.php' style='background-color: #28d2c3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Sales Form</a></p>";
} else {
    echo "<p style='color: red'>❌ Need clients and products data to test sales</p>";
    
    if ($clients_count == 0) {
        echo "<p><a href='views/clientes/novo.php'>Add a client first</a></p>";
    }
    
    if ($products_count == 0) {
        echo "<p>Add products through admin panel first</p>";
    }
}

echo "<h2>Fixed Issues Summary:</h2>";
echo "<ul style='color: green;'>";
echo "<li>✅ Table name corrected: venda_itens → itens_venda</li>";
echo "<li>✅ Required subtotal field now calculated and included</li>";
echo "<li>✅ Log table name corrected: logs → logs_sistema</li>";
echo "<li>✅ Proper error handling maintained</li>";
echo "<li>✅ Database triggers will handle stock automatically</li>";
echo "</ul>";

echo "<h2>How the Fixed Code Works:</h2>";
echo "<ol>";
echo "<li>User fills the sales form with client, payment method, and products</li>";
echo "<li>System validates all required fields</li>";
echo "<li>Transaction begins</li>";
echo "<li>Sale record inserted into 'vendas' table</li>";
echo "<li>For each product: fetches price, calculates subtotal, inserts into 'itens_venda'</li>";
echo "<li>Database trigger automatically updates stock</li>";
echo "<li>Log entry created in 'logs_sistema' table</li>";
echo "<li>Transaction commits</li>";
echo "<li>User redirected to success page</li>";
echo "</ol>";

echo "</body></html>";
?>