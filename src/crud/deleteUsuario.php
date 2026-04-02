<?php
// crud/deleteUsuario.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';

function deleteUsuario(int $userId): array {
    if ($userId <= 0) {
        return ['sucesso' => false, 'mensagem' => 'ID inválido.'];
    }

    $pdo = getDbConnection();
    $deleteStmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $deleteStmt->execute([$userId]);

    $rowsAffected = $deleteStmt->rowCount();
    if ($rowsAffected === 0) {
        return ['sucesso' => false, 'mensagem' => 'Usuário não encontrado.'];
    }

    return ['sucesso' => true, 'mensagem' => 'Usuário removido com sucesso.'];
}

function redirectByUserType() {
    if (!isset($_SESSION['usuarioTipo'])) {
        header('Location: ../index.php');
        exit;
    }

    if ($_SESSION['usuarioTipo'] === 'admin') {
        header('Location: ../pages/dashboard-admin.php');
    } else if ($_SESSION['usuarioTipo'] === 'locador') {
        header('Location: ../pages/dashboard-locador.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';

    // DEBUG: Verifique o que está na sessão
    error_log('SESSION DEBUG: ' . print_r($_SESSION, true));

    if (!isset($_SESSION['usuarioLogado']) || ($_SESSION['usuarioTipo'] !== 'locador' && $_SESSION['usuarioTipo'] !== 'admin')) {
        header('Location: ../index.php');
        exit;
    }

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $_SESSION['flashMessage'] = 'Requisição inválida. Tente novamente.';
        $_SESSION['flashType']    = 'danger';
        redirectByUserType();
    }

    $targetUserId = (int)($_POST['id'] ?? 0);
    //bloqueia auto exclusao de admins
    if (
        $_SESSION['usuarioTipo'] === 'admin' &&
        $targetUserId === $_SESSION['usuarioLogado']
    ) {
        $_SESSION['flashMessage'] = 'Administradores não podem ser removidos.';
        $_SESSION['flashType']    = 'danger';
        redirectByUserType();
    }

    //bloqueia exclusao de admin
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT tipo FROM usuarios WHERE id = ?");
    $stmt->execute([$targetUserId]);
    $user = $stmt->fetch();

    if ($user && $user['tipo'] === 'admin') {
        $_SESSION['flashMessage'] = 'Administradores não podem ser removidos.';
        $_SESSION['flashType']    = 'danger';
        redirectByUserType();
    }
    $responseData = deleteUsuario($targetUserId);
    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';
    redirectByUserType();
}

