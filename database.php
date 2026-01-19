<?php
// Database connection using PDO
// Update these credentials to match your MySQL / MariaDB setup
$host = 'localhost';
$db   = 'cyntzsrb_cyn';
$user = 'cyntzsrb_cyn';
$pass = 'Qj!d$}Zh,-~m';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}
?>
