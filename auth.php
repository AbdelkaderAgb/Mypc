<?php
/**
 * Authentication Logic
 * This file handles login, logout, and session management
 */

// ==========================================
// LOGIN HANDLER
// ==========================================
if (isset($_POST['do_login'])) {
    $u = trim($_POST['username']);
    $p = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users1 WHERE username=?");
    $stmt->execute([$u]);
    $user = $stmt->fetch();

    if ($user && $user['password'] === $p) {
        if($user['status'] == 'banned') {
            setFlash('error', $t['err_banned']);
        } else {
            $_SESSION['user'] = $user;
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

    if(!$u || $u['status'] == 'banned') {
        session_destroy();
        header("Location: index.php");
        exit();
    }

    $_SESSION['user'] = $u;
    $uid = $u['id'];
}
?>
