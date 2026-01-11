<?php
/**
 * Authentication Logic
 * This file handles login, logout, registration, and session management
 */

// ==========================================
// REGISTRATION HANDLER (New Clients)
// ==========================================
if (isset($_POST['do_register'])) {
    $username = trim($_POST['reg_username']);
    $password = trim($_POST['reg_password']);
    $confirm_password = trim($_POST['reg_confirm_password']);
    $full_name = trim($_POST['reg_full_name']);
    $phone = trim($_POST['reg_phone']);

    // Validation
    if (strlen($username) < 3) {
        setFlash('error', $t['err_username_short'] ?? 'Username must be at least 3 characters');
    } elseif (strlen($password) < 4) {
        setFlash('error', $t['err_password_short'] ?? 'Password must be at least 4 characters');
    } elseif ($password !== $confirm_password) {
        setFlash('error', $t['err_password_mismatch'] ?? 'Passwords do not match');
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users1 WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            setFlash('error', $t['err_username_exists'] ?? 'Username already exists');
        } else {
            // Hash password and create user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                // Generate serial number for new customer
                $serial_no = generateSerialNumber($conn, 'customer');

                // Phone is auto-verified when provided (no OTP)
                $phone_verified = !empty($phone) ? 1 : 0;

                $stmt = $conn->prepare("INSERT INTO users1 (serial_no, username, password, role, points, status, full_name, phone, phone_verified) VALUES (?, ?, ?, 'customer', 0, 'active', ?, ?, ?)");
                $stmt->execute([$serial_no, $username, $hashed_password, $full_name, $phone, $phone_verified]);

                setFlash('success', $t['success_register'] ?? 'Registration successful! You can now login.');
                header("Location: index.php");
                exit();
            } catch (PDOException $e) {
                setFlash('error', $t['err_register'] ?? 'Registration failed. Please try again.');
            }
        }
    }
}

// ==========================================
// LOGIN HANDLER
// ==========================================
if (isset($_POST['do_login'])) {
    $u = trim($_POST['username']);
    $p = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users1 WHERE username=?");
    $stmt->execute([$u]);
    $user = $stmt->fetch();

    // Use password_verify for hashed passwords
    if ($user && password_verify($p, $user['password'])) {
        if ($user['status'] == 'banned') {
            setFlash('error', $t['err_banned']);
        } else {
            $_SESSION['user'] = $user;
            $_SESSION['last_order_check'] = time();
            header("Location: index.php");
            exit();
        }
    } else {
        setFlash('error', $t['err_auth']);
    }
}

// ==========================================
// LOGOUT HANDLER
// ==========================================
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// ==========================================
// SESSION VALIDATION
// ==========================================
if (isset($_SESSION['user'])) {
    $stmt = $conn->prepare("SELECT * FROM users1 WHERE id=?");
    $stmt->execute([$_SESSION['user']['id']]);
    $u = $stmt->fetch();

    if (!$u || $u['status'] == 'banned') {
        session_destroy();
        header("Location: index.php");
        exit();
    }

    $_SESSION['user'] = $u;
    $uid = $u['id'];
}
?>
