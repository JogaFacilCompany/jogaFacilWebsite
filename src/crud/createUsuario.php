<?php
// crud/createUsuario.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/validators.php';

function createUsuario(array $inputData): array {
    $pdo      = getDbConnection();
    $userType = $inputData['tipo']   ?? '';
    $fromDash = ($inputData['source'] ?? '') === 'dashboard';

    $allowedTypes = ['locador', 'locatario', 'gerente', 'admin'];
    if (!in_array($userType, $allowedTypes)) {
        return ['sucesso' => false, 'mensagem' => 'Tipo de usuário inválido.'];
    }

    $requiredFields = ['nome', 'email', 'senha', 'tipo'];
    foreach ($requiredFields as $fieldName) {
        if (empty(trim($inputData[$fieldName] ?? ''))) {
            return ['sucesso' => false, 'mensagem' => "Campo obrigatório em falta: {$fieldName}"];
        }
    }

    $inputEmail = filter_var(trim($inputData['email']), FILTER_VALIDATE_EMAIL);
    if (!$inputEmail) {
        return ['sucesso' => false, 'mensagem' => 'E-mail inválido.'];
    }

    $inputCpf = null;
    if ($userType === 'locatario' && !$fromDash) {
        $rawCpf = $inputData['cpf'] ?? '';
        if (!isValidCpf($rawCpf)) {
            return ['sucesso' => false, 'mensagem' => 'CPF inválido. Verifique o número informado.'];
        }
        $inputCpf = preg_replace('/[^0-9]/', '', $rawCpf);

        $cpfCheckStmt = $pdo->prepare("SELECT id FROM usuarios WHERE cpf = ?");
        $cpfCheckStmt->execute([$inputCpf]);
        if ($cpfCheckStmt->fetch()) {
            return ['sucesso' => false, 'mensagem' => 'CPF já cadastrado no sistema.'];
        }
    }

    $emailCheckStmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $emailCheckStmt->execute([$inputEmail]);
    if ($emailCheckStmt->fetch()) {
        return ['sucesso' => false, 'mensagem' => 'E-mail já está em uso.'];
    }

    $hashedPassword = password_hash($inputData['senha'], PASSWORD_BCRYPT);
    $inputName      = trim($inputData['nome']);

    $insertStmt = $pdo->prepare(
        "INSERT INTO usuarios (nome, email, senha, tipo, cpf) VALUES (?, ?, ?, ?, ?)"
    );
    $insertStmt->execute([$inputName, $inputEmail, $hashedPassword, $userType, $inputCpf]);

    return ['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso!', 'userId' => $pdo->lastInsertId()];
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida. Tente novamente.', 'danger');
        $referer = $_SERVER['HTTP_REFERER'] ?? '../pages/dashboardLocador.php';
        header('Location: ' . $referer);
        exit;
    }

    $responseData = createUsuario($_POST);
    setFlashFromResponse($responseData);

    $fromDash = ($_POST['source'] ?? '') === 'dashboard';

    if ($responseData['sucesso']) {
        if ($fromDash) {
            header('Location: ../pages/dashboardLocador.php');
        } else {
            $redirectPage = ($_POST['tipo'] === 'locador' || $_POST['tipo'] === 'gerente')
                ? 'loginLocador.php'
                : 'loginLocatario.php';
            header('Location: ../pages/' . $redirectPage);
        }
    } else {
        $referer = $_SERVER['HTTP_REFERER'] ?? '../pages/dashboardLocador.php';
        header('Location: ' . $referer);
    }
    exit;
}
