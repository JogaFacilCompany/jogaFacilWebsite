<?php
// config/database.php – Backend Specialist | camelCase enforced

function getDbConnection(): PDO {
    $dbHost = getenv('DB_HOST') ?: '127.0.0.1';
    $dbName = getenv('DB_NAME') ?: '';
    $dbUser = getenv('DB_USER') ?: '';
    $dbPass = getenv('DB_PASSWORD') ?: '';
    $dbPort = getenv('DB_PORT') ?: '3306';

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
            'debug' => $errorMessage
        ]));
    }
}
