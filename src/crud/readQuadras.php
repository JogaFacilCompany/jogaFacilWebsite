<?php
// crud/readQuadras.php
require_once __DIR__ . '/../config/database.php';

function ensureGerenteQuadrasTable(PDO $pdo): void {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS gerente_quadras (
            gerente_id INT NOT NULL,
            quadra_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (gerente_id, quadra_id),
            FOREIGN KEY (gerente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (quadra_id) REFERENCES quadras(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
}

/**
 * Busca todas as quadras de um locador específico.
 */
function getQuadrasByLocador(int $locadorId): array {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM quadras WHERE locador_id = :locadorId ORDER BY created_at DESC");
    $stmt->execute(['locadorId' => $locadorId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getQuadrasByGerente(int $gerenteId): array {
    $pdo = getDbConnection();
    ensureGerenteQuadrasTable($pdo);
    $stmt = $pdo->prepare(
        "SELECT q.*
         FROM quadras q
         INNER JOIN gerente_quadras gq ON gq.quadra_id = q.id
         WHERE gq.gerente_id = :gerenteId
         ORDER BY q.created_at DESC"
    );
    $stmt->execute(['gerenteId' => $gerenteId]);
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

function getQuadraByIdAndGerente(int $arenaId, int $gerenteId): ?array {
    $pdo = getDbConnection();
    ensureGerenteQuadrasTable($pdo);
    $stmt = $pdo->prepare(
        "SELECT q.*
         FROM quadras q
         INNER JOIN gerente_quadras gq ON gq.quadra_id = q.id
         WHERE q.id = :arenaId AND gq.gerente_id = :gerenteId
         LIMIT 1"
    );
    $stmt->execute(['arenaId' => $arenaId, 'gerenteId' => $gerenteId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function getActiveQuadraById(int $arenaId): ?array {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM quadras WHERE id = :arenaId AND status = 'ativo' LIMIT 1");
    $stmt->execute(['arenaId' => $arenaId]);
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
