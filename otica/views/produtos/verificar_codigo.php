<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$codigo = trim($_POST['codigo'] ?? '');

if (empty($codigo)) {
    echo json_encode(['error' => 'Código não fornecido']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar se código já existe
    $stmt = $db->prepare("SELECT id, nome, preco FROM produtos WHERE codigo = ?");
    $stmt->execute([$codigo]);
    $produto = $stmt->fetch();
    
    if ($produto) {
        echo json_encode([
            'exists' => true,
            'produto' => [
                'id' => $produto['id'],
                'nome' => $produto['nome'],
                'preco' => $produto['preco']
            ],
            'message' => 'Este código já está cadastrado para o produto: ' . $produto['nome']
        ]);
    } else {
        echo json_encode([
            'exists' => false,
            'message' => 'Código disponível para cadastro'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor']);
}
?>
