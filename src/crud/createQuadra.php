<?php
// crud/createQuadra.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/userTypes.php';

function isValidCnpj(string $cnpjInput): bool {
    $cnpjDigits = preg_replace('/[^0-9]/', '', $cnpjInput);

    if (strlen($cnpjDigits) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpjDigits)) {
        return false;
    }

    $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    for ($t = 12; $t < 14; $t++) {
        $sum = 0;
        $weights = ($t === 12) ? $weights1 : $weights2;
        for ($i = 0; $i < $t; $i++) {
            $sum += (int)$cnpjDigits[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $expected = ($remainder < 2) ? 0 : 11 - $remainder;
        if ((int)$cnpjDigits[$t] !== $expected) {
            return false;
        }
    }

    return true;
}

function createQuadra(array $inputData, int $locadorId): array {
    $pdo = getDbConnection();

    $requiredFields = ['nome', 'endereco', 'precoHora'];
    foreach ($requiredFields as $fieldName) {
        if (empty(trim($inputData[$fieldName] ?? ''))) {
            return ['sucesso' => false, 'mensagem' => "Campo obrigatório em falta: {$fieldName}"];
        }
    }

    $inputNome     = trim($inputData['nome']);
    $inputEndereco = trim($inputData['endereco']);
    $inputPreco    = filter_var($inputData['precoHora'], FILTER_VALIDATE_FLOAT);

    if ($inputPreco === false || $inputPreco <= 0) {
        return ['sucesso' => false, 'mensagem' => 'Preço por hora inválido.'];
    }

    $inputCnpj = null;
    if (!empty(trim($inputData['cnpj'] ?? ''))) {
        if (!isValidCnpj($inputData['cnpj'])) {
            return ['sucesso' => false, 'mensagem' => 'CNPJ inválido.'];
        }
        $inputCnpj = preg_replace('/[^0-9]/', '', $inputData['cnpj']);

        $cnpjCheckStmt = $pdo->prepare("SELECT id FROM quadras WHERE cnpj = ?");
        $cnpjCheckStmt->execute([$inputCnpj]);
        if ($cnpjCheckStmt->fetch()) {
            return ['sucesso' => false, 'mensagem' => 'CNPJ já cadastrado no sistema.'];
        }
    }

    $nomeCheckStmt = $pdo->prepare("SELECT id FROM quadras WHERE locadorId = ? AND nome = ?");
    $nomeCheckStmt->execute([$locadorId, $inputNome]);
    if ($nomeCheckStmt->fetch()) {
        return ['sucesso' => false, 'mensagem' => 'Você já possui uma quadra com esse nome.'];
    }

    $inputDescricao = trim($inputData['descricao'] ?? '') ?: null;
    $inputImagemUrl = trim($inputData['imagemUrl'] ?? '') ?: null;

    $insertStmt = $pdo->prepare(
        "INSERT INTO quadras (locadorId, nome, endereco, cnpj, descricao, precoHora, imagemUrl)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $insertStmt->execute([
        $locadorId, $inputNome, $inputEndereco,
        $inputCnpj, $inputDescricao, $inputPreco, $inputImagemUrl
    ]);

    $quadraId = (int)$pdo->lastInsertId();

    if (!empty($inputData['horarios']) && is_array($inputData['horarios'])) {
        $horarioStmt = $pdo->prepare(
            "INSERT INTO horarios_disponiveis (quadraId, diaSemana, horaInicio, horaFim, preco)
             VALUES (?, ?, ?, ?, ?)"
        );
        foreach ($inputData['horarios'] as $horario) {
            $dia    = (int)($horario['diaSemana'] ?? -1);
            $inicio = $horario['horaInicio'] ?? '';
            $fim    = $horario['horaFim'] ?? '';
            $preco  = !empty($horario['preco']) ? (float)$horario['preco'] : null;

            if ($dia < 0 || $dia > 6 || empty($inicio) || empty($fim)) {
                continue;
            }
            $horarioStmt->execute([$quadraId, $dia, $inicio, $fim, $preco]);
        }
    }

    return ['sucesso' => true, 'mensagem' => 'Quadra cadastrada com sucesso!', 'quadraId' => $quadraId];
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

    $locadorId    = (int)$_SESSION['usuarioLogado'];
    $responseData = createQuadra($_POST, $locadorId);

    $_SESSION['flashMessage'] = $responseData['mensagem'];
    $_SESSION['flashType']    = $responseData['sucesso'] ? 'success' : 'danger';
    header('Location: ../pages/dashboard-locador.php');
    exit;
}
