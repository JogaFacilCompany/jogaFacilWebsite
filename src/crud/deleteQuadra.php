<?php
// crud/deleteQuadra.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function deleteQuadra(int $arenaId, int $locadorId): array {
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare("DELETE FROM quadras WHERE id = :arenaId AND locador_id = :locadorId");
    $success = $stmt->execute(['arenaId' => $arenaId, 'locadorId' => $locadorId]);
    return ['sucesso' => $success, 'mensagem' => $success ? 'Arena excluída com sucesso!' : 'Erro ao excluir arena.'];
}

// Handle POST request for deletion
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($requestMethod !== 'POST') {
    if (isset($_GET['id'])) {
        http_response_code(405);
        header('Allow: POST');
        exit('Método não permitido.');
    }
    return;
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../utils/flashMessage.php';

if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
    setFlash('Requisição inválida.', 'danger');
    header('Location: ../pages/dashboardLocador.php');
    exit;
}

if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'locador') {
    header('Location: ../pages/loginLocador.php');
    exit;
}

$arenaId     = (int)($_POST['id'] ?? 0);
$locadorId   = $_SESSION['usuarioLogado'];
$responseData = deleteQuadra($arenaId, $locadorId);
setFlashFromResponse($responseData);

header('Location: ../pages/dashboardLocador.php');
exit;
