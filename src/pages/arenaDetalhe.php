<?php
// pages/arenaDetalhe.php – camelCase enforced
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../crud/readQuadras.php';
require_once __DIR__ . '/../crud/readHorarios.php';
require_once __DIR__ . '/../crud/readImagensQuadra.php';
require_once __DIR__ . '/../utils/flashMessage.php';
require_once __DIR__ . '/../config/csrf.php';

$arenaId = (int)($_GET['id'] ?? 0);
$quadra = $arenaId > 0 ? getActiveQuadraById($arenaId) : null;

if (!$quadra) {
    header('Location: ../index.php');
    exit;
}

$bookingDate = date('Y-m-d');
$slotsData = getBookingSlotsByQuadraDate((int)$quadra['id'], $bookingDate, $quadra['funcionamento'] ?? '08:00 - 23:00');
$facilidades = json_decode($quadra['facilidades'] ?? '', true) ?: [];

$arenaImagens = getImagensByQuadraId((int)$quadra['id']);

$defaultImage = 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?q=80&w=1600';
$capaSrc = $defaultImage;
if (!empty($quadra['imagem'])) {
    $capaSrc = str_starts_with($quadra['imagem'], 'http') ? $quadra['imagem'] : '../assets/uploads/quadras/' . htmlspecialchars($quadra['imagem']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php
    $pageTitle       = $quadra['nome'] . ' – Joga Fácil';
    $pageDescription = $quadra['nome'] . ' – Detalhes, horários e reserva de quadra esportiva.';
    include __DIR__ . '/../includes/headTag.php';
    ?>
</head>
<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <!-- ARENA HERO BANNER -->
    <div class="arenaDetailHero">
        <img class="arenaDetailHeroImg" src="<?= $capaSrc ?>" alt="<?= htmlspecialchars($quadra['nome']) ?>">
        <div class="arenaDetailHeroOverlay"></div>
        <div class="arenaDetailHeroMeta">
            <h1 class="arenaDetailHeroName"><?= htmlspecialchars($quadra['nome']) ?></h1>
            <div class="arenaDetailHeroAddr">
                <i class="bi bi-geo-alt-fill"></i>
                <?= htmlspecialchars($quadra['endereco']) ?>
            </div>
        </div>
        <div class="arenaDetailRating">
            <i class="bi bi-star-fill"></i> 4.8
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="container py-5">
        <?php renderFlash(); ?>

        <!-- Galeria / Carrossel -->
        <?php if (!empty($arenaImagens)): ?>
        <section class="arena-gallery-section mb-5">
            <div id="arenaGalleryCarousel" class="carousel slide arena-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($arenaImagens as $index => $img): ?>
                        <button type="button" data-bs-target="#arenaGalleryCarousel" data-bs-slide-to="<?= $index ?>" <?= $index === 0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Slide <?= $index + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-inner">
                    <?php foreach ($arenaImagens as $index => $img): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="../assets/uploads/quadras/<?= htmlspecialchars($img['nome_arquivo']) ?>" class="d-block w-100 arena-carousel-img" alt="Imagem da Galeria">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#arenaGalleryCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#arenaGalleryCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Próximo</span>
                </button>
            </div>
        </section>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Left column – Arena info -->
            <div class="col-lg-7">

                <div class="detailInfoCard">
                    <div class="detailInfoCardTitle">
                        <i class="bi bi-info-circle-fill cardTitleIcon"></i>
                        Sobre a Arena
                    </div>
                    <div class="detailInfoRow"><strong>Modalidades:</strong> <?= htmlspecialchars($quadra['modalidades'] ?? 'Não informado') ?></div>
                    <div class="detailInfoRow"><strong>Horário de Funcionamento:</strong> <?= htmlspecialchars($quadra['funcionamento'] ?? 'Não informado') ?></div>
                    <?php if (!empty($quadra['descricao'])): ?>
                        <div class="detailInfoRow mt-3 text-secondary" style="font-size: 0.88rem; font-style: italic;">
                            "<?= nl2br(htmlspecialchars($quadra['descricao'])) ?>"
                        </div>
                    <?php endif; ?>
                    <div class="detailInfoRow">
                        <strong class="detailCancelText">Política de Cancelamento:</strong>
                    </div>
                    <div class="detailCancelText">Cancelamento grátis até <?= htmlspecialchars($quadra['cancelamento_horas']) ?>h antes.</div>
                </div>

                <div class="detailInfoCard">
                    <div class="detailInfoCardTitle">
                        <i class="bi bi-check-circle-fill cardTitleIcon"></i>
                        Facilidades
                    </div>
                    <ul class="facilidadesList">
                        <?php if (empty($facilidades)): ?>
                            <li class="facilidadesItem">Nenhuma facilidade cadastrada</li>
                        <?php else: ?>
                            <?php foreach ($facilidades as $facilidadeItem): ?>
                                <li class="facilidadesItem"><?= htmlspecialchars($facilidadeItem) ?></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="detailInfoCard">
                    <div class="detailInfoCardTitle">
                        <i class="bi bi-telephone-fill cardTitleIcon"></i>
                        Contato
                    </div>
                    <div class="detailPhone"><?= htmlspecialchars($quadra['telefone'] ?? 'Não informado') ?></div>
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

                    <form action="../crud/createReserva.php" method="POST" id="bookingForm">
                        <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="arena_id" value="<?= (int)$quadra['id'] ?>">
                        <input type="hidden" name="horario_id" id="selectedHorarioId">
                        <input type="hidden" name="modo_lobby" id="selectedModoLobby" value="0">

                        <div class="slotsGrid" id="slotsGrid" data-slots='<?= htmlspecialchars(json_encode($slotsData), ENT_QUOTES, 'UTF-8') ?>'></div>

                        <button class="lobbyCard" id="lobbyToggle" type="button">
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

                        <div class="lobbyOptionsPanel d-none" id="lobbyOptionsPanel">
                            <div class="lobbyOptionsLabel">Visibilidade do lobby</div>
                            <div class="lobbyVisibilityTabs">
                                <label class="lobbyVisibilityOption">
                                    <input type="radio" name="visibilidade_lobby" value="publico" checked>
                                    <span><i class="bi bi-globe2"></i> Público</span>
                                </label>
                                <label class="lobbyVisibilityOption">
                                    <input type="radio" name="visibilidade_lobby" value="privado">
                                    <span><i class="bi bi-lock-fill"></i> Privado</span>
                                </label>
                            </div>
                            <div class="lobbyCodeField d-none" id="lobbyCodeField">
                                <label class="form-label small text-secondary mb-1" for="codigoAcessoInput">Código de acesso</label>
                                <input type="text" class="form-control" id="codigoAcessoInput" name="codigo_acesso" maxlength="20" placeholder="Ex: JOGA2026" autocomplete="off">
                                <div class="form-text">Defina um código e compartilhe com quem pode entrar na partida.</div>
                            </div>
                        </div>

                        <button class="bookingConfirmBtn disabled" id="confirmBtn" type="submit" disabled>
                            Selecione um horário
                        </button>
                    </form>

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
