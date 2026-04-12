<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

$db = Database::getInstance()->getConnection();

if (!isset($_GET['id'])) {
    header('Location: index.php?error=id_invalido');
    exit;
}

$stmt = $db->prepare("UPDATE agendamentos SET status = 'concluido' WHERE id = ?");
$stmt->execute([$_GET['id']]);

header('Location: index.php?success=concluido');
exit;