<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();

$sql = "CREATE TABLE IF NOT EXISTS `comprovantes_pagamento` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id` bigint(20) UNSIGNED NOT NULL,
  `venda_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nome_arquivo` varchar(255) NOT NULL,
  `nome_original` varchar(255) NOT NULL,
  `tipo_arquivo` varchar(50) DEFAULT NULL,
  `tamanho_arquivo` int(11) DEFAULT NULL,
  `valor_pagamento` decimal(10,2) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_comprovantes_cliente` (`cliente_id`),
  KEY `idx_comprovantes_venda` (`venda_id`),
  KEY `idx_comprovantes_data` (`criado_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

try {
    $db->exec($sql);
    echo "<div style='padding: 20px; background: #dcfce7; color: #166534; border-radius: 8px; border: 1px solid #bbf7d0;'>";
    echo "<h2 style='margin-top:0;'>✓ Tabela criada com sucesso!</h2>";
    echo "<p>A tabela <code>comprovantes_pagamento</code> foi criada no banco de dados.</p>";
    echo "<p>Agora você pode:</p>";
    echo "<ul>";
    echo "<li>Acessar: <a href='views/comprovantes/index.php'>Lista de Comprovantes</a></li>";
    echo "<li>Ou clicar no botão abaixo para remover este arquivo de instalação</li>";
    echo "</ul>";
    echo "</div>";
} catch (PDOException $e) {
    echo "<div style='padding: 20px; background: #fee2e2; color: #991b1b; border-radius: 8px; border: 1px solid #fecaca;'>";
    echo "<h2 style='margin-top:0;'>✗ Erro ao criar tabela</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}