<?php
// pages/cadastroLocador.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
require_once __DIR__ . '/../utils/flashMessage.php';
require_once __DIR__ . '/../config/csrf.php';

initSession();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php $pageTitle = 'Cadastro de Locador – Joga Fácil'; include __DIR__ . '/../includes/headTag.php'; ?>
</head>
<body class="authPageBody d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="loginFormCard card shadow-sm border-0 p-4" style="max-width: 480px; width: 100%;">
        <h3 class="formTitle fw-bold text-center mb-1">Cadastro de Locador</h3>
        <p class="text-center small mb-4">Crie sua conta para gerenciar suas quadras</p>

        <?php renderFlash(); ?>

        <form action="../crud/createUsuario.php" method="POST" id="cadastroLocadorForm" novalidate>
            <input type="hidden" name="tipo" value="locador">
            <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">

            <div class="mb-3">
                <label for="inputNome" class="form-label fw-medium">Nome Completo</label>
                <input type="text" class="form-control formInput" id="inputNome" name="nome" placeholder="Seu nome" required>
                <div class="invalid-feedback errorMessage">Nome obrigatório.</div>
            </div>
            <div class="mb-3">
                <label for="inputEmail" class="form-label fw-medium">E-mail</label>
                <input type="email" class="form-control formInput" id="inputEmail" name="email" placeholder="email@exemplo.com" required>
                <div class="invalid-feedback errorMessage">E-mail inválido.</div>
            </div>
            <div class="mb-3">
                <label for="inputSenha" class="form-label fw-medium">Senha</label>
                <input type="password" class="form-control formInput" id="inputSenha" name="senha" placeholder="Mínimo 6 caracteres" minlength="6" required>
                <div class="invalid-feedback errorMessage">Senha obrigatória (mín. 6 caracteres).</div>
            </div>

            <button type="submit" class="btn btn-success w-100 submitBtn fw-bold mt-2">Cadastrar como Locador</button>
        </form>
        <p class="text-center mt-3 small">Já tem uma conta? <a href="../pages/escolherLogin.php" class="authLink text-success fw-medium">Entrar</a></p>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/appLogic.js"></script>
</body>
</html>
