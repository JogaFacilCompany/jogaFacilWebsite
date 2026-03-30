<?php
// pages/escolher-login.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolher Login – Joga Fácil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Using Bootstrap Icons for nice visuals -->
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
        <h3 class="formTitle fw-bold text-center mb-1">Entrar no Joga Fácil</h3>
        <p class="text-white-50 text-center small mb-4">Escolha como deseja acessar a plataforma</p>

        <div class="d-flex flex-column gap-3">
            <a href="login-locador.php" class="choiceCard">
                <div class="choiceIcon">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Sou Locador</h5>
                    <small class="text-white-50">Acessar painel e gerenciar quadras</small>
                </div>
            </a>
            
            <!-- Link for Locatario -->
            <a href="login-locatario.php" class="choiceCard">
                <div class="choiceIcon" style="color: #4ade80; background-color: rgba(74, 222, 128, 0.1);">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Sou Locatário</h5>
                    <small class="text-white-50">Agendar, pagar e encontrar jogos</small>
                </div>
            </a>

            <!-- Link for Gerente -->
            <a href="login-gerente.php" class="choiceCard">
                <div class="choiceIcon" style="color: #60a5fa; background-color: rgba(96, 165, 250, 0.1);">
                    <i class="bi bi-briefcase"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Gerente de Quadra</h5>
                    <small class="text-white-50">Gerenciar caixa e marcações</small>
                </div>
            </a>

            <!-- Link for Admin -->
            <a href="login-admin.php" class="choiceCard">
                <div class="choiceIcon" style="color: #f87171; background-color: rgba(248, 113, 113, 0.1);">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-white fw-bold">Administrador</h5>
                    <small class="text-white-50">Acesso completo ao sistema</small>
                </div>
            </a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
