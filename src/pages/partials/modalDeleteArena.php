<?php
// pages/partials/modalDeleteArena.php – Delete arena confirmation modal
?>
<div class="modal fade" id="modalExcluirArena" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="../crud/deleteQuadra.php" method="POST" class="modal-content text-dark">
            <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="id" value="<?= $quadra['id'] ?>">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <i class="bi bi-exclamation-triangle text-danger display-4 mb-3 d-block"></i>
                <p class="mb-0">Tem certeza que deseja excluir a arena <strong><?= htmlspecialchars($quadra['nome']) ?></strong>?</p>
                <p class="small">Esta ação é irreversível e apagará todos os dados vinculados.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sim, Excluir</button>
            </div>
        </form>
    </div>
</div>
