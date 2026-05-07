<?php
// middleware/authGuard.php – camelCase enforced

function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
}

function requireAuth(string $userType, string $loginPage): void {
    initSession();
    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== $userType) {
        header('Location: ' . $loginPage);
        exit;
    }
}

function requireAnyAuth(array $userTypes, string $loginPage): void {
    initSession();
    if (!isset($_SESSION['usuarioLogado']) || !in_array($_SESSION['usuarioTipo'], $userTypes, true)) {
        header('Location: ' . $loginPage);
        exit;
    }
}

function requireGuest(string $redirectPage): void {
    initSession();
    if (isset($_SESSION['usuarioLogado'])) {
        header('Location: ' . $redirectPage);
        exit;
    }
}
