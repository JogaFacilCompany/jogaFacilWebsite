<?php
// crud/createReserva.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/userTypes.php';

function createReserva(int $locatarioId, array $inputData): array {
    $pdo = getDbConnection();

    $requiredFields = ['quadraId', 'dataReserva', 'horaInicio', 'horaFim'];
    foreach ($requiredFields as $fieldName) {
        if (empty(trim($inputData[$fieldName] ?? ''))) {
            return ['sucesso' => false, 'mensagem' => "Campo obrigatório em falta: {$fieldName}"];
        }
    }

    $quadraId    = (int)$inputData['quadraId'];
    $dataReserva = $inputData['dataReserva'];
    $horaInicio  = $inputData['horaInicio'];
    $horaFim     = $inputData['horaFim'];

    $dataObj = DateTime::createFromFormat('Y-m-d', $dataReserva);
    if (!$dataObj || $dataObj->format('Y-m-d') !== $dataReserva) {
        return ['sucesso' => false, 'mensagem' => 'Data inválida.'];
    }

    $hoje = new DateTime('today');
    if ($dataObj < $hoje) {
        return ['sucesso' => false, 'mensagem' => 'Não é possível reservar datas passadas.'];
    }

    $quadraStmt = $pdo->prepare("SELECT id, precoHora FROM quadras WHERE id = ? AND ativo = TRUE");
    $quadraStmt->execute([$quadraId]);
    if (!$quadraStmt->fetch()) {
        return ['sucesso' => false, 'mensagem' => 'Quadra não encontrada ou inativa.'];
    }

    $diaSemana = (int)$dataObj->format('w');
    $horarioStmt = $pdo->prepare(
        "SELECT id FROM horarios_disponiveis
         WHERE quadraId = ? AND diaSemana = ? AND horaInicio = ? AND horaFim = ?"
    );
    $horarioStmt->execute([$quadraId, $diaSemana, $horaInicio, $horaFim]);
    if (!$horarioStmt->fetch()) {
        return ['sucesso' => false, 'mensagem' => 'Horário não disponível para este dia.'];
    }

    // FOR UPDATE previne race condition entre reservas simultâneas
    $pdo->beginTransaction();
    try {
        $lockStmt = $pdo->prepare(
            "SELECT id FROM reservas
             WHERE quadraId = ? AND dataReserva = ? AND horaInicio = ? AND status IN ('pendente', 'confirmada')
             FOR UPDATE"
        );
        $lockStmt->execute([$quadraId, $dataReserva, $horaInicio]);

        if ($lockStmt->fetch()) {
            $pdo->rollBack();
            return ['sucesso' => false, 'mensagem' => 'Este horário já foi reservado por outro usuário.'];
        }

        $insertStmt = $pdo->prepare(
            "INSERT INTO reservas (quadraId, locatarioId, dataReserva, horaInicio, horaFim, status)
             VALUES (?, ?, ?, ?, ?, 'pendente')"
        );
        $insertStmt->execute([$quadraId, $locatarioId, $dataReserva, $horaInicio, $horaFim]);

        $reservaId = (int)$pdo->lastInsertId();
        $pdo->commit();

        return ['sucesso' => true, 'mensagem' => 'Reserva solicitada com sucesso! Aguarde a confirmação.', 'reservaId' => $reservaId];
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log('Erro ao criar reserva: ' . $e->getMessage());
        return ['sucesso' => false, 'mensagem' => 'Erro ao processar reserva. Tente novamente.'];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../config/auth.php';

    requireLocatarioAuth();

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $_SESSION['flashMessage'] = 'Requisição inválida. Tente novamente.';
        $_SESSION['flashType']    = 'danger';
        header('Location: ../index.php');
        exit;
    }

    $locatarioId  = (int)$_SESSION['usuarioLogado'];
    $quadraId     = (int)($_POST['quadraId'] ?? 0);
    $responseData = createReserva($locatarioId, $_POST);

    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';

    $redirectUrl = $quadraId > 0
        ? "../pages/arenaDetalhe.php?id={$quadraId}"
        : '../index.php';
    header('Location: ' . $redirectUrl);
    exit;
}
