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
                <?php
                    // Build avatar for header
                    $headerFoto = $_SESSION['usuarioFoto'] ?? null;
                    $headerFotoUrl = $headerFoto ? $baseUrl . 'assets/uploads/perfil/' . htmlspecialchars($headerFoto) : null;
                ?>
                <?php if ($headerFotoUrl): ?>
                    <img src="<?= $headerFotoUrl ?>" alt="Avatar" class="header-avatar">
                <?php else: ?>
                    <span class="header-avatar-default">
                        <svg viewBox="0 0 24 24" fill="currentColor" color="#6c757d">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v1.2c0 .7.5 1.2 1.2 1.2h16.8c.7 0 1.2-.5 1.2-1.2v-1.2c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </span>
                <?php endif; ?>
                <span class="text-white fw-medium">Olá, <?= htmlspecialchars($_SESSION['usuarioNome']) ?></span>
                <a href="<?= $baseUrl ?>pages/perfil.php" class="text-white text-decoration-none fw-medium">Meu Perfil</a>
                <?php if ($_SESSION['usuarioTipo'] === 'locatario'): ?>
                    <a href="<?= $baseUrl ?>pages/minhasReservas.php" class="text-white text-decoration-none fw-medium">Minhas Reservas</a>
                    <a href="<?= $baseUrl ?>pages/listaLobbies.php" class="text-white text-decoration-none fw-medium">Lobbies</a>
                <?php endif; ?>
                <?php if ($_SESSION['usuarioTipo'] === 'locador' || $_SESSION['usuarioTipo'] === 'gerente'): ?>
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
