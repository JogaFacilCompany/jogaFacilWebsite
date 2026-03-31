<?php
// crud/deleteQuadra.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/userTypes.php';

function deleteQuadra(int $quadraId, int $locadorId): array {
    if ($quadraId <= 0) {
        return ['sucesso' => false, 'mensagem' => 'ID inválido.'];
    }

    $pdo = getDbConnection();

    $ownerCheckStmt = $pdo->prepare("SELECT id, ativo FROM quadras WHERE id = ? AND locadorId = ?");
    $ownerCheckStmt->execute([$quadraId, $locadorId]);
    $quadra = $ownerCheckStmt->fetch();

    if (!$quadra) {
        return ['sucesso' => false, 'mensagem' => 'Quadra não encontrada ou sem permissão.'];
    }

    if (!$quadra['ativo']) {
        return ['sucesso' => false, 'mensagem' => 'Quadra já está desativada.'];
    }

    $pendingCheckStmt = $pdo->prepare(
        "SELECT COUNT(*) AS total FROM reservas WHERE quadraId = ? AND status IN ('pendente', 'confirmada') AND dataReserva >= CURDATE()"
    );
    $pendingCheckStmt->execute([$quadraId]);
    $pendingCount = (int)$pendingCheckStmt->fetch()['total'];

    if ($pendingCount > 0) {
        return ['sucesso' => false, 'mensagem' => "Não é possível desativar: existem {$pendingCount} reserva(s) ativa(s) ou pendente(s)."];
    }

    $updateStmt = $pdo->prepare("UPDATE quadras SET ativo = FALSE WHERE id = ?");
    $updateStmt->execute([$quadraId]);

    return ['sucesso' => true, 'mensagem' => 'Quadra desativada com sucesso.'];
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../config/auth.php';

    requireLocadorAuth();

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $_SESSION['flashMessage'] = 'Requisição inválida. Tente novamente.';
        $_SESSION['flashType']    = 'danger';
        header('Location: ../pages/dashboard-locador.php');
        exit;
    }

    $quadraId     = (int)($_POST['id'] ?? 0);
    $locadorId    = (int)$_SESSION['usuarioLogado'];
    $responseData = deleteQuadra($quadraId, $locadorId);

    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';
    header('Location: ../pages/dashboard-locador.php');
    exit;
}
