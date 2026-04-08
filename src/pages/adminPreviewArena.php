<?php
// pages/adminPreviewArena.php – camelCase enforced
require_once __DIR__ . '/../middleware/authGuard.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/csrf.php';

requireAuth('admin', '../pages/loginAdmin.php');

$arenaId = (int)($_GET['id'] ?? 0);
$pdo     = getDbConnection();
$stmt    = $pdo->prepare("SELECT q.*, u.nome as locador_nome, u.email as locador_email FROM quadras q JOIN usuarios u ON q.locador_id = u.id WHERE q.id = ?");
$stmt->execute([$arenaId]);
$quadra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quadra) {
    echo "Arena não encontrada.";
    exit;
}

$csrfToken   = generateCsrfToken();
$facilidades = json_decode($quadra['facilidades'], true) ?: [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php $pageTitle = 'Moderação de Arena – Joga Fácil'; include __DIR__ . '/../includes/headTag.php'; ?>
</head>
<body style="background-color: var(--bgMain);">

    <!-- Moderation bar -->
    <div class="adminPreviewHeader shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <a href="dashboardAdmin.php" class="btn btn-outline-light btn-sm me-3"><i class="bi bi-arrow-left"></i> Voltar</a>
                <span class="badge bg-warning">MODERAÇÃO: <?= htmlspecialchars($quadra['nome']) ?></span>
            </div>
            <div class="d-flex gap-2">
                <form action="../crud/updateQuadraStatus.php" method="POST">
                    <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">
                    <input type="hidden" name="id" value="<?= $arenaId ?>">
                    <input type="hidden" name="status" value="rejeitado">
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold"><i class="bi bi-x-circle"></i> Rejeitar</button>
                </form>
                <form action="../crud/updateQuadraStatus.php" method="POST">
                    <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">
                    <input type="hidden" name="id" value="<?= $arenaId ?>">
                    <input type="hidden" name="status" value="ativo">
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold"><i class="bi bi-check-circle"></i> Aprovar Arena</button>
                </form>
            </div>
        </div>
    </div>

    <main class="container py-5">
        <div class="row g-4">
            <!-- Sidebar: locador data -->
            <div class="col-lg-4">
                <div class="card bg-dark text-white p-4 shadow-sm h-100 border-0">
                    <h5 class="fw-bold mb-4">Dados do Locador</h5>
                    <div class="mb-3">
                        <label class="text-secondary small d-block">Proprietário:</label>
                        <span class="fs-5"><?= htmlspecialchars($quadra['locador_nome']) ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="text-secondary small d-block">E-mail de contato:</label>
                        <span><?= htmlspecialchars($quadra['locador_email']) ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="text-secondary small d-block">Telefone da Arena:</label>
                        <span><?= htmlspecialchars($quadra['telefone']) ?></span>
                    </div>
                    <div class="mb-3">
                        <label class="text-secondary small d-block">CNPJ:</label>
                        <span class="text-info fw-bold"><?php
                            $cnpjDigits = preg_replace('/\D/', '', $quadra['cnpj']);
                            echo strlen($cnpjDigits) === 14
                                ? substr($cnpjDigits, 0, 2) . '.' . substr($cnpjDigits, 2, 3) . '.' . substr($cnpjDigits, 5, 3) . '/' . substr($cnpjDigits, 8, 4) . '-' . substr($cnpjDigits, 12, 2)
                                : htmlspecialchars($quadra['cnpj']);
                        ?></span>
                    </div>
                    <hr class="border-secondary my-4">
                    <h5 class="fw-bold mb-3">Horário de Funcionamento</h5>
                    <span class="badge bg-secondary p-2 px-3 fs-6"><?= htmlspecialchars($quadra['funcionamento']) ?></span>
                </div>
            </div>

            <!-- Arena details -->
            <div class="col-lg-8">
                <div class="card bg-dark text-white shadow-sm border-0 overflow-hidden">
                    <img src="<?= htmlspecialchars($quadra['imagem'] ?: 'https://images.unsplash.com/photo-1543351611-58f69d7c1781?auto=format&fit=crop&q=80&w=1000') ?>" class="w-100 object-fit-cover" style="height: 300px;">
                    <div class="p-4">
                        <h2 class="fw-bold mb-2"><?= htmlspecialchars($quadra['nome']) ?></h2>
                        <p class="text-secondary"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($quadra['endereco']) ?></p>

                        <div class="mt-4">
                            <h5 class="fw-bold fs-6 text-uppercase text-warning">Descrição</h5>
                            <p class="text-light opacity-75 fs-5">"<?= nl2br(htmlspecialchars($quadra['descricao'])) ?>"</p>
                        </div>

                        <div class="mt-4">
                            <h5 class="fw-bold fs-6 text-uppercase text-warning">Facilidades Informadas</h5>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <?php foreach ($facilidades as $facilidadeItem): ?>
                                    <span class="badge rounded-pill border border-secondary p-2 px-3"><?= htmlspecialchars($facilidadeItem) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top border-secondary">
                            <span class="text-secondary small">Cadastrada em: <?= date('d/m/Y H:i', strtotime($quadra['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
