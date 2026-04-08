<?php
// pages/partials/modalEditArena.php – Edit arena modal
$allFacilities     = ['Cantina', 'Vestiários', 'Aluguel de Bola', 'Bebedouro', 'Wi-Fi'];
$currentFacilities = json_decode($quadra['facilidades'], true) ?: [];
?>
<div class="modal fade" id="modalEditarArena" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="../crud/updateQuadra.php" method="POST" class="modal-content text-dark">
            <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="id" value="<?= $quadra['id'] ?>">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Editar Informações da Arena</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Nome da Arena *</label>
                        <input type="text" class="form-control formInput" name="nome" value="<?= htmlspecialchars($quadra['nome']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Telefone *</label>
                        <input type="text" class="form-control formInput" name="telefone" value="<?= htmlspecialchars($quadra['telefone']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">CNPJ *</label>
                        <input type="text" class="form-control formInput cnpj-mask" name="cnpj" value="<?= htmlspecialchars($quadra['cnpj'] ?? '') ?>" placeholder="00.000.000/0000-00" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Horário de Funcionamento *</label>
                        <input type="text" class="form-control formInput" name="funcionamento" value="<?= htmlspecialchars($quadra['funcionamento'] ?? '08:00 - 22:00') ?>" placeholder="Ex: 08:00 - 22:00">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">Endereço Completo</label>
                        <input type="text" class="form-control formInput" name="endereco" value="<?= htmlspecialchars($quadra['endereco']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">Descrição</label>
                        <textarea class="form-control formInput" name="descricao" rows="3"><?= htmlspecialchars($quadra['descricao'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium mb-2 d-block">Facilidades</label>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach ($allFacilities as $facilityOption): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="facilidades[]" value="<?= $facilityOption ?>" id="facDet_<?= $facilityOption ?>" <?= in_array($facilityOption, $currentFacilities) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="facDet_<?= $facilityOption ?>"><?= $facilityOption ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success fw-bold px-4">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>
