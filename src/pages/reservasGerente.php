<?php
// pages/reservasGerente.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
require_once __DIR__ . '/../crud/readReservasGerente.php';

requireAuth('gerente', '../pages/loginGerente.php');

$gerenteId = (int)$_SESSION['usuarioLogado'];
$reservas  = getReservasByGerente($gerenteId);

$statusFilter = $_GET['status'] ?? 'todas';
if ($statusFilter !== 'todas') {
    $reservas = array_filter($reservas, fn($r) => $r['status'] === $statusFilter);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php $pageTitle = 'Reservas – Joga Fácil'; include __DIR__ . '/../includes/headTag.php'; ?>
    <style>
        .reserva-row {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.5rem;
            padding: 1rem 1.25rem;
            transition: border-color 0.2s;
        }
        .reserva-row:hover {
            border-color: rgba(255, 255, 255, 0.18);
        }
        .badge-pendente {
            background-color: rgba(234, 179, 8, 0.15);
            color: #facc15;
            border-radius: 0.25rem;
            padding: 0.25em 0.6em;
            font-size: 0.8rem;
        }
        .badge-confirmada {
            background-color: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border-radius: 0.25rem;
            padding: 0.25em 0.6em;
            font-size: 0.8rem;
        }
        .badge-cancelada {
            background-color: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border-radius: 0.25rem;
            padding: 0.25em 0.6em;
            font-size: 0.8rem;
        }
        .filter-btn {
            border-radius: 2rem;
            padding: 0.35rem 1.1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border: 1px solid rgba(255,255,255,0.15);
            color: #9ca3af;
            background: transparent;
            text-decoration: none;
            transition: all 0.15s;
        }
        .filter-btn:hover, .filter-btn.active {
            background: rgba(255,255,255,0.08);
            color: #fff;
            border-color: rgba(255,255,255,0.3);
        }
        .filter-btn.active { font-weight: 600; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: var(--bgMain);">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1">
    <div class="container py-5">

        <!-- Cabeçalho -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <a href="dashboardLocador.php" class="text-warning text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i> Voltar ao Painel
                </a>
                <h2 class="fw-bold text-white mt-1 mb-0">Reservas</h2>
            </div>

            <!-- Filtros de status -->
            <div class="d-flex gap-2 flex-wrap">
                <a href="?status=todas"
                   class="filter-btn <?= $statusFilter === 'todas' ? 'active' : '' ?>">
                    Todas
                </a>
                <a href="?status=pendente"
                   class="filter-btn <?= $statusFilter === 'pendente' ? 'active' : '' ?>">
                    Pendentes
                </a>
                <a href="?status=confirmada"
                   class="filter-btn <?= $statusFilter === 'confirmada' ? 'active' : '' ?>">
                    Confirmadas
                </a>
                <a href="?status=cancelada"
                   class="filter-btn <?= $statusFilter === 'cancelada' ? 'active' : '' ?>">
                    Canceladas
                </a>
            </div>
        </div>

        <?php if (empty($reservas)): ?>
            <!-- Estado vazio -->
            <div class="text-center py-5">
                <div class="p-5 rounded-4" style="background: rgba(255,255,255,0.03); border: 1px dashed #374151; max-width: 480px; margin: 0 auto;">
                    <i class="bi bi-calendar-x display-1 text-secondary"></i>
                    <h4 class="mt-3 text-white">Nenhuma reserva cadastrada</h4>
                    <p class="text-secondary mb-0">
                        <?php if ($statusFilter !== 'todas'): ?>
                            Não há reservas com status <strong><?= htmlspecialchars($statusFilter) ?></strong> nas suas quadras.
                        <?php else: ?>
                            Ainda não há reservas registradas nas quadras que você gerencia.
                        <?php endif; ?>
                    </p>
                </div>
            </div>

        <?php else: ?>
            <!-- Lista de reservas -->
            <div class="d-flex flex-column gap-3">
                <?php foreach ($reservas as $reserva): ?>
                    <?php
                        $badgeClass = match($reserva['status']) {
                            'confirmada' => 'badge-confirmada',
                            'cancelada'  => 'badge-cancelada',
                            default      => 'badge-pendente',
                        };
                        $statusLabel = match($reserva['status']) {
                            'confirmada' => 'Confirmada',
                            'cancelada'  => 'Cancelada',
                            default      => 'Pendente',
                        };
                        $dataFormatada = $reserva['data']
                            ? date('d/m/Y', strtotime($reserva['data']))
                            : '–';
                        $horaInicio = substr($reserva['hora_inicio'], 0, 5);
                        $horaFim    = substr($reserva['hora_fim'], 0, 5);
                    ?>
                    <div class="reserva-row d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-white"><?= htmlspecialchars($reserva['locatario_nome']) ?></span>
                                <span class="<?= $badgeClass ?>"><?= $statusLabel ?></span>
                            </div>
                            <small class="text-secondary d-block">
                                <i class="bi bi-calendar-event me-1"></i>
                                <?= $dataFormatada ?> &nbsp;·&nbsp;
                                <?= $horaInicio ?> às <?= $horaFim ?>
                            </small>
                            <small class="text-secondary d-block mt-1">
                                <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($reserva['quadra_nome']) ?>
                                &nbsp;·&nbsp;
                                <i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($reserva['locatario_email']) ?>
                            </small>
                        </div>
                        <div class="align-self-start align-self-sm-center">
                            <a href="dashboardLocador.php?arena_id=<?= $reserva['quadra_id'] ?>&tab=manage"
                               class="btn btn-sm btn-outline-light rounded-pill px-3">
                                <i class="bi bi-arrow-right me-1"></i> Ver Quadra
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
