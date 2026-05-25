<?php
// crud/readReservasGerente.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function getReservasByGerente(int $gerenteId): array {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare(
            "SELECT r.id AS reserva_id, r.status,
                    h.data, h.hora_inicio, h.hora_fim,
                    u.nome AS locatario_nome, u.email AS locatario_email,
                    q.nome AS quadra_nome, q.id AS quadra_id
             FROM reservas r
             INNER JOIN horarios h ON h.id = r.horario_id
             INNER JOIN usuarios u ON u.id = r.usuario_id
             INNER JOIN quadras q ON q.id = r.quadra_id
             INNER JOIN gerente_quadras gq ON gq.quadra_id = r.quadra_id
             WHERE gq.gerente_id = :gerenteId
             ORDER BY h.data DESC, h.hora_inicio ASC"
        );
        $stmt->execute(['gerenteId' => $gerenteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log('Error in getReservasByGerente: ' . $e->getMessage());
        return [];
    }
}
