<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../controllers/ComprovanteController.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID obrigatório']);
    exit;
}

$controller = new ComprovanteController();
$result = $controller->delete($id);

echo json_encode($result);