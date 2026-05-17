<?php
// crud/updateStatusUsuario.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function updateStatusUsuario(int $targetUserId, string $novoStatus, string $motivo = ''): array {
    if ($targetUserId <= 0) {
        return ['sucesso' => false, 'mensagem' => 'ID de usuário inválido.'];
    }

    if (!in_array($novoStatus, ['ativo', 'inativo'], true)) {
        return ['sucesso' => false, 'mensagem' => 'Status inválido.'];
    }

    $pdo = getDbConnection();

    $checkStmt = $pdo->prepare("SELECT id, tipo FROM usuarios WHERE id = ? LIMIT 1");
    $checkStmt->execute([$targetUserId]);
    $targetUser = $checkStmt->fetch();

    if (!$targetUser) {
        return ['sucesso' => false, 'mensagem' => 'Usuário não encontrado.'];
    }

    if ($targetUser['tipo'] === 'admin') {
        return ['sucesso' => false, 'mensagem' => 'Não é possível alterar o status de um administrador.'];
    }

    $motivoFinal = ($novoStatus === 'inativo') ? trim($motivo) : null;

    $updateStmt = $pdo->prepare(
        "UPDATE usuarios SET status = ?, inativo_motivo = ? WHERE id = ?"
    );
    $updateStmt->execute([$novoStatus, $motivoFinal, $targetUserId]);

    $mensagem = ($novoStatus === 'inativo')
        ? 'Conta inativada com sucesso.'
        : 'Conta reativada com sucesso.';

    return ['sucesso' => true, 'mensagem' => $mensagem];
}


if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida. Tente novamente.', 'danger');
        header('Location: ../pages/dashboardAdmin.php');
        exit;
    }

    if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'admin') {
        header('Location: ../pages/loginAdmin.php');
        exit;
    }

    $targetUserId = (int)($_POST['id']     ?? 0);
    $novoStatus   = $_POST['novoStatus']   ?? '';
    $motivo       = trim($_POST['motivo']  ?? '');

    $responseData = updateStatusUsuario($targetUserId, $novoStatus, $motivo);
    setFlashFromResponse($responseData);
    header('Location: ../pages/dashboardAdmin.php');
    exit;
}