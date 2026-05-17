<?php
// pages/partials/locadorArenaManage.php – Gestão da Arena (Horários e Pendências)
?>
<style>
.reserva-card {
    background-color: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 0.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
}
.reserva-card:hover {
    transform: translateY(-2px);
    border-color: rgba(255, 255, 255, 0.15);
}
.reserva-card .reserva-text-primary {
    color: #e5e7eb;
}
.reserva-card .reserva-text-secondary {
    color: #9ca3af;
}
.badge-pendente {
    background-color: rgba(234, 179, 8, 0.15) !important;
    color: #facc15 !important;
    border-radius: 0.25rem;
    padding: 0.25em 0.6em;
}
.badge-confirmada {
    background-color: rgba(34, 197, 94, 0.15) !important;
    color: #4ade80 !important;
    border-radius: 0.25rem;
    padding: 0.25em 0.6em;
}
.btn-aprovar {
    background-color: #22c55e !important;
    color: #022c22 !important;
    border: none !important;
    font-weight: 500;
}
.btn-aprovar:hover {
    background-color: #16a34a !important;
    color: #022c22 !important;
}
.btn-recusar {
    background-color: transparent !important;
    border: 1px solid #ef4444 !important;
    color: #ef4444 !important;
    font-weight: 500;
}
.btn-recusar:hover {
    background-color: rgba(239, 68, 68, 0.1) !important;
}
</style>

<div class="row g-4 mt-3">
    <!-- Esquerda: Solicitações Pendentes -->
    <div class="col-lg-7">
        <div class="detailInfoCard shadow-sm">
            <h5 class="detailInfoCardTitle">
                <i class="bi bi-clock-history cardTitleIcon text-warning"></i> Solicitações Pendentes
            </h5>
            <?php if (empty($reservasPendentes)): ?>
                <p class="text-secondary mt-3 mb-0 small">Não há solicitações pendentes no momento.</p>
            <?php else: ?>
                <div class="mt-4">
                    <?php foreach ($reservasPendentes as $reserva): ?>
                        <div class="reserva-card d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                            <div>
                                <h6 class="mb-1 reserva-text-primary fw-bold">
                                    <?= htmlspecialchars($reserva['usuario_nome']) ?> 
                                    <span class="badge badge-pendente ms-2 fw-normal">Pendente</span>
                                </h6>
                                <small class="reserva-text-secondary d-block mt-2">
                                    <i class="bi bi-calendar-event me-1"></i> 
                                    <?= htmlspecialchars($reserva['data'] ?? 'Sem data') ?> das <?= substr($reserva['hora_inicio'], 0, 5) ?> às <?= substr($reserva['hora_fim'], 0, 5) ?>
                                </small>
                                <small class="reserva-text-secondary d-block mt-1">
                                    <i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($reserva['usuario_email'] ?? 'Sem contato') ?>
                                </small>
                            </div>
                            <div class="d-flex gap-2 align-self-start align-self-sm-center">
                                <form action="../crud/updateReservaStatus.php" method="POST" class="d-inline m-0">
                                    <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                                    <input type="hidden" name="reserva_id" value="<?= $reserva['reserva_id'] ?>">
                                    <input type="hidden" name="arena_id" value="<?= $quadra['id'] ?>">
                                    <input type="hidden" name="status" value="confirmada">
                                    <button type="submit" class="btn btn-sm btn-aprovar rounded-pill px-3" title="Aprovar">
                                        <i class="bi bi-check-circle me-1"></i> Aprovar
                                    </button>
                                </form>
                                <form action="../crud/updateReservaStatus.php" method="POST" class="d-inline m-0" onsubmit="return confirm('Recusar esta solicitação?');">
                                    <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                                    <input type="hidden" name="reserva_id" value="<?= $reserva['reserva_id'] ?>">
                                    <input type="hidden" name="arena_id" value="<?= $quadra['id'] ?>">
                                    <input type="hidden" name="status" value="cancelada">
                                    <button type="submit" class="btn btn-sm btn-recusar rounded-pill px-3" title="Recusar">
                                        <i class="bi bi-x-circle me-1"></i> Recusar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="detailInfoCard shadow-sm mt-4">
            <h5 class="detailInfoCardTitle">
                <i class="bi bi-calendar-check-fill cardTitleIcon text-success"></i> Reservas Confirmadas
            </h5>
            <?php if (empty($reservasConfirmadas)): ?>
                <p class="text-secondary mt-3 mb-0 small">Não há reservas confirmadas no momento.</p>
            <?php else: ?>
                <div class="mt-4">
                    <?php foreach ($reservasConfirmadas as $reserva): ?>
                        <div class="reserva-card d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                            <div>
                                <h6 class="mb-1 reserva-text-primary fw-bold">
                                    <?= htmlspecialchars($reserva['usuario_nome']) ?> 
                                    <span class="badge badge-confirmada ms-2 fw-normal">Confirmada</span>
                                </h6>
                                <small class="reserva-text-secondary d-block mt-2">
                                    <i class="bi bi-calendar-event me-1"></i> 
                                    <?= htmlspecialchars($reserva['data'] ?? 'Sem data') ?> das <?= substr($reserva['hora_inicio'], 0, 5) ?> às <?= substr($reserva['hora_fim'], 0, 5) ?>
                                </small>
                                <small class="reserva-text-secondary d-block mt-1">
                                    <i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($reserva['usuario_email'] ?? 'Sem contato') ?>
                                </small>
                            </div>
                            <div class="align-self-start align-self-sm-center">
                                <form action="../crud/updateReservaStatus.php" method="POST" class="d-inline m-0" onsubmit="return confirm('Tem certeza que deseja CANCELAR esta reserva que já estava confirmada?');">
                                    <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                                    <input type="hidden" name="reserva_id" value="<?= $reserva['reserva_id'] ?>">
                                    <input type="hidden" name="arena_id" value="<?= $quadra['id'] ?>">
                                    <input type="hidden" name="status" value="cancelada">
                                    <button type="submit" class="btn btn-sm btn-recusar rounded-pill px-3" title="Cancelar">
                                        <i class="bi bi-x-circle me-1"></i> Cancelar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Direita: Widget de Reservas -->
    <div class="col-lg-5">
        <div class="bookingWidget shadow h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="bookingWidgetTitle m-0">Gerenciar Horários</h4>
                <span class="badge bg-success rounded-pill px-3">Hoje</span>
            </div>
            <div class="periodTabsWrapper shadow-sm">
                <button class="periodTab active" onclick="selecionarAba('Manhã')">Manhã</button>
                <button class="periodTab" onclick="selecionarAba('Tarde')">Tarde</button>
                <button class="periodTab" onclick="selecionarAba('Noite')">Noite</button>
            </div>
            <div class="slotsGrid" id="slotsContainer" data-horarios='<?= json_encode($selectableTimeSlots) ?>'></div>
            <p class="small mt-2 mb-3">Clique em um horário para bloqueá-lo ou liberá-lo.</p>
            <button class="bookingConfirmBtn" id="btnSalvarEstado">Selecione um horário</button>
        </div>
    </div>
</div>
