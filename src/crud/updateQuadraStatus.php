<?php
// crud/updateQuadraStatus.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function updateArenaStatus(int $arenaId, string $status): array {
    $pdo = getDbConnection();

    $allowedStatus = ['ativo', 'rejeitado', 'pendente'];
    if (!in_array($status, $allowedStatus)) {
        return ['sucesso' => false, 'mensagem' => 'Status inválido.'];
    }

    $quadraStmt = $pdo->prepare("SELECT nome, locador_id FROM quadras WHERE id = ? LIMIT 1");
    $quadraStmt->execute([$arenaId]);
    $quadraData = $quadraStmt->fetch();

    $stmt    = $pdo->prepare("UPDATE quadras SET status = :status WHERE id = :arenaId");
    $success = $stmt->execute(['status' => $status, 'arenaId' => $arenaId]);

    $labels = ['ativo' => 'aprovada', 'rejeitado' => 'rejeitada', 'pendente' => 'em análise'];
    $label  = $labels[$status] ?? $status;

    if ($success && $quadraData && in_array($status, ['ativo', 'rejeitado'])) {
        require_once __DIR__ . '/readNotificacoes.php';
        $nomeArena = $quadraData['nome'];
        $locadorId = (int)$quadraData['locador_id'];
        $linkArena = '../pages/dashboardLocador.php?arena_id=' . $arenaId;
        $mensagem  = $status === 'ativo'
            ? "Sua arena \"{$nomeArena}\" foi aprovada!"
            : "Sua arena \"{$nomeArena}\" foi rejeitada.";
        inserirNotificacao($locadorId, $mensagem, $linkArena);
    }

    return ['sucesso' => $success, 'mensagem' => $success ? "Arena {$label} com sucesso!" : 'Erro ao atualizar status.'];
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida.', 'danger');
        header('Location: ../pages/dashboardAdmin.php');
        exit;
    }

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'admin') {
        header('Location: ../pages/loginAdmin.php');
        exit;
    }

    $arenaId = (int)($_POST['id'] ?? 0);
    $status  = $_POST['status'] ?? '';

    $responseData = updateArenaStatus($arenaId, $status);
    setFlashFromResponse($responseData);

    header('Location: ../pages/dashboardAdmin.php');
    exit;
}
