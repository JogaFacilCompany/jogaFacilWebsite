<?php
// crud/readUsuarios.php – camelCase enforced
require_once __DIR__ . '/../config/database.php';

function readAllUsuarios(): array {
    $pdo        = getDbConnection();
    $selectStmt = $pdo->query("SELECT id, nome, email, tipo, cpf, foto_perfil, status, inativo_motivo, created_at FROM usuarios ORDER BY created_at DESC");
    return $selectStmt->fetchAll();
}

function readUsuarioById(int $userId): ?array {
    $pdo        = getDbConnection();
    $selectStmt = $pdo->prepare("SELECT id, nome, email, tipo, cpf, foto_perfil, status, inativo_motivo, created_at FROM usuarios WHERE id = ?");
    $selectStmt->execute([$userId]);
    $foundUser = $selectStmt->fetch();
    return $foundUser ?: null;
}

function findUsuarioByEmailAndSenha(string $inputEmail, string $inputSenha): ?array {
    $pdo        = getDbConnection();
    $selectStmt = $pdo->prepare("SELECT id, nome, email, tipo, cpf, foto_perfil, status, inativo_motivo, created_at, senha FROM usuarios WHERE email = ?");
    $selectStmt->execute([$inputEmail]);
    $foundUser = $selectStmt->fetch();

    if ($foundUser && password_verify($inputSenha, $foundUser['senha'])) {
        unset($foundUser['senha']);
        return $foundUser;
    }

    return null;
}

function findUsuarioByEmail(string $inputEmail): ?array {
    $pdo        = getDbConnection();
    $selectStmt = $pdo->prepare("SELECT id, nome, email, tipo, cpf, status, inativo_motivo FROM usuarios WHERE email = ? LIMIT 1");
    $selectStmt->execute([$inputEmail]);
    $foundUser  = $selectStmt->fetch();
    return $foundUser ?: null;
}
