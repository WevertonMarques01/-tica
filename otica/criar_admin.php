<?php
/**
 * Script para criar conta do dono/administrador geral
 * Sistema de Ótica
 */

// Incluir configuração do banco
require_once 'config/database.php';

try {
    // Conectar ao banco
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🚀 Criando Conta do Dono/Administrador Geral</h2>";
    echo "<hr>";
    
    // Verificar se o banco existe
    $stmt = $pdo->query("SELECT DATABASE() as current_db");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>Banco atual:</strong> " . $result['current_db'] . "</p>";
    
    // Verificar se a tabela usuarios existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Tabela 'usuarios' encontrada!</p>";
        
        // Verificar se o usuário dono já existe
        $stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios WHERE email = ?");
        $stmt->execute(['dono@otica.com']);
        $usuarioExistente = $stmt->fetch();
        
        if ($usuarioExistente) {
            echo "<p style='color: orange;'>⚠️ Usuário dono já existe!</p>";
            echo "<p><strong>ID:</strong> " . $usuarioExistente['id'] . "</p>";
            echo "<p><strong>Nome:</strong> " . $usuarioExistente['nome'] . "</p>";
            echo "<p><strong>Email:</strong> " . $usuarioExistente['email'] . "</p>";
        } else {
            // Criar usuário dono
            $senhaHash = password_hash('admin123', PASSWORD_DEFAULT);
            
                         $stmt = $pdo->prepare("
                 INSERT INTO usuarios (nome, email, senha, perfil, ativo, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, NOW(), NOW())
             ");
             
             $stmt->execute([
                 'Dono da Ótica',
                 'dono@otica.com',
                 $senhaHash,
                 'admin', // perfil = admin
                 1  // ativo = 1
             ]);
            
            $usuarioId = $pdo->lastInsertId();
            
            echo "<p style='color: green;'>✅ Usuário dono criado com sucesso!</p>";
            echo "<p><strong>ID:</strong> " . $usuarioId . "</p>";
            echo "<p><strong>Nome:</strong> Dono da Ótica</p>";
            echo "<p><strong>Email:</strong> dono@otica.com</p>";
            echo "<p><strong>Senha:</strong> admin123</p>";
            echo "<p><strong>Perfil:</strong> Administrador</p>";
        }
        
        // Mostrar todos os usuários cadastrados
        echo "<hr>";
        echo "<h3>👥 Usuários Cadastrados:</h3>";
        
                 $stmt = $pdo->query("SELECT id, nome, email, perfil, ativo, created_at FROM usuarios ORDER BY id");
        $usuarios = $stmt->fetchAll();
        
        if (count($usuarios) > 0) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-top: 10px;'>";
            echo "<tr style='background-color: #f0f0f0;'>";
            echo "<th style='padding: 8px; text-align: left;'>ID</th>";
            echo "<th style='padding: 8px; text-align: left;'>Nome</th>";
            echo "<th style='padding: 8px; text-align: left;'>Email</th>";
                         echo "<th style='padding: 8px; text-align: left;'>Perfil</th>";
            echo "<th style='padding: 8px; text-align: left;'>Status</th>";
            echo "<th style='padding: 8px; text-align: left;'>Criado em</th>";
            echo "</tr>";
            
                         foreach ($usuarios as $usuario) {
                 $perfil = ucfirst($usuario['perfil']);
                 $status = $usuario['ativo'] == 1 ? 'Ativo' : 'Inativo';
                 $statusColor = $usuario['ativo'] == 1 ? 'green' : 'red';
                 
                 echo "<tr>";
                 echo "<td style='padding: 8px;'>" . $usuario['id'] . "</td>";
                 echo "<td style='padding: 8px;'>" . htmlspecialchars($usuario['nome']) . "</td>";
                 echo "<td style='padding: 8px;'>" . htmlspecialchars($usuario['email']) . "</td>";
                 echo "<td style='padding: 8px;'>" . $perfil . "</td>";
                 echo "<td style='padding: 8px; color: " . $statusColor . ";'>" . $status . "</td>";
                 echo "<td style='padding: 8px;'>" . date('d/m/Y H:i', strtotime($usuario['created_at'])) . "</td>";
                 echo "</tr>";
             }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>❌ Nenhum usuário encontrado!</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Tabela 'usuarios' não encontrada!</p>";
        echo "<p>Execute primeiro o script <strong>otica_db_v2.sql</strong> para criar o banco de dados.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ <strong>Erro de conexão:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Possíveis soluções:</strong></p>";
    echo "<ul>";
    echo "<li>Verifique se o MySQL está rodando</li>";
    echo "<li>Verifique se o banco 'otica_db' existe</li>";
    echo "<li>Verifique as credenciais no arquivo config/database.php</li>";
    echo "<li>Verifique se o XAMPP está ativo</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h3>🔗 Links Importantes:</h3>";
echo "<ul>";
echo "<li><a href='login.php' style='color: blue;'>📝 Página de Login</a></li>";
echo "<li><a href='index.php' style='color: blue;'>🏠 Página Inicial</a></li>";
echo "<li><a href='views/admin/index.php' style='color: blue;'>⚙️ Painel Administrativo</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h3>📋 Credenciais do Dono:</h3>";
echo "<div style='background-color: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Email:</strong> dono@otica.com</p>";
echo "<p><strong>Senha:</strong> admin123</p>";
echo "<p><strong>Perfil:</strong> Administrador (acesso total)</p>";
echo "</div>";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Administrador - Sistema de Ótica</title>
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
    </style>
</head>
<body>
    <div class="container">
        <!-- O conteúdo PHP será exibido aqui -->
    </div>
</body>
</html>
