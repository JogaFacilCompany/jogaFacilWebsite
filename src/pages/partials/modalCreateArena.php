<?php
// pages/partials/modalCreateArena.php – Create arena modal
?>
<div class="modal fade" id="modalNovoArena" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="../crud/createQuadra.php" method="POST" class="modal-content text-dark">
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success fw-bold px-4">Cadastrar Arena</button>
            </div>
        </form>
    </div>
</div>
