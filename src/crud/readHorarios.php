<?php
// crud/readHorarios.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/timeSlotGenerator.php';

function getSlotPriceByHour(int $hour): float {
    if ($hour < 12) {
        return 150.00;
    }
    if ($hour < 18) {
        return 180.00;
    }
    return 200.00;
}

function ensureHorariosForQuadraDate(int $quadraId, string $date, string $funcionamento): void {
    $pdo = getDbConnection();
    $existingStmt = $pdo->prepare(
        "SELECT TIME_FORMAT(hora_inicio, '%H:%i') FROM horarios WHERE quadra_id = ? AND data = ?"
    );
    $existingStmt->execute([$quadraId, $date]);
    $existingTimes = array_flip($existingStmt->fetchAll(PDO::FETCH_COLUMN));

    $insertStmt = $pdo->prepare(
        "INSERT INTO horarios (quadra_id, data, hora_inicio, hora_fim, preco)
         VALUES (?, ?, ?, ?, ?)"
    );

    foreach (generateRelativeTimeSlots($funcionamento) as $periodSlots) {
        foreach ($periodSlots as $startTime) {
            if (isset($existingTimes[$startTime])) {
                continue;
            }

            $startHour = (int)substr($startTime, 0, 2);
            $endTime = sprintf('%02d:00', $startHour + 1);
            $insertStmt->execute([
                $quadraId,
                $date,
                $startTime . ':00',
                $endTime . ':00',
                getSlotPriceByHour($startHour),
            ]);
        }
    }
}

function getBookingSlotsByQuadraDate(int $quadraId, string $date, string $funcionamento): array {
    ensureHorariosForQuadraDate($quadraId, $date, $funcionamento);

    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        "SELECT
            h.id,
            TIME_FORMAT(h.hora_inicio, '%H:%i') AS start_time,
            h.preco,
            COUNT(r.id) AS reservas_ativas
         FROM horarios h
         LEFT JOIN reservas r
            ON r.horario_id = h.id
           AND r.status IN ('pendente', 'confirmada')
         WHERE h.quadra_id = ? AND h.data = ?
         GROUP BY h.id, h.hora_inicio, h.preco
         ORDER BY h.hora_inicio ASC"
    );
    $stmt->execute([$quadraId, $date]);

    $groupedSlots = ['manha' => [], 'tarde' => [], 'noite' => []];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $slotRow) {
        $hour = (int)substr($slotRow['start_time'], 0, 2);
        $period = $hour < 12 ? 'manha' : ($hour < 18 ? 'tarde' : 'noite');
        $groupedSlots[$period][] = [
            'id' => (int)$slotRow['id'],
            'startTime' => $slotRow['start_time'],
            'price' => (float)$slotRow['preco'],
            'isAvailable' => (int)$slotRow['reservas_ativas'] === 0,
        ];
    }

    return $groupedSlots;
}
