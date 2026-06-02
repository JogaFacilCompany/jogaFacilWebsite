<?php
// includes/header.php
$baseUrl = (strpos($_SERVER['SCRIPT_NAME'], '/pages/') !== false || strpos($_SERVER['SCRIPT_NAME'], '/crud/') !== false) ? '../' : './';

$notificacoes  = [];
$totalNaoLidas = 0;
$tipoSessao    = $_SESSION['usuarioTipo'] ?? '';
if (isset($_SESSION['usuarioLogado']) && in_array($tipoSessao, ['locador', 'locatario'], true)) {
    require_once __DIR__ . '/../crud/readNotificacoes.php';
    $notificacoes  = getNotificacoesNaoLidas((int)$_SESSION['usuarioLogado']);
    $totalNaoLidas = count($notificacoes);
}
?>
<header class="mainHeader customGreenBg py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="<?= $baseUrl ?>index.php" class="text-decoration-none">
            <h2 class="logoTitle text-white m-0 fst-italic fw-bold">Joga Fácil</h2>
        </a>
        <nav class="authNav d-flex align-items-center gap-3">
            <a href="<?= $baseUrl ?>pages/buscarHorarios.php" class="text-white text-decoration-none fw-medium">Buscar Horários</a>
            <?php if (isset($_SESSION['usuarioLogado'])): ?>
                <?php
                    // Build avatar for header
                    $headerFoto = $_SESSION['usuarioFoto'] ?? null;
                    $headerFotoUrl = $headerFoto ? $baseUrl . 'assets/uploads/perfil/' . htmlspecialchars($headerFoto) : null;
                ?>
                <?php if ($headerFotoUrl): ?>
                    <img src="<?= $headerFotoUrl ?>" alt="Avatar" class="header-avatar">
                <?php else: ?>
                    <span class="header-avatar-default">
                        <svg viewBox="0 0 24 24" fill="currentColor" color="#6c757d">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v1.2c0 .7.5 1.2 1.2 1.2h16.8c.7 0 1.2-.5 1.2-1.2v-1.2c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </span>
                <?php endif; ?>
                <span class="text-white fw-medium">Olá, <?= htmlspecialchars($_SESSION['usuarioNome']) ?></span>
                <a href="<?= $baseUrl ?>pages/perfil.php" class="text-white text-decoration-none fw-medium">Meu Perfil</a>
                <?php if ($_SESSION['usuarioTipo'] === 'locatario'): ?>
                    <a href="<?= $baseUrl ?>pages/minhasReservas.php" class="text-white text-decoration-none fw-medium">Minhas Reservas</a>
                    <a href="<?= $baseUrl ?>pages/listaLobbies.php" class="text-white text-decoration-none fw-medium">Lobbies</a>
                <?php endif; ?>
                <?php if ($_SESSION['usuarioTipo'] === 'locador' || $_SESSION['usuarioTipo'] === 'gerente'): ?>
                    <a href="<?= $baseUrl ?>pages/dashboardLocador.php" class="text-white text-decoration-none fw-medium">Painel</a>
                <?php elseif ($_SESSION['usuarioTipo'] === 'admin'): ?>
                    <a href="<?= $baseUrl ?>pages/dashboardAdmin.php" class="text-white text-decoration-none fw-medium">Painel</a>
                <?php endif; ?>

                <?php if (in_array($tipoSessao, ['locador', 'locatario'], true)): ?>
                <div class="notifWrapper" id="notifWrapper">
                    <button class="notifSino" id="notifSino" aria-label="Notificações">
                        <i class="bi bi-bell-fill"></i>
                        <?php if ($totalNaoLidas > 0): ?>
                            <span class="notifPonto" id="notifPonto"></span>
                        <?php endif; ?>
                    </button>

                    <div class="notifPainel" id="notifPainel">
                        <div class="notifPainelHeader">
                            <span class="fw-bold">Notificações</span>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($totalNaoLidas > 0): ?>
                                    <button class="notifRemoverTodas" id="notifRemoverTodas">
                                        Remover todas
                                    </button>
                                <?php endif; ?>
                                <button class="notifFechar" id="notifFechar" aria-label="Fechar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="notifLista" id="notifLista">
                            <?php if (empty($notificacoes)): ?>
                                <div class="notifVazia" id="notifVazia">Nenhuma notificação nova.</div>
                            <?php else: ?>
                                <?php foreach ($notificacoes as $notif): ?>
                                    <div class="notifItem"
                                         data-notif-id="<?= $notif['id'] ?>"
                                         <?= !empty($notif['link']) ? 'data-link="' . htmlspecialchars($notif['link']) . '"' : '' ?>>
                                        <div class="notifItemConteudo <?= !empty($notif['link']) ? 'notifItemClicavel' : '' ?>">
                                            <span class="notifItemMensagem"><?= htmlspecialchars($notif['mensagem']) ?></span>
                                            <span class="notifItemHora"><?= date('d/m H:i', strtotime($notif['created_at'])) ?></span>
                                        </div>
                                        <button class="notifBtnRemover" aria-label="Remover notificação">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <a href="<?= $baseUrl ?>pages/logout.php" class="btn btn-outline-light rounded-pill px-3">Sair</a>
            <?php else: ?>
                <a href="<?= $baseUrl ?>pages/escolherLogin.php" class="text-white text-decoration-none fw-medium">Entrar</a>
                <a href="<?= $baseUrl ?>pages/escolherCadastro.php" class="btn btn-warning fw-bold text-dark rounded-pill px-4 shadow-sm customYellowBtn">Cadastre-se</a>
            <?php endif; ?>
        </nav>
    </div>
    <script>
        (function () {
            var sino          = document.getElementById('notifSino');
            var painel        = document.getElementById('notifPainel');
            var fechar        = document.getElementById('notifFechar');
            var lista         = document.getElementById('notifLista');
            var removerTodas  = document.getElementById('notifRemoverTodas');

            if (!sino || !painel) { return; }

            var baseUrl = '<?= $baseUrl ?>';

            // Abre e fecha o painel ao clicar no sininho
            sino.addEventListener('click', function (e) {
                e.stopPropagation();
                painel.classList.toggle('notifPainelAberto');
            });

            // Fecha ao clicar no X
            if (fechar) {
                fechar.addEventListener('click', function () {
                    painel.classList.remove('notifPainelAberto');
                });
            }

            // Fecha ao clicar fora
            document.addEventListener('click', function (e) {
                var wrapper = document.getElementById('notifWrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    painel.classList.remove('notifPainelAberto');
                }
            });

            // Função que envia o DELETE para o backend via fetch
            function deletarNotif(acao, notifId) {
                var body = 'acao=' + acao;
                if (notifId) { body += '&notifId=' + notifId; }
                fetch(baseUrl + 'crud/readNotificacoes.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body
                });
            }

            // Função que atualiza o ponto vermelho e o botão "Remover todas"
            // verificando quantos itens ainda restam na lista
            function atualizarPonto() {
                var itens = lista ? lista.querySelectorAll('.notifItem') : [];
                var ponto = document.getElementById('notifPonto');

                if (itens.length === 0) {
                    // Remove o ponto vermelho
                    if (ponto) { ponto.remove(); }
                    // Esconde o botão "Remover todas"
                    if (removerTodas) { removerTodas.style.display = 'none'; }
                    // Mostra a mensagem de vazia
                    if (lista) {
                        lista.innerHTML = '<div class="notifVazia">Nenhuma notificação nova.</div>';
                    }
                }
            }

            // Delegação de eventos na lista — captura cliques em qualquer item filho
            if (lista) {
                lista.addEventListener('click', function (e) {
                    var item = e.target.closest('.notifItem');
                    if (!item) { return; }

                    var notifId  = item.getAttribute('data-notif-id');
                    var link     = item.getAttribute('data-link');
                    var btnRemov = e.target.closest('.notifBtnRemover');
                    var conteudo = e.target.closest('.notifItemConteudo');

                    if (btnRemov) {
                        // Clicou no botão de lixeira — deleta e remove do DOM
                        deletarNotif('deletarUma', notifId);
                        item.remove();
                        atualizarPonto();
                    } else if (conteudo) {
                        // Clicou no conteúdo da notificação — deleta e redireciona
                        deletarNotif('deletarUma', notifId);
                        item.remove();
                        atualizarPonto();
                        if (link) { window.location.href = link; }
                    }
                });
            }

            // Botão "Remover todas"
            if (removerTodas) {
                removerTodas.addEventListener('click', function (e) {
                    e.stopPropagation();
                    deletarNotif('deletarTodas', null);
                    if (lista) {
                        lista.innerHTML = '<div class="notifVazia">Nenhuma notificação nova.</div>';
                    }
                    atualizarPonto();
                });
            }
        })();
    </script>
</header>
