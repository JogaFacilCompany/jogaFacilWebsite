<?php
require_once __DIR__ . '/crud/readUsuarios.php';

$email = 'locador@email.com';
$senha = 'password';

$user = findUsuarioByEmailAndSenha($email, $senha);
if ($user) {
    echo "Found user! Tipo: " . $user['tipo'] . "\n";
    if ($user['tipo'] === 'locador') {
        echo "SUCCESS! Login works.";
    } else {
        echo "Fail: wrong tipo.";
    }
} else {
    echo "Fail: user not found or wrong password.";
}
