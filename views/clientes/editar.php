<?php
// Verificar autenticaÃ§Ã£o
require_once __DIR__ . '/../../includes/auth_check.php';

// Por enquanto, redirecionar para o novo.php com informaÃ§Ãµes do cliente para ediÃ§Ã£o
// ImplementaÃ§Ã£o completa de ediÃ§Ã£o pode ser adicionada posteriormente
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    header("Location: novo.php?edit=$id");
} else {
    header('Location: index.php?error=id_invalido');
}
exit;
?>
