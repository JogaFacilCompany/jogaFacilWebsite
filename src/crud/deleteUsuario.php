<?php
// crud/deleteUsuario.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/userTypes.php';

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

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $_SESSION['flashMessage'] = 'Requisição inválida. Tente novamente.';
        $_SESSION['flashType']    = 'danger';
        header('Location: ../pages/dashboard-locador.php');
        exit;
    }

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== UserTypes::LOCADOR) {
        header('Location: ../pages/login-locador.php');
        exit;
    }

    $targetUserId = (int)($_POST['id'] ?? 0);

    if ($targetUserId === (int)$_SESSION['usuarioLogado']) {
        $_SESSION['flashMessage'] = 'Não é possível remover sua própria conta.';
        $_SESSION['flashType']    = 'warning';
        header('Location: ../pages/dashboard-locador.php');
        exit;
    }

    $responseData = deleteUsuario($targetUserId);
    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';
    header('Location: ../pages/dashboard-locador.php');
    exit;
}
