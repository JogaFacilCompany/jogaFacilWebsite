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

/**
 * Busca todas as quadras pendentes de aprovação.
 */
function getAllPendingQuadras(): array {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT q.*, u.nome as locador_nome FROM quadras q JOIN usuarios u ON q.locador_id = u.id WHERE q.status = 'pendente' ORDER BY q.created_at ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Busca todas as quadras ativas para a página inicial.
 */
function getAllApprovedQuadras(): array {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM quadras WHERE status = 'ativo' ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
