<?php
// pages/escolherCadastro.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
initSession();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php $pageTitle = 'Escolher Cadastro – Joga Fácil'; include __DIR__ . '/../includes/headTag.php'; ?>
</head>
<body class="authPageBody d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="loginFormCard card shadow-sm border-0 p-4" style="max-width: 500px; width: 100%;">
        <h3 class="formTitle fw-bold text-center mb-1">Crie sua Conta</h3>
        <p class="text-white-50 text-center small mb-4">Escolha o seu perfil para começar</p>

        <div class="d-flex flex-column gap-3">
            <a href="cadastroLocador.php" class="choiceCard">
                <div class="choiceIcon">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Quero ser Locador</h5>
                    <small class="text-white-50">Cadastrar e anunciar minhas quadras</small>
                </div>
            </a>

            <a href="cadastroLocatario.php" class="choiceCard">
                <div class="choiceIcon" style="color: #4ade80; background-color: rgba(74, 222, 128, 0.1);">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Quero ser Locatário</h5>
                    <small class="text-white-50">Encontrar e reservar quadras</small>
                </div>
            </a>
        </div>

        <p class="text-center mt-4 small">Já tem uma conta? <a href="../pages/escolherLogin.php" class="authLink text-success fw-medium">Entrar</a></p>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
