<?php
// pages/partials/locadorArenaDetail.php – Arena detail/management view for locador dashboard
$facilidades = json_decode($quadra['facilidades'], true) ?: [];
?>
<!-- Hero Banner -->
<section class="arenaDetailHero">
    <?php 
        $capaSrc = 'https://images.unsplash.com/photo-1518605368461-1ee7e1635338?q=80&w=2000';
        if (!empty($quadra['imagem'])) {
            $capaSrc = str_starts_with($quadra['imagem'], 'http') ? $quadra['imagem'] : '../assets/uploads/quadras/' . htmlspecialchars($quadra['imagem']);
        }
    ?>
    <img src="<?= $capaSrc ?>" alt="Capa da Arena" class="arenaDetailHeroImg">
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
                <?php if (empty($isGerente)): ?>
                    <button class="btn btn-sm btn-outline-light ms-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalEditarArena" style="font-size: 0.75rem;"><i class="bi bi-pencil"></i> Editar Perfil</button>
                <?php endif; ?>
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

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $currentTab === 'manage' ? 'active fw-bold' : 'text-secondary' ?>" href="?arena_id=<?= $arenaId ?>&tab=manage">
                <i class="bi bi-calendar-check"></i> Agenda & Reservas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentTab === 'edit' ? 'active fw-bold' : 'text-secondary' ?>" href="?arena_id=<?= $arenaId ?>&tab=edit">
                <i class="bi bi-gear"></i> Configurações da Arena
            </a>
        </li>
    </ul>

    <?php 
    if ($currentTab === 'manage') {
        include __DIR__ . '/locadorArenaManage.php';
    } else {
        include __DIR__ . '/locadorArenaEdit.php';
    }
    ?>

</section>

<?php if (empty($isGerente)): ?>
    <?php include __DIR__ . '/modalEditArena.php'; ?>
    <?php include __DIR__ . '/modalDeleteArena.php'; ?>
<?php endif; ?>
