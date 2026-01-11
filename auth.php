<?php
/**
 * Authentication Logic
 * This file handles login, logout, registration, and session management
 * Phone + Password authentication for all users
 */

/**
 * Validate Mauritanian phone number
 * Must be 8 digits starting with 2, 3, or 4
 */
function isValidMauritanianPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) === 8 && preg_match('/^[234]/', $phone);
}

// ==========================================
// REGISTRATION HANDLER (Phone + Password)
// ==========================================
if (isset($_POST['do_register'])) {
    $phone = trim($_POST['reg_phone'] ?? '');
    $password = trim($_POST['reg_password'] ?? '');
    $confirm_password = trim($_POST['reg_confirm_password'] ?? '');
    $full_name = trim($_POST['reg_full_name'] ?? '');

    // Clean phone number (remove non-digits)
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Validation
    if (strlen($full_name) < 2) {
        setFlash('error', $t['err_name_required'] ?? 'Full name is required');
    } elseif (!isValidMauritanianPhone($phone)) {
        setFlash('error', $t['err_phone_invalid'] ?? 'Please enter a valid phone number');
    } elseif (strlen($password) < 4) {
        setFlash('error', $t['err_password_short'] ?? 'Password must be at least 4 characters');
    } elseif ($password !== $confirm_password) {
        setFlash('error', $t['err_password_mismatch'] ?? 'Passwords do not match');
    } else {
        // Check if phone exists
        $stmt = $conn->prepare("SELECT id FROM users1 WHERE phone = ?");
        $stmt->execute([$phone]);

        if ($stmt->rowCount() > 0) {
            setFlash('error', $t['err_phone_exists'] ?? 'This phone number is already registered. Please login.');
        } else {
            try {
                // Generate serial number for new customer
                $serial_no = generateSerialNumber($conn, 'customer');

                // Generate username from phone
                $username = 'user_' . $phone;

                // Check if username exists, add random suffix if needed
                $check_stmt = $conn->prepare("SELECT id FROM users1 WHERE username = ?");
                $check_stmt->execute([$username]);
                if ($check_stmt->rowCount() > 0) {
                    $username = 'user_' . $phone . rand(10, 99);
                }

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Phone is auto-verified when provided (no OTP)
                $stmt = $conn->prepare("INSERT INTO users1 (serial_no, username, password, role, points, status, full_name, phone, phone_verified) VALUES (?, ?, ?, 'customer', 0, 'active', ?, ?, 1)");
                $stmt->execute([$serial_no, $username, $hashed_password, $full_name, $phone]);

                // Auto-login after registration
                $new_user_id = $conn->lastInsertId();
                $stmt = $conn->prepare("SELECT * FROM users1 WHERE id = ?");
                $stmt->execute([$new_user_id]);
                $new_user = $stmt->fetch();

                $_SESSION['user'] = $new_user;
                $_SESSION['last_order_check'] = time();

                setFlash('success', $t['success_register'] ?? 'Registration successful! Welcome!');
                header("Location: index.php");
                exit();

            } catch (PDOException $e) {
                setFlash('error', $t['err_register'] ?? 'Registration failed. Please try again.');
            }
        }
    }
}

// ==========================================
// LOGIN HANDLER (Phone + Password)
// ==========================================
if (isset($_POST['do_login'])) {
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Clean phone number
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Find user by phone
    $stmt = $conn->prepare("SELECT * FROM users1 WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
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
