<?php
// crud/readDisponibilidade.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/readQuadras.php';

function readSlotsDisponiveis(int $quadraId, string $dataConsulta): array {
    $pdo = getDbConnection();

    $quadra = readQuadraById($quadraId);
    if (!$quadra || !$quadra['ativo']) {
        return ['sucesso' => false, 'mensagem' => 'Quadra não encontrada ou inativa.', 'slots' => []];
    }

    $dataObj = DateTime::createFromFormat('Y-m-d', $dataConsulta);
    if (!$dataObj || $dataObj->format('Y-m-d') !== $dataConsulta) {
        return ['sucesso' => false, 'mensagem' => 'Data inválida.', 'slots' => []];
    }

    $hoje = new DateTime('today');
    if ($dataObj < $hoje) {
        return ['sucesso' => false, 'mensagem' => 'Não é possível consultar datas passadas.', 'slots' => []];
    }

    $diaSemana = (int)$dataObj->format('w');

    $horariosStmt = $pdo->prepare(
        "SELECT hd.id, hd.horaInicio, hd.horaFim, hd.preco
         FROM horarios_disponiveis hd
         WHERE hd.quadraId = ? AND hd.diaSemana = ?
         ORDER BY hd.horaInicio"
    );
    $horariosStmt->execute([$quadraId, $diaSemana]);
    $horariosConfig = $horariosStmt->fetchAll();

    if (empty($horariosConfig)) {
        return ['sucesso' => true, 'mensagem' => 'Nenhum horário configurado para este dia.', 'slots' => []];
    }

    $reservasStmt = $pdo->prepare(
        "SELECT horaInicio, horaFim
         FROM reservas
         WHERE quadraId = ? AND dataReserva = ? AND status IN ('pendente', 'confirmada')"
    );
    $reservasStmt->execute([$quadraId, $dataConsulta]);
    $reservasExistentes = $reservasStmt->fetchAll();

    $slots = [];
    foreach ($horariosConfig as $horario) {
        $ocupado = false;
        foreach ($reservasExistentes as $reserva) {
            if ($horario['horaInicio'] === $reserva['horaInicio']) {
                $ocupado = true;
                break;
            }
        }

        $slots[] = [
            'horarioId'  => $horario['id'],
            'horaInicio' => $horario['horaInicio'],
            'horaFim'    => $horario['horaFim'],
            'preco'      => $horario['preco'] ?? $quadra['precoHora'],
            'disponivel' => !$ocupado,
        ];
    }

    return ['sucesso' => true, 'mensagem' => '', 'slots' => $slots];
}

// Handle GET request (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['quadraId'], $_GET['data'])) {
    header('Content-Type: application/json; charset=utf-8');

    $quadraId     = (int)$_GET['quadraId'];
    $dataConsulta = $_GET['data'];

    $responseData = readSlotsDisponiveis($quadraId, $dataConsulta);
    echo json_encode($responseData, JSON_UNESCAPED_UNICODE);
    exit;
}
