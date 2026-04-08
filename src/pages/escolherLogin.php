<?php
// pages/escolherLogin.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
initSession();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php $pageTitle = 'Escolher Login – Joga Fácil'; include __DIR__ . '/../includes/headTag.php'; ?>
</head>
<body class="authPageBody d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="loginFormCard card shadow-sm border-0 p-4" style="max-width: 500px; width: 100%;">
        <h3 class="formTitle fw-bold text-center mb-1">Entrar no Joga Fácil</h3>
        <p class="text-white-50 text-center small mb-4">Escolha como deseja acessar a plataforma</p>

        <div class="d-flex flex-column gap-3">
            <a href="loginLocador.php" class="choiceCard">
                <div class="choiceIcon">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Sou Locador</h5>
                    <small class="text-white-50">Acessar painel e gerenciar quadras</small>
                </div>
            </a>

            <a href="loginLocatario.php" class="choiceCard">
                <div class="choiceIcon" style="color: #4ade80; background-color: rgba(74, 222, 128, 0.1);">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Sou Locatário</h5>
                    <small class="text-white-50">Agendar, pagar e encontrar jogos</small>
                </div>
            </a>

            <a href="loginGerente.php" class="choiceCard">
                <div class="choiceIcon" style="color: #60a5fa; background-color: rgba(96, 165, 250, 0.1);">
                    <i class="bi bi-briefcase"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Gerente de Quadra</h5>
                    <small class="text-white-50">Gerenciar caixa e marcações</small>
                </div>
            </a>

            <a href="loginAdmin.php" class="choiceCard">
                <div class="choiceIcon" style="color: #f87171; background-color: rgba(248, 113, 113, 0.1);">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Administrador</h5>
                    <small class="text-white-50">Acesso completo ao sistema</small>
                </div>
            </a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
