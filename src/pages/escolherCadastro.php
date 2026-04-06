<?php
// pages/escolherCadastro.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Cadastro – Joga Fácil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../assets/css/customStyles.css" rel="stylesheet">
    <style>
        .choiceCard {
            background-color: var(--bgSection);
            border: 1px solid var(--bgCardBorder);
            border-radius: var(--radiusMd);
            padding: 20px;
            color: var(--textPrimary) !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 18px;
            transition: var(--transition);
        }
        .choiceCard:hover {
            transform: translateY(-3px);
            border-color: var(--verdeBandeira);
            box-shadow: 0 8px 24px rgba(0, 156, 59, 0.15);
            background-color: rgba(0, 156, 59, 0.05);
        }
        .choiceIcon {
            font-size: 2rem;
            color: var(--amareloOuro);
            background-color: rgba(255, 223, 0, 0.1);
            padding: 10px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
        }
    </style>
</head>
<body class="authPageBody d-flex flex-column min-vh-100">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="loginFormCard card shadow-sm border-0 p-4" style="max-width: 500px; width: 100%;">
        <h3 class="formTitle fw-bold text-center mb-1">Crie sua Conta</h3>
        <p class="text-white-50 text-center small mb-4">Escolha o seu perfil para começar</p>

        <div class="d-flex flex-column gap-3">
            <a href="cadastroLocador.php" class="choiceCard">
                <div class="choiceIcon">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Quero ser Locador</h5>
                    <small class="text-white-50">Cadastrar e anunciar minhas quadras</small>
                </div>
            </a>
            
            <!-- Link to the empty page for locatario for now -->
            <a href="cadastroLocatario.php" class="choiceCard">
                <div class="choiceIcon" style="color: #4ade80; background-color: rgba(74, 222, 128, 0.1);">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Quero ser Locatário</h5>
                    <small class="text-white-50">Encontrar e reservar quadras</small>
                </div>
            </a>
        </div>
        
        <p class="text-center mt-4 small">Já tem uma conta? <a href="../pages/escolherLogin.php" class="authLink text-success fw-medium">Entrar</a></p>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
