<?php
/**
 * Verificação de autenticação
 */

session_start();

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    header('Location: ../../login.php');
    exit;
}

function verificarAcessoFinanceiro() {
    if (!isset($_SESSION['usuario_permissao']) || $_SESSION['usuario_permissao'] !== 'admin') {
        header('Location: ../../login.php?error=financeiro_restrito');
        exit;
    }
}

function verificarSeDono() {
    return isset($_SESSION['usuario_permissao']) && $_SESSION['usuario_permissao'] === 'admin';
}
