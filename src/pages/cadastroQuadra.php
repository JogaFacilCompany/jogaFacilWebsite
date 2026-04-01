<?php
// pages/cadastroQuadra.php – Court registration form (locador only)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/auth.php';
requireLocadorAuth();
require_once __DIR__ . '/../config/csrf.php';

$csrfToken    = generateCsrfToken();
$flashMessage = $_SESSION['flashMessage'] ?? null;
$flashType    = $_SESSION['flashType']    ?? 'info';
unset($_SESSION['flashMessage'], $_SESSION['flashType']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Quadra – Joga Fácil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../assets/css/customStyles.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <h2 class="dashboardTitle fw-bold mb-4">
                <i class="bi bi-plus-circle-fill" style="color: var(--verdeBandeira);"></i>
                Cadastrar Nova Quadra
            </h2>

            <?php if ($flashMessage): ?>
                <div class="alert alert-<?= $flashType ?> alertMessage"><?= htmlspecialchars($flashMessage) ?></div>
            <?php endif; ?>

            <div class="card border-0 p-4 dashboardCard">
                <form action="../crud/createQuadra.php" method="POST" id="cadastroQuadraForm" novalidate>
                    <input type="hidden" name="csrfToken" value="<?= $csrfToken ?>">

                    <!-- Nome -->
                    <div class="mb-3">
                        <label for="inputNome" class="form-label fw-medium">Nome da Quadra <span class="text-danger">*</span></label>
                        <input type="text" class="form-control formInput" id="inputNome" name="nome" placeholder="Ex: Arena Central" required>
                    </div>

                    <!-- Endereço -->
                    <div class="mb-3">
                        <label for="inputEndereco" class="form-label fw-medium">Endereço <span class="text-danger">*</span></label>
                        <input type="text" class="form-control formInput" id="inputEndereco" name="endereco" placeholder="Rua, número – Bairro" required>
                    </div>

                    <div class="row g-3">
                        <!-- CNPJ -->
                        <div class="col-md-6">
                            <label for="inputCnpj" class="form-label fw-medium">CNPJ</label>
                            <input type="text" class="form-control formInput" id="inputCnpj" name="cnpj" placeholder="00.000.000/0000-00" maxlength="18">
                        </div>

                        <!-- Preço/hora -->
                        <div class="col-md-6">
                            <label for="inputPreco" class="form-label fw-medium">Preço por Hora (R$) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control formInput" id="inputPreco" name="precoHora" placeholder="150.00" step="0.01" min="1" required>
                        </div>
                    </div>

                    <!-- Descrição -->
                    <div class="mb-3 mt-3">
                        <label for="inputDescricao" class="form-label fw-medium">Descrição</label>
                        <textarea class="form-control formInput" id="inputDescricao" name="descricao" rows="3" placeholder="Descreva a quadra, facilidades, modalidades..."></textarea>
                    </div>

                    <!-- Imagem URL -->
                    <div class="mb-3">
                        <label for="inputImagem" class="form-label fw-medium">URL da Imagem</label>
                        <input type="url" class="form-control formInput" id="inputImagem" name="imagemUrl" placeholder="https://exemplo.com/imagem.jpg">
                    </div>

                    <!-- Horários disponíveis -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">Horários Disponíveis</label>
                        <p style="color: var(--textMuted); font-size: 0.85rem;">Adicione os horários em que a quadra estará disponível para reserva.</p>

                        <div id="horariosContainer"></div>

                        <button type="button" class="btn btn-outline-success btn-sm mt-2" id="addHorarioBtn">
                            <i class="bi bi-plus-lg"></i> Adicionar Horário
                        </button>
                    </div>

                    <hr style="border-color: var(--bgCardBorder);">

                    <div class="d-flex justify-content-between">
                        <a href="dashboardLocador.php" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-success fw-bold rounded-pill px-5">Cadastrar Quadra</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/appLogic.js"></script>
<script src="../assets/js/quadraFormLogic.js"></script>
</body>
</html>
