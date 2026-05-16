<?php
// crud/uploadImagensQuadra.php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'locador') {
        header('Location: ../pages/loginLocador.php');
        exit;
    }

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida.', 'danger');
        header('Location: ../pages/dashboardLocador.php');
        exit;
    }

    $arenaId = (int)($_POST['arena_id'] ?? 0);
    $locadorId = (int)$_SESSION['usuarioLogado'];
    
    $pdo = getDbConnection();
    
    // Verifica se a quadra pertence ao locador
    $stmt = $pdo->prepare("SELECT id, imagem FROM quadras WHERE id = ? AND locador_id = ?");
    $stmt->execute([$arenaId, $locadorId]);
    $quadra = $stmt->fetch();

    if (!$quadra) {
        setFlash('Arena não encontrada ou acesso negado.', 'danger');
        header('Location: ../pages/dashboardLocador.php');
        exit;
    }

    require_once __DIR__ . '/../utils/imageUpload.php';
    processArenaImages($arenaId, $_FILES);
    
    setFlash('Imagens atualizadas com sucesso!', 'success');
    header('Location: ../pages/dashboardLocador.php?arena_id=' . $arenaId);
    exit;
} else {
    header('Location: ../pages/dashboardLocador.php');
    exit;
}
