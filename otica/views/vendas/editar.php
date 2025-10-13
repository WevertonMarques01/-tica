<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Por segurança, edição de vendas deve ser implementada com cuidado
// Por enquanto, redirecionar para o histórico com uma mensagem
header('Location: historico.php?info=edicao_indisponivel');
exit;
?>