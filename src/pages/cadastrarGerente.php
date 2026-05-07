<?php
// pages/cadastrarGerente.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
require_once __DIR__ . '/../utils/flashMessage.php';
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../crud/readQuadras.php';

requireAuth('locador', '../pages/loginLocador.php');

$locadorId = (int)$_SESSION['usuarioLogado'];
$quadras = getQuadrasByLocador($locadorId);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php $pageTitle = 'Cadastrar Gerente – Joga Fácil'; include __DIR__ . '/../includes/headTag.php'; ?>
</head>
<body class="authPageBody d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="loginFormCard card shadow-sm border-0 p-4" style="max-width: 480px; width: 100%;">
        <h3 class="formTitle fw-bold text-center mb-1">Cadastrar Gerente</h3>
        <p class="text-center small mb-4">O gerente receberá acesso administrativo às suas quadras</p>

        <?php renderFlash(); ?>

        <form action="../crud/createUsuario.php" method="POST" id="cadastroGerenteForm" novalidate>
            <input type="hidden" name="tipo" value="gerente">
            <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="source" value="dashboard">

            <div class="mb-3">
                <label for="inputNome" class="form-label fw-medium">Nome do Gerente</label>
                <input type="text" class="form-control formInput" id="inputNome" name="nome" placeholder="Nome do gerente" required>
            </div>
            <div class="mb-3">
                <label for="inputEmail" class="form-label fw-medium">E-mail</label>
                <input type="email" class="form-control formInput" id="inputEmail" name="email" placeholder="email@exemplo.com" required>
            </div>
            <div class="mb-3">
                <label for="inputCpf" class="form-label fw-medium">CPF</label>
                <input type="text" class="form-control formInput" id="inputCpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
                <div class="invalid-feedback errorMessage">CPF inválido.</div>
            </div>
            <div class="mb-3">
                <label for="inputSenha" class="form-label fw-medium">Senha</label>
                <input type="password" class="form-control formInput" id="inputSenha" name="senha" placeholder="Mínimo 6 caracteres" minlength="6" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Quadras que o gerente pode acessar</label>
                <?php if (empty($quadras)): ?>
                    <div class="alert alert-warning small mb-0">
                        Cadastre uma quadra antes de adicionar gerentes.
                    </div>
                <?php else: ?>
                    <div class="d-grid gap-2">
                        <?php foreach ($quadras as $quadraItem): ?>
                            <label class="form-check rounded-3 border p-3">
                                <input class="form-check-input me-2" type="checkbox" name="quadraIds[]" value="<?= (int)$quadraItem['id'] ?>">
                                <span class="form-check-label">
                                    <?= htmlspecialchars($quadraItem['nome']) ?>
                                    <small class="d-block text-secondary"><?= htmlspecialchars($quadraItem['endereco']) ?></small>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-warning w-100 submitBtn fw-bold text-dark mt-2" <?= empty($quadras) ? 'disabled' : '' ?>>Cadastrar Gerente</button>
        </form>
        <p class="text-center mt-3 small">
            <a href="../pages/dashboardLocador.php">← Voltar ao Painel</a>
        </p>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/appLogic.js"></script>
</body>
</html>
