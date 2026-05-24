<?php
// crud/createReserva.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function createReserva(array $data): array {
    $usuarioId = (int)($data['usuario_id'] ?? 0);
    $arenaId = (int)($data['arena_id'] ?? 0);
    $horarioId = (int)($data['horario_id'] ?? 0);
    $modoLobby = !empty($data['modo_lobby']) ? 1 : 0;

    if ($usuarioId <= 0 || $arenaId <= 0 || $horarioId <= 0) {
        return ['sucesso' => false, 'mensagem' => 'Dados de reserva inválidos.'];
    }

    $pdo = getDbConnection();

    try {
        $pdo->beginTransaction();

        $horarioStmt = $pdo->prepare(
            "SELECT h.id, h.quadra_id, q.status AS quadra_status
             FROM horarios h
             INNER JOIN quadras q ON q.id = h.quadra_id
             WHERE h.id = ? AND h.quadra_id = ?
             FOR UPDATE"
        );
        $horarioStmt->execute([$horarioId, $arenaId]);
        $horario = $horarioStmt->fetch(PDO::FETCH_ASSOC);

        if (!$horario || $horario['quadra_status'] !== 'ativo') {
            $pdo->rollBack();
            return ['sucesso' => false, 'mensagem' => 'Horário indisponível para reserva.'];
        }

        $reservaCheckStmt = $pdo->prepare(
            "SELECT id FROM reservas WHERE horario_id = ? AND status IN ('pendente', 'confirmada') LIMIT 1"
        );
        $reservaCheckStmt->execute([$horarioId]);
        if ($reservaCheckStmt->fetch()) {
            $pdo->rollBack();
            return ['sucesso' => false, 'mensagem' => 'Este horário acabou de ser reservado. Escolha outro horário.'];
        }

        $insertStmt = $pdo->prepare(
            "INSERT INTO reservas (horario_id, quadra_id, usuario_id, status, modo_lobby)
             VALUES (?, ?, ?, 'pendente', ?)"
        );
        $insertStmt->execute([$horarioId, (int)$horario['quadra_id'], $usuarioId, $modoLobby]);

        $pdo->commit();
        return ['sucesso' => true, 'mensagem' => 'Reserva solicitada com sucesso! Aguardando aprovação da quadra.'];
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Create reservation error: ' . $error->getMessage());
        return ['sucesso' => false, 'mensagem' => 'Erro ao confirmar reserva. Tente novamente.'];
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    $arenaId = (int)($_POST['arena_id'] ?? 0);
    $redirect = $arenaId > 0 ? "../pages/arenaDetalhe.php?id={$arenaId}" : '../index.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida. Tente novamente.', 'danger');
        header('Location: ' . $redirect);
        exit;
    }

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'locatario') {
        setFlash('Entre como locatário para reservar um horário.', 'warning');
        header('Location: ../pages/loginLocatario.php');
        exit;
    }

    $_POST['usuario_id'] = $_SESSION['usuarioLogado'];
    $responseData = createReserva($_POST);
    setFlashFromResponse($responseData);

    header('Location: ' . $redirect);
    exit;
}
