<?php
// crud/readNotificacoes.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function getNotificacoesNaoLidas(int $userId): array {
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        "SELECT id, mensagem, link, created_at
         FROM notificacoes
         WHERE usuario_id = ?
         ORDER BY created_at DESC"
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll() ?: [];
}

function deletarNotificacao(int $notifId, int $userId): void {
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        "DELETE FROM notificacoes WHERE id = ? AND usuario_id = ?"
    );
    $stmt->execute([$notifId, $userId]);
}


function deletarTodasNotificacoes(int $userId): void {
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        "DELETE FROM notificacoes WHERE usuario_id = ?"
    );
    $stmt->execute([$userId]);
}

function inserirNotificacao(int $userId, string $mensagem, ?string $link = null): void {
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        "INSERT INTO notificacoes (usuario_id, mensagem, link) VALUES (?, ?, ?)"
    );
    $stmt->execute([$userId, $mensagem, $link]);
}

// Handler POST — chamado pelo JavaScript via fetch para deletar notificações
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    header('Content-Type: application/json');
    header('Cache-Control: no-store');

    if (!isset($_SESSION['usuarioLogado'])) {
        echo json_encode(['sucesso' => false]);
        exit;
    }

    $userId = (int)$_SESSION['usuarioLogado'];
    $acao   = $_POST['acao'] ?? '';

    if ($acao === 'deletarUma') {
        $notifId = (int)($_POST['notifId'] ?? 0);
        if ($notifId > 0) {
            deletarNotificacao($notifId, $userId);
        }
        echo json_encode(['sucesso' => true]);
    } elseif ($acao === 'deletarTodas') {
        deletarTodasNotificacoes($userId);
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);
    }

    exit;
}