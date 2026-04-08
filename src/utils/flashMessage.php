<?php
// utils/flashMessage.php – camelCase enforced

function setFlash(string $message, string $type = 'info'): void {
    $_SESSION['flashMessage'] = $message;
    $_SESSION['flashType']    = $type;
}

function setFlashFromResponse(array $responseData): void {
    setFlash($responseData['mensagem'], $responseData['sucesso'] ? 'success' : 'danger');
}

function getFlash(): ?array {
    $message = $_SESSION['flashMessage'] ?? null;
    $type    = $_SESSION['flashType']    ?? 'info';
    unset($_SESSION['flashMessage'], $_SESSION['flashType']);

    if ($message === null) { return null; }
    return ['message' => $message, 'type' => $type];
}

function renderFlash(): void {
    $flash = getFlash();
    if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alertMessage"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif;
}
