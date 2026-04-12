<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();

try {
    $db->query("ALTER TABLE `venda_produtos` ADD COLUMN `retirado` tinyint(1) NOT NULL DEFAULT 0 AFTER `preco_unitario`");
    echo "Coluna 'retirado' adicionada com sucesso!<br>";
    
    $db->query("ALTER TABLE `venda_produtos` ADD COLUMN `data_retirada` datetime DEFAULT NULL AFTER `retirado`");
    echo "Coluna 'data_retirada' adicionada com sucesso!<br>";
    
    echo "Migração concluída!";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}