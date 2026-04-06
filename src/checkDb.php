<?php
require_once __DIR__ . '/config/database.php';
$pdo = getDbConnection();
$stmt = $pdo->query("SELECT * FROM usuarios");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($users, JSON_PRETTY_PRINT);

$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
echo "\nHash test: " . (password_verify('password', $hash) ? 'Match!' : 'No match!');
