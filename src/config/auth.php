<?php
// config/auth.php – Shared authentication helpers.
require_once __DIR__ . '/userTypes.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/../crud/readUsuarios.php';

const SESSION_TIMEOUT_SECONDS = 1800; // 30 minutes

function enforceSessionTimeout(): void {
    if (isset($_SESSION['ultimaAtividade']) && (time() - $_SESSION['ultimaAtividade']) > SESSION_TIMEOUT_SECONDS) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['flashMessage'] = 'Sua sessão expirou. Faça login novamente.';
        $_SESSION['flashType']    = 'warning';
        header('Location: ../pages/login-locador.php');
        exit;
    }
    $_SESSION['ultimaAtividade'] = time();
}

function requireLocadorAuth(): void {
    enforceSessionTimeout();
    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== UserTypes::LOCADOR) {
        header('Location: ../pages/login-locador.php');
        exit;
    }
}

/**
 * Processes a login POST attempt for a given user type.
 * On success: sets session variables and redirects — never returns.
 * On failure: returns the error message string.
 */
function processLoginAttempt(string $expectedType, string $successRedirect): string {
    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        return 'Requisição inválida. Tente novamente.';
    }

    $inputEmail = trim($_POST['email'] ?? '');
    $inputSenha = $_POST['senha'] ?? '';
    $foundUser  = findUsuarioByEmailAndSenha($inputEmail, $inputSenha);

    if ($foundUser && $foundUser['tipo'] === $expectedType) {
        session_regenerate_id(true);
        $_SESSION['usuarioLogado']   = $foundUser['id'];
        $_SESSION['usuarioNome']     = $foundUser['nome'];
        $_SESSION['usuarioTipo']     = $foundUser['tipo'];
        $_SESSION['ultimaAtividade'] = time();
        header('Location: ' . $successRedirect);
        exit;
    }

    return 'Credenciais inválidas. Verifique e-mail e senha.';
}
