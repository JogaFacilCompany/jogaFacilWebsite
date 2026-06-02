<?php
// crud/updateReservaStatus.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../utils/flashMessage.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservaId = (int)($_POST['reserva_id'] ?? 0);
    $novoStatus = $_POST['status'] ?? '';
    $arenaId = (int)($_POST['arena_id'] ?? 0);
    $redirectUrl = $arenaId > 0 ? "../pages/dashboardLocador.php?arena_id={$arenaId}" : '../pages/dashboardLocador.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida (CSRF).', 'danger');
        header('Location: ' . $redirectUrl);
        exit;
    }

    if (!in_array($novoStatus, ['confirmada', 'cancelada', 'concluida'])) {
        setFlash('Status inválido.', 'danger');
        header('Location: ' . $redirectUrl);
        exit;
    }

    $pdo = getDbConnection();
    try {
        $usuarioTipo = $_SESSION['usuarioTipo'] ?? '';

        if (!in_array($usuarioTipo, ['locador', 'gerente'])) {
            setFlash('Acesso negado.', 'danger');
            header('Location: ../index.php');
            exit;
        }

        // Verifica se a reserva pertence a arena fornecida (segurança básica)
        $checkStmt = $pdo->prepare("SELECT id FROM reservas WHERE id = ? AND quadra_id = ?");
        $checkStmt->execute([$reservaId, $arenaId]);
        if (!$checkStmt->fetch()) {
            setFlash('Reserva não encontrada para esta quadra.', 'danger');
            header('Location: ' . $redirectUrl);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE reservas SET status = ? WHERE id = ?");
        $stmt->execute([$novoStatus, $reservaId]);
        // Busca o locatário e o nome da arena para montar a notificação
        $reservaInfoStmt = $pdo->prepare(
            "SELECT r.usuario_id, q.nome AS quadra_nome
            FROM reservas r
            INNER JOIN quadras q ON q.id = r.quadra_id
            WHERE r.id = ? LIMIT 1"
        );
        $reservaInfoStmt->execute([$reservaId]);
        $reservaInfo = $reservaInfoStmt->fetch();

        if ($reservaInfo) {
            require_once __DIR__ . '/readNotificacoes.php';
            $locatarioId = (int)$reservaInfo['usuario_id'];
            $nomeArena   = $reservaInfo['quadra_nome'];

            if ($novoStatus === 'confirmada') {
                $mensagem = "Sua reserva na arena \"{$nomeArena}\" foi confirmada!";
            } elseif ($novoStatus === 'cancelada') {
                $mensagem = "Sua reserva na arena \"{$nomeArena}\" foi cancelada.";
            } else {
                $mensagem = "Sua reserva na arena \"{$nomeArena}\" foi concluída.";
            }

            inserirNotificacao($locatarioId, $mensagem, '../pages/minhasReservas.php');
        }

        $msg = match($novoStatus) {
            'confirmada' => 'Reserva aprovada com sucesso.',
            'concluida'  => 'Chegada do locatário confirmada com sucesso.',
            default      => 'Reserva recusada.',
        };
        setFlash($msg, 'success');

    } catch (Throwable $e) {
        error_log('Error updating reserva status: ' . $e->getMessage());
        setFlash('Erro: ' . $e->getMessage(), 'danger');
    }

    header('Location: ' . $redirectUrl);
    exit;
}
