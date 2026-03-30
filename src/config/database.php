<?php
// config/database.php – Backend Specialist | camelCase enforced

function loadEnv(string $envPath): void {
    if (!file_exists($envPath)) return;
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) continue;
        $key   = trim($parts[0]);
        $value = trim($parts[1]);
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

loadEnv(__DIR__ . '/../../.env');

function getDbConnection(): PDO {
    $dbHost = getenv('DB_HOST') ?: '127.0.0.1';
    $dbName = getenv('DB_NAME') ?: '';
    $dbUser = getenv('DB_USER') ?: '';
    $dbPass = getenv('DB_PASS') ?: '';
    $dbPort = getenv('DB_PORT') ?: '3307';

    $dataSrcName = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";

    try {
        $pdoOptions = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dataSrcName, $dbUser, $dbPass, $pdoOptions);
    } catch (PDOException $connectionError) {
        $errorMessage = $connectionError->getMessage();
        error_log('DB connection error: ' . $errorMessage);
        http_response_code(500);
        die(json_encode([
            'sucesso' => false,
            'mensagem' => 'Erro interno ao conectar ao banco de dados.',
        ]));
    }
}
