<?php
// pages/cadastrarGerente.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'locador') {
    header('Location: ../pages/loginLocador.php');
    exit;
}
require_once __DIR__ . '/../config/csrf.php';

$flashMessage = $_SESSION['flashMessage'] ?? null;
$flashType    = $_SESSION['flashType']    ?? 'info';
unset($_SESSION['flashMessage'], $_SESSION['flashType']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Gerente – Joga Fácil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/customStyles.css" rel="stylesheet">
</head>
<body class="authPageBody d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="loginFormCard card shadow-sm border-0 p-4" style="max-width: 480px; width: 100%;">
        <h3 class="formTitle fw-bold text-center mb-1">Cadastrar Gerente</h3>
        <p class=" text-center small mb-4">O gerente receberá acesso administrativo às suas quadras</p>

        <?php if ($flashMessage): ?>
            <div class="alert alert-<?= $flashType ?> alertMessage"><?= htmlspecialchars($flashMessage) ?></div>
        <?php endif; ?>

        <form action="../crud/createUsuario.php" method="POST" id="cadastroGerenteForm" novalidate>
            <input type="hidden" name="tipo" value="gerente">
            <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">

            <div class="mb-3">
                <label for="inputNome" class="form-label fw-medium">Nome do Gerente</label>
                <input type="text" class="form-control formInput" id="inputNome" name="nome" placeholder="Nome do gerente" required>
            </div>
            <div class="mb-3">
                <label for="inputEmail" class="form-label fw-medium">E-mail</label>
                <input type="email" class="form-control formInput" id="inputEmail" name="email" placeholder="email@exemplo.com" required>
            </div>
            <div class="mb-3">
                <label for="inputSenha" class="form-label fw-medium">Senha</label>
                <input type="password" class="form-control formInput" id="inputSenha" name="senha" placeholder="Mínimo 6 caracteres" minlength="6" required>
            </div>

            <button type="submit" class="btn btn-warning w-100 submitBtn fw-bold text-dark mt-2">Cadastrar Gerente</button>
        </form>
        <p class="text-center mt-3 small">
            <a href="../pages/dashboardLocador.php" class="">← Voltar ao Painel</a>
        </p>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/appLogic.js"></script>
</body>
</html>
