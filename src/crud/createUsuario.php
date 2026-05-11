<?php
// crud/createUsuario.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/validators.php';

function ensureGerenteQuadrasTableForCreate(PDO $pdo): void {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS gerente_quadras (
            gerente_id INT NOT NULL,
            quadra_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (gerente_id, quadra_id),
            FOREIGN KEY (gerente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (quadra_id) REFERENCES quadras(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
}

function createUsuario(array $inputData): array {
    $pdo      = getDbConnection();
    $userType = $inputData['tipo']   ?? '';
    $fromDash = ($inputData['source'] ?? '') === 'dashboard';
    $loggedUserId   = (int)($inputData['loggedUserId'] ?? 0);
    $loggedUserType = $inputData['loggedUserType'] ?? '';

    $allowedTypes = ['locador', 'locatario', 'gerente'];
    if (!in_array($userType, $allowedTypes, true)) {
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
    $requiresCpf = $userType === 'gerente' || ($userType === 'locatario' && !$fromDash);
    if ($requiresCpf) {
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

    $gerenteQuadraIds = [];
    if ($userType === 'gerente') {
        if ($loggedUserType !== 'locador' || $loggedUserId <= 0) {
            return ['sucesso' => false, 'mensagem' => 'Apenas locadores podem cadastrar gerentes.'];
        }

        $rawQuadraIds = $inputData['quadraIds'] ?? [];
        if (!is_array($rawQuadraIds)) {
            $rawQuadraIds = [$rawQuadraIds];
        }

        $gerenteQuadraIds = array_values(array_unique(array_filter(array_map('intval', $rawQuadraIds))));
        if (empty($gerenteQuadraIds)) {
            return ['sucesso' => false, 'mensagem' => 'Selecione pelo menos uma quadra para o gerente.'];
        }

        $placeholders = implode(',', array_fill(0, count($gerenteQuadraIds), '?'));
        $quadraCheckStmt = $pdo->prepare(
            "SELECT id FROM quadras WHERE locador_id = ? AND id IN ({$placeholders})"
        );
        $quadraCheckStmt->execute(array_merge([$loggedUserId], $gerenteQuadraIds));
        $validQuadraIds = array_map('intval', $quadraCheckStmt->fetchAll(PDO::FETCH_COLUMN));

        if (count($validQuadraIds) !== count($gerenteQuadraIds)) {
            return ['sucesso' => false, 'mensagem' => 'Uma ou mais quadras selecionadas são inválidas.'];
        }
    }

    $emailCheckStmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $emailCheckStmt->execute([$inputEmail]);
    if ($emailCheckStmt->fetch()) {
        return ['sucesso' => false, 'mensagem' => 'E-mail já está em uso.'];
    }

    $hashedPassword = password_hash($inputData['senha'], PASSWORD_BCRYPT);
    $inputName      = trim($inputData['nome']);

    try {
        if ($userType === 'gerente') {
            ensureGerenteQuadrasTableForCreate($pdo);
        }

        $pdo->beginTransaction();

        $insertStmt = $pdo->prepare(
            "INSERT INTO usuarios (nome, email, senha, tipo, cpf) VALUES (?, ?, ?, ?, ?)"
        );
        $insertStmt->execute([$inputName, $inputEmail, $hashedPassword, $userType, $inputCpf]);
        $createdUserId = (int)$pdo->lastInsertId();

        if ($userType === 'gerente') {
            $vinculoStmt = $pdo->prepare(
                "INSERT INTO gerente_quadras (gerente_id, quadra_id) VALUES (?, ?)"
            );
            foreach ($gerenteQuadraIds as $quadraId) {
                $vinculoStmt->execute([$createdUserId, $quadraId]);
            }
        }

        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Create user error: ' . $error->getMessage());
        return ['sucesso' => false, 'mensagem' => 'Erro ao cadastrar usuário. Tente novamente.'];
    }

    return ['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso!', 'userId' => $createdUserId];
}

// Handle POST request
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida. Tente novamente.', 'danger');
        $referer = $_SERVER['HTTP_REFERER'] ?? '../pages/dashboardLocador.php';
        header('Location: ' . $referer);
        exit;
    }

    $_POST['loggedUserId'] = $_SESSION['usuarioLogado'] ?? 0;
    $_POST['loggedUserType'] = $_SESSION['usuarioTipo'] ?? '';
    $_POST['quadraIds'] = $_POST['quadraIds'] ?? [];

    $responseData = createUsuario($_POST);
    setFlashFromResponse($responseData);

    $fromDash = ($_POST['source'] ?? '') === 'dashboard';

    if ($responseData['sucesso']) {
        if ($fromDash) {
            header('Location: ../pages/dashboardLocador.php');
        } elseif ($_POST['tipo'] === 'gerente') {
            header('Location: ../pages/dashboardLocador.php');
        } else {
            $redirectPage = ($_POST['tipo'] === 'locador')
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
