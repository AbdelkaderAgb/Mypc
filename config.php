<?php
/**
 * Configuration & Database Connection
 * This file contains database configuration and connection setup
 */

// Force UTF-8 encoding from the start
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

session_start();
date_default_timezone_set('Africa/Nouakchott');

// ==========================================
// DATABASE CONFIGURATION
// ==========================================
$db_config = [
    'host' => 'localhost',
    'name' => 'barqvkxs_barq_delivery',
    'user' => 'barqvkxs_barqvkxs_barq_delivery',
    'pass' => 'barqvkxs_barq_delivery',
    'charset' => 'utf8mb4'
];

$whatsapp_number = "22241312931";
$points_cost_per_order = 20;

// ==========================================
// DATABASE CONNECTION
// ==========================================
try {
    $dsn = "mysql:host={$db_config['host']};dbname={$db_config['name']};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    $conn = new PDO($dsn, $db_config['user'], $db_config['pass'], $options);

    // Additional UTF-8 enforcement
    $conn->exec("SET NAMES utf8mb4");
    $conn->exec("SET CHARACTER SET utf8mb4");
    $conn->exec("SET character_set_connection=utf8mb4");
    $conn->exec("SET character_set_client=utf8mb4");
    $conn->exec("SET character_set_results=utf8mb4");

    // ==========================================
    // DATABASE INSTALLER / REPAIR
    // ==========================================

    // 1. Create Users Table with banned status
    $conn->exec("CREATE TABLE IF NOT EXISTS users1 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','driver','customer') NOT NULL,
        points INT DEFAULT 0,
        status ENUM('active','banned') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 2. Create Orders Table
    $conn->exec("CREATE TABLE IF NOT EXISTS orders1 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(50) NOT NULL,
        details TEXT NOT NULL,
        address VARCHAR(255) NOT NULL,
        status ENUM('pending','accepted','delivered','cancelled') DEFAULT 'pending',
        driver_id INT DEFAULT NULL,
        delivery_code VARCHAR(10) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_driver (driver_id),
        INDEX idx_customer (customer_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Check if status column exists in users1, if not add it
    $check = $conn->query("SHOW COLUMNS FROM users1 LIKE 'status'")->rowCount();
    if($check == 0) {
        $conn->exec("ALTER TABLE users1 ADD COLUMN status ENUM('active','banned') DEFAULT 'active' AFTER points");
    }

    // 3. Create Default Users
    $check_users = $conn->query("SELECT count(*) FROM users1")->fetchColumn();
    if ($check_users == 0) {
        $conn->prepare("INSERT INTO users1 (username, password, role, points, status) VALUES (?, ?, ?, ?, 'active')")
             ->execute(['admin', '123', 'admin', 0]);
        $conn->prepare("INSERT INTO users1 (username, password, role, points, status) VALUES (?, ?, ?, ?, 'active')")
             ->execute(['driver', '123', 'driver', 50]);
        $conn->prepare("INSERT INTO users1 (username, password, role, points, status) VALUES (?, ?, ?, ?, 'active')")
             ->execute(['client', '123', 'customer', 0]);
    }

} catch(PDOException $e) {
    die("
    <div style='font-family:sans-serif; text-align:center; padding:50px; color:#721c24; background:#f8d7da;'>
        <h3>Database Connection Failed</h3>
        <p>Please check your config variables at the top of the file.</p>
        <small>Error: ".$e->getMessage()."</small>
    </div>");
}
?>
