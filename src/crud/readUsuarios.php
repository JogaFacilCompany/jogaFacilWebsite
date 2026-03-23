<?php
// crud/readUsuarios.php – Backend Specialist | camelCase enforced
require_once __DIR__ . '/../config/database.php';

function readAllUsuarios(): array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->query("SELECT id, nome, email, tipo, cpf, criadoEm FROM usuarios ORDER BY criadoEm DESC");
    return $selectStmt->fetchAll();
}

function readUsuarioById(int $userId): ?array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->prepare("SELECT id, nome, email, tipo, cpf, criadoEm FROM usuarios WHERE id = ?");
    $selectStmt->execute([$userId]);
    $foundUser = $selectStmt->fetch();
    return $foundUser ?: null;
}

function findUsuarioByEmailAndSenha(string $inputEmail, string $inputSenha): ?array {
    $pdo = getDbConnection();
    $selectStmt = $pdo->prepare("SELECT id, nome, email, tipo, cpf, criadoEm, senha FROM usuarios WHERE email = ?");
    $selectStmt->execute([$inputEmail]);
    $foundUser = $selectStmt->fetch();

    if ($foundUser && password_verify($inputSenha, $foundUser['senha'])) {
        unset($foundUser['senha']);
        return $foundUser;
    }

    return null;
}
