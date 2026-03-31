<?php
// pages/arena-detalhe.php – Dynamic court detail page
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../crud/readQuadras.php';
require_once __DIR__ . '/../config/csrf.php';

$quadraId = (int)($_GET['id'] ?? 0);
$quadra   = readQuadraById($quadraId);

if (!$quadra || !$quadra['ativo']) {
    $_SESSION['flashMessage'] = 'Quadra não encontrada.';
    $_SESSION['flashType']    = 'warning';
    header('Location: ../index.php');
    exit;
}

$horarios    = readHorariosByQuadra($quadraId);
$csrfToken   = generateCsrfToken();
$isLocatario = isset($_SESSION['usuarioTipo']) && $_SESSION['usuarioTipo'] === 'locatario';
$isLoggedIn  = isset($_SESSION['usuarioLogado']);

$flashMessage = $_SESSION['flashMessage'] ?? null;
$flashType    = $_SESSION['flashType']    ?? 'info';
unset($_SESSION['flashMessage'], $_SESSION['flashType']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($quadra['nome']) ?> – Detalhes, horários e reserva.">
    <title><?= htmlspecialchars($quadra['nome']) ?> – Joga Fácil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../assets/css/customStyles.css" rel="stylesheet">
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <!-- ARENA HERO BANNER -->
    <div class="arenaDetailHero">
        <?php if (!empty($quadra['imagemUrl'])): ?>
            <img class="arenaDetailHeroImg" src="<?= htmlspecialchars($quadra['imagemUrl']) ?>" alt="<?= htmlspecialchars($quadra['nome']) ?>">
        <?php else: ?>
            <div class="arenaDetailHeroImg arenaDetailHeroPlaceholder">
                <i class="bi bi-dribbble"></i>
            </div>
        <?php endif; ?>
        <div class="arenaDetailHeroOverlay"></div>
        <div class="arenaDetailHeroMeta">
            <h1 class="arenaDetailHeroName"><?= htmlspecialchars($quadra['nome']) ?></h1>
            <div class="arenaDetailHeroAddr">
                <i class="bi bi-geo-alt-fill"></i>
                <?= htmlspecialchars($quadra['endereco']) ?>
            </div>
        </div>
        <div class="arenaDetailRating">
            R$ <?= number_format($quadra['precoHora'], 2, ',', '.') ?>/h
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="container py-5">

        <?php if ($flashMessage): ?>
            <div class="alert alert-<?= $flashType ?> alertMessage mb-4"><?= htmlspecialchars($flashMessage) ?></div>
        <?php endif; ?>

        <div class="row g-4">

            <!-- LEFT COLUMN – Arena Info -->
            <div class="col-lg-7">

                <?php if (!empty($quadra['descricao'])): ?>
                <div class="detailInfoCard">
                    <div class="detailInfoCardTitle">
                        <i class="bi bi-info-circle-fill cardTitleIcon"></i>
                        Sobre a Quadra
                    </div>
                    <div class="detailInfoRow"><?= nl2br(htmlspecialchars($quadra['descricao'])) ?></div>
                </div>
                <?php endif; ?>

                <div class="detailInfoCard">
                    <div class="detailInfoCardTitle">
                        <i class="bi bi-clock-fill cardTitleIcon"></i>
                        Horários de Funcionamento
                    </div>
                    <?php if (empty($horarios)): ?>
                        <div class="detailInfoRow">Nenhum horário configurado.</div>
                    <?php else: ?>
                        <?php
                        $diasNome = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                        $horariosPorDia = [];
                        foreach ($horarios as $horario) {
                            $horariosPorDia[$horario['diaSemana']][] = $horario;
                        }
                        ?>
                        <?php foreach ($horariosPorDia as $dia => $slotsNoDia): ?>
                            <div class="detailInfoRow">
                                <strong><?= $diasNome[$dia] ?>:</strong>
                                <?php foreach ($slotsNoDia as $slot): ?>
                                    <span class="ms-2"><?= substr($slot['horaInicio'], 0, 5) ?>–<?= substr($slot['horaFim'], 0, 5) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="detailInfoCard">
                    <div class="detailInfoCardTitle">
                        <i class="bi bi-person-fill cardTitleIcon"></i>
                        Responsável
                    </div>
                    <div class="detailInfoRow"><?= htmlspecialchars($quadra['locadorNome']) ?></div>
                </div>

            </div>

            <!-- RIGHT COLUMN – Booking Widget -->
            <div class="col-lg-5">
                <div class="bookingWidget">

                    <div class="bookingWidgetTitle">Reservar Horário</div>

                    <!-- Date Picker -->
                    <div class="mb-3">
                        <label for="datePicker" class="form-label fw-medium" style="color: var(--textSecondary); font-size: 0.9rem;">Selecione a data</label>
                        <input type="date" class="form-control formInput" id="datePicker" min="<?= date('Y-m-d') ?>">
                    </div>

                    <!-- Slots grid (filled by JS) -->
                    <div id="slotsLoading" class="text-center py-3" style="display:none;">
                        <div class="spinner-border spinner-border-sm" style="color: var(--verdeBandeira);" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <span class="ms-2" style="color: var(--textSecondary);">Buscando horários...</span>
                    </div>

                    <div id="slotsEmpty" class="text-center py-3" style="color: var(--textMuted);">
                        Selecione uma data para ver os horários disponíveis.
                    </div>

                    <div class="slotsGrid" id="slotsGrid"></div>

                    <!-- Hidden form for reservation -->
                    <form id="reservaForm" action="../crud/createReserva.php" method="POST" style="display:none;">
                        <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">
                        <input type="hidden" name="quadraId" value="<?= $quadraId ?>">
                        <input type="hidden" name="dataReserva" id="formDataReserva">
                        <input type="hidden" name="horaInicio" id="formHoraInicio">
                        <input type="hidden" name="horaFim" id="formHoraFim">
                    </form>

                    <!-- CTA Button -->
                    <?php if ($isLocatario): ?>
                        <button class="bookingConfirmBtn disabled" id="confirmBtn" disabled>
                            Selecione um horário
                        </button>
                    <?php elseif ($isLoggedIn): ?>
                        <div class="text-center py-2" style="color: var(--textMuted); font-size: 0.9rem;">
                            Apenas locatários podem reservar horários.
                        </div>
                    <?php else: ?>
                        <a href="login-locatario.php" class="bookingConfirmBtn enabled d-block text-center text-decoration-none">
                            Faça login para reservar
                        </a>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/appLogic.js"></script>
    <script>
        window.arenaConfig = {
            quadraId: <?= $quadraId ?>,
            canBook: <?= $isLocatario ? 'true' : 'false' ?>,
            apiUrl: '../crud/readDisponibilidade.php'
        };
    </script>
    <script src="../assets/js/arenaDetailLogic.js"></script>

</body>
</html>
