<?php
// pages/buscarHorarios.php – camelCase enforced
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../crud/readHorariosBusca.php';
require_once __DIR__ . '/../utils/flashMessage.php';

$dataFiltro = $_GET['data'] ?? date('Y-m-d');
$periodoFiltro = $_GET['periodo'] ?? 'todos';
$modalidadeFiltro = $_GET['modalidade'] ?? 'todos';
$buscaTexto = trim($_GET['busca'] ?? '');
$filtrosAplicados = isset($_GET['aplicar']);

$resultados = [];
if ($filtrosAplicados) {
    $resultados = searchHorariosDisponiveis([
        'data' => $dataFiltro,
        'periodo' => $periodoFiltro,
        'modalidade' => $modalidadeFiltro,
        'busca' => $buscaTexto,
    ]);
}

$pageTitle = 'Buscar Horários – Joga Fácil';
$pageDescription = 'Encontre horários disponíveis por data, período e tipo de quadra.';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include __DIR__ . '/../includes/headTag.php'; ?>
</head>
<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <section class="buscaHorariosHero">
        <div class="container">
            <h1 class="buscaHorariosTitle">Buscar Horários</h1>
            <p class="buscaHorariosSubtitle">Filtre por data, horário e tipo de quadra para encontrar opções disponíveis.</p>
        </div>
    </section>

    <main class="container pb-5">
        <?php renderFlash(); ?>

        <div class="buscaFiltrosCard">
            <form method="GET" action="buscarHorarios.php" id="buscaHorariosForm">
                <input type="hidden" name="aplicar" value="1">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filtroData" class="form-label">Data</label>
                        <input
                            type="date"
                            class="form-control"
                            id="filtroData"
                            name="data"
                            value="<?= htmlspecialchars($dataFiltro) ?>"
                            min="<?= date('Y-m-d') ?>"
                            required
                        >
                    </div>
                    <div class="col-md-3">
                        <label for="filtroPeriodo" class="form-label">Horário</label>
                        <select class="form-select" id="filtroPeriodo" name="periodo">
                            <option value="todos" <?= $periodoFiltro === 'todos' ? 'selected' : '' ?>>Todos os períodos</option>
                            <option value="manha" <?= $periodoFiltro === 'manha' ? 'selected' : '' ?>>Manhã (até 12h)</option>
                            <option value="tarde" <?= $periodoFiltro === 'tarde' ? 'selected' : '' ?>>Tarde (12h – 18h)</option>
                            <option value="noite" <?= $periodoFiltro === 'noite' ? 'selected' : '' ?>>Noite (após 18h)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtroModalidade" class="form-label">Tipo de quadra</label>
                        <select class="form-select" id="filtroModalidade" name="modalidade">
                            <option value="todos" <?= $modalidadeFiltro === 'todos' ? 'selected' : '' ?>>Todas</option>
                            <option value="futebol" <?= $modalidadeFiltro === 'futebol' ? 'selected' : '' ?>>Futebol</option>
                            <option value="volei" <?= $modalidadeFiltro === 'volei' ? 'selected' : '' ?>>Vôlei</option>
                            <option value="tenis" <?= $modalidadeFiltro === 'tenis' ? 'selected' : '' ?>>Tênis</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtroBusca" class="form-label">Arena ou região</label>
                        <input
                            type="text"
                            class="form-control"
                            id="filtroBusca"
                            name="busca"
                            value="<?= htmlspecialchars($buscaTexto) ?>"
                            placeholder="Opcional"
                        >
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                            <i class="bi bi-funnel-fill"></i> Aplicar filtros
                        </button>
                        <a href="buscarHorarios.php" class="btn btn-outline-secondary rounded-pill px-4 ms-2">Limpar</a>
                    </div>
                </div>
            </form>
        </div>

        <?php if (!$filtrosAplicados): ?>
            <div class="buscaInitialHint">
                <i class="bi bi-calendar-check fs-2 d-block mb-2 opacity-50"></i>
                Defina os critérios acima e clique em <strong>Aplicar filtros</strong> para ver os horários disponíveis.
            </div>
        <?php elseif (empty($resultados)): ?>
            <div class="buscaEmptyState">
                <i class="bi bi-calendar-x fs-1 d-block mb-3 opacity-50"></i>
                <p class="mb-2 fw-semibold">Nenhum horário encontrado</p>
                <p class="small mb-0">
                    Não foram encontrados horários com base nos critérios selecionados.
                    Tente outra data, período ou tipo de quadra.
                </p>
            </div>
        <?php else: ?>
            <p class="text-secondary mb-3">
                <strong><?= count($resultados) ?></strong> horário(s) disponível(is)
            </p>
            <?php foreach ($resultados as $slot): ?>
                <?php
                    $dataFmt = date('d/m/Y', strtotime($slot['data']));
                    $precoFmt = number_format((float)$slot['preco'], 2, ',', '.');
                    $modalidades = array_filter(array_map('trim', explode(',', $slot['modalidades'] ?? '')));
                ?>
                <article class="buscaResultadoCard">
                    <div>
                        <div class="buscaResultadoArena"><?= htmlspecialchars($slot['quadra_nome']) ?></div>
                        <div class="buscaResultadoMeta">
                            <i class="bi bi-geo-alt-fill"></i>
                            <?= htmlspecialchars($slot['quadra_endereco']) ?>
                        </div>
                        <?php if (!empty($modalidades)): ?>
                            <div class="mt-1">
                                <?php foreach ($modalidades as $mod): ?>
                                    <span class="buscaSportChip"><?= htmlspecialchars($mod) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="buscaResultadoMeta mt-1">
                            <i class="bi bi-calendar3"></i> <?= $dataFmt ?>
                        </div>
                    </div>
                    <div class="text-md-end">
                        <div class="buscaResultadoHorario">
                            <?= htmlspecialchars($slot['hora_inicio']) ?> – <?= htmlspecialchars($slot['hora_fim']) ?>
                        </div>
                        <div class="buscaResultadoPreco">R$ <?= $precoFmt ?></div>
                    </div>
                    <div>
                        <a
                            href="arenaDetalhe.php?id=<?= (int)$slot['quadra_id'] ?>"
                            class="btn btn-success rounded-pill px-4 fw-semibold"
                        >
                            Reservar
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
