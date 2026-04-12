<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';
require_once '../../controllers/ComprovanteController.php';

header('Content-Type: application/json');

$controller = new ComprovanteController();
$result = $controller->upload();

if ($result['success']) {
    $clienteId = $_POST['cliente_id'] ?? null;
    header('Location: index.php?cliente_id=' . $clienteId . '&success=upload');
    exit;
} else {
    $clienteId = $_POST['cliente_id'] ?? '';
    header('Location: novo.php?cliente_id=' . $clienteId . '&error=' . urlencode($result['message']));
    exit;
}