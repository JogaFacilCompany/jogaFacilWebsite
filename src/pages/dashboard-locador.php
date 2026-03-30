<?php
// pages/dashboard-locador.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/auth.php';
requireLocadorAuth();
require_once __DIR__ . '/../config/csrf.php';

$csrfToken    = generateCsrfToken();
$todoUsuarios  = readAllUsuarios();
$flashMessage  = $_SESSION['flashMessage'] ?? null;
$flashType     = $_SESSION['flashType']    ?? 'info';
unset($_SESSION['flashMessage'], $_SESSION['flashType']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Locador – Joga Fácil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/customStyles.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="dashboardTitle fw-bold">Painel do Locador</h2>
            <?php if (isset($_SESSION['usuarioTipo']) && $_SESSION['usuarioTipo'] !== 'locatario'): ?>
                <a href="../pages/cadastrar-gerente.php" class="btn btn-warning fw-bold px-4 rounded-pill">+ Cadastrar Gerente</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($flashMessage): ?>
        <div class="alert alert-<?= $flashType ?> alertMessage"><?= htmlspecialchars($flashMessage) ?></div>
    <?php endif; ?>

    <!-- CRUD: Add User -->
    <div class="card border-0 shadow-sm mb-4 p-4 dashboardCard">
        <h5 class="fw-bold mb-3">Adicionar Usuário</h5>
        <form action="../crud/createUsuario.php" method="POST" id="addUsuarioForm" class="row g-3">
            <input type="hidden" name="source" value="dashboard">
            <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">
            <div class="col-md-3">
                <input type="text" class="form-control formInput" name="nome" placeholder="Nome" required>
            </div>
            <div class="col-md-3">
                <input type="email" class="form-control formInput" name="email" placeholder="E-mail" required>
            </div>
            <div class="col-md-2">
                <input type="password" class="form-control formInput" name="senha" placeholder="Senha" required>
            </div>
            <div class="col-md-2">
                <select class="form-select formInput" name="tipo">
                    <option value="locatario">Locatário</option>
                    <?php if (isset($_SESSION['usuarioTipo']) && $_SESSION['usuarioTipo'] !== 'locatario'): ?>
                        <option value="locador">Locador</option>
                        <option value="gerente">Gerente</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success w-100 fw-bold">Adicionar</button>
            </div>
        </form>
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
                                <button class="btn btn-sm btn-outline-primary editarBtn"
                                    data-id="<?= $rowUsuario['id'] ?>"
                                    data-nome="<?= htmlspecialchars($rowUsuario['nome']) ?>"
                                    data-email="<?= htmlspecialchars($rowUsuario['email']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#editarModal">
                                    Editar
                                </button>
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

<!-- Edit Modal -->
<div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="../crud/updateUsuario.php" method="POST" class="modal-content">
            <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="editarModalLabel">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="editarId">
                <div class="mb-3">
                    <label class="form-label fw-medium">Nome</label>
                    <input type="text" class="form-control formInput" name="nome" id="editarNome">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">E-mail</label>
                    <input type="email" class="form-control formInput" name="email" id="editarEmail">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">Nova Senha (deixe em branco para manter)</label>
                    <input type="password" class="form-control formInput" name="senha" placeholder="Nova senha">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success fw-bold">Salvar</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/appLogic.js"></script>
</body>
</html>
