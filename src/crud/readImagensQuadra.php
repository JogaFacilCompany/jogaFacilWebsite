<?php
// crud/readImagensQuadra.php
require_once __DIR__ . '/../config/database.php';

/**
 * Busca todas as imagens da galeria de uma quadra específica, ordenadas por ordem de inserção.
 */
function getImagensByQuadraId(int $quadraId): array {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM quadra_imagens WHERE quadra_id = :quadraId ORDER BY ordem ASC, created_at ASC");
    $stmt->execute(['quadraId' => $quadraId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
