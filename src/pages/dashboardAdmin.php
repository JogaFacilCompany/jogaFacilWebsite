<?php
// pages/dashboardAdmin.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
require_once __DIR__ . '/../utils/flashMessage.php';
require_once __DIR__ . '/../crud/readUsuarios.php';
require_once __DIR__ . '/../crud/readQuadras.php';
require_once __DIR__ . '/../config/csrf.php';

requireAuth('admin', '../pages/loginAdmin.php');

$csrfToken       = generateCsrfToken();
$allUsers        = readAllUsuarios();
$pendingArenas   = getAllPendingQuadras();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php $pageTitle = 'Painel do Administrador – Joga Fácil'; include __DIR__ . '/../includes/headTag.php'; ?>
</head>
<body class="d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="dashboardTitle fw-bold">Painel do Administrador</h2>
    </div>

    <?php renderFlash(); ?>

    <!-- Arena moderation section -->
    <div class="card border-0 shadow-sm p-4 mb-5 dashboardCard" style="border-left: 4px solid var(--amareloOuro) !important;">
        <h5 class="fw-bold mb-3 d-flex align-items-center">
            <i class="bi bi-clock-history me-2 text-warning"></i>
            Solicitações de Novas Arenas
            <span class="badge bg-warning ms-2"><?= count($pendingArenas) ?></span>
        </h5>
        <div class="table-responsive">
            <table class="table dashboardTable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Arena</th>
                        <th>Locador</th>
                        <th>Endereço</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendingArenas)): ?>
                        <tr><td colspan="5" class="text-center py-4">Nenhuma solicitação pendente</td></tr>
                    <?php else: ?>
                        <?php foreach ($pendingArenas as $pendingArena): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($pendingArena['nome']) ?></strong></td>
                            <td><?= htmlspecialchars($pendingArena['locador_nome']) ?></td>
                            <td class="text-secondary small"><?= htmlspecialchars($pendingArena['endereco']) ?></td>
                            <td><?= date('d/m/Y', strtotime($pendingArena['created_at'])) ?></td>
                            <td>
                                <a href="adminPreviewArena.php?id=<?= $pendingArena['id'] ?>" class="btn btn-sm btn-primary px-3 rounded-pill">Visualizar e Avaliar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Users CRUD table -->
    <div class="card border-0 shadow-sm p-4 dashboardCard">
        <h5 class="fw-bold mb-3">Todos os Usuários</h5>
        <div class="table-responsive">
            <table class="table dashboardTable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                        <th>CPF</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($allUsers)): ?>
                        <tr><td colspan="8" class="text-center">Nenhum usuário cadastrado</td></tr>
                    <?php else: ?>
                        <?php foreach ($allUsers as $userRow): ?>
                        <?php $estaInativo = $userRow['status'] === 'inativo'; ?>
                        <tr class="<?= $estaInativo ? 'usuarioInativoRow' : '' ?>">
                            <td><?= $userRow['id'] ?></td>
                            <td><?= htmlspecialchars($userRow['nome']) ?></td>
                            <td><?= htmlspecialchars($userRow['email']) ?></td>
                            <td><span class="badge bg-success"><?= $userRow['tipo'] ?></span></td>
                            <td><?= $userRow['cpf'] ?? '—' ?></td>
                            <td>
                                <?php if ($estaInativo): ?>
                                    <span class="badge statusBadgeInativo">Inativo</span>
                                <?php else: ?>
                                    <span class="badge statusBadgeAtivo">Ativo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($userRow['created_at'])) ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <?php if ($userRow['tipo'] !== 'admin'): ?>
                                        <?php if ($estaInativo): ?>
                                            <form action="../crud/updateUsuarioStatus.php" method="POST" class="d-inline">
                                                <input type="hidden" name="csrfToken"  value="<?= $csrfToken ?>">
                                                <input type="hidden" name="id"         value="<?= $userRow['id'] ?>">
                                                <input type="hidden" name="novoStatus" value="ativo">
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                        onclick="return confirm('Reativar a conta de <?= htmlspecialchars($userRow['nome'], ENT_QUOTES) ?>?')">
                                                    Ativar
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-warning inativarBtn"
                                                    data-id="<?= $userRow['id'] ?>"
                                                    data-nome="<?= htmlspecialchars($userRow['nome'], ENT_QUOTES) ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalInativarUsuario">
                                                Inativar
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <form action="../crud/deleteUsuario.php" method="POST" class="d-inline deleteForm">
                                        <input type="hidden" name="id"        value="<?= $userRow['id'] ?>">
                                        <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Modal de confirmação de inativação -->
<div class="modal fade" id="modalInativarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--bgCard); border: 1px solid var(--bgCardBorder); color: var(--textPrimary);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-warning">Inativar Conta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../crud/updateUsuarioStatus.php" method="POST">
                <div class="modal-body pt-3">
                    <input type="hidden" name="csrfToken"  value="<?= $csrfToken ?>">
                    <input type="hidden" name="novoStatus" value="inativo">
                    <input type="hidden" name="id"         id="inativarUserId">

                    <p class="mb-3">
                        Você está prestes a inativar a conta de
                        <strong id="inativarUserNome" class="text-warning"></strong>.
                    </p>

                    <div class="mb-2">
                        <label for="inativarMotivo" class="form-label fw-medium">
                            Motivo da inativação <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control"
                                  id="inativarMotivo"
                                  name="motivo"
                                  rows="3"
                                  maxlength="500"
                                  placeholder="Descreva o motivo da inativação desta conta..."
                                  required
                                  style="background: var(--bgSection); border-color: var(--bgCardBorder); color: var(--textPrimary); resize: none;"></textarea>
                        <div class="invalid-feedback">O motivo é obrigatório.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold" id="inativarConfirmarBtn">
                        Confirmar Inativação
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/appLogic.js"></script>
<script>

document.querySelectorAll('.inativarBtn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('inativarUserId').value          = btn.dataset.id;
        document.getElementById('inativarUserNome').textContent  = btn.dataset.nome;
        document.getElementById('inativarMotivo').value          = '';
        document.getElementById('inativarMotivo').classList.remove('is-invalid');
    });
});


document.getElementById('modalInativarUsuario')
    .querySelector('form')
    .addEventListener('submit', function(e) {
        var motivo = document.getElementById('inativarMotivo');
        if (!motivo.value.trim()) {
            e.preventDefault();
            motivo.classList.add('is-invalid');
        }
    });
</script>
</body>
</html>
