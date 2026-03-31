<?php
// crud/readReservas.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';

function readReservasByLocatario(int $locatarioId, int $limit = 20, int $offset = 0): array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->prepare(
        "SELECT r.id, r.dataReserva, r.horaInicio, r.horaFim, r.status, r.criadoEm,
                q.nome AS quadraNome, q.endereco AS quadraEndereco
         FROM reservas r
         JOIN quadras q ON q.id = r.quadraId
         WHERE r.locatarioId = ?
         ORDER BY r.dataReserva DESC, r.horaInicio DESC
         LIMIT ? OFFSET ?"
    );
    $selectStmt->execute([$locatarioId, $limit, $offset]);
    return $selectStmt->fetchAll();
}

function readReservasByQuadra(int $quadraId, int $limit = 20, int $offset = 0): array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->prepare(
        "SELECT r.id, r.dataReserva, r.horaInicio, r.horaFim, r.status, r.motivoRecusa, r.criadoEm,
                u.nome AS locatarioNome, u.email AS locatarioEmail
         FROM reservas r
         JOIN usuarios u ON u.id = r.locatarioId
         WHERE r.quadraId = ?
         ORDER BY r.dataReserva DESC, r.horaInicio DESC
         LIMIT ? OFFSET ?"
    );
    $selectStmt->execute([$quadraId, $limit, $offset]);
    return $selectStmt->fetchAll();
}

function readReservasPendentes(int $limit = 50, int $offset = 0): array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->prepare(
        "SELECT r.id, r.quadraId, r.dataReserva, r.horaInicio, r.horaFim, r.status, r.criadoEm,
                q.nome AS quadraNome, q.endereco AS quadraEndereco,
                u.nome AS locatarioNome, u.email AS locatarioEmail
         FROM reservas r
         JOIN quadras q ON q.id = r.quadraId
         JOIN usuarios u ON u.id = r.locatarioId
         WHERE r.status = 'pendente'
         ORDER BY r.dataReserva ASC, r.horaInicio ASC
         LIMIT ? OFFSET ?"
    );
    $selectStmt->execute([$limit, $offset]);
    return $selectStmt->fetchAll();
}

function readReservaById(int $reservaId): ?array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->prepare(
        "SELECT r.id, r.quadraId, r.locatarioId, r.dataReserva, r.horaInicio, r.horaFim,
                r.status, r.motivoRecusa, r.criadoEm, r.atualizadoEm,
                q.nome AS quadraNome, q.locadorId,
                u.nome AS locatarioNome
         FROM reservas r
         JOIN quadras q ON q.id = r.quadraId
         JOIN usuarios u ON u.id = r.locatarioId
         WHERE r.id = ?"
    );
    $selectStmt->execute([$reservaId]);
    $found = $selectStmt->fetch();
    return $found ?: null;
}
