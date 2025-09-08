<?php
/**
 * Teste de Login
 * Sistema Ótica
 */

// Incluir arquivo de configuração
require_once 'otica/config/database.php';

echo "<h2>Teste de Login - Sistema Ótica</h2>";
echo "<hr>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Testar conexão
    if ($db) {
        echo "<p style='color: green;'>✅ <strong>Conexão com banco estabelecida!</strong></p>";
        
        // Verificar usuários cadastrados
        $stmt = $db->query("SELECT id, nome, email, perfil FROM usuarios");
        $usuarios = $stmt->fetchAll();
        
        echo "<p><strong>Usuários cadastrados:</strong></p>";
        echo "<ul>";
        foreach ($usuarios as $usuario) {
            echo "<li><strong>" . htmlspecialchars($usuario['nome']) . "</strong> (" . htmlspecialchars($usuario['email']) . ") - Perfil: " . htmlspecialchars($usuario['perfil']) . "</li>";
        }
        echo "</ul>";
        
        // Testar login com senha conhecida
        $email_teste = 'admin@otica.com';
        $senha_teste = 'password'; // Senha padrão comum
        
        $stmt = $db->prepare("SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = ?");
        $stmt->execute([$email_teste]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            echo "<p><strong>Testando login para:</strong> " . htmlspecialchars($usuario['email']) . "</p>";
            
            if (password_verify($senha_teste, $usuario['senha'])) {
                echo "<p style='color: green;'>✅ <strong>Login bem-sucedido!</strong></p>";
                echo "<p>Usuário: " . htmlspecialchars($usuario['nome']) . "</p>";
                echo "<p>Perfil: " . htmlspecialchars($usuario['perfil']) . "</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ <strong>Senha incorreta para o teste.</strong></p>";
                echo "<p>Dica: A senha padrão pode ser diferente de 'password'</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ <strong>Usuário não encontrado!</strong></p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ <strong>Falha na conexão!</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Credenciais para teste:</h3>";
echo "<ul>";
echo "<li><strong>Email:</strong> admin@otica.com</li>";
echo "<li><strong>Email:</strong> dono@otica.com</li>";
echo "<li><strong>Senha:</strong> (verifique no banco de dados ou use senha padrão)</li>";
echo "</ul>";

echo "<p><a href='otica/login.php'>Ir para página de login</a></p>";
echo "<p><a href='test_connection.php'>Testar conexão do banco</a></p>";
?>
