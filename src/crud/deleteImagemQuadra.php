<?php
// crud/deleteImagemQuadra.php
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

    $imagemId = (int)($_POST['imagem_id'] ?? 0);
    $arenaId = (int)($_POST['arena_id'] ?? 0);
    $locadorId = (int)$_SESSION['usuarioLogado'];
    
    $pdo = getDbConnection();
    
    // Verifica se a quadra pertence ao locador e se a imagem existe nela
    $stmt = $pdo->prepare("
        SELECT qi.nome_arquivo 
        FROM quadra_imagens qi
        JOIN quadras q ON qi.quadra_id = q.id
        WHERE qi.id = ? AND q.id = ? AND q.locador_id = ?
    ");
    $stmt->execute([$imagemId, $arenaId, $locadorId]);
    $imagem = $stmt->fetch();

    if ($imagem) {
        $uploadDir = __DIR__ . '/../assets/uploads/quadras/';
        $filePath = $uploadDir . $imagem['nome_arquivo'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $delStmt = $pdo->prepare("DELETE FROM quadra_imagens WHERE id = ?");
        $delStmt->execute([$imagemId]);

        setFlash('Imagem removida com sucesso.', 'success');
    } else {
        setFlash('Imagem não encontrada ou acesso negado.', 'danger');
    }

    header('Location: ../pages/dashboardLocador.php?arena_id=' . $arenaId);
    exit;
} else {
    header('Location: ../pages/dashboardLocador.php');
    exit;
}
