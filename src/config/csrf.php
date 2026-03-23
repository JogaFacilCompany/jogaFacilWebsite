<?php
// config/csrf.php – CSRF token helpers

function generateCsrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (empty($_SESSION['csrfToken'])) {
        $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrfToken'];
}

function validateCsrfToken(string $submittedToken): bool {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    $sessionToken = $_SESSION['csrfToken'] ?? '';
    return hash_equals($sessionToken, $submittedToken);
}
