<?php
// crud/updateUsuario.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function updateUsuario(int $userId, array $inputData): array {
    $pdo = getDbConnection();

    $updateFields = [];
    $bindValues   = [];

    if (!empty($inputData['nome'])) {
        $updateFields[] = 'nome = ?';
        $bindValues[]   = trim($inputData['nome']);
    }

    if (!empty($inputData['email'])) {
        $newEmail = filter_var(trim($inputData['email']), FILTER_VALIDATE_EMAIL);
        if (!$newEmail) {
            return ['sucesso' => false, 'mensagem' => 'E-mail inválido.'];
        }
        $updateFields[] = 'email = ?';
        $bindValues[]   = $newEmail;
    }

    if (!empty($inputData['senha'])) {
        $updateFields[] = 'senha = ?';
        $bindValues[]   = password_hash($inputData['senha'], PASSWORD_BCRYPT);
    }

    if (empty($updateFields)) {
        return ['sucesso' => false, 'mensagem' => 'Nenhum dado para atualizar.'];
    }

    $bindValues[] = $userId;
    $sqlQuery     = 'UPDATE usuarios SET ' . implode(', ', $updateFields) . ' WHERE id = ?';
    $updateStmt   = $pdo->prepare($sqlQuery);
    $updateStmt->execute($bindValues);

    return ['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso!'];
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida. Tente novamente.', 'danger');
        header('Location: ../pages/dashboardLocador.php');
        exit;
    }

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'locador') {
        header('Location: ../pages/loginLocador.php');
        exit;
    }

    $targetUserId = (int)($_POST['id'] ?? 0);
    $responseData = updateUsuario($targetUserId, $_POST);
    setFlashFromResponse($responseData);
    header('Location: ../pages/dashboardLocador.php');
    exit;
}
