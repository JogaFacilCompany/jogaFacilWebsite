<?php
// includes/header.php
$baseUrl = (strpos($_SERVER['SCRIPT_NAME'], '/pages/') !== false || strpos($_SERVER['SCRIPT_NAME'], '/crud/') !== false) ? '../' : './';
?>
<header class="mainHeader customGreenBg py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="<?= $baseUrl ?>index.php" class="text-decoration-none">
            <h2 class="logoTitle text-white m-0 fst-italic fw-bold">Joga Fácil</h2>
        </a>
        <nav class="authNav d-flex align-items-center gap-3">
            <?php if (isset($_SESSION['usuarioLogado'])): ?>
                <span class="text-white fw-medium">Olá, <?= htmlspecialchars($_SESSION['usuarioNome']) ?></span>
                <?php if ($_SESSION['usuarioTipo'] === 'locador'): ?>
                    <a href="<?= $baseUrl ?>pages/dashboardLocador.php" class="text-white text-decoration-none fw-medium">Painel</a>
                <?php elseif ($_SESSION['usuarioTipo'] === 'admin'): ?>
                    <a href="<?= $baseUrl ?>pages/dashboardAdmin.php" class="text-white text-decoration-none fw-medium">Painel</a>
                <?php endif; ?>
                <a href="<?= $baseUrl ?>pages/logout.php" class="btn btn-outline-light rounded-pill px-3">Sair</a>
            <?php else: ?>
                <a href="<?= $baseUrl ?>pages/escolherLogin.php" class="text-white text-decoration-none fw-medium">Entrar</a>
                <a href="<?= $baseUrl ?>pages/escolherCadastro.php" class="btn btn-warning fw-bold text-dark rounded-pill px-4 shadow-sm customYellowBtn">Cadastre-se</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
