<?php
// crud/updateQuadra.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/validators.php';

function updateQuadra(array $data): array {
    $validation = validateQuadraData($data);
    if (!$validation['valido']) {
        return ['sucesso' => false, 'mensagem' => $validation['mensagem']];
    }

    $campos    = $validation['campos'];
    $locadorId = $data['locador_id'];
    $arenaId   = (int)($data['id'] ?? 0);
    $pdo       = getDbConnection();

    $stmt = $pdo->prepare("
        UPDATE quadras
        SET nome = :nome, endereco = :endereco, telefone = :telefone, cnpj = :cnpj,
            descricao = :descricao, modalidades = :modalidades,
            funcionamento = :funcionamento, cancelamento_horas = :cancelamento,
            facilidades = :facilidades
        WHERE id = :arenaId AND locador_id = :locadorId
    ");
    $success = $stmt->execute([
        'nome'          => $campos['nome'],
        'endereco'      => $campos['endereco'],
        'telefone'      => $campos['telefone'],
        'cnpj'          => $campos['cnpj'],
        'descricao'     => $campos['descricao'],
        'modalidades'   => $campos['modalidades'],
        'funcionamento' => $campos['funcionamento'],
        'cancelamento'  => $campos['cancelamento'],
        'facilidades'   => $campos['facilidades'],
        'arenaId'       => $arenaId,
        'locadorId'     => $locadorId,
    ]);

    return ['sucesso' => $success, 'mensagem' => $success ? 'Arena atualizada com sucesso!' : 'Erro ao atualizar arena.'];
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida.', 'danger');
        header('Location: ../pages/dashboardLocador.php');
        exit;
    }

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'locador') {
        header('Location: ../pages/loginLocador.php');
        exit;
    }

    $_POST['locador_id'] = $_SESSION['usuarioLogado'];
    $responseData = updateQuadra($_POST);
    setFlashFromResponse($responseData);

    $arenaId  = (int)($_POST['id'] ?? 0);
    $redirect = $arenaId ? "../pages/dashboardLocador.php?arena_id=" . $arenaId : "../pages/dashboardLocador.php";
    header('Location: ' . $redirect);
    exit;
}
