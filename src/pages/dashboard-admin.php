<?php

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'admin') {
    header('Location: ../pages/login-admin.php');
    exit;
}
require_once __DIR__ . '/../crud/readUsuarios.php';
require_once __DIR__ . '/../crud/readQuadras.php';
require_once __DIR__ . '/../config/csrf.php';

$csrfToken    = generateCsrfToken();
$todoUsuarios  = readAllUsuarios();
$arenasPendentes = getAllPendingQuadras();
$flashMessage  = $_SESSION['flashMessage'] ?? null;
$flashType     = $_SESSION['flashType']    ?? 'info';
unset($_SESSION['flashMessage'], $_SESSION['flashType']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador – Joga Fácil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/customStyles.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="dashboardTitle fw-bold">Painel do Administrador</h2>
    </div>

    <?php if ($flashMessage): ?>
        <div class="alert alert-<?= $flashType ?> alertMessage shadow-sm"><?= htmlspecialchars($flashMessage) ?></div>
    <?php endif; ?>

    <!-- Seção de Moderação de Arenas -->
    <div class="card border-0 shadow-sm p-4 mb-5 dashboardCard" style="border-left: 4px solid var(--amareloOuro) !important;">
        <h5 class="fw-bold mb-3 d-flex align-items-center">
            <i class="bi bi-clock-history me-2 text-warning"></i> 
            Solicitações de Novas Arenas 
            <span class="badge bg-warning ms-2"><?= count($arenasPendentes) ?></span>
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
                    <?php if (empty($arenasPendentes)): ?>
                        <tr><td colspan="5" class="text-center py-4">Nenhuma solicitação pendente</td></tr>
                    <?php else: ?>
                        <?php foreach ($arenasPendentes as $pend): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($pend['nome']) ?></strong></td>
                            <td><?= htmlspecialchars($pend['locador_nome']) ?></td>
                            <td class="text-secondary small"><?= htmlspecialchars($pend['endereco']) ?></td>
                            <td><?= date('d/m/Y', strtotime($pend['created_at'])) ?></td>
                            <td>
                                <a href="admin-preview-arena.php?id=<?= $pend['id'] ?>" class="btn btn-sm btn-primary px-3 rounded-pill">Visualizar e Avaliar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- CRUD Table -->
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
                    <?php if (empty($todoUsuarios)): ?>
                        <tr><td colspan="7" class="text-center text-muted">Nenhum usuário cadastrado</td></tr>
                    <?php else: ?>
                        <?php foreach ($todoUsuarios as $rowUsuario): ?>
                        <tr>
                            <td><?= $rowUsuario['id'] ?></td>
                            <td><?= htmlspecialchars($rowUsuario['nome']) ?></td>
                            <td><?= htmlspecialchars($rowUsuario['email']) ?></td>
                            <td><span class="badge bg-success"><?= $rowUsuario['tipo'] ?></span></td>
                            <td><?= $rowUsuario['cpf'] ?? '—' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rowUsuario['criadoEm'])) ?></td>
                            <td>
                                <form action="../crud/deleteUsuario.php" method="POST" class="d-inline deleteForm">
                                    <input type="hidden" name="id" value="<?= $rowUsuario['id'] ?>">
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
