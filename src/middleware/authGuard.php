<?php
// middleware/authGuard.php – camelCase enforced

function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
}

function _verificarStatusSessao(string $loginPage): void {
    if (!isset($_SESSION['usuarioLogado'])) { return; }
    require_once __DIR__ . '/../config/database.php';
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare("SELECT status FROM usuarios WHERE id = ? LIMIT 1");
    $stmt->execute([(int)$_SESSION['usuarioLogado']]);
    $row  = $stmt->fetch();
    if (!$row || $row['status'] === 'inativo') {
        session_unset();
        session_destroy();
        header('Location: ' . $loginPage);
        exit;
    }
}

function requireAuth(string $userType, string $loginPage): void {
    initSession();
    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== $userType) {
        header('Location: ' . $loginPage);
        exit;
    }
    _verificarStatusSessao($loginPage);
}

function requireAnyAuth(array $userTypes, string $loginPage): void {
    initSession();
    if (!isset($_SESSION['usuarioLogado']) || !in_array($_SESSION['usuarioTipo'], $userTypes, true)) {
        header('Location: ' . $loginPage);
        exit;
    }
    _verificarStatusSessao($loginPage);
}

function requireGuest(string $redirectPage): void {
    initSession();
    if (isset($_SESSION['usuarioLogado'])) {
        header('Location: ' . $redirectPage);
        exit;
    }
}
