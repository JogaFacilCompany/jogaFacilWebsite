<?php
// crud/readReservasPendentes.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function getReservasPendentesByQuadra(int $quadraId): array {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare(
            "SELECT r.id AS reserva_id, r.status, 
                    h.hora_inicio, h.hora_fim, h.data,
                    u.nome AS usuario_nome, u.email AS usuario_email
             FROM reservas r
             INNER JOIN horarios h ON h.id = r.horario_id
             INNER JOIN usuarios u ON u.id = r.usuario_id
             WHERE r.quadra_id = ? AND r.status = 'pendente'
             ORDER BY h.data ASC, h.hora_inicio ASC"
        );
        $stmt->execute([$quadraId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log('Error in getReservasPendentesByQuadra: ' . $e->getMessage());
        return [];
    }
}

function getReservasConfirmadasByQuadra(int $quadraId): array {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare(
            "SELECT r.id AS reserva_id, r.status, 
                    h.hora_inicio, h.hora_fim, h.data,
                    u.nome AS usuario_nome, u.email AS usuario_email
             FROM reservas r
             INNER JOIN horarios h ON h.id = r.horario_id
             INNER JOIN usuarios u ON u.id = r.usuario_id
             WHERE r.quadra_id = ? AND r.status = 'confirmada'
             ORDER BY h.data ASC, h.hora_inicio ASC"
        );
        $stmt->execute([$quadraId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log('Error in getReservasConfirmadasByQuadra: ' . $e->getMessage());
        return [];
    }
}
