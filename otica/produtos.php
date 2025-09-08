<?php
/**
 * Roteamento para produtos
 */
require_once 'includes/auth_check.php';
require_once 'controllers/ProdutoController.php';

$controller = new ProdutoController();

// Determinar ação baseada na URL
$action = $_GET['action'] ?? 'index';

// Executar ação correspondente
switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'novo':
        $controller->novo();
        break;
    case 'editar':
        $controller->editar();
        break;
    case 'excluir':
        $controller->excluir();
        break;
    case 'buscar':
        $controller->buscar();
        break;
    case 'verificar_codigo':
        $controller->verificarCodigo();
        break;
    case 'atualizar_estoque':
        $controller->atualizarEstoque();
        break;
    default:
        $controller->index();
        break;
}
?>

