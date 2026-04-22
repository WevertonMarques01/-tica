<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();

if (!isset($_GET['id'])) {
    echo json_encode(['erro' => 'ID nÃ£o informado']);
    exit;
}

$stmt = $db->prepare("SELECT a.*, c.nome as cliente_nome, c.telefone as cliente_telefone, c.email as cliente_email 
                      FROM agendamentos a 
                      LEFT JOIN clientes c ON a.cliente_id = c.id 
                      WHERE a.id = ?");
$stmt->execute([$_GET['id']]);
$agendamento = $stmt->fetch();

if (!$agendamento) {
    echo json_encode(['erro' => 'Agendamento nÃ£o encontrado']);
    exit;
}

$agendamento['data_consulta'] = date('d/m/Y', strtotime($agendamento['data_consulta']));

echo json_encode($agendamento);
