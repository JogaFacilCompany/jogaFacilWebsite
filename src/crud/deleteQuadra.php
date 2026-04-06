<?php
// crud/deleteQuadra.php
require_once __DIR__ . '/../config/database.php';

function deleteQuadra(int $arenaId, int $locadorId): array {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("DELETE FROM quadras WHERE id = :arenaId AND locador_id = :locadorId");
    $success = $stmt->execute(['arenaId' => $arenaId, 'locadorId' => $locadorId]);
    return ['sucesso' => $success, 'mensagem' => $success ? 'Arena excluída com sucesso!' : 'Erro ao excluir arena.'];
}

// Handle POST/GET request for deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['id'])) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';

    // Se for POST, valida CSRF
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $_SESSION['flashMessage'] = 'Requisição inválida.';
        $_SESSION['flashType']    = 'danger';
        header('Location: ../pages/dashboardLocador.php');
        exit;
    }

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'locador') {
        header('Location: ../pages/loginLocador.php');
        exit;
    }

    $arenaId     = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
    $locadorId   = $_SESSION['usuarioLogado'];
    $responseData = deleteQuadra($arenaId, $locadorId);
    
    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';

    header('Location: ../pages/dashboardLocador.php');
    exit;
}
