<?php
/**
 * Authentication Logic
 * This file handles login, logout, registration, and session management
 * Enhanced with phone-only registration
 */

// ==========================================
// REGISTRATION HANDLER (Phone-Only for Clients)
// ==========================================
if (isset($_POST['do_register'])) {
    $phone = trim($_POST['reg_phone'] ?? '');
    $full_name = trim($_POST['reg_full_name'] ?? '');

    // Validate phone number
    $phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-digits

    if (strlen($phone) < 8) {
        setFlash('error', $t['err_phone_invalid'] ?? 'Please enter a valid phone number');
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

                // Generate username from phone (last 8 digits)
                $username = 'user_' . substr($phone, -8);

                // Check if username exists, add random suffix if needed
                $check_stmt = $conn->prepare("SELECT id FROM users1 WHERE username = ?");
                $check_stmt->execute([$username]);
                if ($check_stmt->rowCount() > 0) {
                    $username = 'user_' . substr($phone, -8) . rand(10, 99);
                }

                // Use phone as initial password (user can change later)
                $hashed_password = password_hash($phone, PASSWORD_DEFAULT);

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
                $_SESSION['first_login'] = true; // Flag to show "complete profile" prompt

                setFlash('success', $t['success_register_phone'] ?? 'Welcome! Your account has been created. You can complete your profile in Settings.');
                header("Location: index.php");
                exit();

            } catch (PDOException $e) {
                setFlash('error', $t['err_register'] ?? 'Registration failed. Please try again.');
            }
        }
    }
}

// ==========================================
// LOGIN HANDLER (Phone or Username)
// ==========================================
if (isset($_POST['do_login'])) {
    $identifier = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Try to find user by username OR phone
    $stmt = $conn->prepare("SELECT * FROM users1 WHERE username = ? OR phone = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    // Use password_verify for hashed passwords
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
// COMPLETE PROFILE HANDLER
// ==========================================
if (isset($_POST['complete_profile']) && isset($_SESSION['user'])) {
    $uid = $_SESSION['user']['id'];
    $new_username = trim($_POST['new_username'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_new_password'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');

    $errors = [];

    // Validate new username if provided
    if (!empty($new_username)) {
        if (strlen($new_username) < 3) {
            $errors[] = $t['err_username_short'] ?? 'Username must be at least 3 characters';
        } else {
            // Check if username exists (not current user)
            $stmt = $conn->prepare("SELECT id FROM users1 WHERE username = ? AND id != ?");
            $stmt->execute([$new_username, $uid]);
            if ($stmt->rowCount() > 0) {
                $errors[] = $t['err_username_exists'] ?? 'Username already exists';
            }
        }
    }

    // Validate password if provided
    if (!empty($new_password)) {
        if (strlen($new_password) < 4) {
            $errors[] = $t['err_password_short'] ?? 'Password must be at least 4 characters';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = $t['err_password_mismatch'] ?? 'Passwords do not match';
        }
    }

    if (count($errors) > 0) {
        setFlash('error', implode('<br>', $errors));
    } else {
        try {
            // Build update query
            $updates = [];
            $params = [];

            if (!empty($new_username)) {
                $updates[] = "username = ?";
                $params[] = $new_username;
            }
            if (!empty($new_password)) {
                $updates[] = "password = ?";
                $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            }
            if (!empty($full_name)) {
                $updates[] = "full_name = ?";
                $params[] = $full_name;
            }

            if (count($updates) > 0) {
                $params[] = $uid;
                $sql = "UPDATE users1 SET " . implode(", ", $updates) . " WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute($params);

                // Refresh session
                $stmt = $conn->prepare("SELECT * FROM users1 WHERE id = ?");
                $stmt->execute([$uid]);
                $_SESSION['user'] = $stmt->fetch();

                unset($_SESSION['first_login']);
                setFlash('success', $t['profile_completed'] ?? 'Profile updated successfully!');
            }

            header("Location: index.php");
            exit();

        } catch (PDOException $e) {
            setFlash('error', $t['err_update'] ?? 'Update failed. Please try again.');
        }
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
