<?php
require_once __DIR__ . '/config/database.php';
echo "Tentando conectar...\n";
echo "HOST: " . getenv('DB_HOST') . "\n";
echo "PASS: " . getenv('DB_PASS') . "\n";
$pdo = getDbConnection();
echo "Conectado!\n";
