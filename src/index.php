<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

            <!-- Category Filter Pills -->
            <div class="categoryFilters d-flex justify-content-center gap-4 mt-5 mb-1">
                <button class="categoryBtn btn active" data-category="todos">Todos</button>
                <button class="categoryBtn btn" data-category="futebol">Futebol</button>
                <button class="categoryBtn btn" data-category="volei">Vôlei</button>
                <button class="categoryBtn btn" data-category="tenis">Tênis</button>
            </div>
        </div>
    </section>

    <!-- ============================================================
         MAIN CONTENT
    ============================================================ -->
    <main class="container contentSection">

        <!-- Em Alta / Recomendados -->
        <h2 class="sectionTitle fw-bold mb-4">Em Alta / Recomendados</h2>

        <div class="row g-4" id="arenasContainer">

            <!-- Arena Card 1 -->
            <div class="col-md-4" data-sport="futebol">
                <a href="./pages/arena-detalhe.php" class="arenaCard h-100">
                    <div class="arenaCardImgWrapper">
                        <img class="arenaCardImg" src="https://images.unsplash.com/photo-1529900748604-07564a03e7a6?q=80&w=800" alt="Arena Gol de Placa">
                        <span class="arenaCardBadge">Recomendado</span>
                        <div class="arenaCardRating">
                            <i class="bi bi-star-fill starIcon"></i> 4.8
                        </div>
                    </div>
                    <div class="arenaCardBody">
                        <div class="arenaCardName">Arena Gol de Placa</div>
                        <div class="arenaCardAddress">
                            <i class="bi bi-geo-alt-fill" style="color:#4ade80; font-size:0.8rem;"></i>
                            Rua do Ouro, 123 – Centro
                        </div>
                        <div class="arenaCardSports">
                            <span class="sportChip">Futebol</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Arena Card 2 -->
            <div class="col-md-4" data-sport="volei">
                <a href="./pages/arena-detalhe.php" class="arenaCard h-100">
                    <div class="arenaCardImgWrapper">
                        <img class="arenaCardImg" src="https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?q=80&w=800" alt="Quadra Sol e Mar">
                        <span class="arenaCardBadge">Recomendado</span>
                        <div class="arenaCardRating">
                            <i class="bi bi-star-fill starIcon"></i> 4.9
                        </div>
                    </div>
                    <div class="arenaCardBody">
                        <div class="arenaCardName">Quadra Sol e Mar</div>
                        <div class="arenaCardAddress">
                            <i class="bi bi-geo-alt-fill" style="color:#4ade80; font-size:0.8rem;"></i>
                            Av. Beira Mar, 500 – Balneário
                        </div>
                        <div class="arenaCardSports">
                            <span class="sportChip">Vôlei</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Arena Card 3 -->
            <div class="col-md-4" data-sport="tenis">
                <a href="./pages/arena-detalhe.php" class="arenaCard h-100">
                    <div class="arenaCardImgWrapper">
                        <img class="arenaCardImg" src="https://images.unsplash.com/photo-1554068865-24cecd4e34b8?q=80&w=800" alt="Tênis Club Premium">
                        <span class="arenaCardBadge">Recomendado</span>
                        <div class="arenaCardRating">
                            <i class="bi bi-star-fill starIcon"></i> 4.7
                        </div>
                    </div>
                    <div class="arenaCardBody">
                        <div class="arenaCardName">Tênis Club Premium</div>
                        <div class="arenaCardAddress">
                            <i class="bi bi-geo-alt-fill" style="color:#4ade80; font-size:0.8rem;"></i>
                            Rua das Raquetes, 77 – Jardins
                        </div>
                        <div class="arenaCardSports">
                            <span class="sportChip">Tênis</span>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <!-- Próximo a Você -->
        <h2 class="sectionTitle fw-bold mb-4 mt-5">Próximo a Você</h2>

        <div class="row g-4" id="nearYouContainer">

            <!-- Near Card 1 -->
            <div class="col-md-4" data-sport="futebol">
                <a href="./pages/arena-detalhe.php" class="arenaCard h-100">
                    <div class="arenaCardImgWrapper">
                        <img class="arenaCardImg" src="https://images.unsplash.com/photo-1606925797300-0b35e9d1794e?q=80&w=800" alt="Estádio do Bairro">
                        <span class="arenaCardBadge arenaCardBadgeNear">Perto de Você</span>
                        <div class="arenaCardRating">
                            <i class="bi bi-star-fill starIcon"></i> 4.5
                        </div>
                    </div>
                    <div class="arenaCardBody">
                        <div class="arenaCardName">Estádio do Bairro</div>
                        <div class="arenaCardAddress">
                            <i class="bi bi-geo-alt-fill" style="color:#4ade80; font-size:0.8rem;"></i>
                            Rua XV, 200 – Vila Nova
                        </div>
                        <div class="arenaCardSports">
                            <span class="sportChip">Futebol</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Near Card 2 -->
            <div class="col-md-4" data-sport="volei">
                <a href="./pages/arena-detalhe.php" class="arenaCard h-100">
                    <div class="arenaCardImgWrapper">
                        <img class="arenaCardImg" src="https://images.unsplash.com/photo-1544717305-2782549b5136?q=80&w=800" alt="Arena Fit Center">
                        <span class="arenaCardBadge arenaCardBadgeNear">Perto de Você</span>
                        <div class="arenaCardRating">
                            <i class="bi bi-star-fill starIcon"></i> 4.3
                        </div>
                    </div>
                    <div class="arenaCardBody">
                        <div class="arenaCardName">Arena Fit Center</div>
                        <div class="arenaCardAddress">
                            <i class="bi bi-geo-alt-fill" style="color:#4ade80; font-size:0.8rem;"></i>
                            Av. Central, 89 – Centro
                        </div>
                        <div class="arenaCardSports">
                            <span class="sportChip">Vôlei</span>
                            <span class="sportChip">Futebol</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Near Card 3 -->
            <div class="col-md-4" data-sport="tenis">
                <a href="./pages/arena-detalhe.php" class="arenaCard h-100">
                    <div class="arenaCardImgWrapper">
                        <img class="arenaCardImg" src="https://images.unsplash.com/photo-1511886929837-354d827aae26?q=80&w=800" alt="Ace Sports Arena">
                        <span class="arenaCardBadge arenaCardBadgeNear">Perto de Você</span>
                        <div class="arenaCardRating">
                            <i class="bi bi-star-fill starIcon"></i> 4.6
                        </div>
                    </div>
                    <div class="arenaCardBody">
                        <div class="arenaCardName">Ace Sports Arena</div>
                        <div class="arenaCardAddress">
                            <i class="bi bi-geo-alt-fill" style="color:#4ade80; font-size:0.8rem;"></i>
                            Rua dos Esportes, 31 – Pinheiros
                        </div>
                        <div class="arenaCardSports">
                            <span class="sportChip">Tênis</span>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Logic -->
    <script src="./assets/js/appLogic.js"></script>
</body>
</html>
