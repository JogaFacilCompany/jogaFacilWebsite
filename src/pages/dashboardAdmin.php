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
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($allUsers)): ?>
                        <tr><td colspan="7" class="text-center">Nenhum usuário cadastrado</td></tr>
                    <?php else: ?>
                        <?php foreach ($allUsers as $userRow): ?>
                        <tr>
                            <td><?= $userRow['id'] ?></td>
                            <td><?= htmlspecialchars($userRow['nome']) ?></td>
                            <td><?= htmlspecialchars($userRow['email']) ?></td>
                            <td><span class="badge bg-success"><?= $userRow['tipo'] ?></span></td>
                            <td><?= $userRow['cpf'] ?? '—' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($userRow['created_at'])) ?></td>
                            <td>
                                <form action="../crud/deleteUsuario.php" method="POST" class="d-inline deleteForm">
                                    <input type="hidden" name="id" value="<?= $userRow['id'] ?>">
                                    <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                                </form>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/appLogic.js"></script>
</body>
</html>
