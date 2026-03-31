<?php
// crud/readQuadras.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';

function readAllQuadrasAtivas(int $limit = 20, int $offset = 0): array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->prepare(
        "SELECT q.id, q.locadorId, q.nome, q.endereco, q.descricao, q.precoHora, q.imagemUrl, q.criadoEm,
                u.nome AS locadorNome
         FROM quadras q
         JOIN usuarios u ON u.id = q.locadorId
         WHERE q.ativo = TRUE
         ORDER BY q.criadoEm DESC
         LIMIT ? OFFSET ?"
    );
    $selectStmt->execute([$limit, $offset]);
    return $selectStmt->fetchAll();
}

function readQuadraById(int $quadraId): ?array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->prepare(
        "SELECT q.id, q.locadorId, q.nome, q.endereco, q.cnpj, q.descricao, q.precoHora,
                q.imagemUrl, q.ativo, q.criadoEm, q.atualizadoEm,
                u.nome AS locadorNome
         FROM quadras q
         JOIN usuarios u ON u.id = q.locadorId
         WHERE q.id = ?"
    );
    $selectStmt->execute([$quadraId]);
    $found = $selectStmt->fetch();
    return $found ?: null;
}

function readQuadrasByLocador(int $locadorId): array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->prepare(
        "SELECT id, nome, endereco, precoHora, imagemUrl, ativo, criadoEm
         FROM quadras
         WHERE locadorId = ?
         ORDER BY criadoEm DESC"
    );
    $selectStmt->execute([$locadorId]);
    return $selectStmt->fetchAll();
}

function readHorariosByQuadra(int $quadraId): array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->prepare(
        "SELECT id, diaSemana, horaInicio, horaFim, preco
         FROM horarios_disponiveis
         WHERE quadraId = ?
         ORDER BY diaSemana, horaInicio"
    );
    $selectStmt->execute([$quadraId]);
    return $selectStmt->fetchAll();
}
