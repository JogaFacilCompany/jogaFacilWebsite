<?php
// crud/updatePerfil.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../crud/updateUsuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    require_once __DIR__ . '/../config/csrf.php';
    require_once __DIR__ . '/../utils/flashMessage.php';

    if (!isset($_SESSION['usuarioLogado'])) {
        header('Location: ../pages/escolherLogin.php');
        exit;
    }

    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        setFlash('Requisição inválida. Tente novamente.', 'danger');
        header('Location: ../pages/perfil.php');
        exit;
    }

    $targetUserId = (int)$_SESSION['usuarioLogado'];
    
    // We only pass nome, email, cpf, senha to be updated
    // Tipo is deliberately ignored as it is not passed and even if passed, updateUsuario doesn't process it.
    
    $updateData = [
        'nome'  => $_POST['nome'] ?? '',
        'email' => $_POST['email'] ?? '',
        'cpf'   => $_POST['cpf'] ?? '',
        'senha' => $_POST['senha'] ?? ''
    ];

    $responseData = updateUsuario($targetUserId, $updateData);
    setFlashFromResponse($responseData);
    
    if ($responseData['sucesso'] && !empty($updateData['nome'])) {
        // Atualizar o nome na sessão para refletir no cabeçalho imediatamente
        $_SESSION['usuarioNome'] = trim($updateData['nome']);
    }

    header('Location: ../pages/perfil.php');
    exit;
} else {
    header('Location: ../pages/perfil.php');
    exit;
}
