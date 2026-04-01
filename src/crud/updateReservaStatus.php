<?php
// crud/updateReservaStatus.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/userTypes.php';
require_once __DIR__ . '/readReservas.php';

function updateReservaStatus(int $reservaId, string $novoStatus, ?string $motivoRecusa = null): array {
    $statusValidos = ['confirmada', 'recusada', 'cancelada'];
    if (!in_array($novoStatus, $statusValidos, true)) {
        return ['sucesso' => false, 'mensagem' => 'Status inválido.'];
    }

    if ($novoStatus === 'recusada' && empty(trim($motivoRecusa ?? ''))) {
        return ['sucesso' => false, 'mensagem' => 'Motivo da recusa é obrigatório.'];
    }

    $reserva = readReservaById($reservaId);
    if (!$reserva) {
        return ['sucesso' => false, 'mensagem' => 'Reserva não encontrada.'];
    }

    if ($reserva['status'] !== 'pendente') {
        return ['sucesso' => false, 'mensagem' => "Reserva já está com status '{$reserva['status']}'. Apenas reservas pendentes podem ser alteradas."];
    }

    $pdo = getDbConnection();

    $updateStmt = $pdo->prepare(
        "UPDATE reservas SET status = ?, motivoRecusa = ? WHERE id = ?"
    );
    $motivo = ($novoStatus === 'recusada') ? trim($motivoRecusa) : null;
    $updateStmt->execute([$novoStatus, $motivo, $reservaId]);

    $mensagens = [
        'confirmada' => 'Reserva confirmada com sucesso!',
        'recusada'   => 'Reserva recusada. O horário foi liberado.',
        'cancelada'  => 'Reserva cancelada com sucesso.',
    ];

    return ['sucesso' => true, 'mensagem' => $mensagens[$novoStatus]];
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../config/auth.php';

    requireGerenteAuth();

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $_SESSION['flashMessage'] = 'Requisição inválida. Tente novamente.';
        $_SESSION['flashType']    = 'danger';
        header('Location: ../pages/dashboardGerente.php');
        exit;
    }

    $reservaId    = (int)($_POST['reservaId'] ?? 0);
    $novoStatus   = $_POST['status'] ?? '';
    $motivoRecusa = $_POST['motivoRecusa'] ?? null;

    $responseData = updateReservaStatus($reservaId, $novoStatus, $motivoRecusa);

    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';
    header('Location: ../pages/dashboardGerente.php');
    exit;
}
