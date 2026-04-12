<?php
require_once '../../config/database.php';

$db = Database::getInstance()->getConnection();

$sql = "CREATE TABLE IF NOT EXISTS agendamentos (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    cliente_id bigint(20) UNSIGNED NOT NULL,
    data_consulta date NOT NULL,
    hora_consulta time NOT NULL,
    tipo_consulta varchar(50) DEFAULT NULL,
    status varchar(20) NOT NULL DEFAULT 'agendado',
    observacoes text DEFAULT NULL,
    criado_em timestamp NOT NULL DEFAULT current_timestamp(),
    atualizado_em timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
    PRIMARY KEY (id),
    KEY idx_agendamentos_cliente (cliente_id),
    KEY idx_agendamentos_data (data_consulta),
    KEY idx_agendamentos_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

try {
    $db->exec($sql);
    echo "Tabela 'agendamentos' criada com sucesso!";
    echo "<br><br>";
    echo "<a href='index.php'>Ir para Agendamentos</a>";
} catch (PDOException $e) {
    echo "Erro ao criar tabela: " . $e->getMessage();
}
?> 