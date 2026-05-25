<?php
// crud/readReservasLocatario.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function getReservasByLocatario(int $usuarioId): array {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare(
            "SELECT r.id AS reserva_id,
                    r.status,
                    r.modo_lobby,
                    r.visibilidade_lobby,
                    r.codigo_acesso,
                    r.created_at,
                    h.data,
                    h.hora_inicio,
                    h.hora_fim,
                    h.preco,
                    q.id AS quadra_id,
                    q.nome AS quadra_nome,
                    q.endereco AS quadra_endereco,
                    (SELECT COUNT(*) FROM lobby_participantes lp WHERE lp.reserva_id = r.id) AS total_participantes
             FROM reservas r
             INNER JOIN horarios h ON h.id = r.horario_id
             INNER JOIN quadras q ON q.id = r.quadra_id
             WHERE r.usuario_id = ?
             ORDER BY h.data DESC, h.hora_inicio DESC"
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log('Error in getReservasByLocatario: ' . $e->getMessage());
        return [];
    }
}

function getLobbiesOrganizadosByLocatario(int $usuarioId): array {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare(
            "SELECT r.id AS reserva_id,
                    r.status,
                    r.visibilidade_lobby,
                    r.codigo_acesso,
                    h.data,
                    h.hora_inicio,
                    h.hora_fim,
                    h.preco,
                    q.nome AS quadra_nome,
                    q.endereco AS quadra_endereco,
                    (SELECT COUNT(*) FROM lobby_participantes lp WHERE lp.reserva_id = r.id) AS total_participantes
             FROM reservas r
             INNER JOIN horarios h ON h.id = r.horario_id
             INNER JOIN quadras q ON q.id = r.quadra_id
             WHERE r.usuario_id = ?
               AND r.modo_lobby = 1
               AND r.status IN ('pendente', 'confirmada')
             ORDER BY h.data ASC, h.hora_inicio ASC"
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log('Error in getLobbiesOrganizadosByLocatario: ' . $e->getMessage());
        return [];
    }
}

function getLobbiesParticipandoByLocatario(int $usuarioId): array {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare(
            "SELECT r.id AS reserva_id,
                    r.status,
                    r.visibilidade_lobby,
                    h.data,
                    h.hora_inicio,
                    h.hora_fim,
                    h.preco,
                    q.nome AS quadra_nome,
                    q.endereco AS quadra_endereco,
                    u.nome AS host_nome
             FROM lobby_participantes lp
             INNER JOIN reservas r ON r.id = lp.reserva_id
             INNER JOIN horarios h ON h.id = r.horario_id
             INNER JOIN quadras q ON q.id = r.quadra_id
             INNER JOIN usuarios u ON u.id = r.usuario_id
             WHERE lp.usuario_id = ?
               AND r.status IN ('pendente', 'confirmada')
             ORDER BY h.data ASC, h.hora_inicio ASC"
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log('Error in getLobbiesParticipandoByLocatario: ' . $e->getMessage());
        return [];
    }
}
