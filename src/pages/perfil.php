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

$pageTitle = 'Meu Perfil – Joga Fácil';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include __DIR__ . '/../includes/headTag.php'; ?>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnEditar = document.getElementById('btnEditar');
    const btnCancelar = document.getElementById('btnCancelar');
    const divSalvar = document.getElementById('divSalvar');
    const editableFields = document.querySelectorAll('.editable-field');
    const formPerfil = document.getElementById('formPerfil');
    
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
        btnEditar.classList.remove('d-none');
        
        // Remove error states if any
        formPerfil.classList.remove('was-validated');
    });
});
</script>
</body>
</html>
