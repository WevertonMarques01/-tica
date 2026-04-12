<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

$db = Database::getInstance()->getConnection();

if (!isset($_GET['id'])) {
    echo json_encode(['erro' => 'ID não informado']);
    exit;
}

$stmt = $db->prepare("SELECT a.*, c.telefone as cliente_telefone FROM agendamentos a LEFT JOIN clientes c ON a.cliente_id = c.id WHERE a.id = ?");
$stmt->execute([$_GET['id']]);
$agendamento = $stmt->fetch();

if (!$agendamento) {
    echo json_encode(['erro' => 'Agendamento não encontrado']);
    exit;
}

$telefone = $agendamento['cliente_telefone'];
$telefone = preg_replace('/[^0-9]/', '', $telefone);

if (strlen($telefone) < 10) {
    echo json_encode(['erro' => 'Telefone inválido ou não cadastrado']);
    exit;
}

$data = date('d/m/Y', strtotime($agendamento['data_consulta']));
$hora = date('H:i', strtotime($agendamento['hora_consulta']));

$mensagem = "Olá! Sua consulta na Wiz Óptica foi agendada para {$data} às {$hora}.";
if ($agendamento['tipo_consulta']) {
    $mensagem .= "\n\nTipo: " . $agendamento['tipo_consulta'];
}
if ($agendamento['observacoes']) {
    $mensagem .= "\n\nObservações: " . $agendamento['observacoes'];
}
$mensagem .= "\n\nPor favor, arrive 15 minutos antes.\n\nAtt, Wiz Óptica";

$mensagemUrl = urlencode($mensagem);
$whatsappUrl = "https://wa.me/55{$telefone}?text={$mensagemUrl}";

echo json_encode(['url' => $whatsappUrl]);