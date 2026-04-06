<?php
// pages/arenaDetalhe.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Arena Gol de Placa – Detalhes, horários e reserva de quadra esportiva.">
    <title>Arena Gol de Placa – Joga Fácil</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Styles -->
    <link href="../assets/css/customStyles.css" rel="stylesheet">
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <!-- ============================================================
         ARENA HERO BANNER
    ============================================================ -->
    <div class="arenaDetailHero">
        <img
            class="arenaDetailHeroImg"
            src="https://images.unsplash.com/photo-1529900748604-07564a03e7a6?q=80&w=1600"
            alt="Arena Gol de Placa"
        >
        <div class="arenaDetailHeroOverlay"></div>
        <div class="arenaDetailHeroMeta">
            <h1 class="arenaDetailHeroName">Arena Gol de Placa</h1>
            <div class="arenaDetailHeroAddr">
                <i class="bi bi-geo-alt-fill"></i>
                Rua do Ouro, 123 – Centro
            </div>
        </div>
        <div class="arenaDetailRating">
            <i class="bi bi-star-fill"></i> 4.8
        </div>
    </div>

    <!-- ============================================================
         MAIN CONTENT
    ============================================================ -->
    <main class="container py-5">
        <div class="row g-4">

            <!-- ========================================
                 LEFT COLUMN – Arena Info
            ======================================== -->
            <div class="col-lg-7">

                <!-- Sobre a Arena -->
                <div class="detailInfoCard">
                    <div class="detailInfoCardTitle">
                        <i class="bi bi-info-circle-fill cardTitleIcon"></i>
                        Sobre a Arena
                    </div>
                    <div class="detailInfoRow"><strong>Modalidades:</strong> Futebol</div>
                    <div class="detailInfoRow"><strong>Horário de Funcionamento:</strong> 08:00 – 23:00</div>
                    <div class="detailInfoRow">
                        <strong class="detailCancelText">Política de Cancelamento:</strong>
                    </div>
                    <div class="detailCancelText">Cancelamento grátis até 24h antes.</div>
                </div>

                <!-- Facilidades -->
                <div class="detailInfoCard">
                    <div class="detailInfoCardTitle">
                        <i class="bi bi-check-circle-fill cardTitleIcon"></i>
                        Facilidades
                    </div>
                    <ul class="facilidadesList">
                        <li class="facilidadesItem">Cantina</li>
                        <li class="facilidadesItem">Aluguel de Bola</li>
                        <li class="facilidadesItem">Vestiários</li>
                        <li class="facilidadesItem">Bebedouro</li>
                    </ul>
                </div>

                <!-- Contato -->
                <div class="detailInfoCard">
                    <div class="detailInfoCardTitle">
                        <i class="bi bi-telephone-fill cardTitleIcon"></i>
                        Contato
                    </div>
                    <div class="detailPhone">(11) 99999-9999</div>
                </div>

            </div>

            <!-- ========================================
                 RIGHT COLUMN – Booking Widget (sticky)
            ======================================== -->
            <div class="col-lg-5">
                <div class="bookingWidget">

                    <!-- Title -->
                    <div class="bookingWidgetTitle">Escolha um Horário</div>

                    <!-- Period tabs: Manhã / Tarde / Noite -->
                    <div class="periodTabsWrapper">
                        <button class="periodTab active" id="tabManha" data-period="manha">Manhã</button>
                        <button class="periodTab" id="tabTarde" data-period="tarde">Tarde</button>
                        <button class="periodTab" id="tabNoite" data-period="noite">Noite</button>
                    </div>

                    <!-- Slots grid (filled by JS) -->
                    <div class="slotsGrid" id="slotsGrid">
                        <!-- populated by arenaDetailLogic below -->
                    </div>

                    <!-- Lobby Mode Select -->
                    <button class="lobbyCard" id="lobbyToggle">
                        <div class="lobbyIconWrapper" id="lobbyIconWrapper">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="lobbyTitle">Abrir Partida (Modo Lobby)</div>
                            <div class="lobbyDesc">
                                Permita que outras pessoas entrem na sua reserva. Ideal para rachões e fechar times. O valor da quadra será dividido entre os participantes.
                            </div>
                        </div>
                        <div class="lobbyRadio" id="lobbyRadio">
                            <div class="lobbyRadioInner" id="lobbyRadioInner"></div>
                        </div>
                    </button>

                    <!-- CTA Button -->
                    <button class="bookingConfirmBtn disabled" id="confirmBtn" disabled>
                        Selecione um horário
                    </button>

                </div>
            </div>

        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Logic -->
    <script src="../assets/js/appLogic.js"></script>

    <!-- Arena Detail Logic -->
    <script src="../assets/js/arenaDetailLogic.js"></script>

</body>
</html>
