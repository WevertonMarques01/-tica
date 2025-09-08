<?php
/**
 * Verificação de autenticação
 * Incluir este arquivo no início de todas as páginas que precisam de autenticação
 */

session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    // Redirecionar para a página de login
    header('Location: ../../login.php');
    exit;
}

// Verificar se o usuário tem permissão de administrador (opcional)

if (isset($_SESSION['usuario_permissao']) && $_SESSION['usuario_permissao'] != 'admin') 

if (isset($_SESSION['usuario_permissao']) && $_SESSION['usuario_permissao'] !== 'admin') {

    // Redirecionar para uma página de acesso negado
    header('Location: ../../login.php?error=access_denied');
    exit;
}

/**
 * Função para verificar se o usuário tem acesso ao financeiro
 * Apenas o dono (admin) pode acessar
 */
function verificarAcessoFinanceiro() {
    if (!isset($_SESSION['usuario_permissao']) || $_SESSION['usuario_permissao'] !== 'admin') {
        header('Location: ../../login.php?error=financeiro_restrito');
        exit;
    }
}

/**
 * Função para verificar se o usuário é dono (admin)
 */
function verificarSeDono() {
    return isset($_SESSION['usuario_permissao']) && $_SESSION['usuario_permissao'] === 'admin';
}

/**
 * Função para verificar se o usuário é funcionário
 */
function verificarSeFuncionario() {
    return isset($_SESSION['usuario_permissao']) && $_SESSION['usuario_permissao'] !== 'admin';
}
?>