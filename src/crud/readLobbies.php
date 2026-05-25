<?php
// crud/readLobbies.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function getPublicLobbies(int $usuarioId): array {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare(
            "SELECT r.id AS reserva_id,
                    r.status,
                    h.data,
                    h.hora_inicio,
                    h.hora_fim,
                    h.preco,
                    q.id AS quadra_id,
                    q.nome AS quadra_nome,
                    q.endereco AS quadra_endereco,
                    u.id AS host_id,
                    u.nome AS host_nome,
                    (SELECT COUNT(*) FROM lobby_participantes lp WHERE lp.reserva_id = r.id) AS total_participantes
             FROM reservas r
             INNER JOIN horarios h ON h.id = r.horario_id
             INNER JOIN quadras q ON q.id = r.quadra_id
             INNER JOIN usuarios u ON u.id = r.usuario_id
             WHERE r.modo_lobby = 1
               AND (r.visibilidade_lobby = 'publico' OR r.visibilidade_lobby IS NULL)
               AND r.status IN ('pendente', 'confirmada')
               AND q.status = 'ativo'
               AND r.usuario_id != ?
               AND NOT EXISTS (
                   SELECT 1 FROM lobby_participantes lp3
                   WHERE lp3.reserva_id = r.id AND lp3.usuario_id = ?
               )
             ORDER BY h.data ASC, h.hora_inicio ASC"
        );
        $stmt->execute([$usuarioId, $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log('Error in getPublicLobbies: ' . $e->getMessage());
        return [];
    }
}

function getLobbyByIdForJoin(int $reservaId): ?array {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare(
            "SELECT r.id AS reserva_id,
                    r.usuario_id AS host_id,
                    r.modo_lobby,
                    r.visibilidade_lobby,
                    r.status,
                    q.status AS quadra_status
             FROM reservas r
             INNER JOIN quadras q ON q.id = r.quadra_id
             WHERE r.id = ?
             LIMIT 1"
        );
        $stmt->execute([$reservaId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    } catch (Throwable $e) {
        error_log('Error in getLobbyByIdForJoin: ' . $e->getMessage());
        return null;
    }
}

function getLobbyByCodigoAcesso(string $codigo): ?array {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare(
            "SELECT r.id AS reserva_id,
                    r.usuario_id AS host_id,
                    r.modo_lobby,
                    r.visibilidade_lobby,
                    r.status,
                    q.status AS quadra_status
             FROM reservas r
             INNER JOIN quadras q ON q.id = r.quadra_id
             WHERE r.modo_lobby = 1
               AND r.visibilidade_lobby = 'privado'
               AND r.codigo_acesso = ?
               AND r.status IN ('pendente', 'confirmada')
             LIMIT 1"
        );
        $stmt->execute([$codigo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    } catch (Throwable $e) {
        error_log('Error in getLobbyByCodigoAcesso: ' . $e->getMessage());
        return null;
    }
}

function usuarioParticipaLobby(int $reservaId, int $usuarioId): bool {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare(
            "SELECT 1 FROM lobby_participantes WHERE reserva_id = ? AND usuario_id = ? LIMIT 1"
        );
        $stmt->execute([$reservaId, $usuarioId]);
        return (bool)$stmt->fetch();
    } catch (Throwable $e) {
        error_log('Error in usuarioParticipaLobby: ' . $e->getMessage());
        return false;
    }
}
