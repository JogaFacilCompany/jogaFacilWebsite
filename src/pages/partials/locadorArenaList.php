<?php
// pages/partials/locadorArenaList.php – Arena list view for locador dashboard
?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-white"><?= !empty($isGerente) ? 'Quadras Gerenciadas' : 'Minhas Quadras' ?></h2>
        <?php if (empty($isGerente)): ?>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-light px-4 rounded-pill" href="../pages/cadastrarGerente.php">
                    <i class="bi bi-person-plus me-2"></i> Cadastrar Gerente
                </a>
                <button class="btn customYellowBtn px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalNovoArena">
                    <i class="bi bi-plus-circle me-2"></i> Adicionar Nova Quadra
                </button>
            </div>
        <?php else: ?>
            <a class="btn btn-outline-light px-4 rounded-pill" href="../pages/reservasGerente.php">
                <i class="bi bi-calendar-check me-2"></i> Ver Reservas
            </a>
        <?php endif; ?>
    </div>

    <?php renderFlash(); ?>

    <div class="row g-4 mt-2">
        <?php if (empty($quadras)): ?>
            <div class="col-12 text-center py-5">
                <div class="p-5 rounded-4 shadow-sm" style="background: rgba(255,255,255,0.03); border: 1px dashed #374151;">
                    <i class="bi bi-calendar-x display-1"></i>
                    <h4 class="mt-3 text-white"><?= !empty($isGerente) ? 'Nenhuma quadra vinculada.' : 'Você ainda não tem quadras.' ?></h4>
                    <p class="text-secondary"><?= !empty($isGerente) ? 'Peça para o locador liberar seu acesso a uma quadra.' : 'Cadastre sua primeira arena para começar a disponibilizar horários!' ?></p>
                    <?php if (empty($isGerente)): ?>
                        <button class="btn btn-success fw-bold px-5 py-2 mt-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalNovoArena">Cadastrar Primeira Arena</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($quadras as $quadraItem): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card arenaSelectionCard h-100 shadow-sm overflow-hidden" onclick="window.location.href='?arena_id=<?= $quadraItem['id'] ?>'">
                    <div style="height: 160px; overflow: hidden;">
                        <?php 
                            $cardImgSrc = 'https://images.unsplash.com/photo-1543351611-58f69d7c1781?auto=format&fit=crop&q=80&w=1000';
                            if (!empty($quadraItem['imagem'])) {
                                $cardImgSrc = str_starts_with($quadraItem['imagem'], 'http') ? $quadraItem['imagem'] : '../assets/uploads/quadras/' . htmlspecialchars($quadraItem['imagem']);
                            }
                        ?>
                        <img src="<?= $cardImgSrc ?>" class="card-img-top w-100 h-100 object-fit-cover">
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <h5 class="card-title fw-bold text-white mb-2"><?= htmlspecialchars($quadraItem['nome']) ?></h5>
                        <p class="card-text text-secondary small mb-3"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($quadraItem['endereco']) ?></p>
                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <?php
                                $statusClass = 'bg-warning';
                                $statusLabel = 'Aguardando';
                                if ($quadraItem['status'] === 'ativo')     { $statusClass = 'bg-success'; $statusLabel = 'Ativo'; }
                                if ($quadraItem['status'] === 'rejeitado') { $statusClass = 'bg-danger';  $statusLabel = 'Rejeitado'; }
                            ?>
                            <span class="badge <?= $statusClass ?> opacity-75 shadow-sm"><?= $statusLabel ?></span>
                            <span class="text-warning fw-bold small">Gerenciar <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($isGerente)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card addArenaCard h-100" data-bs-toggle="modal" data-bs-target="#modalNovoArena" style="cursor: pointer;">
                        <div class="text-center">
                            <i class="bi bi-plus-lg display-4 d-block mb-2"></i>
                            <span class="fw-bold">Nova Arena</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
