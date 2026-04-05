<?php
// crud/readQuadras.php
require_once __DIR__ . '/../config/database.php';

/**
 * Busca todas as quadras de um locador específico.
 */
function getQuadrasByLocador(int $locadorId): array {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM quadras WHERE locador_id = :locadorId ORDER BY created_at DESC");
    $stmt->execute(['locadorId' => $locadorId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Busca uma única quadra por ID, garantindo que pertença ao locador.
 */
function getQuadraByIdAndLocador(int $arenaId, int $locadorId): ?array {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM quadras WHERE id = :arenaId AND locador_id = :locadorId LIMIT 1");
    $stmt->execute(['arenaId' => $arenaId, 'locadorId' => $locadorId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
