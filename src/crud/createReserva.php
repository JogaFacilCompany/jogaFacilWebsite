<?php
// crud/createReserva.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function normalizarCodigoLobby(string $codigo): string {
    return strtoupper(preg_replace('/\s+/', '', trim($codigo)));
}

function createReserva(array $data): array {
    $usuarioId = (int)($data['usuario_id'] ?? 0);
    $arenaId = (int)($data['arena_id'] ?? 0);
    $horarioId = (int)($data['horario_id'] ?? 0);
    $modoLobby = !empty($data['modo_lobby']) ? 1 : 0;
    $visibilidadeLobby = null;
    $codigoAcesso = null;

    if ($modoLobby) {
        $visibilidadeLobby = ($data['visibilidade_lobby'] ?? 'publico') === 'privado' ? 'privado' : 'publico';
        if ($visibilidadeLobby === 'privado') {
            $codigoAcesso = normalizarCodigoLobby((string)($data['codigo_acesso'] ?? ''));
            if (strlen($codigoAcesso) < 4 || strlen($codigoAcesso) > 20) {
                return ['sucesso' => false, 'mensagem' => 'Informe um código de acesso entre 4 e 20 caracteres para lobby privado.'];
            }
        }
    }

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
            "INSERT INTO reservas (horario_id, quadra_id, usuario_id, status, modo_lobby, visibilidade_lobby, codigo_acesso)
             VALUES (?, ?, ?, 'pendente', ?, ?, ?)"
        );
        $insertStmt->execute([
            $horarioId,
            (int)$horario['quadra_id'],
            $usuarioId,
            $modoLobby,
            $visibilidadeLobby,
            $codigoAcesso,
        ]);

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
