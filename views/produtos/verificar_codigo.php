<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'MÃ©todo nÃ£o permitido']);
    exit;
}

$codigo = trim($_POST['codigo'] ?? '');

if (empty($codigo)) {
    echo json_encode(['error' => 'CÃ³digo nÃ£o fornecido']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Verificar se cÃ³digo jÃ¡ existe
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
            'message' => 'Este cÃ³digo jÃ¡ estÃ¡ cadastrado para o produto: ' . $produto['nome']
        ]);
    } else {
        echo json_encode([
            'exists' => false,
            'message' => 'CÃ³digo disponÃ­vel para cadastro'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor']);
}
?>

