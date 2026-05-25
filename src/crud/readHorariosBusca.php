<?php
// crud/readHorariosBusca.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/readQuadras.php';
require_once __DIR__ . '/readHorarios.php';

function getModalidadeLikePattern(string $modalidade): string {
    return match (strtolower($modalidade)) {
        'futebol' => '%futebol%',
        'volei' => '%vol%',
        'tenis' => '%ten%',
        default => '%' . strtolower($modalidade) . '%',
    };
}

function getPeriodoHourCondition(string $periodo): string {
    return match ($periodo) {
        'manha' => 'HOUR(h.hora_inicio) < 12',
        'tarde' => 'HOUR(h.hora_inicio) >= 12 AND HOUR(h.hora_inicio) < 18',
        'noite' => 'HOUR(h.hora_inicio) >= 18',
        default => '1=1',
    };
}

function searchHorariosDisponiveis(array $filtros): array {
    $data = trim($filtros['data'] ?? '');
    $periodo = trim($filtros['periodo'] ?? 'todos');
    $modalidade = trim($filtros['modalidade'] ?? 'todos');
    $buscaTexto = trim($filtros['busca'] ?? '');

    if ($data === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        return [];
    }

    $quadrasAtivas = getAllApprovedQuadras();
    foreach ($quadrasAtivas as $quadra) {
        ensureHorariosForQuadraDate(
            (int)$quadra['id'],
            $data,
            $quadra['funcionamento'] ?? '08:00 - 23:00'
        );
    }

    $pdo = getDbConnection();
    $periodoSql = getPeriodoHourCondition($periodo);

    $sql = "SELECT q.id AS quadra_id,
                   q.nome AS quadra_nome,
                   q.endereco AS quadra_endereco,
                   q.modalidades,
                   h.id AS horario_id,
                   h.data,
                   TIME_FORMAT(h.hora_inicio, '%H:%i') AS hora_inicio,
                   TIME_FORMAT(h.hora_fim, '%H:%i') AS hora_fim,
                   h.preco,
                   COUNT(r.id) AS reservas_ativas
            FROM horarios h
            INNER JOIN quadras q ON q.id = h.quadra_id
            LEFT JOIN reservas r
                ON r.horario_id = h.id
               AND r.status IN ('pendente', 'confirmada')
            WHERE q.status = 'ativo'
              AND h.data = ?
              AND {$periodoSql}";

    $params = [$data];

    if ($modalidade !== 'todos' && $modalidade !== '') {
        $sql .= ' AND LOWER(q.modalidades) LIKE ?';
        $params[] = getModalidadeLikePattern($modalidade);
    }

    if ($buscaTexto !== '') {
        $sql .= ' AND (LOWER(q.nome) LIKE ? OR LOWER(q.endereco) LIKE ?)';
        $termo = '%' . strtolower($buscaTexto) . '%';
        $params[] = $termo;
        $params[] = $termo;
    }

    $sql .= ' GROUP BY q.id, q.nome, q.endereco, q.modalidades, h.id, h.data, h.hora_inicio, h.hora_fim, h.preco
              HAVING reservas_ativas = 0
              ORDER BY q.nome ASC, h.hora_inicio ASC';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log('Error in searchHorariosDisponiveis: ' . $e->getMessage());
        return [];
    }
}
