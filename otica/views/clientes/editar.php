<?php
// Verificar autenticação
require_once '../../includes/auth_check.php';

// Por enquanto, redirecionar para o novo.php com informações do cliente para edição
// Implementação completa de edição pode ser adicionada posteriormente
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    header("Location: novo.php?edit=$id");
} else {
    header('Location: index.php?error=id_invalido');
}
exit;
?>