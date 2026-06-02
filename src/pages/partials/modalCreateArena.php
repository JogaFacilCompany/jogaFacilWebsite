<?php
// pages/partials/modalCreateArena.php – Create arena modal
?>
<div class="modal fade" id="modalNovoArena" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="../crud/createQuadra.php" method="POST" enctype="multipart/form-data" class="modal-content text-dark">
            <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Cadastrar Nova Arena</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Nome da Arena</label>
                        <input type="text" class="form-control formInput" name="nome" placeholder="Ex: Arena Joga Fácil 2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Horário de Funcionamento</label>
                        <input type="text" class="form-control formInput" name="funcionamento" placeholder="Ex: 08:00 - 22:00" value="08:00 - 22:00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Telefone *</label>
                        <input type="text" class="form-control formInput" name="telefone" placeholder="(00) 00000-0000" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">CNPJ *</label>
                        <input type="text" class="form-control formInput cnpj-mask" name="cnpj" placeholder="00.000.000/0000-00" required>
                    </div>
                    //aqui insere o form
                    <div class="col-12">
                        <label class="form-label fw-medium">Endereço Completo *</label>
                        <input type="text" class="form-control formInput" name="endereco" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Modalidades</label>
                        <input type="text" class="form-control formInput" name="modalidades" value="Futebol">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">Descrição</label>
                        <textarea class="form-control formInput" name="descricao" rows="2" placeholder="Conte um pouco sobre esta nova unidade..."></textarea>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-images text-success"></i> Imagens da Arena</h6>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Imagem de Capa</label>
                        <input type="file" class="form-control formInput" name="imagemCapa" id="createCapaInput" accept="image/jpeg,image/png,image/webp,image/gif">
                        <div class="form-text">Usada no topo da página e nos cards (1200x400).</div>
                        <div class="upload-preview-container">
                            <img id="createCapaPreview" class="cover-preview" src="" alt="Preview">
                            <span id="createCapaPlaceholder" class="text-secondary small d-block my-3"><i class="bi bi-image" style="font-size: 2rem;"></i><br>Nenhuma capa selecionada</span>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Galeria (Até 6 imagens)</label>
                        <input type="file" class="form-control formInput" name="imagensGaleria[]" id="createGaleriaInput" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
                        <div class="form-text">Imagens adicionais da estrutura (800x600).</div>
                        <div class="upload-preview-container" style="min-height: 154px;">
                            <div id="createGaleriaPreview" class="gallery-preview-grid"></div>
                            <span id="createGaleriaPlaceholder" class="text-secondary small d-block my-3"><i class="bi bi-images" style="font-size: 2rem;"></i><br>Nenhuma imagem selecionada</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success fw-bold px-4">Cadastrar Arena</button>
            </div>
        </form>
    </div>
</div>
