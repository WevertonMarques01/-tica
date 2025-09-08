<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Verificar se foi passado um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=id_invalido');
    exit;
}

$id = (int)$_GET['id'];

// Conectar ao banco de dados
require_once '../../config/database.php';
$db = Database::getInstance()->getConnection();

try {
    // Verificar se a receita existe
    $stmt = $db->prepare("SELECT id, cliente_id FROM receitas WHERE id = ?");
    $stmt->execute([$id]);
    $receita = $stmt->fetch();
    
    if (!$receita) {
        header('Location: index.php?error=receita_nao_encontrada');
        exit;
    }
    
    // Excluir a receita
    $stmt = $db->prepare("DELETE FROM receitas WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        // Registrar log
        $logStmt = $db->prepare("INSERT INTO logs_sistema (usuario_id, acao, detalhes) VALUES (?, ?, ?)");
        $logStmt->execute([$_SESSION['usuario_id'], 'receita_excluida', "Receita ID: $id excluída"]);
        
        header('Location: index.php?success=excluida');
    } else {
        header('Location: index.php?error=erro_exclusao');
    }
    
} catch (PDOException $e) {
    error_log("Erro ao excluir receita: " . $e->getMessage());
    header('Location: index.php?error=erro_sistema');
}
exit;
?> 