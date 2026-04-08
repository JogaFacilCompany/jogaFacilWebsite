<?php
// pages/dashboardLocador.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
require_once __DIR__ . '/../utils/flashMessage.php';
require_once __DIR__ . '/../utils/timeSlotGenerator.php';
require_once __DIR__ . '/../crud/readQuadras.php';
require_once __DIR__ . '/../config/csrf.php';

requireAuth('locador', '../pages/loginLocador.php');

$locadorId = $_SESSION['usuarioLogado'];
$arenaId   = isset($_GET['arena_id']) ? (int)$_GET['arena_id'] : null;

if ($arenaId) {
    $quadra = getQuadraByIdAndLocador($arenaId, $locadorId);
    if (!$quadra) {
        header('Location: dashboardLocador.php');
        exit;
    }
    $selectableTimeSlots = generateRelativeTimeSlots($quadra['funcionamento']);
} else {
    $quadras = getQuadrasByLocador($locadorId);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php $pageTitle = 'Painel do Locador – Joga Fácil'; include __DIR__ . '/../includes/headTag.php'; ?>
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: var(--bgMain);">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1">
    <?php if (!$arenaId): ?>
        <?php include __DIR__ . '/partials/locadorArenaList.php'; ?>
    <?php else: ?>
        <?php include __DIR__ . '/partials/locadorArenaDetail.php'; ?>
    <?php endif; ?>

    <?php include __DIR__ . '/partials/modalCreateArena.php'; ?>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/appLogic.js"></script>
<?php if ($arenaId): ?>
<script src="../assets/js/dashboardLocadorLogic.js"></script>
<?php endif; ?>
</body>
</html>
