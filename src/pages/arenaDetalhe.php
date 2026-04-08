<?php
// pages/arenaDetalhe.php – camelCase enforced
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php
    $pageTitle       = 'Arena Gol de Placa – Joga Fácil';
    $pageDescription = 'Arena Gol de Placa – Detalhes, horários e reserva de quadra esportiva.';
    include __DIR__ . '/../includes/headTag.php';
    ?>
</head>
<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <!-- ARENA HERO BANNER -->
    <div class="arenaDetailHero">
        <img class="arenaDetailHeroImg" src="https://images.unsplash.com/photo-1529900748604-07564a03e7a6?q=80&w=1600" alt="Arena Gol de Placa">
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

    <!-- MAIN CONTENT -->
    <main class="container py-5">
        <div class="row g-4">

            <!-- Left column – Arena info -->
            <div class="col-lg-7">

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

                <div class="detailInfoCard">
                    <div class="detailInfoCardTitle">
                        <i class="bi bi-telephone-fill cardTitleIcon"></i>
                        Contato
                    </div>
                    <div class="detailPhone">(11) 99999-9999</div>
                </div>

            </div>

            <!-- Right column – Booking widget -->
            <div class="col-lg-5">
                <div class="bookingWidget">

                    <div class="bookingWidgetTitle">Escolha um Horário</div>

                    <div class="periodTabsWrapper">
                        <button class="periodTab active" id="tabManha" data-period="manha">Manhã</button>
                        <button class="periodTab" id="tabTarde" data-period="tarde">Tarde</button>
                        <button class="periodTab" id="tabNoite" data-period="noite">Noite</button>
                    </div>

                    <div class="slotsGrid" id="slotsGrid"></div>

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

                    <button class="bookingConfirmBtn disabled" id="confirmBtn" disabled>
                        Selecione um horário
                    </button>

                </div>
            </div>

        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/appLogic.js"></script>
    <script src="../assets/js/arenaDetailLogic.js"></script>
</body>
</html>
