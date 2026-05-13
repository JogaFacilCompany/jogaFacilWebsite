<?php
// pages/perfil.php
require_once __DIR__ . '/../middleware/authGuard.php';
require_once __DIR__ . '/../utils/flashMessage.php';
require_once __DIR__ . '/../crud/readUsuarios.php';
require_once __DIR__ . '/../config/csrf.php';

initSession();
if (!isset($_SESSION['usuarioLogado'])) {
    header('Location: ../pages/escolherLogin.php');
    exit;
}

$userId = $_SESSION['usuarioLogado'];
$usuario = readUsuarioById($userId);

if (!$usuario) {
    setFlash('Usuário não encontrado.', 'danger');
    header('Location: ../index.php');
    exit;
}

// Build avatar URL
$fotoPerfilUrl = null;
if (!empty($usuario['foto_perfil'])) {
    $fotoPerfilUrl = '../assets/uploads/perfil/' . htmlspecialchars($usuario['foto_perfil']);
}

$pageTitle = 'Meu Perfil – Joga Fácil';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include __DIR__ . '/../includes/headTag.php'; ?>
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <style>
        .perfil-form-control:disabled, .perfil-form-control[readonly] {
            background-color: #f8f9fa;
            opacity: 1;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">Meu Perfil</h4>
                    <button type="button" class="btn btn-warning btn-sm fw-bold" id="btnEditar">
                        <i class="bi bi-pencil-square"></i> Editar Informações
                    </button>
                </div>
                <div class="card-body p-4">
                    <?php renderFlash(); ?>
                    
                    <!-- Avatar Section -->
                    <div class="text-center mb-3">
                        <div class="perfil-avatar-wrapper">
                            <?php if ($fotoPerfilUrl): ?>
                                <img src="<?= $fotoPerfilUrl ?>" alt="Foto de perfil" class="perfil-avatar-img" id="avatarDisplay">
                            <?php else: ?>
                                <div class="perfil-avatar-default" id="avatarDisplay">
                                    <svg viewBox="0 0 24 24" fill="currentColor" color="#6c757d">
                                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v1.2c0 .7.5 1.2 1.2 1.2h16.8c.7 0 1.2-.5 1.2-1.2v-1.2c0-3.2-6.4-4.8-9.6-4.8z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Photo action buttons (hidden until edit mode) -->
                        <div class="perfil-foto-actions d-none" id="fotoActions">
                            <button type="button" class="btn btn-outline-success btn-sm" id="btnAlterarFoto">
                                <i class="bi bi-camera"></i> Alterar Foto
                            </button>
                            <?php if ($fotoPerfilUrl): ?>
                                <form action="../crud/deleteFotoPerfil.php" method="POST" class="d-inline" id="formRemoverFoto">
                                    <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remover foto de perfil?')">
                                        <i class="bi bi-trash"></i> Remover Foto
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <form action="../crud/updatePerfil.php" method="POST" id="formPerfil">
                        <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                        
                        <div class="mb-3">
                            <label for="tipo" class="form-label fw-medium text-muted">Tipo de Usuário (Não editável)</label>
                            <input type="text" class="form-control perfil-form-control text-capitalize" id="tipo" value="<?= htmlspecialchars($usuario['tipo'] ?? '') ?>" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label fw-medium">Nome Completo</label>
                            <input type="text" class="form-control perfil-form-control editable-field" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>" readonly required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">E-mail</label>
                            <input type="email" class="form-control perfil-form-control editable-field" id="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" readonly required>
                        </div>

                        <div class="mb-3">
                            <label for="cpf" class="form-label fw-medium">CPF</label>
                            <input type="text" class="form-control perfil-form-control editable-field" id="cpf" name="cpf" value="<?= htmlspecialchars($usuario['cpf'] ?? '') ?>" readonly>
                        </div>
                        
                        <div class="mb-4">
                            <label for="senha" class="form-label fw-medium">Nova Senha (deixe em branco para manter a atual)</label>
                            <input type="password" class="form-control perfil-form-control editable-field" id="senha" name="senha" placeholder="Nova senha" readonly>
                        </div>

                        <div class="d-none" id="divSalvar">
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary fw-bold" id="btnCancelar">Cancelar</button>
                                <button type="submit" class="btn btn-success fw-bold">Salvar Alterações</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Crop Modal -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="cropModalLabel">
                    <i class="bi bi-crop"></i> Recortar Foto de Perfil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body crop-modal-body">
                <!-- Step 1: File selection -->
                <div id="cropStep1">
                    <div class="crop-file-input-wrapper">
                        <label for="cropFileInput" class="form-label fw-medium">Selecionar imagem</label>
                        <input type="file" class="form-control" id="cropFileInput" accept="image/jpeg,image/png,image/webp,image/gif">
                        <div class="form-text">Formatos aceitos: JPEG, PNG, WebP, GIF. Máximo: 5MB.</div>
                    </div>
                </div>

                <!-- Step 2: Cropping area -->
                <div id="cropStep2" class="d-none">
                    <div class="crop-preview-container mb-3">
                        <img id="cropImage" src="" alt="Imagem para recorte">
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropRotateLeft" title="Girar para esquerda">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropRotateRight" title="Girar para direita">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropReset" title="Resetar">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-info-circle"></i> Arraste para ajustar a área de corte
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success fw-bold d-none" id="btnCropConfirm">
                    <i class="bi bi-check-lg"></i> Confirmar e Enviar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for photo upload (submitted by JS after crop) -->
<form action="../crud/uploadFotoPerfil.php" method="POST" enctype="multipart/form-data" id="formUploadFoto" class="d-none">
    <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
    <input type="file" name="fotoPerfil" id="hiddenFotoInput">
    <input type="hidden" name="cropX" id="hiddenCropX">
    <input type="hidden" name="cropY" id="hiddenCropY">
    <input type="hidden" name="cropWidth" id="hiddenCropWidth">
    <input type="hidden" name="cropHeight" id="hiddenCropHeight">
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Cropper.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnEditar = document.getElementById('btnEditar');
    const btnCancelar = document.getElementById('btnCancelar');
    const divSalvar = document.getElementById('divSalvar');
    const editableFields = document.querySelectorAll('.editable-field');
    const formPerfil = document.getElementById('formPerfil');
    const fotoActions = document.getElementById('fotoActions');
    
    // Store original values
    const originalValues = {};
    editableFields.forEach(field => {
        originalValues[field.id] = field.value;
    });

    btnEditar.addEventListener('click', function() {
        editableFields.forEach(field => {
            field.removeAttribute('readonly');
        });
        divSalvar.classList.remove('d-none');
        fotoActions.classList.remove('d-none');
        btnEditar.classList.add('d-none');
        document.getElementById('nome').focus();
    });

    btnCancelar.addEventListener('click', function() {
        editableFields.forEach(field => {
            field.setAttribute('readonly', true);
            // Restore original values
            field.value = originalValues[field.id] || '';
            // Reset senha since it might be empty originally but user typed something
            if(field.id === 'senha') field.value = '';
        });
        divSalvar.classList.add('d-none');
        fotoActions.classList.add('d-none');
        btnEditar.classList.remove('d-none');
        
        // Remove error states if any
        formPerfil.classList.remove('was-validated');
    });

    // ── Cropper.js logic ──
    const btnAlterarFoto = document.getElementById('btnAlterarFoto');
    const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
    const cropFileInput = document.getElementById('cropFileInput');
    const cropImage = document.getElementById('cropImage');
    const cropStep1 = document.getElementById('cropStep1');
    const cropStep2 = document.getElementById('cropStep2');
    const btnCropConfirm = document.getElementById('btnCropConfirm');
    const btnCropRotateLeft = document.getElementById('btnCropRotateLeft');
    const btnCropRotateRight = document.getElementById('btnCropRotateRight');
    const btnCropReset = document.getElementById('btnCropReset');

    let cropper = null;
    let selectedFile = null;

    btnAlterarFoto.addEventListener('click', function() {
        // Reset state
        cropFileInput.value = '';
        cropStep1.classList.remove('d-none');
        cropStep2.classList.add('d-none');
        btnCropConfirm.classList.add('d-none');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        cropModal.show();
    });

    cropFileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('A imagem deve ter no máximo 5MB.');
            cropFileInput.value = '';
            return;
        }

        // Validate type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Formato não suportado. Use JPEG, PNG, WebP ou GIF.');
            cropFileInput.value = '';
            return;
        }

        selectedFile = file;

        const reader = new FileReader();
        reader.onload = function(event) {
            cropImage.src = event.target.result;
            cropStep1.classList.add('d-none');
            cropStep2.classList.remove('d-none');
            btnCropConfirm.classList.remove('d-none');

            // Destroy previous cropper if any
            if (cropper) {
                cropper.destroy();
            }

            // Initialize Cropper.js
            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.8,
                restore: false,
                guides: true,
                center: true,
                highlight: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        };
        reader.readAsDataURL(file);
    });

    // Rotation buttons
    btnCropRotateLeft.addEventListener('click', function() {
        if (cropper) cropper.rotate(-90);
    });

    btnCropRotateRight.addEventListener('click', function() {
        if (cropper) cropper.rotate(90);
    });

    btnCropReset.addEventListener('click', function() {
        if (cropper) cropper.reset();
    });

    // Confirm and submit
    btnCropConfirm.addEventListener('click', function() {
        if (!cropper || !selectedFile) return;

        const cropData = cropper.getData(true); // rounded values

        // Transfer file to hidden form
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(selectedFile);
        document.getElementById('hiddenFotoInput').files = dataTransfer.files;

        // Set crop coordinates
        document.getElementById('hiddenCropX').value = Math.round(cropData.x);
        document.getElementById('hiddenCropY').value = Math.round(cropData.y);
        document.getElementById('hiddenCropWidth').value = Math.round(cropData.width);
        document.getElementById('hiddenCropHeight').value = Math.round(cropData.height);

        // Submit
        document.getElementById('formUploadFoto').submit();
    });

    // Reset modal on close
    document.getElementById('cropModal').addEventListener('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        selectedFile = null;
        cropFileInput.value = '';
        cropStep1.classList.remove('d-none');
        cropStep2.classList.add('d-none');
        btnCropConfirm.classList.add('d-none');
    });
});
</script>
</body>
</html>
