<?php
// pages/dashboard-gerente.php – Manager dashboard for reservation management
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/auth.php';
requireGerenteAuth();
require_once __DIR__ . '/../config/csrf.php';
require_once __DIR__ . '/../crud/readReservas.php';

$csrfToken        = generateCsrfToken();
$reservasPendentes = readReservasPendentes();
$flashMessage     = $_SESSION['flashMessage'] ?? null;
$flashType        = $_SESSION['flashType']    ?? 'info';
unset($_SESSION['flashMessage'], $_SESSION['flashType']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Gerente – Joga Fácil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../assets/css/customStyles.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 container py-5">
    <h2 class="dashboardTitle fw-bold mb-4">
        <i class="bi bi-clipboard-check-fill" style="color: var(--verdeBandeira);"></i>
        Reservas Pendentes
    </h2>

    <?php if ($flashMessage): ?>
        <div class="alert alert-<?= $flashType ?> alertMessage"><?= htmlspecialchars($flashMessage) ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm p-4 dashboardCard">
        <div class="table-responsive">
            <table class="table dashboardTable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Quadra</th>
                        <th>Locatário</th>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Solicitado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservasPendentes)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 textMuted">
                                <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                                <div class="mt-2">Nenhuma reserva pendente. Tudo em dia!</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservasPendentes as $reserva): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($reserva['quadraNome']) ?></strong>
                                <div class="textSmall textMuted"><?= htmlspecialchars($reserva['quadraEndereco']) ?></div>
                            </td>
                            <td>
                                <?= htmlspecialchars($reserva['locatarioNome']) ?>
                                <div class="textSmall textMuted"><?= htmlspecialchars($reserva['locatarioEmail']) ?></div>
                            </td>
                            <td><?= date('d/m/Y', strtotime($reserva['dataReserva'])) ?></td>
                            <td><?= substr($reserva['horaInicio'], 0, 5) ?>–<?= substr($reserva['horaFim'], 0, 5) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($reserva['criadoEm'])) ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <!-- Confirmar -->
                                    <form action="../crud/updateReservaStatus.php" method="POST" class="d-inline confirmarForm">
                                        <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="reservaId" value="<?= $reserva['id'] ?>">
                                        <input type="hidden" name="status" value="confirmada">
                                        <button type="submit" class="btn btn-sm btn-success fw-bold" aria-label="Confirmar reserva">
                                            <i class="bi bi-check-lg"></i> Confirmar
                                        </button>
                                    </form>

                                    <!-- Recusar (abre modal) -->
                                    <button type="button" class="btn btn-sm btn-outline-danger recusarBtn"
                                        data-reserva-id="<?= $reserva['id'] ?>"
                                        data-quadra="<?= htmlspecialchars($reserva['quadraNome']) ?>"
                                        data-locatario="<?= htmlspecialchars($reserva['locatarioNome']) ?>"
                                        data-bs-toggle="modal" data-bs-target="#recusarModal"
                                        aria-label="Recusar reserva">
                                        <i class="bi bi-x-lg"></i> Recusar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Recusar Modal -->
<div class="modal fade" id="recusarModal" tabindex="-1" aria-labelledby="recusarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="../crud/updateReservaStatus.php" method="POST" class="modal-content" style="background-color: var(--bgCard); color: var(--textPrimary);">
            <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">
            <input type="hidden" name="reservaId" id="recusarReservaId">
            <input type="hidden" name="status" value="recusada">
            <div class="modal-header" style="border-color: var(--bgCardBorder);">
                <h5 class="modal-title" id="recusarModalLabel">Recusar Reserva</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p id="recusarInfo" style="color: var(--textSecondary);"></p>
                <div class="mb-3">
                    <label for="motivoRecusa" class="form-label fw-medium">Motivo da Recusa <span class="text-danger">*</span></label>
                    <textarea class="form-control formInput" id="motivoRecusa" name="motivoRecusa" rows="3"
                        placeholder="Informe o motivo da recusa..." required></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-color: var(--bgCardBorder);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger fw-bold">Recusar Reserva</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/appLogic.js"></script>
<script>
    document.querySelectorAll('.recusarBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('recusarReservaId').value = btn.dataset.reservaId;
            document.getElementById('recusarInfo').textContent =
                `Quadra: ${btn.dataset.quadra} — Locatário: ${btn.dataset.locatario}`;
        });
    });

    document.querySelectorAll('.confirmarForm').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm('Tem certeza que deseja confirmar esta reserva?')) {
                e.preventDefault();
            }
        });
    });
</script>
</body>
</html>
