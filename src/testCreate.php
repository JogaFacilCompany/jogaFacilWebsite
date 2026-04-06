<?php
require_once __DIR__ . '/crud/createUsuario.php';

$testData = [
    'nome' => 'Test Locador',
    'email' => 'testloc@email.com',
    'senha' => 'password123',
    'tipo' => 'locador'
];

$result = createUsuario($testData);
echo json_encode($result);
