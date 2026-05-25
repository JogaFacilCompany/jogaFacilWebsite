<?php
// crud/joinLobby.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/readLobbies.php';

function normalizarCodigoAcesso(string $codigo): string {
    return strtoupper(preg_replace('/\s+/', '', trim($codigo)));
}

function joinLobbyPublico(int $reservaId, int $usuarioId): array {
    $lobby = getLobbyByIdForJoin($reservaId);

    if (!$lobby || !(int)$lobby['modo_lobby']) {
        return ['sucesso' => false, 'mensagem' => 'Lobby não encontrado.'];
    }

    $visibilidade = $lobby['visibilidade_lobby'] ?? 'publico';
    if ($visibilidade === 'privado') {
        return ['sucesso' => false, 'mensagem' => 'Este lobby é privado. Use o código de acesso.'];
    }

    return inserirParticipanteLobby($lobby, $usuarioId);
}

function joinLobbyPrivado(string $codigo, int $usuarioId): array {
    $codigoNormalizado = normalizarCodigoAcesso($codigo);

    if (strlen($codigoNormalizado) < 4) {
        return ['sucesso' => false, 'mensagem' => 'Informe um código de acesso válido.'];
    }

    $lobby = getLobbyByCodigoAcesso($codigoNormalizado);

    if (!$lobby) {
        return ['sucesso' => false, 'mensagem' => 'Código de acesso inválido ou lobby indisponível.'];
    }

    return inserirParticipanteLobby($lobby, $usuarioId);
}

function inserirParticipanteLobby(array $lobby, int $usuarioId): array {
    $reservaId = (int)$lobby['reserva_id'];
    $hostId = (int)$lobby['host_id'];

    if ($hostId === $usuarioId) {
        return ['sucesso' => false, 'mensagem' => 'Você já é o organizador deste lobby.'];
    }

    if (!in_array($lobby['status'], ['pendente', 'confirmada'], true)) {
        return ['sucesso' => false, 'mensagem' => 'Este lobby não está mais disponível.'];
    }

    if (($lobby['quadra_status'] ?? '') !== 'ativo') {
        return ['sucesso' => false, 'mensagem' => 'A arena deste lobby não está ativa.'];
    }

    if (usuarioParticipaLobby($reservaId, $usuarioId)) {
        return ['sucesso' => false, 'mensagem' => 'Você já está neste lobby.'];
    }

    $pdo = getDbConnection();

    try {
        $pdo->beginTransaction();

        $lockStmt = $pdo->prepare(
            "SELECT id, usuario_id, modo_lobby, status
             FROM reservas
             WHERE id = ? AND modo_lobby = 1 AND status IN ('pendente', 'confirmada')
             FOR UPDATE"
        );
        $lockStmt->execute([$reservaId]);
        $locked = $lockStmt->fetch(PDO::FETCH_ASSOC);

        if (!$locked) {
            $pdo->rollBack();
            return ['sucesso' => false, 'mensagem' => 'Lobby indisponível no momento.'];
        }

        if ((int)$locked['usuario_id'] === $usuarioId) {
            $pdo->rollBack();
            return ['sucesso' => false, 'mensagem' => 'Você já é o organizador deste lobby.'];
        }

        $dupStmt = $pdo->prepare(
            "SELECT id FROM lobby_participantes WHERE reserva_id = ? AND usuario_id = ? LIMIT 1"
        );
        $dupStmt->execute([$reservaId, $usuarioId]);
        if ($dupStmt->fetch()) {
            $pdo->rollBack();
            return ['sucesso' => false, 'mensagem' => 'Você já está neste lobby.'];
        }

        $insertStmt = $pdo->prepare(
            "INSERT INTO lobby_participantes (reserva_id, usuario_id) VALUES (?, ?)"
        );
        $insertStmt->execute([$reservaId, $usuarioId]);

        $pdo->commit();
        return ['sucesso' => true, 'mensagem' => 'Você entrou no lobby com sucesso!'];
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Join lobby error: ' . $error->getMessage());
        return ['sucesso' => false, 'mensagem' => 'Erro ao entrar no lobby. Tente novamente.'];
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    $redirect = '../pages/listaLobbies.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida. Tente novamente.', 'danger');
        header('Location: ' . $redirect);
        exit;
    }

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'locatario') {
        setFlash('Entre como locatário para participar de um lobby.', 'warning');
        header('Location: ../pages/loginLocatario.php');
        exit;
    }

    $usuarioId = (int)$_SESSION['usuarioLogado'];
    $tipoEntrada = $_POST['tipo_entrada'] ?? 'publico';

    if ($tipoEntrada === 'privado') {
        $responseData = joinLobbyPrivado($_POST['codigo_acesso'] ?? '', $usuarioId);
    } else {
        $reservaId = (int)($_POST['reserva_id'] ?? 0);
        if ($reservaId <= 0) {
            $responseData = ['sucesso' => false, 'mensagem' => 'Lobby inválido.'];
        } else {
            $responseData = joinLobbyPublico($reservaId, $usuarioId);
        }
    }

    setFlashFromResponse($responseData);
    header('Location: ' . $redirect);
    exit;
}
