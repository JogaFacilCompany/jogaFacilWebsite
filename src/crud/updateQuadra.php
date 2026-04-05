<?php
// crud/updateQuadra.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/validators.php';

function updateQuadra(array $data): array {
    $pdo = getDbConnection();
    
    $locadorId = $data['locador_id'];
    $arenaId   = (int)($data['id'] ?? 0);
    $nome      = trim($data['nome'] ?? '');
    $endereco  = trim($data['endereco'] ?? '');
    $telefone  = trim($data['telefone'] ?? '');
    $cnpj      = preg_replace('/[^0-9]/', '', $data['cnpj'] ?? '');
    $descricao = trim($data['descricao'] ?? '');
    $modalidades = trim($data['modalidades'] ?? 'Futebol');
    $funcionamento = trim($data['funcionamento'] ?? '08:00 - 22:00');
    $cancelamento = (int)($data['cancelamento_horas'] ?? 24);

    // Validações
    if (empty($nome) || empty($endereco) || empty($telefone) || empty($cnpj)) {
        return ['sucesso' => false, 'mensagem' => 'Todos os campos obrigatórios (*) devem ser preenchidos.'];
    }

    if (!isValidCnpj($cnpj)) {
        return ['sucesso' => false, 'mensagem' => 'O CNPJ informado é inválido. Verifique os números.'];
    }

    if (!isValidOperatingHours($funcionamento)) {
        return ['sucesso' => false, 'mensagem' => 'O formato do horário de funcionamento deve ser HH:MM - HH:MM.'];
    }
    
    // Facilidades
    $facilidades = isset($data['facilidades']) ? json_encode($data['facilidades'], JSON_UNESCAPED_UNICODE) : '[]';

    $stmt = $pdo->prepare("
        UPDATE quadras 
        SET nome = :nome, endereco = :endereco, telefone = :telefone, cnpj = :cnpj,
            descricao = :descricao, modalidades = :modalidades, 
            funcionamento = :funcionamento, cancelamento_horas = :cancelamento,
            facilidades = :facilidades
        WHERE id = :arenaId AND locador_id = :locadorId
    ");
    $success = $stmt->execute([
        'nome' => $nome,
        'endereco' => $endereco,
        'telefone' => $telefone,
        'cnpj' => $cnpj,
        'descricao' => $descricao,
        'modalidades' => $modalidades,
        'funcionamento' => $funcionamento,
        'cancelamento' => $cancelamento,
        'facilidades' => $facilidades,
        'arenaId' => $arenaId,
        'locadorId' => $locadorId
    ]);
    return ['sucesso' => $success, 'mensagem' => $success ? 'Arena atualizada com sucesso!' : 'Erro ao atualizar arena.'];
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $_SESSION['flashMessage'] = 'Requisição inválida.';
        $_SESSION['flashType']    = 'danger';
        header('Location: ../pages/dashboard-locador.php');
        exit;
    }

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'locador') {
        header('Location: ../pages/login-locador.php');
        exit;
    }

    $_POST['locador_id'] = $_SESSION['usuarioLogado'];
    $responseData = updateQuadra($_POST);
    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';

    // Se editou uma arena, redireciona para a mesma página detalhada se houver ID
    $arenaId = (int)($_POST['id'] ?? 0);
    $redirect = $arenaId ? "../pages/dashboard-locador.php?arena_id=" . $arenaId : "../pages/dashboard-locador.php";
    header('Location: ' . $redirect);
    exit;
}
