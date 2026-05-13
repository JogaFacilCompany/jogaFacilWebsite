<?php
// crud/deleteFotoPerfil.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!isset($_SESSION['usuarioLogado'])) {
        header('Location: ../pages/escolherLogin.php');
        exit;
    }

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida. Tente novamente.', 'danger');
        header('Location: ../pages/perfil.php');
        exit;
    }

    $targetUserId = (int)$_SESSION['usuarioLogado'];
    $uploadDir    = __DIR__ . '/../assets/uploads/perfil/';
    $pdo          = getDbConnection();

    // Get current photo
    $selectStmt  = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
    $selectStmt->execute([$targetUserId]);
    $currentUser = $selectStmt->fetch();

    if ($currentUser && !empty($currentUser['foto_perfil'])) {
        $filePath = $uploadDir . $currentUser['foto_perfil'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Clear in database
    $updateStmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = NULL WHERE id = ?");
    $updateStmt->execute([$targetUserId]);

    // Clear session
    $_SESSION['usuarioFoto'] = null;

    setFlash('Foto de perfil removida.', 'success');
    header('Location: ../pages/perfil.php');
    exit;
} else {
    header('Location: ../pages/perfil.php');
    exit;
}
