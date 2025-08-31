<?php
/**
 * Atualizar Senha do Usuário Admin
 * Sistema Ótica
 */

// Incluir arquivo de configuração
require_once 'otica/config/database.php';

echo "<h2>Atualizando Senha do Usuário Admin</h2>";
echo "<hr>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Nova senha
    $nova_senha = '123456';
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    
    // Atualizar senha do admin
    $stmt = $db->prepare("UPDATE usuarios SET senha = ? WHERE email = 'admin@otica.com'");
    $result = $stmt->execute([$senha_hash]);
    
    if ($result) {
        echo "<p style='color: green;'>✅ <strong>Senha atualizada com sucesso!</strong></p>";
        echo "<p><strong>Email:</strong> admin@otica.com</p>";
        echo "<p><strong>Nova senha:</strong> " . $nova_senha . "</p>";
        echo "<p><strong>Hash gerado:</strong> " . substr($senha_hash, 0, 20) . "...</p>";
        
        // Verificar se foi atualizado
        $stmt = $db->prepare("SELECT nome, email FROM usuarios WHERE email = 'admin@otica.com'");
        $stmt->execute();
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            echo "<p><strong>Usuário:</strong> " . htmlspecialchars($usuario['nome']) . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ <strong>Erro ao atualizar senha!</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Credenciais para login:</h3>";
echo "<ul>";
echo "<li><strong>Email:</strong> admin@otica.com</li>";
echo "<li><strong>Senha:</strong> 123456</li>";
echo "</ul>";

echo "<p><a href='otica/login.php'>Ir para página de login</a></p>";
echo "<p><a href='test_login.php'>Testar login</a></p>";
?>
