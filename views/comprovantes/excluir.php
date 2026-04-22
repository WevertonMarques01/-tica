<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/ComprovanteController.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID obrigatÃ³rio']);
    exit;
}

$controller = new ComprovanteController();
$result = $controller->delete($id);

echo json_encode($result);
