<?php
// crud/updateQuadra.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/userTypes.php';

function updateQuadra(int $quadraId, int $locadorId, array $inputData): array {
    $pdo = getDbConnection();

    $ownerCheckStmt = $pdo->prepare("SELECT id FROM quadras WHERE id = ? AND locadorId = ?");
    $ownerCheckStmt->execute([$quadraId, $locadorId]);
    if (!$ownerCheckStmt->fetch()) {
        return ['sucesso' => false, 'mensagem' => 'Quadra não encontrada ou sem permissão.'];
    }

    $updateFields = [];
    $bindValues   = [];

    if (!empty(trim($inputData['nome'] ?? ''))) {
        $nomeCheckStmt = $pdo->prepare("SELECT id FROM quadras WHERE locadorId = ? AND nome = ? AND id != ?");
        $nomeCheckStmt->execute([$locadorId, trim($inputData['nome']), $quadraId]);
        if ($nomeCheckStmt->fetch()) {
            return ['sucesso' => false, 'mensagem' => 'Você já possui outra quadra com esse nome.'];
        }
        $updateFields[] = 'nome = ?';
        $bindValues[]   = trim($inputData['nome']);
    }

    if (!empty(trim($inputData['endereco'] ?? ''))) {
        $updateFields[] = 'endereco = ?';
        $bindValues[]   = trim($inputData['endereco']);
    }

    if (isset($inputData['precoHora']) && $inputData['precoHora'] !== '') {
        $preco = filter_var($inputData['precoHora'], FILTER_VALIDATE_FLOAT);
        if ($preco === false || $preco <= 0) {
            return ['sucesso' => false, 'mensagem' => 'Preço por hora inválido.'];
        }
        $updateFields[] = 'precoHora = ?';
        $bindValues[]   = $preco;
    }

    if (isset($inputData['descricao'])) {
        $updateFields[] = 'descricao = ?';
        $bindValues[]   = trim($inputData['descricao']) ?: null;
    }

    if (isset($inputData['imagemUrl'])) {
        $updateFields[] = 'imagemUrl = ?';
        $bindValues[]   = trim($inputData['imagemUrl']) ?: null;
    }

    if (empty($updateFields)) {
        return ['sucesso' => false, 'mensagem' => 'Nenhum dado para atualizar.'];
    }

    $bindValues[] = $quadraId;
    $sqlQuery     = 'UPDATE quadras SET ' . implode(', ', $updateFields) . ' WHERE id = ?';
    $updateStmt   = $pdo->prepare($sqlQuery);
    $updateStmt->execute($bindValues);

    return ['sucesso' => true, 'mensagem' => 'Quadra atualizada com sucesso!'];
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../config/auth.php';

    requireLocadorAuth();

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $_SESSION['flashMessage'] = 'Requisição inválida. Tente novamente.';
        $_SESSION['flashType']    = 'danger';
        header('Location: ../pages/dashboard-locador.php');
        exit;
    }

    $quadraId     = (int)($_POST['id'] ?? 0);
    $locadorId    = (int)$_SESSION['usuarioLogado'];
    $responseData = updateQuadra($quadraId, $locadorId, $_POST);

    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';
    header('Location: ../pages/dashboard-locador.php');
    exit;
}
