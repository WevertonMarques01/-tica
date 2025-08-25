<?php
/**
 * Diagnóstico Completo do Banco de Dados
 * Sistema de Ótica
 */

// Incluir configuração do banco
require_once 'config/database.php';

echo "<h2>🔍 Diagnóstico Completo do Banco de Dados</h2>";
echo "<hr>";

try {
    // Conectar ao banco
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ <strong>Conexão estabelecida!</strong></p>";
    
    // 1. Verificar estrutura da tabela usuarios
    echo "<h3>📋 Estrutura da Tabela 'usuarios':</h3>";
    $stmt = $pdo->query("DESCRIBE usuarios");
    $colunas = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th style='padding: 8px;'>Campo</th>";
    echo "<th style='padding: 8px;'>Tipo</th>";
    echo "<th style='padding: 8px;'>Null</th>";
    echo "<th style='padding: 8px;'>Chave</th>";
    echo "<th style='padding: 8px;'>Padrão</th>";
    echo "</tr>";
    
    foreach ($colunas as $coluna) {
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . $coluna['Field'] . "</td>";
        echo "<td style='padding: 8px;'>" . $coluna['Type'] . "</td>";
        echo "<td style='padding: 8px;'>" . $coluna['Null'] . "</td>";
        echo "<td style='padding: 8px;'>" . $coluna['Key'] . "</td>";
        echo "<td style='padding: 8px;'>" . $coluna['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 2. Verificar dados na tabela usuarios
    echo "<h3>👥 Dados na Tabela 'usuarios':</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $total = $stmt->fetch();
    
    echo "<p><strong>Total de usuários:</strong> " . $total['total'] . "</p>";
    
    if ($total['total'] > 0) {
        $stmt = $pdo->query("SELECT id, nome, email, perfil, ativo, created_at FROM usuarios ORDER BY id");
        $usuarios = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>ID</th>";
        echo "<th style='padding: 8px;'>Nome</th>";
        echo "<th style='padding: 8px;'>Email</th>";
        echo "<th style='padding: 8px;'>Perfil</th>";
        echo "<th style='padding: 8px;'>Ativo</th>";
        echo "<th style='padding: 8px;'>Criado em</th>";
        echo "</tr>";
        
        foreach ($usuarios as $usuario) {
            $status = $usuario['ativo'] == 1 ? 'Sim' : 'Não';
            $statusColor = $usuario['ativo'] == 1 ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td style='padding: 8px;'>" . $usuario['id'] . "</td>";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($usuario['nome']) . "</td>";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($usuario['email']) . "</td>";
            echo "<td style='padding: 8px;'>" . htmlspecialchars($usuario['perfil']) . "</td>";
            echo "<td style='padding: 8px; color: " . $statusColor . ";'>" . $status . "</td>";
            echo "<td style='padding: 8px;'>" . date('d/m/Y H:i', strtotime($usuario['created_at'])) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 3. Testar consulta de login
    echo "<h3>🔐 Teste de Consulta de Login:</h3>";
    
    // Testar com email existente
    $stmt = $pdo->prepare("SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = ? AND ativo = 1");
    $stmt->execute(['dono@otica.com']);
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        echo "<p style='color: green;'>✅ Usuário 'dono@otica.com' encontrado!</p>";
        echo "<ul>";
        echo "<li><strong>ID:</strong> " . $usuario['id'] . "</li>";
        echo "<li><strong>Nome:</strong> " . htmlspecialchars($usuario['nome']) . "</li>";
        echo "<li><strong>Email:</strong> " . htmlspecialchars($usuario['email']) . "</li>";
        echo "<li><strong>Perfil:</strong> " . htmlspecialchars($usuario['perfil']) . "</li>";
        echo "<li><strong>Senha hash:</strong> " . substr($usuario['senha'], 0, 20) . "...</li>";
        echo "</ul>";
        
        // Testar verificação de senha
        if (password_verify('admin123', $usuario['senha'])) {
            echo "<p style='color: green;'>✅ Senha 'admin123' é válida!</p>";
        } else {
            echo "<p style='color: red;'>❌ Senha 'admin123' é inválida!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Usuário 'dono@otica.com' não encontrado!</p>";
    }
    
    // 4. Verificar outras tabelas importantes
    echo "<h3>📊 Outras Tabelas do Sistema:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tabelas = $stmt->fetchAll();
    
    echo "<ul>";
    foreach ($tabelas as $tabela) {
        $nomeTabela = array_values($tabela)[0];
        
        // Contar registros
        $stmt2 = $pdo->query("SELECT COUNT(*) as total FROM `$nomeTabela`");
        $total = $stmt2->fetch();
        
        echo "<li><strong>$nomeTabela:</strong> " . $total['total'] . " registros</li>";
    }
    echo "</ul>";
    
    // 5. Verificar configurações do sistema
    echo "<h3>⚙️ Configurações do Sistema:</h3>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> " . DB_HOST . "</li>";
    echo "<li><strong>Banco:</strong> " . DB_NAME . "</li>";
    echo "<li><strong>Usuário:</strong> " . DB_USER . "</li>";
    echo "<li><strong>Charset:</strong> " . DB_CHARSET . "</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ <strong>Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🔗 Links Importantes:</h3>";
echo "<ul>";
echo "<li><a href='login.php' style='color: blue;'>📝 Testar Login</a></li>";
echo "<li><a href='criar_admin.php' style='color: blue;'>👑 Criar Administrador</a></li>";
echo "<li><a href='teste_conexao.php' style='color: blue;'>🔧 Teste de Conexão</a></li>";
echo "</ul>";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico - Sistema de Ótica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
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
        h2, h3 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        table {
            margin: 15px 0;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- O conteúdo PHP será exibido aqui -->
    </div>
</body>
</html>
