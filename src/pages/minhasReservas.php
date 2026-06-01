<?php
// pages/minhasReservas.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
require_once __DIR__ . '/../utils/flashMessage.php';
require_once __DIR__ . '/../crud/readReservasLocatario.php';

requireAuth('locatario', '../pages/loginLocatario.php');

$usuarioId = (int)$_SESSION['usuarioLogado'];
$reservas = getReservasByLocatario($usuarioId);

$statusFilter = $_GET['status'] ?? 'todas';
if ($statusFilter !== 'todas') {
    $reservas = array_values(array_filter($reservas, fn($r) => $r['status'] === $statusFilter));
}

$pageTitle = 'Minhas Reservas – Joga Fácil';
$pageDescription = 'Acompanhe suas reservas de quadras e partidas em lobby.';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include __DIR__ . '/../includes/headTag.php'; ?>
</head>
<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="container py-5">
        <?php renderFlash(); ?>

        <h1 class="reservasPageTitle">Minhas Reservas</h1>
        <p class="reservasPageSubtitle">Histórico e status das suas reservas de quadras.</p>

        <div class="d-flex flex-wrap gap-2 mb-4">
            <?php
            $filtros = ['todas' => 'Todas', 'pendente' => 'Pendentes', 'confirmada' => 'Confirmadas', 'cancelada' => 'Canceladas'];
            foreach ($filtros as $valor => $label):
                $active = $statusFilter === $valor ? 'active' : '';
            ?>
                <a href="?status=<?= urlencode($valor) ?>" class="btn btn-sm btn-outline-secondary rounded-pill <?= $active ? 'active' : '' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($reservas)): ?>
            <div class="reservaEmptyState">
                <i class="bi bi-calendar-x fs-1 d-block mb-3 opacity-50"></i>
                <p class="mb-2">Nenhuma reserva encontrada.</p>
                <p class="small mb-3">Reserve um horário em uma arena para começar.</p>
                <a href="../index.php" class="btn btn-success rounded-pill px-4">Buscar arenas</a>
            </div>
        <?php else: ?>
            <?php foreach ($reservas as $reserva): ?>
                <?php
                    $dataFmt = date('d/m/Y', strtotime($reserva['data']));
                    $horaInicio = substr($reserva['hora_inicio'], 0, 5);
                    $horaFim = substr($reserva['hora_fim'], 0, 5);
                    $precoFmt = number_format((float)$reserva['preco'], 2, ',', '.');
                    $status = $reserva['status'];
                    $isLobby = (int)$reserva['modo_lobby'] === 1;
                    $visibilidade = $reserva['visibilidade_lobby'] ?? '';
                    $participantes = $isLobby ? (int)$reserva['total_participantes'] + 1 : 0;
                ?>
                <article class="reservaCard">
                    <div class="reservaCardHeader">
                        <div>
                            <div class="reservaCardArena"><?= htmlspecialchars($reserva['quadra_nome']) ?></div>
                            <div class="reservaCardMeta">
                                <i class="bi bi-geo-alt-fill"></i>
                                <?= htmlspecialchars($reserva['quadra_endereco']) ?>
                            </div>
                        </div>
                        <span class="reservaStatusBadge <?= htmlspecialchars($status) ?>">
                            <?= ucfirst(htmlspecialchars($status)) ?>
                        </span>
                    </div>
                    <div class="reservaCardMeta">
                        <div><i class="bi bi-calendar3"></i> <?= $dataFmt ?> · <?= $horaInicio ?> – <?= $horaFim ?></div>
                        <div><i class="bi bi-cash"></i> R$ <?= $precoFmt ?></div>
                        <?php if ($isLobby): ?>
                            <div class="reservaLobbyBadge">
                                <i class="bi bi-people-fill"></i>
                                Lobby <?= $visibilidade === 'privado' ? 'privado' : 'público' ?>
                                · <?= $participantes ?> participante(s)
                            </div>
                            <?php if ($visibilidade === 'privado' && !empty($reserva['codigo_acesso'])): ?>
                                <div class="reservaCodigoBox">
                                    Código de acesso: <strong><?= htmlspecialchars($reserva['codigo_acesso']) ?></strong>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    //aqui aparece o campo
                    <div class="mt-3">
                        <a href="arenaDetalhe.php?id=<?= (int)$reserva['quadra_id'] ?>" class="btn btn-sm btn-outline-success rounded-pill">
                            Ver arena
                        </a>
                        <?php if ($isLobby): ?>
                            <a href="listaLobbies.php" class="btn btn-sm btn-outline-secondary rounded-pill ms-1">Meus lobbies</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
