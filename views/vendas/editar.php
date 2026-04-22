<?php
// Verificar autenticaÃ§Ã£o
require_once __DIR__ . '/../../includes/auth_check.php';

// Por seguranÃ§a, ediÃ§Ã£o de vendas deve ser implementada com cuidado
// Por enquanto, redirecionar para o histÃ³rico com uma mensagem
header('Location: historico.php?info=edicao_indisponivel');
exit;
?>
