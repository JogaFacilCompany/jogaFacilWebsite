<?php
// crud/updateUsuario.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/userTypes.php';

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

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $_SESSION['flashMessage'] = 'Requisição inválida. Tente novamente.';
        $_SESSION['flashType']    = 'danger';
        header('Location: ../pages/dashboard-locador.php');
        exit;
    }

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== UserTypes::LOCADOR) {
        header('Location: ../pages/login-locador.php');
        exit;
    }

    $targetUserId = (int)($_POST['id'] ?? 0);

    if ($targetUserId === (int)$_SESSION['usuarioLogado']) {
        $_SESSION['flashMessage'] = 'Use as configurações de perfil para editar sua própria conta.';
        $_SESSION['flashType']    = 'warning';
        header('Location: ../pages/dashboard-locador.php');
        exit;
    }

    $responseData = updateUsuario($targetUserId, $_POST);
    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';
    header('Location: ../pages/dashboard-locador.php');
    exit;
}
