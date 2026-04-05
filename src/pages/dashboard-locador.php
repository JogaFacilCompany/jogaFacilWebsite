<?php
// pages/dashboard-locador.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuarioLogado']) || $_SESSION['usuarioTipo'] !== 'locador') {
    header('Location: ../pages/login-locador.php');
    exit;
}

require_once __DIR__ . '/../crud/readQuadras.php';
require_once __DIR__ . '/../config/csrf.php';

$locadorId = $_SESSION['usuarioLogado'];
$arenaId   = isset($_GET['arena_id']) ? (int)$_GET['arena_id'] : null;

// Busca os dados dependendo da visão
if ($arenaId) {
    $quadra = getQuadraByIdAndLocador($arenaId, $locadorId);
    // Se não encontrou ou não pertence ao locador, redireciona para a lista
    if (!$quadra) {
        header('Location: dashboard-locador.php');
        exit;
    }
} else {
    $quadras = getQuadrasByLocador($locadorId);
}

$flashMessage = $_SESSION['flashMessage'] ?? null;
$flashType    = $_SESSION['flashType']    ?? 'info';
unset($_SESSION['flashMessage'], $_SESSION['flashType']);

// Função para gerar horários dinamicamente baseados no funcionamento (ex: 08:00 - 22:00)
function gerarHorariosRelativos($funcionamento) {
    $horarios = ['Manhã' => [], 'Tarde' => [], 'Noite' => []];
    
    // Tenta extrair os horários (esperado: HH:MM - HH:MM)
    if (!preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $funcionamento, $matches)) {
        $inicio = 8;
        $fim = 22;
    } else {
        $inicio = (int)explode(':', $matches[1])[0];
        $fim = (int)explode(':', $matches[2])[0];
    }

    for ($h = $inicio; $h < $fim; $h++) {
        $horaStr = sprintf('%02d:00', $h);
        if ($h < 12) {
            $horarios['Manhã'][] = $horaStr;
        } elseif ($h < 18) {
            $horarios['Tarde'][] = $horaStr;
        } else {
            $horarios['Noite'][] = $horaStr;
        }
    }
    return $horarios;
}

$horariosSelecionaveis = $arenaId ? gerarHorariosRelativos($quadra['funcionamento']) : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Locador – Joga Fácil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../assets/css/customStyles.css" rel="stylesheet">
    <style>
        .arenaSelectionCard {
            transition: transform 0.3s ease, border-color 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent !important;
            background-color: var(--bgCard) !important;
        }
        .arenaSelectionCard:hover {
            transform: translateY(-8px);
            border-color: var(--verdeBandeira) !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .addArenaCard {
            border: 2px dashed var(--bgCardBorder) !important;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
            color: var(--textSecondary);
            transition: all 0.3s ease;
        }
        .addArenaCard:hover {
            border-color: var(--amareloOuro) !important;
            color: var(--amareloOuro);
            background: rgba(255, 223, 0, 0.05) !important;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100" style="background-color: var(--bgMain);">
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="flex-grow-1">

    <?php if (!$arenaId): ?>
        <!-- VISÃO DE LISTAGEM (MULTIPLE ARENAS) -->
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-white">Minhas Quadras</h2>
                <button class="btn customYellowBtn px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalNovoArena">
                    <i class="bi bi-plus-circle me-2"></i> Adicionar Nova Quadra
                </button>
            </div>

            <?php if ($flashMessage): ?>
                <div class="alert alert-<?= $flashType ?> alertMessage"><?= htmlspecialchars($flashMessage) ?></div>
            <?php endif; ?>

            <div class="row g-4 mt-2">
                <?php if (empty($quadras)): ?>
                    <div class="col-12 text-center py-5">
                        <div class="p-5 rounded-4 shadow-sm" style="background: rgba(255,255,255,0.03); border: 1px dashed #374151;">
                            <i class="bi bi-calendar-x display-1"></i>
                            <h4 class="mt-3 text-white">Você ainda não tem quadras.</h4>
                            <p class="text-secondary">Cadastre sua primeira arena para começar a disponibilizar horários!</p>
                            <button class="btn btn-success fw-bold px-5 py-2 mt-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalNovoArena">Cadastrar Primeira Arena</button>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($quadras as $q): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card arenaSelectionCard h-100 shadow-sm overflow-hidden" onclick="window.location.href='?arena_id=<?= $q['id'] ?>'">
                            <div style="height: 160px; overflow: hidden;">
                                <img src="<?= $q['imagem'] ?: 'https://images.unsplash.com/photo-1543351611-58f69d7c1781?auto=format&fit=crop&q=80&w=1000' ?>" class="card-img-top w-100 h-100 object-fit-cover">
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <h5 class="card-title fw-bold text-white mb-2"><?= htmlspecialchars($q['nome']) ?></h5>
                                <p class="card-text text-secondary small mb-3"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($q['endereco']) ?></p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <?php
                                        $statusClass = 'bg-warning';
                                        $statusLabel = 'Aguardando';
                                        if ($q['status'] === 'ativo') { $statusClass = 'bg-success'; $statusLabel = 'Ativo'; }
                                        if ($q['status'] === 'rejeitado') { $statusClass = 'bg-danger'; $statusLabel = 'Rejeitado'; }
                                    ?>
                                    <span class="badge <?= $statusClass ?> opacity-75 shadow-sm"><?= $statusLabel ?></span>
                                    <span class="text-warning fw-bold small">Gerenciar <i class="bi bi-arrow-right"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Card de atalho para adicionar nova -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card addArenaCard h-100" data-bs-toggle="modal" data-bs-target="#modalNovoArena" style="cursor: pointer;">
                            <div class="text-center">
                                <i class="bi bi-plus-lg display-4 d-block mb-2"></i>
                                <span class="fw-bold">Nova Arena</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- VISÃO DE DETALHES (GERENCIAMENTO DA ARENA SELECIONADA) -->
        <!-- Hero Banner (Capa da Quadra) -->
        <section class="arenaDetailHero">
            <img src="<?= htmlspecialchars($quadra['imagem'] ?: "https://images.unsplash.com/photo-1518605368461-1ee7e1635338?q=80&w=2000") ?>" alt="Capa da Arena" class="arenaDetailHeroImg">
            <div class="arenaDetailHeroOverlay"></div>
            <div class="container position-relative h-100">
                <div class="arenaDetailHeroMeta">
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="dashboard-locador.php" class="text-warning text-decoration-none"><i class="bi bi-arrow-left"></i> Meus Painéis</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Gerenciando</li>
                        </ol>
                    </nav>
                    <h1 class="arenaDetailHeroName"><?= htmlspecialchars($quadra['nome']) ?></h1>
                    <p class="arenaDetailHeroAddr">
                        <i class="bi bi-geo-alt-fill text-warning"></i> <?= htmlspecialchars($quadra['endereco']) ?>
                        <button class="btn btn-sm btn-outline-light ms-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalEditarArena" style="font-size: 0.75rem;"><i class="bi bi-pencil"></i> Editar Perfil</button>
                    </p>
                </div>
                <div class="arenaDetailRating shadow-sm">
                    <i class="bi bi-star-fill"></i> 4.8
                </div>
            </div>
        </section>

        <!-- Conteúdo Principal -->
        <section class="container py-5 contentSection">
            
            <?php if ($flashMessage): ?>
                <div class="alert alert-<?= $flashType ?> alertMessage"><?= htmlspecialchars($flashMessage) ?></div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Coluna Esquerda: Informações -->
                <div class="col-lg-7">
                    
                    <!-- Card Sobre -->
                    <div class="detailInfoCard shadow-sm">
                        <h5 class="detailInfoCardTitle">
                            <i class="bi bi-info-circle-fill cardTitleIcon"></i> Sobre a Arena
                        </h5>
                        <div class="detailInfoRow">
                            <strong>Modalidades:</strong> <?= htmlspecialchars($quadra['modalidades'] ?? 'Não informado') ?>
                        </div>
                        <div class="detailInfoRow">
                            <strong>Horário de Funcionamento:</strong> <?= htmlspecialchars($quadra['funcionamento'] ?? 'Não informado') ?>
                        </div>
                        <?php if (!empty($quadra['descricao'])): ?>
                            <div class="detailInfoRow mt-3 text-secondary" style="font-size: 0.88rem; font-style: italic;">
                                "<?= nl2br(htmlspecialchars($quadra['descricao'])) ?>"
                            </div>
                        <?php endif; ?>
                        <div class="detailInfoRow mt-3">
                            <strong>Política de Cancelamento:</strong>
                        </div>
                        <div class="detailCancelText">
                            Cancelamento grátis até <?= htmlspecialchars($quadra['cancelamento_horas']) ?>h antes.
                        </div>
                    </div>

                    <!-- Card Facilidades -->
                    <div class="detailInfoCard shadow-sm">
                        <h5 class="detailInfoCardTitle">
                            <i class="bi bi-check-circle-fill cardTitleIcon text-success"></i> Facilidades
                        </h5>
                        <ul class="facilidadesList mt-3">
                            <?php 
                            $facilidades = json_decode($quadra['facilidades'], true) ?: [];
                            if (empty($facilidades)): ?>
                                <li class="facilidadesItem ">Nenhuma facilidade cadastrada</li>
                            <?php else:
                                foreach ($facilidades as $fac): ?>
                                    <li class="facilidadesItem"><?= htmlspecialchars($fac) ?></li>
                            <?php endforeach; endif; ?>
                        </ul>
                    </div>

                    <!-- Card Contato -->
                    <div class="detailInfoCard shadow-sm">
                        <h5 class="detailInfoCardTitle">
                            <i class="bi bi-telephone-fill cardTitleIcon"></i> Contato
                        </h5>
                        <div class="detailPhone">
                            <?= htmlspecialchars($quadra['telefone'] ?? 'Não informado') ?>
                        </div>
                    </div>

                </div>

                <!-- Coluna Direita: Agendamento -->
                <div class="col-lg-5">
                    <div class="bookingWidget shadow">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="bookingWidgetTitle m-0">Gerenciar Horários</h4>
                            <span class="badge bg-success rounded-pill px-3">Hoje</span>
                        </div>
                        <!-- ... (Restante do widget de horários igual ao anterior) ... -->
                        <div class="periodTabsWrapper shadow-sm">
                            <button class="periodTab active" onclick="selecionarAba('Manhã')">Manhã</button>
                            <button class="periodTab" onclick="selecionarAba('Tarde')">Tarde</button>
                            <button class="periodTab" onclick="selecionarAba('Noite')">Noite</button>
                        </div>
                        <div class="slotsGrid" id="slotsContainer"></div>
                        <p class=" small mt-2 mb-3">Clique em um horário para bloqueá-lo ou liberá-lo.</p>
                        
                        <button class="bookingConfirmBtn" id="btnSalvarEstado">Selecione um horário</button>

                        <!-- Botão de Excluir Arena (Discreto abaixo do widget) -->
                        <div class="mt-4 pt-3 border-top border-secondary opacity-75">
                            <button class="btn btn-sm btn-outline-danger w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalExcluirArena">
                                <i class="bi bi-trash3"></i> Excluir Arena Permanentemente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal Editar (específico desta arena) -->
        <div class="modal fade" id="modalEditarArena" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="../crud/updateQuadra.php" method="POST" class="modal-content text-dark">
                    <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="id" value="<?= $quadra['id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Editar Informações da Arena</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Nome da Arena *</label>
                                <input type="text" class="form-control formInput" name="nome" value="<?= htmlspecialchars($quadra['nome']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Telefone *</label>
                                <input type="text" class="form-control formInput" name="telefone" value="<?= htmlspecialchars($quadra['telefone']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">CNPJ *</label>
                                <input type="text" class="form-control formInput cnpj-mask" name="cnpj" value="<?= htmlspecialchars($quadra['cnpj'] ?? '') ?>" placeholder="00.000.000/0000-00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Horário de Funcionamento *</label>
                                <input type="text" class="form-control formInput" name="funcionamento" value="<?= htmlspecialchars($quadra['funcionamento'] ?? '08:00 - 22:00') ?>" placeholder="Ex: 08:00 - 22:00">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Endereço Completo</label>
                                <input type="text" class="form-control formInput" name="endereco" value="<?= htmlspecialchars($quadra['endereco']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium">Descrição</label>
                                <textarea class="form-control formInput" name="descricao" rows="3"><?= htmlspecialchars($quadra['descricao'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-medium mb-2 d-block">Facilidades</label>
                                <div class="d-flex flex-wrap gap-3">
                                    <?php 
                                    $todasFac = ["Cantina", "Vestiários", "Aluguel de Bola", "Bebedouro", "Wi-Fi"];
                                    foreach ($todasFac as $fac): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="facilidades[]" value="<?= $fac ?>" id="facDet_<?= $fac ?>" <?= in_array($fac, $facilidades) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="facDet_<?= $fac ?>"><?= $fac ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success fw-bold px-4">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Confirmar Exclusão -->
        <div class="modal fade" id="modalExcluirArena" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="../crud/deleteQuadra.php" method="POST" class="modal-content text-dark">
                    <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="id" value="<?= $quadra['id'] ?>">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold text-danger">Confirmar Exclusão</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-4 text-center">
                        <i class="bi bi-exclamation-triangle text-danger display-4 mb-3 d-block"></i>
                        <p class="mb-0">Tem certeza que deseja excluir a arena <strong><?= htmlspecialchars($quadra['nome']) ?></strong>?</p>
                        <p class=" small">Esta ação é irreversível e apagará todos os dados vinculados.</p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sim, Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal Novo Arena (sempre acessível na lista) -->
    <div class="modal fade" id="modalNovoArena" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="../crud/createQuadra.php" method="POST" class="modal-content text-dark">
                <input type="hidden" name="csrfToken" value="<?= generateCsrfToken() ?>">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Cadastrar Nova Arena</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nome da Arena</label>
                            <input type="text" class="form-control formInput" name="nome" placeholder="Ex: Arena Joga Fácil 2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Horário de Funcionamento</label>
                            <input type="text" class="form-control formInput" name="funcionamento" placeholder="Ex: 08:00 - 22:00" value="08:00 - 22:00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Telefone *</label>
                            <input type="text" class="form-control formInput" name="telefone" placeholder="(00) 00000-0000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">CNPJ *</label>
                            <input type="text" class="form-control formInput cnpj-mask" name="cnpj" placeholder="00.000.000/0000-00" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Endereço Completo *</label>
                            <input type="text" class="form-control formInput" name="endereco" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Modalidades</label>
                            <input type="text" class="form-control formInput" name="modalidades" value="Futebol">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-medium">Descrição</label>
                            <textarea class="form-control formInput" name="descricao" rows="2" placeholder="Conte um pouco sobre esta nova unidade..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">Cadastrar Arena</button>
                </div>
            </form>
        </div>
    </div>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($arenaId): ?>
<script>
    const horarios = <?= json_encode($horariosSelecionaveis) ?>;
    let slotSelecionado = null;
    let abaAtiva = 'Manhã';

    function renderSlots() {
        const container = document.getElementById('slotsContainer');
        if(!container) return;
        container.innerHTML = '';
        horarios[abaAtiva].forEach(hora => {
            const btn = document.createElement('button');
            btn.className = `slotBtn ${slotSelecionado === hora ? 'selected' : ''}`;
            btn.innerHTML = `${hora}<div class="slotPrice">R$ 150</div>`;
            btn.onclick = () => { slotSelecionado = hora; renderSlots(); atualizarBotao(); };
            container.appendChild(btn);
        });
    }

    function selecionarAba(aba) {
        abaAtiva = aba;
        document.querySelectorAll('.periodTab').forEach(el => el.classList.toggle('active', el.textContent === aba));
        slotSelecionado = null;
        renderSlots();
        atualizarBotao();
    }

    function atualizarBotao() {
        const btn = document.getElementById('btnSalvarEstado');
        if(!btn) return;
        btn.textContent = slotSelecionado ? `Bloquear / Reservar as ${slotSelecionado}` : 'Selecione um horário';
        btn.classList.toggle('enabled', !!slotSelecionado);
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderSlots();
        
        // Máscara Simples para CNPJ
        document.querySelectorAll('.cnpj-mask').forEach(input => {
            input.addEventListener('input', (e) => {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,3})(\d{0,3})(\d{0,4})(\d{0,2})/);
                e.target.value = !x[2] ? x[1] : x[1] + '.' + x[2] + '.' + x[3] + '/' + x[4] + (x[5] ? '-' + x[5] : '');
            });
        });
    });
</script>
<?php endif; ?>
<script src="../assets/js/appLogic.js"></script>
</body>
</html>
