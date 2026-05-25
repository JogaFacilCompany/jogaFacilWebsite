<?php
// pages/listaLobbies.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
require_once __DIR__ . '/../utils/flashMessage.php';
require_once __DIR__ . '/../crud/readLobbies.php';
require_once __DIR__ . '/../crud/readReservasLocatario.php';
require_once __DIR__ . '/../config/csrf.php';

requireAuth('locatario', '../pages/loginLocatario.php');

$usuarioId = (int)$_SESSION['usuarioLogado'];
$meusLobbiesOrganizados = getLobbiesOrganizadosByLocatario($usuarioId);
$meusLobbiesParticipando = getLobbiesParticipandoByLocatario($usuarioId);
$lobbiesPublicos = getPublicLobbies($usuarioId);

$pageTitle = 'Lobbies – Joga Fácil';
$pageDescription = 'Gerencie suas partidas em lobby, entre em lobbies públicos ou use código para lobby privado.';
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

        <h1 class="lobbiesPageTitle">Lobbies</h1>
        <p class="lobbiesPageSubtitle">Gerencie suas partidas, entre em lobbies públicos ou use um código para lobby privado.</p>

        <section class="mb-5">
            <h2 class="h5 fw-bold mb-3">
                <i class="bi bi-star-fill text-primary"></i> Meus lobbies (organizador)
            </h2>

            <?php if (empty($meusLobbiesOrganizados)): ?>
                <div class="lobbyEmptyState mb-0">
                    <p class="mb-2">Você ainda não abriu nenhuma partida em modo lobby.</p>
                    <p class="small mb-3">Ao reservar, ative <strong>Modo Lobby</strong> na página da arena.</p>
                    <a href="../index.php" class="btn btn-success btn-sm rounded-pill px-3">Reservar quadra</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($meusLobbiesOrganizados as $lobby): ?>
                        <?php
                            $dataFmt = date('d/m/Y', strtotime($lobby['data']));
                            $horaInicio = substr($lobby['hora_inicio'], 0, 5);
                            $horaFim = substr($lobby['hora_fim'], 0, 5);
                            $participantes = (int)$lobby['total_participantes'] + 1;
                            $precoFmt = number_format((float)$lobby['preco'], 2, ',', '.');
                            $isPrivado = ($lobby['visibilidade_lobby'] ?? '') === 'privado';
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <article class="lobbyListCard">
                                <div class="lobbyListCardHeader">
                                    <div>
                                        <div class="lobbyListCardArena"><?= htmlspecialchars($lobby['quadra_nome']) ?></div>
                                        <div class="lobbyListCardMeta">
                                            <i class="bi bi-calendar3"></i> <?= $dataFmt ?> · <?= $horaInicio ?> – <?= $horaFim ?>
                                        </div>
                                    </div>
                                    <span class="lobbyBadgeHost">Organizador</span>
                                </div>
                                <div class="lobbyListCardMeta">
                                    <div><i class="bi bi-people-fill"></i> <?= $participantes ?> participante(s)</div>
                                    <div><i class="bi bi-cash"></i> R$ <?= $precoFmt ?></div>
                                    <div>
                                        <span class="<?= $isPrivado ? 'lobbyBadgePrivate' : 'lobbyBadgePublic' ?>">
                                            <?= $isPrivado ? 'Privado' : 'Público' ?>
                                        </span>
                                        · <?= ucfirst(htmlspecialchars($lobby['status'])) ?>
                                    </div>
                                    <?php if ($isPrivado && !empty($lobby['codigo_acesso'])): ?>
                                        <div class="lobbyCodigoHint">
                                            Compartilhe o código: <strong><?= htmlspecialchars($lobby['codigo_acesso']) ?></strong>
                                        </div>
                                    <?php elseif (!$isPrivado): ?>
                                        <div class="lobbyCodigoHint small mb-0">
                                            Visível na seção <strong>Lobbies públicos</strong> abaixo.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <?php if (!empty($meusLobbiesParticipando)): ?>
        <section class="mb-5">
            <h2 class="h5 fw-bold mb-3">
                <i class="bi bi-person-check-fill text-info"></i> Lobbies que participo
            </h2>
            <div class="row g-4">
                <?php foreach ($meusLobbiesParticipando as $lobby): ?>
                    <?php
                        $dataFmt = date('d/m/Y', strtotime($lobby['data']));
                        $horaInicio = substr($lobby['hora_inicio'], 0, 5);
                        $horaFim = substr($lobby['hora_fim'], 0, 5);
                        $precoFmt = number_format((float)$lobby['preco'], 2, ',', '.');
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="lobbyListCard">
                            <div class="lobbyListCardHeader">
                                <div>
                                    <div class="lobbyListCardArena"><?= htmlspecialchars($lobby['quadra_nome']) ?></div>
                                    <div class="lobbyListCardMeta">
                                        Organizador: <?= htmlspecialchars($lobby['host_nome']) ?>
                                    </div>
                                </div>
                                <span class="lobbyBadgePublic">Participante</span>
                            </div>
                            <div class="lobbyListCardMeta">
                                <div><i class="bi bi-calendar3"></i> <?= $dataFmt ?> · <?= $horaInicio ?> – <?= $horaFim ?></div>
                                <div><i class="bi bi-cash"></i> R$ <?= $precoFmt ?></div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="mb-5">
            <h2 class="h5 fw-bold mb-3">
                <i class="bi bi-globe2 text-success"></i> Lobbies públicos de outros jogadores
            </h2>

            <?php if (empty($lobbiesPublicos)): ?>
                <div class="lobbyEmptyState">
                    <i class="bi bi-people fs-1 d-block mb-3 opacity-50"></i>
                    <p class="mb-2">Nenhum lobby público de outros jogadores no momento.</p>
                    <p class="small mb-0">Lobbies privados exigem o código definido pelo organizador.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($lobbiesPublicos as $lobby): ?>
                        <?php
                            $dataFmt = date('d/m/Y', strtotime($lobby['data']));
                            $horaInicio = substr($lobby['hora_inicio'], 0, 5);
                            $horaFim = substr($lobby['hora_fim'], 0, 5);
                            $participantes = (int)$lobby['total_participantes'] + 1;
                            $precoFmt = number_format((float)$lobby['preco'], 2, ',', '.');
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <article class="lobbyListCard">
                                <div class="lobbyListCardHeader">
                                    <div>
                                        <div class="lobbyListCardArena"><?= htmlspecialchars($lobby['quadra_nome']) ?></div>
                                        <div class="lobbyListCardMeta">
                                            <i class="bi bi-geo-alt-fill"></i>
                                            <?= htmlspecialchars($lobby['quadra_endereco']) ?>
                                        </div>
                                    </div>
                                    <span class="lobbyBadgePublic">Público</span>
                                </div>
                                <div class="lobbyListCardMeta">
                                    <div><i class="bi bi-calendar3"></i> <?= $dataFmt ?> · <?= $horaInicio ?> – <?= $horaFim ?></div>
                                    <div><i class="bi bi-person-fill"></i> Organizador: <?= htmlspecialchars($lobby['host_nome']) ?></div>
                                    <div><i class="bi bi-people-fill"></i> <?= $participantes ?> participante(s)</div>
                                    <div><i class="bi bi-cash"></i> R$ <?= $precoFmt ?></div>
                                </div>
                                <div class="lobbyListCardFooter">
                                    <form action="../crud/joinLobby.php" method="POST">
                                        <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                                        <input type="hidden" name="tipo_entrada" value="publico">
                                        <input type="hidden" name="reserva_id" value="<?= (int)$lobby['reserva_id'] ?>">
                                        <button type="submit" class="lobbyJoinBtn">Solicitar entrada</button>
                                    </form>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section>
            <h2 class="h5 fw-bold mb-3">
                <i class="bi bi-lock-fill text-warning"></i> Entrar em lobby privado
            </h2>
            <div class="lobbyPrivateCard">
                <div class="lobbyPrivateCardTitle">Código de acesso</div>
                <p class="lobbyPrivateCardDesc">
                    Informe o código definido pelo organizador da partida.
                </p>
                <form action="../crud/joinLobby.php" method="POST">
                    <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="tipo_entrada" value="privado">
                    <div class="mb-3">
                        <label for="codigoAcessoLobby" class="form-label fw-semibold">Código</label>
                        <input
                            type="text"
                            class="form-control text-uppercase"
                            id="codigoAcessoLobby"
                            name="codigo_acesso"
                            maxlength="20"
                            placeholder="Ex: JOGA2026"
                            required
                            autocomplete="off"
                        >
                    </div>
                    <button type="submit" class="lobbyJoinBtn">Entrar com código</button>
                </form>
            </div>
        </section>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
