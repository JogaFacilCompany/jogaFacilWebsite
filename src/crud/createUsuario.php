<?php
// crud/createUsuario.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/userTypes.php';

function isValidCpf(string $cpfInput): bool {
    $cpfDigits = preg_replace('/[^0-9]/', '', $cpfInput);

    if (strlen($cpfDigits) !== 11 || preg_match('/^(\d)\1{10}$/', $cpfDigits)) {
        return false;
    }

    for ($i = 9; $i < 11; $i++) {
        $expectedDigit = 0;
        for ($j = 0; $j < $i; $j++) {
            $expectedDigit += (int)$cpfDigits[$j] * (($i + 1) - $j);
        }
        $expectedDigit = (($expectedDigit % 11) < 2) ? 0 : 11 - ($expectedDigit % 11);
        if ((int)$cpfDigits[$i] !== $expectedDigit) return false;
    }

    return true;
}

function createUsuario(array $inputData): array {
    $pdo      = getDbConnection();
    $userType = $inputData['tipo']   ?? '';
    $fromDash = ($inputData['source'] ?? '') === 'dashboard';

    if (!in_array($userType, UserTypes::ALL, true)) {
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

    // CPF only required on the public signup page (not from dashboard admin panel)
    $inputCpf = null;
    if ($userType === UserTypes::LOCATARIO && !$fromDash) {
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

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $_SESSION['flashMessage'] = 'Requisição inválida. Tente novamente.';
        $_SESSION['flashType']    = 'danger';
        $referer = $_SERVER['HTTP_REFERER'] ?? '../pages/dashboard-locador.php';
        header('Location: ' . $referer);
        exit;
    }

    $responseData = createUsuario($_POST);
    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';

    $fromDash = ($_POST['source'] ?? '') === 'dashboard';

    if ($responseData['sucesso']) {
        if ($fromDash) {
            header('Location: ../pages/dashboard-locador.php');
        } else {
            $redirectPage = ($_POST['tipo'] === UserTypes::LOCADOR || $_POST['tipo'] === UserTypes::GERENTE)
                ? 'login-locador.php'
                : 'login-locatario.php';
            header('Location: ../pages/' . $redirectPage);
        }
    } else {
        $referer = $_SERVER['HTTP_REFERER'] ?? '../pages/dashboard-locador.php';
        header('Location: ' . $referer);
    }
    exit;
}
