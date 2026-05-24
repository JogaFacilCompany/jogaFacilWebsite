<?php
// pages/partials/locadorArenaEdit.php – Edição do Perfil da Arena
?>
<div class="row g-4 mt-3">
    <!-- Coluna Esquerda: Galeria e Facilidades -->
    <div class="col-lg-7">
        <?php if (empty($isGerente)): ?>
        <div class="arena-image-manager shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="detailInfoCardTitle m-0">
                    <i class="bi bi-images cardTitleIcon text-info"></i> Galeria de Imagens
                </h5>
                <span class="badge bg-secondary"><?= count($arenaImagens) ?>/6 imagens</span>
            </div>
            
            <?php if (empty($arenaImagens)): ?>
                <div class="text-center py-4 text-secondary">
                    <i class="bi bi-camera" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">Nenhuma imagem na galeria.</p>
                    <p class="small">Adicione imagens editando o perfil da arena.</p>
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($arenaImagens as $img): ?>
                        <div class="gallery-item">
                            <img src="../assets/uploads/quadras/<?= htmlspecialchars($img['nome_arquivo']) ?>" alt="Imagem da Galeria">
                            <form action="../crud/deleteImagemQuadra.php" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja apagar esta imagem?');">
                                <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                                <input type="hidden" name="imagem_id" value="<?= $img['id'] ?>">
                                <input type="hidden" name="arena_id" value="<?= $quadra['id'] ?>">
                                <button type="submit" class="gallery-item-delete" title="Remover imagem">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="mt-4 text-center">
                <button class="btn btn-outline-info btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalEditarArena">
                    <i class="bi bi-upload"></i> Gerenciar Imagens
                </button>
            </div>
        </div>
        <?php endif; ?>

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
    </div>

    <!-- Coluna Direita: Informações e Exclusão -->
    <div class="col-lg-5">
        <div class="detailInfoCard shadow-sm mb-4">
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

        <div class="detailInfoCard shadow-sm mb-4">
            <h5 class="detailInfoCardTitle">
                <i class="bi bi-telephone-fill cardTitleIcon"></i> Contato
            </h5>
            <div class="detailPhone">
                <?= htmlspecialchars($quadra['telefone'] ?? 'Não informado') ?>
            </div>
        </div>

        <?php if (empty($isGerente)): ?>
            <div class="mt-4 border-top border-secondary opacity-75 pt-4">
                <button class="btn btn-outline-danger w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalExcluirArena">
                    <i class="bi bi-trash3"></i> Excluir Arena Permanentemente
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>
