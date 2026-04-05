<?php
// pages/login-admin.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (isset($_SESSION['usuarioLogado'])) {
    header('Location: ../index.php');
    exit;
}
require_once __DIR__ . '/../crud/readUsuarios.php';
require_once __DIR__ . '/../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrfToken'] ?? '')) {
        $loginError = 'Requisição inválida. Tente novamente.';
    } else {
        $inputEmail = trim($_POST['email'] ?? '');
        $inputSenha = $_POST['senha'] ?? '';
        $foundUser  = findUsuarioByEmailAndSenha($inputEmail, $inputSenha);
        
        if (!$foundUser) {
            $loginError = 'E-mail ou senha incorretos.';
        } elseif ($foundUser['tipo'] !== 'admin') {
            $loginError = 'Acesso Negado. Esta conta não possui privilégios de Administrador.';
        } else {
            session_regenerate_id(true);
            $_SESSION['usuarioLogado'] = $foundUser['id'];
            $_SESSION['usuarioNome']   = $foundUser['nome'];
            $_SESSION['usuarioTipo']   = $foundUser['tipo'];
            header('Location: ../pages/dashboard-admin.php');
            exit;
        }
    }
}

$flashMessage = $_SESSION['flashMessage'] ?? null;
$flashType    = $_SESSION['flashType']    ?? 'info';
unset($_SESSION['flashMessage'], $_SESSION['flashType']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login do Administrador – Joga Fácil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/customStyles.css" rel="stylesheet">
</head>
<body class="authPageBody d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="loginFormCard card shadow-sm border-0 p-4" style="max-width: 440px; width: 100%;">
        <h3 class="formTitle fw-bold text-center mb-1 text-danger">Acesso Administrativo</h3>
        <p class="text-white-50 text-center small mb-4">Acesso restrito a administradores do sistema</p>

        <?php if (!empty($loginError)): ?>
            <div class="alert alert-danger alertMessage"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>
        <?php if ($flashMessage): ?>
            <div class="alert alert-<?= $flashType ?> alertMessage"><?= htmlspecialchars($flashMessage) ?></div>
        <?php endif; ?>

        <form action="" method="POST" id="loginAdminForm" novalidate>
            <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
            <div class="mb-3">
                <label for="inputEmail" class="form-label fw-medium">E-mail de Admin</label>
                <input type="email" class="form-control formInput" id="inputEmail" name="email" placeholder="admin@jogafacil.com" required>
            </div>
            <div class="mb-3">
                <label for="inputSenha" class="form-label fw-medium">Senha</label>
                <input type="password" class="form-control formInput" id="inputSenha" name="senha" placeholder="Sua senha secreta" required>
            </div>
            <button type="submit" class="btn btn-danger w-100 submitBtn fw-bold mt-2" style="background-color: #ef4444; border-color: #ef4444;">Autenticar Admin</button>
        </form>
        <p class="text-center mt-4 small"><a href="../pages/escolher-login.php" class="text-white-50 text-decoration-none">Voltar para opções de login</a></p>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
