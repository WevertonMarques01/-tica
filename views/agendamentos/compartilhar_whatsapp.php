<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();

if (!isset($_GET['id'])) {
    echo json_encode(['erro' => 'ID nÃ£o informado']);
    exit;
}

$stmt = $db->prepare("SELECT a.*, c.telefone as cliente_telefone FROM agendamentos a LEFT JOIN clientes c ON a.cliente_id = c.id WHERE a.id = ?");
$stmt->execute([$_GET['id']]);
$agendamento = $stmt->fetch();

if (!$agendamento) {
    echo json_encode(['erro' => 'Agendamento nÃ£o encontrado']);
    exit;
}

$telefone = $agendamento['cliente_telefone'];
$telefone = preg_replace('/[^0-9]/', '', $telefone);

if (strlen($telefone) < 10) {
    echo json_encode(['erro' => 'Telefone invÃ¡lido ou nÃ£o cadastrado']);
    exit;
}

$data = date('d/m/Y', strtotime($agendamento['data_consulta']));
$hora = date('H:i', strtotime($agendamento['hora_consulta']));

$mensagem = "OlÃ¡! Sua consulta na Wiz Ã“ptica foi agendada para {$data} Ã s {$hora}.";
if ($agendamento['tipo_consulta']) {
    $mensagem .= "\n\nTipo: " . $agendamento['tipo_consulta'];
}
if ($agendamento['observacoes']) {
    $mensagem .= "\n\nObservaÃ§Ãµes: " . $agendamento['observacoes'];
}
$mensagem .= "\n\nPor favor, arrive 15 minutos antes.\n\nAtt, Wiz Ã“ptica";

$mensagemUrl = urlencode($mensagem);
$whatsappUrl = "https://wa.me/55{$telefone}?text={$mensagemUrl}";

echo json_encode(['url' => $whatsappUrl]);
