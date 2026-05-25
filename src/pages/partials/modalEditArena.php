<?php
// pages/partials/modalEditArena.php – Edit arena modal
$allFacilities     = ['Cantina', 'Vestiários', 'Aluguel de Bola', 'Bebedouro', 'Wi-Fi'];
$currentFacilities = json_decode($quadra['facilidades'], true) ?: [];
?>
<div class="modal fade" id="modalEditarArena" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="../crud/updateQuadra.php" method="POST" enctype="multipart/form-data" class="modal-content text-dark">
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
                        <input type="text" class="form-control formInput cnpj-mask" name="cnpj" value="<?= htmlspecialchars(preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $quadra['cnpj'] ?? '')) ?>" placeholder="00.000.000/0000-00" required>
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
                    
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-images text-success"></i> Adicionar Novas Imagens</h6>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Atualizar Imagem de Capa</label>
                        <input type="file" class="form-control formInput" name="imagemCapa" id="editCapaInput" accept="image/jpeg,image/png,image/webp,image/gif">
                        <div class="form-text">Deixe em branco para manter a atual.</div>
                        <div class="upload-preview-container">
                            <img id="editCapaPreview" class="cover-preview" src="" alt="Preview">
                            <span id="editCapaPlaceholder" class="text-secondary small d-block my-3"><i class="bi bi-image" style="font-size: 2rem;"></i><br>Nenhuma nova capa selecionada</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Adicionar Imagens à Galeria</label>
                        <input type="file" class="form-control formInput" name="imagensGaleria[]" id="editGaleriaInput" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
                        <div class="form-text">Você pode adicionar mais imagens (limite de 6 no total).</div>
                        <div class="upload-preview-container" style="min-height: 154px;">
                            <div id="editGaleriaPreview" class="gallery-preview-grid"></div>
                            <span id="editGaleriaPlaceholder" class="text-secondary small d-block my-3"><i class="bi bi-images" style="font-size: 2rem;"></i><br>Nenhuma nova imagem selecionada</span>
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
