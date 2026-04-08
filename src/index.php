<?php
// index.php – camelCase enforced
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/crud/readQuadras.php';
$activeArenas = getAllApprovedQuadras();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php
    $pageTitle       = 'Joga Fácil – Marketplace de Quadras Esportivas';
    $pageDescription = 'Encontre, reserve e jogue nas melhores quadras esportivas da sua região.';
    include __DIR__ . '/includes/headTag.php';
    ?>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <!-- HERO SECTION -->
    <section class="heroSection text-center text-white py-5">
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

            <!-- Category Filter Pills -->
            <div class="categoryFilters d-flex justify-content-center gap-4 mt-5 mb-1">
                <button class="categoryBtn btn active" data-category="todos">Todos</button>
                <button class="categoryBtn btn" data-category="futebol">Futebol</button>
                <button class="categoryBtn btn" data-category="volei">Vôlei</button>
                <button class="categoryBtn btn" data-category="tenis">Tênis</button>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <main class="container contentSection">

        <h2 class="sectionTitle fw-bold mb-4">Em Alta / Recomendados</h2>

        <div class="row g-4" id="arenasContainer">
            <?php if (empty($activeArenas)): ?>
                <div class="col-12">
                    <p class="text-center">Nenhuma quadra encontrada no momento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($activeArenas as $index => $arena): ?>
                    <?php
                        $primarySport      = !empty($arena['modalidades']) ? strtolower(explode(',', $arena['modalidades'])[0]) : 'futebol';
                        $primarySportBadge = !empty($arena['modalidades']) ? explode(',', $arena['modalidades'])[0] : 'Futebol';
                        $rating            = number_format(rand(40, 50) / 10, 1);
                        $bgImage           = $arena['imagem'] ?: 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?q=80&w=800';
                        $isNear            = ($index % 2 !== 0);
                    ?>
                    <div class="col-md-4 arena-item" data-sport="<?= htmlspecialchars($primarySport) ?>">
                        <a href="./pages/arenaDetalhe.php?id=<?= $arena['id'] ?>" class="arenaCard h-100">
                            <div class="arenaCardImgWrapper">
                                <img class="arenaCardImg" src="<?= htmlspecialchars($bgImage) ?>" alt="<?= htmlspecialchars($arena['nome']) ?>">
                                <?php if ($isNear): ?>
                                    <span class="arenaCardBadge arenaCardBadgeNear">Perto de Você</span>
                                <?php else: ?>
                                    <span class="arenaCardBadge">Recomendado</span>
                                <?php endif; ?>
                                <div class="arenaCardRating">
                                    <i class="bi bi-star-fill starIcon"></i> <?= $rating ?>
                                </div>
                            </div>
                            <div class="arenaCardBody">
                                <div class="arenaCardName"><?= htmlspecialchars($arena['nome']) ?></div>
                                <div class="arenaCardAddress">
                                    <i class="bi bi-geo-alt-fill" style="color:#4ade80; font-size:0.8rem;"></i>
                                    <?= htmlspecialchars($arena['endereco']) ?>
                                </div>
                                <div class="arenaCardSports">
                                    <span class="sportChip"><?= htmlspecialchars($primarySportBadge) ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/appLogic.js"></script>
</body>
</html>
