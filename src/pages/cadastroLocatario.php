<?php
// pages/cadastroLocatario.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
require_once __DIR__ . '/../utils/flashMessage.php';
require_once __DIR__ . '/../config/csrf.php';

initSession();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php $pageTitle = 'Cadastro de Locatário – Joga Fácil'; include __DIR__ . '/../includes/headTag.php'; ?>
</head>
<body class="authPageBody d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="loginFormCard card shadow-sm border-0 p-4" style="max-width: 480px; width: 100%;">
        <h3 class="formTitle fw-bold text-center mb-1">Cadastro de Locatário</h3>
        <p class="text-center small mb-4">Crie sua conta para reservar quadras</p>

        <?php renderFlash(); ?>

        <form action="../crud/createUsuario.php" method="POST" id="cadastroLocatarioForm" novalidate>
            <input type="hidden" name="tipo" value="locatario">
            <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">

            <div class="mb-3">
                <label for="inputNome" class="form-label fw-medium">Nome Completo</label>
                <input type="text" class="form-control formInput" id="inputNome" name="nome" placeholder="Seu nome" required>
            </div>
            <div class="mb-3">
                <label for="inputCpf" class="form-label fw-medium">CPF</label>
                <input type="text" class="form-control formInput" id="inputCpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
                <div class="invalid-feedback errorMessage">CPF inválido.</div>
            </div>
            <div class="mb-3">
                <label for="inputEmail" class="form-label fw-medium">E-mail</label>
                <input type="email" class="form-control formInput" id="inputEmail" name="email" placeholder="email@exemplo.com" required>
            </div>
            <div class="mb-3">
                <label for="inputSenha" class="form-label fw-medium">Senha</label>
                <input type="password" class="form-control formInput" id="inputSenha" name="senha" placeholder="Mínimo 6 caracteres" minlength="6" required>
            </div>

            <button type="submit" class="btn btn-success w-100 submitBtn fw-bold mt-2">Cadastrar como Locatário</button>
        </form>
        <p class="text-center mt-3 small">Já tem conta? <a href="../pages/loginLocatario.php" class="authLink text-success fw-medium">Entrar</a></p>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/appLogic.js"></script>
</body>
</html>
