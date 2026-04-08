<?php
// pages/partials/locadorArenaDetail.php – Arena detail/management view for locador dashboard
$facilidades = json_decode($quadra['facilidades'], true) ?: [];
?>
<!-- Hero Banner -->
<section class="arenaDetailHero">
    <img src="<?= htmlspecialchars($quadra['imagem'] ?: 'https://images.unsplash.com/photo-1518605368461-1ee7e1635338?q=80&w=2000') ?>" alt="Capa da Arena" class="arenaDetailHeroImg">
    <div class="arenaDetailHeroOverlay"></div>
    <div class="container position-relative h-100">
        <div class="arenaDetailHeroMeta">
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="dashboardLocador.php" class="text-warning text-decoration-none"><i class="bi bi-arrow-left"></i> Meus Painéis</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Gerenciando</li>
                </ol>
            </nav>
            <h1 class="arenaDetailHeroName"><?= htmlspecialchars($quadra['nome']) ?></h1>
            <p class="arenaDetailHeroAddr">
                <i class="bi bi-geo-alt-fill text-warning"></i> <?= htmlspecialchars($quadra['endereco']) ?>
                <button class="btn btn-sm btn-outline-light ms-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalEditarArena" style="font-size: 0.75rem;"><i class="bi bi-pencil"></i> Editar Perfil</button>
            </p>
        </div>
        <div class="arenaDetailRating shadow-sm">
            <i class="bi bi-star-fill"></i> 4.8
        </div>
    </div>
</section>

<!-- Main content -->
<section class="container py-5 contentSection">

    <?php renderFlash(); ?>

    <div class="row g-4">
        <!-- Left column: Info -->
        <div class="col-lg-7">

            <div class="detailInfoCard shadow-sm">
                <h5 class="detailInfoCardTitle">
                    <i class="bi bi-info-circle-fill cardTitleIcon"></i> Sobre a Arena
                </h5>
                <div class="detailInfoRow">
                    <strong>Modalidades:</strong> <?= htmlspecialchars($quadra['modalidades'] ?? 'Não informado') ?>
                </div>
                <div class="detailInfoRow">
                    <strong>Horário de Funcionamento:</strong> <?= htmlspecialchars($quadra['funcionamento'] ?? 'Não informado') ?>
                </div>
                <?php if (!empty($quadra['descricao'])): ?>
                    <div class="detailInfoRow mt-3 text-secondary" style="font-size: 0.88rem; font-style: italic;">
                        "<?= nl2br(htmlspecialchars($quadra['descricao'])) ?>"
                    </div>
                <?php endif; ?>
                <div class="detailInfoRow mt-3">
                    <strong>Política de Cancelamento:</strong>
                </div>
                <div class="detailCancelText">
                    Cancelamento grátis até <?= htmlspecialchars($quadra['cancelamento_horas']) ?>h antes.
                </div>
            </div>

            <div class="detailInfoCard shadow-sm">
                <h5 class="detailInfoCardTitle">
                    <i class="bi bi-check-circle-fill cardTitleIcon text-success"></i> Facilidades
                </h5>
                <ul class="facilidadesList mt-3">
                    <?php if (empty($facilidades)): ?>
                        <li class="facilidadesItem">Nenhuma facilidade cadastrada</li>
                    <?php else: ?>
                        <?php foreach ($facilidades as $facilidadeItem): ?>
                            <li class="facilidadesItem"><?= htmlspecialchars($facilidadeItem) ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="detailInfoCard shadow-sm">
                <h5 class="detailInfoCardTitle">
                    <i class="bi bi-telephone-fill cardTitleIcon"></i> Contato
                </h5>
                <div class="detailPhone">
                    <?= htmlspecialchars($quadra['telefone'] ?? 'Não informado') ?>
                </div>
            </div>

        </div>

        <!-- Right column: Booking widget -->
        <div class="col-lg-5">
            <div class="bookingWidget shadow">
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

                <div class="mt-4 pt-3 border-top border-secondary opacity-75">
                    <button class="btn btn-sm btn-outline-danger w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalExcluirArena">
                        <i class="bi bi-trash3"></i> Excluir Arena Permanentemente
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/modalEditArena.php'; ?>
<?php include __DIR__ . '/modalDeleteArena.php'; ?>
