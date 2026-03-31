<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/crud/readQuadras.php';

$quadrasAtivas = readAllQuadrasAtivas();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Encontre, reserve e jogue nas melhores quadras esportivas da sua região.">
    <title>Joga Fácil – Marketplace de Quadras Esportivas</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Styles -->
    <link href="./assets/css/customStyles.css" rel="stylesheet">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <!-- ============================================================
         HERO SECTION
    ============================================================ -->
    <section class="heroSection text-center text-white py-5">
        <!-- Background overlay texture -->
        <div class="heroBgOverlay"></div>

        <div class="container py-5 heroInner">
            <h1 class="heroTitle fw-bold mb-3">Onde é o jogo hoje?</h1>
            <p class="heroSubtitle lead mb-5">Encontre, reserve e jogue. As melhores quadras da sua região estão a apenas um clique de distância.</p>

            <!-- Search Bar -->
            <div class="searchContainer mx-auto mb-4 d-flex align-items-center p-2" style="max-width: 800px;">
                <span class="searchIcon ps-3 pe-2">
                    <i class="bi bi-search" style="font-size:1.1rem;"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-0 shadow-none bg-transparent" placeholder="Buscar por nome, região ou arena...">
                <button type="button" id="searchButton" class="btn heroSearchBtn rounded-pill px-4 ms-2">Pesquisar</button>
            </div>

            <!-- Category Filter Pills (disabled until data model supports categories) -->
        </div>
    </section>

    <!-- ============================================================
         MAIN CONTENT
    ============================================================ -->
    <main class="container contentSection">

        <h2 class="sectionTitle fw-bold mb-4">Quadras Disponíveis</h2>

        <div class="row g-4" id="arenasContainer">
            <?php if (empty($quadrasAtivas)): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-geo-alt emptyStateIcon"></i>
                        <p class="emptyStateText">Nenhuma quadra disponível no momento. Volte em breve!</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($quadrasAtivas as $quadra): ?>
                    <div class="col-md-4">
                        <a href="./pages/arena-detalhe.php?id=<?= $quadra['id'] ?>" class="arenaCard h-100">
                            <div class="arenaCardImgWrapper">
                                <?php if (!empty($quadra['imagemUrl'])): ?>
                                    <img class="arenaCardImg" src="<?= htmlspecialchars($quadra['imagemUrl']) ?>" alt="<?= htmlspecialchars($quadra['nome']) ?>">
                                <?php else: ?>
                                    <div class="arenaCardImg arenaCardImgPlaceholder">
                                        <i class="bi bi-dribbble"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="arenaCardBody">
                                <div class="arenaCardName"><?= htmlspecialchars($quadra['nome']) ?></div>
                                <div class="arenaCardAddress">
                                    <i class="bi bi-geo-alt-fill" style="color:#4ade80; font-size:0.8rem;"></i>
                                    <?= htmlspecialchars($quadra['endereco']) ?>
                                </div>
                                <div class="arenaCardPrice">
                                    <span class="priceChip">R$ <?= number_format($quadra['precoHora'], 2, ',', '.') ?>/h</span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Logic -->
    <script src="./assets/js/appLogic.js"></script>
</body>
</html>
