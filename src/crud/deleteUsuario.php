<?php
// crud/deleteUsuario.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function deleteUsuario(int $userId): array {
    if ($userId <= 0) {
        return ['sucesso' => false, 'mensagem' => 'ID inválido.'];
    }

    $pdo        = getDbConnection();
    $deleteStmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $deleteStmt->execute([$userId]);

    $rowsAffected = $deleteStmt->rowCount();
    if ($rowsAffected === 0) {
        return ['sucesso' => false, 'mensagem' => 'Usuário não encontrado.'];
    }

    return ['sucesso' => true, 'mensagem' => 'Usuário removido com sucesso.'];
}

function redirectByUserType(): void {
    if (!isset($_SESSION['usuarioTipo'])) {
        header('Location: ../index.php');
        exit;
    }

    if ($_SESSION['usuarioTipo'] === 'admin') {
        header('Location: ../pages/dashboardAdmin.php');
    } elseif ($_SESSION['usuarioTipo'] === 'locador') {
        header('Location: ../pages/dashboardLocador.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!isset($_SESSION['usuarioLogado']) || ($_SESSION['usuarioTipo'] !== 'locador' && $_SESSION['usuarioTipo'] !== 'admin')) {
        header('Location: ../index.php');
        exit;
    }

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida. Tente novamente.', 'danger');
        redirectByUserType();
    }

    $targetUserId = (int)($_POST['id'] ?? 0);

    // Bloqueia auto-exclusao de admins
    if ($_SESSION['usuarioTipo'] === 'admin' && $targetUserId === $_SESSION['usuarioLogado']) {
        setFlash('Administradores não podem ser removidos.', 'danger');
        redirectByUserType();
    }

    // Bloqueia exclusao de admin por outros
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare("SELECT tipo FROM usuarios WHERE id = ?");
    $stmt->execute([$targetUserId]);
    $targetUser = $stmt->fetch();

    if ($targetUser && $targetUser['tipo'] === 'admin') {
        setFlash('Administradores não podem ser removidos.', 'danger');
        redirectByUserType();
    }

    $responseData = deleteUsuario($targetUserId);
    setFlashFromResponse($responseData);
    redirectByUserType();
}
