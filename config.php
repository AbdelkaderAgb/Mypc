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

    // 1. Create Users Table with profile fields
    $conn->exec("CREATE TABLE IF NOT EXISTS users1 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','driver','customer') NOT NULL,
        points INT DEFAULT 0,
        status ENUM('active','banned') DEFAULT 'active',
        full_name VARCHAR(100) DEFAULT NULL,
        phone VARCHAR(20) DEFAULT NULL,
        email VARCHAR(100) DEFAULT NULL,
        address VARCHAR(255) DEFAULT NULL,
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
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_driver (driver_id),
        INDEX idx_customer (customer_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Check and add missing columns
    $columns_to_check = [
        'status' => "ALTER TABLE users1 ADD COLUMN status ENUM('active','banned') DEFAULT 'active' AFTER points",
        'full_name' => "ALTER TABLE users1 ADD COLUMN full_name VARCHAR(100) DEFAULT NULL AFTER status",
        'phone' => "ALTER TABLE users1 ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER full_name",
        'email' => "ALTER TABLE users1 ADD COLUMN email VARCHAR(100) DEFAULT NULL AFTER phone",
        'address' => "ALTER TABLE users1 ADD COLUMN address VARCHAR(255) DEFAULT NULL AFTER email"
    ];

    foreach ($columns_to_check as $column => $alter_sql) {
        $check = $conn->query("SHOW COLUMNS FROM users1 LIKE '$column'")->rowCount();
        if ($check == 0) {
            $conn->exec($alter_sql);
        }
    }

    // Check if updated_at column exists in orders1
    $check = $conn->query("SHOW COLUMNS FROM orders1 LIKE 'updated_at'")->rowCount();
    if ($check == 0) {
        $conn->exec("ALTER TABLE orders1 ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
    }

    // 3. Create Default Users with HASHED passwords
    $check_users = $conn->query("SELECT count(*) FROM users1")->fetchColumn();
    if ($check_users == 0) {
        // Hash the default password '123'
        $hashed_password = password_hash('123', PASSWORD_DEFAULT);

        $conn->prepare("INSERT INTO users1 (username, password, role, points, status, full_name) VALUES (?, ?, ?, ?, 'active', ?)")
             ->execute(['admin', $hashed_password, 'admin', 0, 'Administrator']);
        $conn->prepare("INSERT INTO users1 (username, password, role, points, status, full_name) VALUES (?, ?, ?, ?, 'active', ?)")
             ->execute(['driver', $hashed_password, 'driver', 50, 'Demo Driver']);
        $conn->prepare("INSERT INTO users1 (username, password, role, points, status, full_name) VALUES (?, ?, ?, ?, 'active', ?)")
             ->execute(['client', $hashed_password, 'customer', 0, 'Demo Client']);
    }

    // Migrate old plain-text passwords to hashed (one-time migration)
    $users_to_migrate = $conn->query("SELECT id, password FROM users1 WHERE LENGTH(password) < 60");
    while ($user = $users_to_migrate->fetch()) {
        $hashed = password_hash($user['password'], PASSWORD_DEFAULT);
        $conn->prepare("UPDATE users1 SET password = ? WHERE id = ?")->execute([$hashed, $user['id']]);
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
