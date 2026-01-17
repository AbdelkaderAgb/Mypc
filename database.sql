-- ============================================
-- Delivery Pro System - Database Schema v2.0
-- Phone + Password Authentication
-- Mauritanian Phone Format: 8 digits starting with 2, 3, or 4
-- ============================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS users1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serial_no VARCHAR(15) UNIQUE COMMENT 'Format: XX-YYMM-00001 (CL/DR/AD)',
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Hashed with password_hash()',
    role ENUM('admin','driver','customer') NOT NULL,
    points INT DEFAULT 0 COMMENT 'Driver balance for accepting orders',
    status ENUM('active','banned') DEFAULT 'active',
    full_name VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL UNIQUE COMMENT 'Mauritanian: 8 digits starting with 2/3/4',
    phone_verified TINYINT(1) DEFAULT 0,
    email VARCHAR(100) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    avatar_url VARCHAR(255) DEFAULT NULL COMMENT 'Profile picture path',
    rating DECIMAL(3,2) DEFAULT 5.00 COMMENT 'Average rating 1-5',
    total_orders INT DEFAULT 0 COMMENT 'Total orders completed',
    total_earnings INT DEFAULT 0 COMMENT 'Total points earned',
    is_online TINYINT(1) DEFAULT 0 COMMENT 'Driver availability status',
    is_verified TINYINT(1) DEFAULT 0 COMMENT 'Admin-verified driver',
    last_lat DECIMAL(10,8) DEFAULT NULL COMMENT 'Last known latitude',
    last_lng DECIMAL(11,8) DEFAULT NULL COMMENT 'Last known longitude',
    location_updated_at TIMESTAMP NULL COMMENT 'When location was last updated',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_role_status (role, status),
    INDEX idx_serial (serial_no),
    INDEX idx_online (is_online, role),
    INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. ORDERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS orders1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT DEFAULT NULL COMMENT 'Link to users1.id',
    customer_name VARCHAR(50) NOT NULL COMMENT 'Customer username/display name',
    details TEXT NOT NULL COMMENT 'Order details/items',
    address VARCHAR(255) NOT NULL COMMENT 'Delivery address',
    client_phone VARCHAR(20) DEFAULT NULL COMMENT 'Customer phone number',
    pickup_lat DECIMAL(10,8) DEFAULT NULL,
    pickup_lng DECIMAL(11,8) DEFAULT NULL,
    dropoff_lat DECIMAL(10,8) DEFAULT NULL,
    dropoff_lng DECIMAL(11,8) DEFAULT NULL,
    distance_km DECIMAL(6,2) DEFAULT NULL,
    status ENUM('pending','accepted','picked_up','delivered','cancelled') DEFAULT 'pending',
    driver_id INT DEFAULT NULL COMMENT 'Assigned driver users1.id',
    delivery_code VARCHAR(10) DEFAULT NULL COMMENT '4-digit PIN for delivery verification',
    points_cost INT DEFAULT 0 COMMENT 'Points deducted from driver',
    accepted_at TIMESTAMP NULL,
    picked_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    cancel_reason TEXT DEFAULT NULL,
    promo_code VARCHAR(50) DEFAULT NULL COMMENT 'Applied promo code',
    discount_amount DECIMAL(10,2) DEFAULT 0 COMMENT 'Discount amount applied',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_status (status),
    INDEX idx_driver (driver_id),
    INDEX idx_client (client_id),
    INDEX idx_customer (customer_name),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. SERIAL NUMBER COUNTERS
-- ============================================
CREATE TABLE IF NOT EXISTS serial_counters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prefix CHAR(2) NOT NULL COMMENT 'CL, DR, AD',
    `year_month` CHAR(4) NOT NULL COMMENT 'YYMM format',
    current_count INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_prefix_month (prefix, `year_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 4. RATINGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    rater_id INT NOT NULL COMMENT 'User who gave the rating',
    ratee_id INT NOT NULL COMMENT 'User who received the rating',
    score TINYINT NOT NULL COMMENT 'Rating 1-5',
    comment TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_ratee (ratee_id),
    INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 5. ORDER TRACKING (Real-time location)
-- ============================================
CREATE TABLE IF NOT EXISTS order_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    driver_id INT NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    accuracy FLOAT DEFAULT NULL COMMENT 'GPS accuracy in meters',
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_order_time (order_id, recorded_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 6. PROMO CODES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS promo_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE COMMENT 'Promo code (e.g., SUMMER2026)',
    discount_type ENUM('percentage', 'fixed') NOT NULL COMMENT 'Percentage or fixed amount',
    discount_value DECIMAL(10,2) NOT NULL COMMENT 'Value (e.g., 20 for 20% or 50 for 50 MRU)',
    max_uses INT DEFAULT NULL COMMENT 'Maximum uses (NULL = unlimited)',
    used_count INT DEFAULT 0 COMMENT 'Number of times used',
    valid_from TIMESTAMP NULL COMMENT 'Start date',
    valid_until TIMESTAMP NULL COMMENT 'End date',
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Active status',
    created_by INT DEFAULT NULL COMMENT 'Admin who created it',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_code (code),
    INDEX idx_active (is_active),
    INDEX idx_valid (valid_from, valid_until)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. PROMO CODE USAGE TRACKING
-- ============================================
CREATE TABLE IF NOT EXISTS promo_code_uses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    promo_code_id INT NOT NULL COMMENT 'Link to promo_codes.id',
    user_id INT NOT NULL COMMENT 'Customer who used the code',
    order_id INT NOT NULL COMMENT 'Order where code was used',
    discount_amount DECIMAL(10,2) NOT NULL COMMENT 'Actual discount applied',
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_promo_code (promo_code_id),
    INDEX idx_user (user_id),
    INDEX idx_order (order_id),
    UNIQUE KEY unique_user_promo (user_id, promo_code_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEFAULT USERS
-- Login with phone number + password
-- Mauritanian phone: 8 digits starting with 2, 3, or 4
-- ============================================
-- Password for all: 123 (hashed)

INSERT INTO users1 (serial_no, username, password, role, points, status, full_name, phone, phone_verified, is_verified) VALUES
('AD-2501-00001', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 0, 'active', 'Administrator', '20000001', 1, 1),
('DR-2501-00001', 'driver', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'driver', 50, 'active', 'Demo Driver', '30000002', 1, 1),
('CL-2501-00001', 'client', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 0, 'active', 'Demo Client', '40000003', 1, 0);

-- ============================================
-- DEMO ACCOUNTS LOGIN INFO:
-- ============================================
-- Admin:    Phone: 20000001  Password: 123
-- Driver:   Phone: 30000002  Password: 123
-- Client:   Phone: 40000003  Password: 123
-- ============================================
-- Phone format: 8 digits starting with 2, 3, or 4
-- ============================================
