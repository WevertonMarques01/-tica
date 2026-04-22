<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getInstance()->getConnection();

if (!isset($_GET['id'])) {
    header('Location: index.php?error=id_invalido');
    exit;
}

$stmt = $db->prepare("DELETE FROM agendamentos WHERE id = ?");
$stmt->execute([$_GET['id']]);

header('Location: index.php?success=excluido');
exit;
