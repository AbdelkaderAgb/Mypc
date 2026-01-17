<?php
/**
 * Main Entry Point - Delivery Pro System
 * This file includes all components and renders the views
 */

// Include configuration and database connection
require_once 'config.php';

// Include helper functions and translations
require_once 'functions.php';

// Include authentication logic
require_once 'auth.php';

// Include action handlers
require_once 'actions.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?php echo $t['app_name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.<?php echo $lang=='ar'?'rtl.':''; ?>min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>


<!-- Notification Toast Container -->
<div id="notificationContainer" class="notification-toast <?php echo $dir == 'rtl' ? 'rtl' : ''; ?>"></div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<!-- Order Notification Bubbles Container (for drivers) -->
<?php if(isset($_SESSION['user']) && $role === 'driver'): ?>
<div id="orderBubbleContainer" class="order-notification-container"></div>
<?php endif; ?>

<?php if (!isset($_SESSION['user'])): ?>
    <!-- ================= LOGIN/REGISTER SCREEN ================= -->
    <div class="login-wrapper">
        <div class="login-card">

            <!-- Logo Area -->
            <div class="login-logo-area">
                <img src="logo.png" alt="<?php echo $t['app_name']; ?>" class="login-logo-img" style="height: 80px; width: auto; margin-bottom: 15px;">
                <h1 class="login-app-title"><?php echo $t['app_name']; ?></h1>
                <p class="login-app-subtitle"><?php echo $t['app_desc']; ?></p>
            </div>

            <!-- Language Switcher -->
            <div class="btn-group btn-group-sm lang-switcher mb-4" role="group">
                <a href="?lang=ar" class="btn btn-outline-secondary <?php echo $lang=='ar'?'active':''; ?>">العربية</a>
                <a href="?lang=fr" class="btn btn-outline-secondary <?php echo $lang=='fr'?'active':''; ?>">Français</a>
            </div>

            <?php echo getFlash(); ?>

            <!-- Auth Toggle -->
            <div class="auth-toggle">
                <button type="button" id="loginToggle" class="active" onclick="showAuthForm('login')">
                    <i class="fas fa-sign-in-alt me-2"></i><?php echo $t['login_title']; ?>
                </button>
                <button type="button" id="registerToggle" onclick="showAuthForm('register')">
                    <i class="fas fa-user-plus me-2"></i><?php echo $t['register_title']; ?>
                </button>
            </div>

            <!-- Login Form -->
            <form method="POST" id="loginForm" class="auth-form active" onsubmit="this.querySelector('button').classList.add('loading')">
                <div class="login-input-group">
                    <i class="fa-solid fa-mobile-screen login-input-icon"></i>
                    <input type="tel" name="phone" class="login-form-control" placeholder="<?php echo $t['phone_example'] ?? '2XXXXXXX'; ?>" required inputmode="tel" maxlength="8" pattern="[234][0-9]{7}" style="direction: ltr; text-align: <?php echo ($lang=='ar')?'right':'left'; ?>;">
                </div>

                <div class="login-input-group">
                    <i class="fa-solid fa-lock login-input-icon"></i>
                    <input type="password" name="password" class="login-form-control" placeholder="<?php echo $t['pass_ph']; ?>" required autocomplete="current-password">
                </div>

                <button name="do_login" class="btn-login-main">
                    <?php echo $t['btn_login']; ?> <i class="fas fa-arrow-<?php echo ($lang=='ar')?'left':'right'; ?>" style="margin-<?php echo ($lang=='ar')?'right':'left'; ?>: 8px;"></i>
                </button>
            </form>

            <!-- Register Form -->
            <form method="POST" id="registerForm" class="auth-form" onsubmit="this.querySelector('button').classList.add('loading')">
                <div class="login-input-group">
                    <i class="fa-solid fa-user login-input-icon"></i>
                    <input type="text" name="reg_full_name" class="login-form-control" placeholder="<?php echo $t['full_name_ph']; ?>" required minlength="2">
                </div>

                <div class="login-input-group">
                    <i class="fa-solid fa-mobile-screen login-input-icon"></i>
                    <input type="tel" name="reg_phone" class="login-form-control" placeholder="<?php echo $t['phone_example'] ?? '2XXXXXXX'; ?>" required inputmode="tel" maxlength="8" minlength="8" pattern="[234][0-9]{7}" style="direction: ltr; text-align: <?php echo ($lang=='ar')?'right':'left'; ?>;">
                </div>

                <div class="login-input-group">
                    <i class="fa-solid fa-lock login-input-icon"></i>
                    <input type="password" name="reg_password" class="login-form-control" placeholder="<?php echo $t['pass_ph']; ?>" required minlength="4" autocomplete="new-password">
                </div>

                <div class="login-input-group">
                    <i class="fa-solid fa-lock login-input-icon"></i>
                    <input type="password" name="reg_confirm_password" class="login-form-control" placeholder="<?php echo $t['confirm_pass_ph']; ?>" required autocomplete="new-password">
                </div>

                <button name="do_register" class="btn-register-main">
                    <?php echo $t['btn_register']; ?> <i class="fas fa-user-plus" style="margin-<?php echo ($lang=='ar')?'right':'left'; ?>: 8px;"></i>
                </button>
            </form>

            <!-- Divider -->
            <div class="login-divider"><?php echo $t['need_help'] ?? 'Need help?'; ?></div>

            <!-- Contact Hub -->
            <div class="login-contact-hub">
                <a href="tel:+<?php echo $whatsapp_number; ?>" class="login-social-btn phone" title="<?php echo $t['call_us'] ?? 'Call Us'; ?>">
                    <i class="fa-solid fa-phone"></i>
                </a>

                <a href="mailto:<?php echo $help_email; ?>" class="login-social-btn email" title="<?php echo $help_email; ?>">
                    <i class="fa-regular fa-envelope"></i>
                </a>
            </div>

            <!-- Footer Text -->
            <div class="login-footer-text" id="loginFooterText">
                <?php echo $t['no_account'] ?? "Don't have an account?"; ?> <a href="#" onclick="showAuthForm('register'); return false;"><?php echo $t['register_title']; ?></a>
            </div>
            <div class="login-footer-text" id="registerFooterText" style="display: none;">
                <?php echo $t['have_account'] ?? 'Already have an account?'; ?> <a href="#" onclick="showAuthForm('login'); return false;"><?php echo $t['login_title']; ?></a>
            </div>

        </div>
    </div>

<?php else:
    $u = $_SESSION['user'];
    $role = $u['role'];
    $uid = $u['id'];
?>
    <!-- ================= DASHBOARD ================= -->
    <nav class="navbar app-navbar sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <img src="logo.png" alt="<?php echo $t['app_name']; ?>" style="height: 40px; width: auto;" onerror="this.style.display='none'">
                <span class="text-primary d-none d-sm-inline"><?php echo $t['app_name']; ?></span>
            </a>
            <div class="d-flex align-items-center gap-2 gap-md-3">
                <?php if($role == 'driver'): ?>
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill" id="pointsBadge">
                    <i class="fas fa-coins"></i> <span id="currentPoints"><?php echo $u['points'] ?? 0; ?></span>
                </span>
                <?php endif; ?>
                <a href="?settings=1" class="d-flex align-items-center gap-2 text-decoration-none" title="<?php echo $t['settings']; ?>">
                    <div class="profile-avatar avatar-sm avatar-<?php echo $role; ?>">
                        <?php
                        $navAvatarUrl = getAvatarUrl($u);
                        if ($navAvatarUrl): ?>
                            <img src="<?php echo e($navAvatarUrl); ?>" alt="">
                        <?php else: ?>
                            <i class="fas fa-<?php echo $role == 'admin' ? 'crown' : ($role == 'driver' ? 'truck' : 'user'); ?>"></i>
                        <?php endif; ?>
                    </div>
                    <div class="d-none d-md-block text-end lh-1">
                        <span class="d-block fw-bold small text-dark"><?php echo e($u['full_name'] ?: $u['username']); ?></span>
                        <span class="role-badge role-badge-<?php echo $role; ?>" style="padding: 2px 8px; font-size: 0.6rem;">
                            <?php echo $t[$role]; ?>
                        </span>
                    </div>
                </a>
                <a href="?logout=1" class="btn btn-light text-danger settings-btn rounded-circle shadow-sm" title="<?php echo $t['logout']; ?>">
                    <i class="fas fa-power-off"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <?php echo getFlash(); ?>

        <?php if(isset($_GET['settings'])): ?>
            <!-- ================= SETTINGS/PROFILE PAGE ================= -->
            <div class="row justify-content-center animate-fadeInUp">
                <div class="col-lg-6 col-xl-5">
                    <div class="card content-card">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-user-cog text-primary me-2"></i><?php echo $t['profile']; ?></h5>
                            <a href="index.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-arrow-<?php echo $dir=='rtl'?'right':'left'; ?> me-1"></i> <?php echo $t['dashboard']; ?>
                            </a>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" enctype="multipart/form-data" onsubmit="this.querySelector('button[type=submit]').classList.add('loading')">
                                <!-- Profile Header -->
                                <div class="text-center mb-4">
                                    <label for="avatarInput" class="d-inline-block" style="cursor: pointer;">
                                        <div class="avatar-with-badge">
                                            <div class="profile-avatar avatar-lg avatar-<?php echo $role; ?> mb-3">
                                                <?php
                                                $avatarUrl = getAvatarUrl($u);
                                                if ($avatarUrl): ?>
                                                    <img src="<?php echo e($avatarUrl); ?>" alt="Avatar">
                                                <?php else: ?>
                                                    <i class="fas fa-<?php echo $role == 'admin' ? 'crown' : ($role == 'driver' ? 'truck' : 'user'); ?>"></i>
                                                <?php endif; ?>
                                                <div class="profile-avatar-edit">
                                                    <i class="fas fa-camera"></i> <?php echo $t['change_photo'] ?? 'Change'; ?>
                                                </div>
                                            </div>
                                            <?php if($role == 'driver' && !empty($u['is_verified'])): ?>
                                                <div class="verified-badge" title="<?php echo $t['driver_verified'] ?? 'Verified Driver'; ?>">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            <?php elseif($role == 'admin'): ?>
                                                <div class="verified-badge" style="background: linear-gradient(135deg, #dc2626, #f97316);" title="Admin">
                                                    <i class="fas fa-star"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                    <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="this.form.submit()">

                                    <h5 class="fw-bold mb-1"><?php echo e($u['full_name'] ?: $u['username']); ?></h5>
                                    <span class="role-badge role-badge-<?php echo $role; ?>">
                                        <i class="fas fa-<?php echo $role == 'admin' ? 'crown' : ($role == 'driver' ? 'truck' : 'user'); ?>"></i>
                                        <?php echo $t[$role]; ?>
                                    </span>

                                    <?php if(!empty($u['serial_no'])): ?>
                                    <div class="mt-2">
                                        <span class="serial-badge"><i class="fas fa-id-badge me-1"></i><?php echo e($u['serial_no']); ?></span>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($role == 'driver'): ?>
                                    <!-- Driver Verification Status -->
                                    <?php if(!empty($u['is_verified'])): ?>
                                        <div class="mt-2">
                                            <span class="badge bg-success px-3 py-2"><i class="fas fa-certificate me-1"></i> <?php echo $t['driver_verified'] ?? 'Verified Driver'; ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2">
                                            <span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-clock me-1"></i> <?php echo $t['pending_verification'] ?? 'Pending Verification'; ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="mt-2">
                                        <span class="badge bg-warning text-dark px-3 py-2">
                                            <i class="fas fa-coins me-1"></i> <?php echo $u['points'] ?? 0; ?> <?php echo $t['pts']; ?>
                                        </span>
                                        <?php if(!empty($u['rating'])): ?>
                                        <span class="badge bg-light text-dark px-3 py-2 ms-1">
                                            <span class="rating-stars"><i class="fas fa-star"></i></span>
                                            <span class="rating-value"><?php echo number_format($u['rating'], 1); ?></span>
                                        </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php
                                    // Get driver stats
                                    $driverStats = getDriverStats($conn, $uid);
                                    ?>
                                    <div class="mini-stats">
                                        <div class="mini-stat">
                                            <div class="mini-stat-value"><?php echo $driverStats['total_delivered']; ?></div>
                                            <div class="mini-stat-label"><?php echo $t['completed_orders'] ?? 'Completed'; ?></div>
                                        </div>
                                        <div class="mini-stat">
                                            <div class="mini-stat-value"><?php echo $driverStats['total_earnings']; ?></div>
                                            <div class="mini-stat-label"><?php echo $t['total_earnings'] ?? 'Earnings'; ?></div>
                                        </div>
                                        <div class="mini-stat">
                                            <div class="mini-stat-value"><?php echo $driverStats['this_month']; ?></div>
                                            <div class="mini-stat-label"><?php echo $t['this_month'] ?? 'This Month'; ?></div>
                                        </div>
                                        <div class="mini-stat">
                                            <div class="mini-stat-value"><?php echo $driverStats['active_orders']; ?></div>
                                            <div class="mini-stat-label"><?php echo $t['active_orders'] ?? 'Active'; ?></div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if($role == 'customer'): ?>
                                    <?php
                                    // Get client stats
                                    $clientStats = getClientStats($conn, $u['id'], $u['username']);
                                    ?>
                                    <div class="mini-stats">
                                        <div class="mini-stat">
                                            <div class="mini-stat-value"><?php echo $clientStats['total_orders']; ?></div>
                                            <div class="mini-stat-label"><?php echo $t['total_orders'] ?? 'Total Orders'; ?></div>
                                        </div>
                                        <div class="mini-stat">
                                            <div class="mini-stat-value"><?php echo $clientStats['delivered']; ?></div>
                                            <div class="mini-stat-label"><?php echo $t['delivered'] ?? 'Delivered'; ?></div>
                                        </div>
                                        <div class="mini-stat">
                                            <div class="mini-stat-value"><?php echo $clientStats['active']; ?></div>
                                            <div class="mini-stat-label"><?php echo $t['active_orders'] ?? 'Active'; ?></div>
                                        </div>
                                        <div class="mini-stat">
                                            <div class="mini-stat-value"><?php echo $clientStats['this_month']; ?></div>
                                            <div class="mini-stat-label"><?php echo $t['this_month'] ?? 'This Month'; ?></div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Personal Info Section -->
                                <div class="section-divider">
                                    <i class="fas fa-id-card me-2"></i> <?php echo $t['personal_info']; ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary"><?php echo $t['full_name_ph']; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" name="full_name" class="form-control" value="<?php echo e($u['full_name']); ?>" placeholder="<?php echo $t['full_name_ph']; ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary">
                                        <?php echo $t['phone_ph']; ?>
                                        <?php if(isPhoneVerified($u)): ?>
                                            <span class="phone-verified ms-2"><i class="fas fa-check-circle"></i> <?php echo $t['phone_verified'] ?? 'Verified'; ?></span>
                                        <?php elseif(!empty($u['phone'])): ?>
                                            <span class="phone-not-verified ms-2"><i class="fas fa-exclamation-circle"></i> <?php echo $t['phone_not_verified'] ?? 'Not Verified'; ?></span>
                                        <?php endif; ?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="tel" name="phone" class="form-control" value="<?php echo e($u['phone']); ?>" placeholder="<?php echo $t['phone_ph']; ?>">
                                    </div>
                                    <small class="text-muted"><?php echo $t['phone_auto_verify'] ?? 'Phone is auto-verified when added'; ?></small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary"><?php echo $t['email_ph']; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control" value="<?php echo e($u['email']); ?>" placeholder="<?php echo $t['email_ph']; ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary"><?php echo $t['address']; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                        <input type="text" name="profile_address" class="form-control" value="<?php echo e($u['address']); ?>" placeholder="<?php echo $t['address']; ?>">
                                    </div>
                                </div>

                                <!-- Security Section -->
                                <div class="section-divider">
                                    <i class="fas fa-shield-alt me-2"></i> <?php echo $t['security']; ?>
                                </div>

                                <div class="alert alert-light border-0 bg-light rounded-3 mb-3">
                                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i> <?php echo $t['leave_empty_password']; ?></small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary"><?php echo $t['new_password']; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-key text-muted"></i></span>
                                        <input type="password" name="new_password" class="form-control" minlength="4" placeholder="<?php echo $t['new_password']; ?>">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-secondary"><?php echo $t['confirm_new_password']; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-key text-muted"></i></span>
                                        <input type="password" name="confirm_new_password" class="form-control" placeholder="<?php echo $t['confirm_new_password']; ?>">
                                    </div>
                                </div>

                                <button type="submit" name="update_profile" class="btn btn-primary w-100 py-3 fw-bold rounded-pill">
                                    <i class="fas fa-check-circle me-2"></i><?php echo $t['save_changes']; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif($role == 'admin'): ?>
            <!-- ================= ADMIN DASHBOARD ================= -->

            <!-- Statistics -->
            <?php
            // Enhanced Statistics Queries
            $totalCustomers = $conn->query("SELECT COUNT(*) FROM users1 WHERE role='customer'")->fetchColumn();
            $totalDrivers = $conn->query("SELECT COUNT(*) FROM users1 WHERE role='driver'")->fetchColumn();
            $activeDrivers = $conn->query("SELECT COUNT(*) FROM users1 WHERE role='driver' AND status='active'")->fetchColumn();
            $verifiedDrivers = $conn->query("SELECT COUNT(*) FROM users1 WHERE role='driver' AND is_verified=1")->fetchColumn();

            $totalOrders = $conn->query("SELECT COUNT(*) FROM orders1")->fetchColumn();
            $pendingOrders = $conn->query("SELECT COUNT(*) FROM orders1 WHERE status='pending'")->fetchColumn();
            $activeOrders = $conn->query("SELECT COUNT(*) FROM orders1 WHERE status IN ('accepted', 'picked_up')")->fetchColumn();
            $deliveredOrders = $conn->query("SELECT COUNT(*) FROM orders1 WHERE status='delivered'")->fetchColumn();
            $cancelledOrders = $conn->query("SELECT COUNT(*) FROM orders1 WHERE status='cancelled'")->fetchColumn();

            $todayOrders = $conn->query("SELECT COUNT(*) FROM orders1 WHERE DATE(created_at) = CURDATE()")->fetchColumn();
            $todayDelivered = $conn->query("SELECT COUNT(*) FROM orders1 WHERE DATE(delivered_at) = CURDATE() AND status='delivered'")->fetchColumn();

            $totalRevenue = $conn->query("SELECT COALESCE(SUM(points_cost), 0) FROM orders1 WHERE status='delivered'")->fetchColumn();
            $todayRevenue = $conn->query("SELECT COALESCE(SUM(points_cost), 0) FROM orders1 WHERE DATE(delivered_at) = CURDATE() AND status='delivered'")->fetchColumn();
            ?>

            <!-- Enhanced Statistics Grid -->
            <div class="row g-3 mb-4">
                <!-- User Statistics -->
                <div class="col-6 col-lg-3">
                    <div class="ultra-card stat-card-enhanced">
                        <div class="card-inner text-center">
                            <div class="stat-icon-circle bg-primary-soft mb-2">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            <h3 class="stat-number mb-1"><?php echo number_format($totalCustomers); ?></h3>
                            <small class="text-muted text-uppercase fw-bold"><?php echo $t['customers'] ?? 'Customers'; ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="ultra-card stat-card-enhanced">
                        <div class="card-inner text-center">
                            <div class="stat-icon-circle bg-info-soft mb-2">
                                <i class="fas fa-motorcycle text-info"></i>
                            </div>
                            <h3 class="stat-number mb-1"><?php echo number_format($totalDrivers); ?></h3>
                            <small class="text-muted text-uppercase fw-bold"><?php echo $t['drivers'] ?? 'Drivers'; ?></small>
                            <div class="mt-2">
                                <span class="badge bg-success"><?php echo $activeDrivers; ?> <?php echo $t['active'] ?? 'Active'; ?></span>
                                <span class="badge bg-primary"><?php echo $verifiedDrivers; ?> <?php echo $t['verified'] ?? 'Verified'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Statistics -->
                <div class="col-6 col-lg-3">
                    <div class="ultra-card stat-card-enhanced">
                        <div class="card-inner text-center">
                            <div class="stat-icon-circle bg-success-soft mb-2">
                                <i class="fas fa-box text-success"></i>
                            </div>
                            <h3 class="stat-number mb-1"><?php echo number_format($totalOrders); ?></h3>
                            <small class="text-muted text-uppercase fw-bold"><?php echo $t['total_orders'] ?? 'Total Orders'; ?></small>
                            <div class="mt-2">
                                <span class="badge bg-warning text-dark"><?php echo $pendingOrders; ?> <?php echo $t['pending'] ?? 'Pending'; ?></span>
                                <span class="badge bg-success"><?php echo $deliveredOrders; ?> <?php echo $t['delivered'] ?? 'Delivered'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="ultra-card stat-card-enhanced">
                        <div class="card-inner text-center">
                            <div class="stat-icon-circle bg-warning-soft mb-2">
                                <i class="fas fa-chart-line text-warning"></i>
                            </div>
                            <h3 class="stat-number mb-1"><?php echo number_format($todayOrders); ?></h3>
                            <small class="text-muted text-uppercase fw-bold"><?php echo $t['today_orders'] ?? 'Today\'s Orders'; ?></small>
                            <div class="mt-2">
                                <span class="badge bg-success"><?php echo $todayDelivered; ?> <?php echo $t['delivered'] ?? 'Delivered'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue and Performance Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="ultra-card">
                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block mb-1"><?php echo $t['total_revenue'] ?? 'Total Revenue'; ?></small>
                                    <h4 class="mb-0 text-success"><?php echo number_format($totalRevenue); ?> <small class="text-muted">pts</small></h4>
                                </div>
                                <div class="stat-icon-circle bg-success-soft">
                                    <i class="fas fa-coins text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="ultra-card">
                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block mb-1"><?php echo $t['today_revenue'] ?? 'Today\'s Revenue'; ?></small>
                                    <h4 class="mb-0 text-primary"><?php echo number_format($todayRevenue); ?> <small class="text-muted">pts</small></h4>
                                </div>
                                <div class="stat-icon-circle bg-primary-soft">
                                    <i class="fas fa-calendar-day text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="ultra-card">
                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block mb-1"><?php echo $t['success_rate'] ?? 'Success Rate'; ?></small>
                                    <h4 class="mb-0 text-info">
                                        <?php
                                        $successRate = $totalOrders > 0 ? round(($deliveredOrders / $totalOrders) * 100, 1) : 0;
                                        echo $successRate;
                                        ?>%
                                    </h4>
                                </div>
                                <div class="stat-icon-circle bg-info-soft">
                                    <i class="fas fa-percentage text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Tabs -->
            <ul class="nav nav-tabs mb-4 flex-nowrap overflow-auto" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#customers"><i class="fas fa-users"></i> <span class="d-none d-sm-inline"><?php echo $t['manage_users']; ?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#drivers"><i class="fas fa-motorcycle"></i> <span class="d-none d-sm-inline"><?php echo $t['manage_drivers']; ?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#orders"><i class="fas fa-box"></i> <span class="d-none d-sm-inline"><?php echo $t['manage_orders']; ?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#points"><i class="fas fa-coins"></i> <span class="d-none d-sm-inline"><?php echo $t['add_points']; ?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#promo-codes"><i class="fas fa-tag"></i> <span class="d-none d-sm-inline"><?php echo $t['promo_codes'] ?? 'Promo Codes'; ?></span></a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- CUSTOMERS TAB -->
                <div class="tab-pane fade show active" id="customers">
                    <div class="card content-card">
                        <div class="card-header bg-white py-3 d-flex justify-content-between flex-wrap gap-2">
                            <h5 class="mb-0"><i class="fas fa-users text-primary"></i> <?php echo $t['manage_users']; ?></h5>
                            <button class="btn btn-sm btn-primary" onclick="showAddUserModal('customer')">
                                <i class="fas fa-plus"></i> <?php echo $t['add_user']; ?>
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div class="admin-cards-grid">
                                <?php
                                $users = $conn->query("SELECT * FROM users1 WHERE role='customer' ORDER BY id DESC");
                                if($users->rowCount() == 0): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <p><?php echo $t['no_users'] ?? 'No customers yet'; ?></p>
                                </div>
                                <?php else: while($user = $users->fetch()):
                                    $userAvatarUrl = getAvatarUrl($user);
                                ?>
                                <div class="ultra-card admin-card">
                                    <div class="card-inner">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="profile-avatar avatar-sm avatar-customer">
                                                <?php if($userAvatarUrl): ?>
                                                    <img src="<?php echo e($userAvatarUrl); ?>" alt="">
                                                <?php else: ?>
                                                    <i class="fas fa-user"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold"><?php echo e($user['full_name'] ?? $user['username']); ?></h6>
                                                        <small class="text-muted">@<?php echo e($user['username']); ?> #<?php echo $user['id']; ?></small>
                                                    </div>
                                                    <?php if($user['status'] == 'active'): ?>
                                                        <span class="badge bg-success"><?php echo $t['active']; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger"><?php echo $t['banned']; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if(!empty($user['phone'])): ?>
                                                <div class="mb-2">
                                                    <a href="tel:+222<?php echo $user['phone']; ?>" class="text-primary small">
                                                        <i class="fas fa-phone me-1"></i>+222 <?php echo e($user['phone']); ?>
                                                    </a>
                                                </div>
                                                <?php endif; ?>
                                                <div class="d-flex gap-2 mt-2">
                                                    <button class="btn btn-sm btn-outline-primary flex-grow-1" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                        <i class="fas fa-edit me-1"></i><?php echo $t['edit'] ?? 'Edit'; ?>
                                                    </button>
                                                    <a href="?toggle_ban=<?php echo $user['id']; ?>" class="btn btn-sm btn-<?php echo $user['status']=='active'?'warning':'success'; ?>" onclick="return confirm('Confirm?')">
                                                        <i class="fas fa-<?php echo $user['status']=='active'?'ban':'check'; ?>"></i>
                                                    </a>
                                                    <a href="?delete_user=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete permanently?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DRIVERS TAB -->
                <div class="tab-pane fade" id="drivers">
                    <div class="card content-card">
                        <div class="card-header bg-white py-3 d-flex justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <h5 class="mb-0"><i class="fas fa-motorcycle text-info"></i> <?php echo $t['manage_drivers']; ?></h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAllDrivers" onclick="toggleAllDrivers(this)">
                                    <label class="form-check-label small" for="selectAllDrivers">
                                        <?php echo $t['select_all'] ?? 'Select All'; ?>
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-success" onclick="showBulkRechargeModal()" id="bulkRechargeBtn" style="display:none;">
                                    <i class="fas fa-coins"></i> <?php echo $t['bulk_recharge'] ?? 'Bulk Recharge'; ?>
                                </button>
                                <button class="btn btn-sm btn-info text-white" onclick="showAddUserModal('driver')">
                                    <i class="fas fa-plus"></i> <?php echo $t['add_user']; ?>
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="admin-cards-grid">
                                <?php
                                $drivers = $conn->query("SELECT * FROM users1 WHERE role='driver' ORDER BY id DESC");
                                if($drivers->rowCount() == 0): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-motorcycle fa-2x mb-2"></i>
                                    <p><?php echo $t['no_drivers'] ?? 'No drivers yet'; ?></p>
                                </div>
                                <?php else: while($driver = $drivers->fetch()):
                                    $driverAvatarUrl = getAvatarUrl($driver);
                                    $isVerified = !empty($driver['is_verified']);
                                ?>
                                <div class="ultra-card admin-card">
                                    <div class="card-inner">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input driver-checkbox" type="checkbox" value="<?php echo $driver['id']; ?>" id="driver<?php echo $driver['id']; ?>" onchange="updateBulkRechargeButton()">
                                            </div>
                                            <div class="avatar-with-badge">
                                                <div class="profile-avatar avatar-sm avatar-driver">
                                                    <?php if($driverAvatarUrl): ?>
                                                        <img src="<?php echo e($driverAvatarUrl); ?>" alt="">
                                                    <?php else: ?>
                                                        <?php echo getUserInitials($driver); ?>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if($isVerified): ?>
                                                    <div class="verified-badge verified-badge-sm" title="<?php echo $t['driver_verified'] ?? 'Verified'; ?>">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold"><?php echo e($driver['full_name'] ?? $driver['username']); ?></h6>
                                                        <small class="text-muted">@<?php echo e($driver['username']); ?></small>
                                                        <?php if(!empty($driver['serial_no'])): ?>
                                                        <br><small class="text-primary fw-bold"><?php echo e($driver['serial_no']); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-end">
                                                        <?php if($driver['status'] == 'active'): ?>
                                                            <span class="badge bg-success mb-1"><?php echo $t['active']; ?></span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger mb-1"><?php echo $t['banned']; ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                    <span class="badge bg-warning text-dark"><i class="fas fa-coins me-1"></i><?php echo $driver['points'] ?? 0; ?> pts</span>
                                                    <?php if($isVerified): ?>
                                                        <span class="badge bg-success"><i class="fas fa-certificate me-1"></i><?php echo $t['verified'] ?? 'Verified'; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><i class="fas fa-clock me-1"></i><?php echo $t['pending_verification'] ?? 'Pending'; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if(!empty($driver['phone'])): ?>
                                                <div class="mb-2">
                                                    <a href="tel:+222<?php echo $driver['phone']; ?>" class="text-primary small">
                                                        <i class="fas fa-phone me-1"></i>+222 <?php echo e($driver['phone']); ?>
                                                        <?php if(!empty($driver['phone_verified'])): ?>
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                                <?php endif; ?>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    <a href="?toggle_verify=<?php echo $driver['id']; ?>" class="btn btn-sm btn-<?php echo $isVerified ? 'success' : 'outline-success'; ?>" onclick="return confirm('<?php echo $isVerified ? ($t['confirm_unverify'] ?? 'Remove verification?') : ($t['confirm_verify'] ?? 'Verify this driver?'); ?>')">
                                                        <i class="fas fa-<?php echo $isVerified ? 'certificate' : 'user-check'; ?> me-1"></i><?php echo $isVerified ? ($t['verified'] ?? 'Verified') : ($t['verify'] ?? 'Verify'); ?>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="editUser(<?php echo htmlspecialchars(json_encode($driver)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="?toggle_ban=<?php echo $driver['id']; ?>" class="btn btn-sm btn-<?php echo $driver['status']=='active'?'warning':'success'; ?>" onclick="return confirm('Confirm?')">
                                                        <i class="fas fa-<?php echo $driver['status']=='active'?'ban':'check'; ?>"></i>
                                                    </a>
                                                    <a href="?delete_user=<?php echo $driver['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete permanently?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ORDERS TAB -->
                <div class="tab-pane fade" id="orders">
                    <div class="card content-card">
                        <div class="card-header bg-white py-3 d-flex justify-content-between flex-wrap gap-2">
                            <h5 class="mb-0"><i class="fas fa-box text-success"></i> <?php echo $t['manage_orders']; ?></h5>
                            <button class="btn btn-sm btn-success text-white" data-bs-toggle="modal" data-bs-target="#addOrderModal">
                                <i class="fas fa-plus"></i> <?php echo $t['add_order']; ?>
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div class="admin-cards-grid">
                                <?php
                                $orders = $conn->query("SELECT o.*, u.username as driver_name FROM orders1 o LEFT JOIN users1 u ON o.driver_id=u.id ORDER BY o.id DESC LIMIT 100");
                                if($orders->rowCount() == 0): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <p><?php echo $t['no_orders'] ?? 'No orders yet'; ?></p>
                                </div>
                                <?php else: while($order = $orders->fetch()):
                                    $st = $order['status'] ?? 'pending';
                                    $statusTagClass = ($st == 'pending') ? 'tag-pending' : (($st == 'accepted') ? 'tag-accepted' : (($st == 'picked_up') ? 'tag-picked' : (($st == 'cancelled') ? 'badge-cancelled' : 'tag-delivered')));
                                ?>
                                <div class="ultra-card admin-card">
                                    <div class="card-inner">
                                        <!-- Card Header -->
                                        <div class="c-header">
                                            <div class="price-tag">#<?php echo $order['id']; ?></div>
                                            <div class="tag-new <?php echo $statusTagClass; ?>">
                                                <i class="fas fa-<?php echo getStatusIcon($st); ?>"></i>
                                                <?php echo $t['st_'.$st] ?? ucfirst($st); ?>
                                            </div>
                                        </div>

                                        <!-- Route Visual -->
                                        <div class="route-row">
                                            <div class="visual-connector">
                                                <div class="dot-circle dot-p"></div>
                                                <div class="line-dashed"></div>
                                                <div class="dot-circle dot-d"></div>
                                            </div>
                                            <div class="text-info-route">
                                                <div>
                                                    <div class="loc-title"><?php echo e($order['details'] ?? ''); ?></div>
                                                    <div class="loc-sub"><?php echo e($order['customer_name'] ?? ''); ?></div>
                                                </div>
                                                <div>
                                                    <div class="loc-title"><i class="fas fa-map-marker-alt text-danger me-1"></i><?php echo e($order['address'] ?? ''); ?></div>
                                                    <?php if(!empty($order['client_phone'])): ?>
                                                    <div class="loc-sub">
                                                        <a href="tel:+222<?php echo $order['client_phone']; ?>" class="text-primary">
                                                            <i class="fas fa-phone me-1"></i>+222 <?php echo $order['client_phone']; ?>
                                                        </a>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Meta Info -->
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <span class="badge bg-light text-dark border"><i class="fas fa-key me-1"></i>PIN: <?php echo $order['delivery_code'] ?? '----'; ?></span>
                                            <?php if(!empty($order['driver_name'])): ?>
                                            <span class="badge bg-info text-white"><i class="fas fa-motorcycle me-1"></i><?php echo e($order['driver_name']); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Actions -->
                                        <div class="d-flex flex-wrap gap-2">
                                            <button class="btn btn-sm btn-outline-primary flex-grow-1" onclick="editOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)">
                                                <i class="fas fa-edit me-1"></i><?php echo $t['edit'] ?? 'Edit'; ?>
                                            </button>
                                            <?php if($st == 'pending' || $st == 'accepted'): ?>
                                            <a href="?cancel_order=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-warning" onclick="return confirm('Cancel this order?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            <?php endif; ?>
                                            <a href="?delete_order=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete permanently?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- POINTS TAB -->
                <div class="tab-pane fade" id="points">
                    <div class="card content-card" style="max-width: 500px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4"><i class="fas fa-coins text-warning"></i> <?php echo $t['add_points']; ?></h5>

                            <!-- Search Box -->
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-search me-1"></i> <?php echo $t['search'] ?? 'Search'; ?> (ID / <?php echo $t['serial_no'] ?? 'Serial No.'; ?>)</label>
                                <input type="text" id="driverSearchInput" class="form-control" placeholder="<?php echo $t['search'] ?? 'Search'; ?>..." onkeyup="filterDrivers()">
                            </div>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['driver'] ?? 'Driver'; ?></label>
                                    <select name="driver_id" id="driverSelect" class="form-select" required>
                                        <option value=""><?php echo $t['select_driver'] ?? 'Select Driver'; ?></option>
                                        <?php
                                        $ds = $conn->query("SELECT id, serial_no, username, full_name, phone, points FROM users1 WHERE role='driver' ORDER BY username");
                                        while($d=$ds->fetch()) {
                                            $displayName = $d['full_name'] ?: $d['username'];
                                            $serialNo = $d['serial_no'] ?: 'N/A';
                                            echo "<option value='{$d['id']}' data-serial='{$serialNo}' data-phone='{$d['phone']}'>[{$serialNo}] {$displayName} ({$d['points']} pts)</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label"><?php echo $t['amount'] ?? 'Amount'; ?></label>
                                    <input type="number" name="amount" class="form-control" placeholder="20" min="1" required>
                                </div>
                                <button name="recharge" class="btn btn-warning w-100 fw-bold">
                                    <i class="fas fa-plus"></i> <?php echo $t['add_points'] ?? 'Add Points'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- PROMO CODES TAB -->
                <div class="tab-pane fade" id="promo-codes">
                    <div class="card content-card">
                        <div class="card-header bg-white py-3 d-flex justify-content-between flex-wrap gap-2">
                            <h5 class="mb-0"><i class="fas fa-tag text-success"></i> <?php echo $t['promo_codes'] ?? 'Promo Codes'; ?></h5>
                            <button class="btn btn-sm btn-success" onclick="showAddPromoCodeModal()">
                                <i class="fas fa-plus"></i> <?php echo $t['create_promo'] ?? 'Create Promo Code'; ?>
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo $t['code'] ?? 'Code'; ?></th>
                                            <th><?php echo $t['discount'] ?? 'Discount'; ?></th>
                                            <th><?php echo $t['usage'] ?? 'Usage'; ?></th>
                                            <th><?php echo $t['validity'] ?? 'Validity'; ?></th>
                                            <th><?php echo $t['status'] ?? 'Status'; ?></th>
                                            <th><?php echo $t['actions'] ?? 'Actions'; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $promo_codes = $conn->query("SELECT * FROM promo_codes ORDER BY created_at DESC");
                                        if($promo_codes->rowCount() == 0): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="fas fa-tag fa-2x mb-2 d-block"></i>
                                                <?php echo $t['no_promo_codes'] ?? 'No promo codes yet. Create one to get started!'; ?>
                                            </td>
                                        </tr>
                                        <?php else: while($promo = $promo_codes->fetch()):
                                            $is_expired = $promo['valid_until'] && strtotime($promo['valid_until']) < time();
                                            $is_maxed = $promo['max_uses'] && $promo['used_count'] >= $promo['max_uses'];
                                        ?>
                                        <tr>
                                            <td><strong class="text-primary"><?php echo e($promo['code']); ?></strong></td>
                                            <td>
                                                <?php if($promo['discount_type'] == 'percentage'): ?>
                                                    <span class="badge bg-info"><?php echo $promo['discount_value']; ?>%</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><?php echo $promo['discount_value']; ?> MRU</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo $promo['used_count']; ?> / <?php echo $promo['max_uses'] ?? '∞'; ?>
                                            </td>
                                            <td class="small">
                                                <?php if($promo['valid_from']): ?>
                                                    <div>From: <?php echo date('Y-m-d', strtotime($promo['valid_from'])); ?></div>
                                                <?php endif; ?>
                                                <?php if($promo['valid_until']): ?>
                                                    <div>Until: <?php echo date('Y-m-d', strtotime($promo['valid_until'])); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(!$promo['is_active']): ?>
                                                    <span class="badge bg-secondary"><?php echo $t['inactive'] ?? 'Inactive'; ?></span>
                                                <?php elseif($is_expired): ?>
                                                    <span class="badge bg-danger"><?php echo $t['expired'] ?? 'Expired'; ?></span>
                                                <?php elseif($is_maxed): ?>
                                                    <span class="badge bg-warning text-dark"><?php echo $t['max_uses_reached'] ?? 'Max Uses'; ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><?php echo $t['active'] ?? 'Active'; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick='editPromoCode(<?php echo json_encode($promo); ?>)'>
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="?toggle_promo=<?php echo $promo['id']; ?>" class="btn btn-outline-<?php echo $promo['is_active'] ? 'warning' : 'success'; ?>">
                                                        <i class="fas fa-<?php echo $promo['is_active'] ? 'pause' : 'play'; ?>"></i>
                                                    </a>
                                                    <a href="?delete_promo=<?php echo $promo['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('<?php echo $t['confirm_delete'] ?? 'Delete this promo code?'; ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                function filterDrivers() {
                    const search = document.getElementById('driverSearchInput').value.toLowerCase();
                    const select = document.getElementById('driverSelect');
                    const options = select.querySelectorAll('option');

                    options.forEach(option => {
                        if (option.value === '') {
                            option.style.display = '';
                            return;
                        }
                        const text = option.textContent.toLowerCase();
                        const serial = (option.dataset.serial || '').toLowerCase();
                        const phone = (option.dataset.phone || '').toLowerCase();
                        const id = option.value;

                        if (text.includes(search) || serial.includes(search) || phone.includes(search) || id.includes(search)) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    });

                    // Auto-select if only one match
                    const visible = Array.from(options).filter(o => o.style.display !== 'none' && o.value !== '');
                    if (visible.length === 1) {
                        select.value = visible[0].value;
                    }
                }
                </script>
            </div>

            <!-- Add User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-user-plus"></i> <?php echo $t['add_user']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['username']; ?></label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['password']; ?></label>
                                    <input type="text" name="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['role']; ?></label>
                                    <select name="role" id="addUserRole" class="form-select" required>
                                        <option value="customer"><?php echo $t['customer']; ?></option>
                                        <option value="driver"><?php echo $t['driver']; ?></option>
                                        <option value="admin"><?php echo $t['admin']; ?></option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['points']; ?></label>
                                    <input type="number" name="points" class="form-control" value="0" min="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $t['cancel']; ?></button>
                                <button type="submit" name="admin_add_user" class="btn btn-primary">Add User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-edit"></i> <?php echo $t['edit']; ?> User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="user_id" id="edit_user_id">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['username']; ?></label>
                                    <input type="text" id="edit_username" class="form-control" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['password']; ?> (leave empty to keep)</label>
                                    <input type="text" name="password" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['role']; ?></label>
                                    <select name="role" id="edit_role" class="form-select" required>
                                        <option value="customer"><?php echo $t['customer']; ?></option>
                                        <option value="driver"><?php echo $t['driver']; ?></option>
                                        <option value="admin"><?php echo $t['admin']; ?></option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['points']; ?></label>
                                    <input type="number" name="points" id="edit_points" class="form-control" min="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $t['cancel']; ?></button>
                                <button type="submit" name="admin_edit_user" class="btn btn-primary"><i class="fas fa-save me-1"></i><?php echo $t['save']; ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bulk Recharge Modal -->
            <div class="modal fade" id="bulkRechargeModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-coins text-success"></i> <?php echo $t['bulk_recharge'] ?? 'Bulk Recharge Drivers'; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="selectedDriverCount">0</span> <?php echo $t['drivers_selected'] ?? 'drivers selected'; ?>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold"><?php echo $t['amount_to_add'] ?? 'Amount to Add'; ?></label>
                                    <div class="input-group">
                                        <input type="number" name="bulk_amount" id="bulkAmount" class="form-control form-control-lg" min="1" required placeholder="<?php echo $t['enter_amount'] ?? 'Enter amount'; ?>">
                                        <span class="input-group-text"><?php echo $t['pts'] ?? 'pts'; ?></span>
                                    </div>
                                </div>
                                <input type="hidden" name="driver_ids" id="selectedDriverIds">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $t['cancel']; ?></button>
                                <button type="submit" name="bulk_recharge_drivers" class="btn btn-success">
                                    <i class="fas fa-check-circle me-1"></i><?php echo $t['confirm_recharge'] ?? 'Confirm Recharge'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Promo Code Modal -->
            <div class="modal fade" id="promoCodeModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-tag text-success"></i> <span id="promoModalTitle"><?php echo $t['create_promo'] ?? 'Create Promo Code'; ?></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" id="promoCodeForm">
                            <div class="modal-body">
                                <input type="hidden" name="promo_id" id="promoId">

                                <div class="mb-3">
                                    <label class="form-label fw-bold"><?php echo $t['code'] ?? 'Code'; ?> *</label>
                                    <input type="text" name="promo_code" id="promoCode" class="form-control text-uppercase" required pattern="[A-Z0-9]+" placeholder="e.g. SUMMER2026" maxlength="50">
                                    <small class="text-muted"><?php echo $t['code_help'] ?? 'Uppercase letters and numbers only'; ?></small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold"><?php echo $t['discount_type'] ?? 'Discount Type'; ?> *</label>
                                    <select name="discount_type" id="discountType" class="form-select" required onchange="updateDiscountLabel()">
                                        <option value="percentage"><?php echo $t['percentage'] ?? 'Percentage'; ?> (%)</option>
                                        <option value="fixed"><?php echo $t['fixed_amount'] ?? 'Fixed Amount'; ?> (MRU)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold"><span id="discountLabel"><?php echo $t['discount_value'] ?? 'Discount Value'; ?></span> *</label>
                                    <input type="number" name="discount_value" id="discountValue" class="form-control" required min="0" step="0.01" placeholder="e.g. 20">
                                    <small class="text-muted" id="discountHelp"><?php echo $t['percentage_help'] ?? 'Enter percentage (e.g., 20 for 20% off)'; ?></small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold"><?php echo $t['max_uses'] ?? 'Maximum Uses'; ?></label>
                                    <input type="number" name="max_uses" id="maxUses" class="form-control" min="1" placeholder="<?php echo $t['unlimited'] ?? 'Leave empty for unlimited'; ?>">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold"><?php echo $t['valid_from'] ?? 'Valid From'; ?></label>
                                        <input type="date" name="valid_from" id="validFrom" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold"><?php echo $t['valid_until'] ?? 'Valid Until'; ?></label>
                                        <input type="date" name="valid_until" id="validUntil" class="form-control">
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                                    <label class="form-check-label" for="isActive">
                                        <?php echo $t['active'] ?? 'Active'; ?>
                                    </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $t['cancel']; ?></button>
                                <button type="submit" name="save_promo_code" class="btn btn-success">
                                    <i class="fas fa-check-circle me-1"></i><?php echo $t['save'] ?? 'Save'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add Order Modal -->
            <div class="modal fade" id="addOrderModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-plus-circle"></i> <?php echo $t['add_order']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['customer_name']; ?></label>
                                    <input type="text" name="customer_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['order_details']; ?></label>
                                    <textarea name="details" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['address']; ?></label>
                                    <input type="text" name="address" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['status']; ?></label>
                                    <select name="status" class="form-select" required>
                                        <option value="pending"><?php echo $t['st_pending']; ?></option>
                                        <option value="accepted"><?php echo $t['st_accepted']; ?></option>
                                        <option value="delivered"><?php echo $t['st_delivered']; ?></option>
                                        <option value="cancelled"><?php echo $t['st_cancelled']; ?></option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['assign_driver']; ?> (optional)</label>
                                    <select name="driver_id" class="form-select">
                                        <option value=""><?php echo $t['no_driver']; ?></option>
                                        <?php
                                        $ds = $conn->query("SELECT id, username FROM users1 WHERE role='driver' ORDER BY username");
                                        while($d=$ds->fetch()) {
                                            echo "<option value='{$d['id']}'>{$d['username']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $t['cancel']; ?></button>
                                <button type="submit" name="admin_add_order" class="btn btn-success">Add Order</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Order Modal -->
            <div class="modal fade" id="editOrderModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-edit"></i> <?php echo $t['edit_order']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="order_id" id="edit_order_id">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['customer_name']; ?></label>
                                    <input type="text" name="customer_name" id="edit_order_customer" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['order_details']; ?></label>
                                    <textarea name="details" id="edit_order_details" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['address']; ?></label>
                                    <input type="text" name="address" id="edit_order_address" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['status']; ?></label>
                                    <select name="status" id="edit_order_status" class="form-select" required>
                                        <option value="pending"><?php echo $t['st_pending']; ?></option>
                                        <option value="accepted"><?php echo $t['st_accepted']; ?></option>
                                        <option value="delivered"><?php echo $t['st_delivered']; ?></option>
                                        <option value="cancelled"><?php echo $t['st_cancelled']; ?></option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $t['assign_driver']; ?></label>
                                    <select name="driver_id" id="edit_order_driver" class="form-select">
                                        <option value=""><?php echo $t['no_driver']; ?></option>
                                        <?php
                                        $ds = $conn->query("SELECT id, username FROM users1 WHERE role='driver' ORDER BY username");
                                        while($d=$ds->fetch()) {
                                            echo "<option value='{$d['id']}'>{$d['username']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $t['cancel']; ?></button>
                                <button type="submit" name="admin_edit_order" class="btn btn-primary"><i class="fas fa-save me-1"></i><?php echo $t['update_order'] ?? $t['save']; ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- ================= BARQ ULTRA PREMIUM DASHBOARD ================= -->
            <?php
            // Get driver stats for driver role
            if($role == 'driver') {
                $driverStats = getDriverStats($conn, $uid);

                // Calculate driver priority tier
                $completedOrders = (int)$driverStats['total_orders'];
                $avgRating = (float)($u['rating'] ?? 0);

                $priorityTier = 4; // Default: New driver
                $priorityBadge = $t['new_driver'] ?? 'New Driver';
                $priorityColor = 'secondary';
                $priorityIcon = 'fa-user';

                if ($completedOrders >= 50 && $avgRating >= 4) {
                    $priorityTier = 1;
                    $priorityBadge = $t['vip_driver'] ?? 'VIP Driver';
                    $priorityColor = 'warning';
                    $priorityIcon = 'fa-crown';
                } elseif ($completedOrders >= 20 && $avgRating >= 3) {
                    $priorityTier = 2;
                    $priorityBadge = $t['pro_driver'] ?? 'Pro Driver';
                    $priorityColor = 'primary';
                    $priorityIcon = 'fa-medal';
                } elseif ($completedOrders >= 5) {
                    $priorityTier = 3;
                    $priorityBadge = $t['regular_driver'] ?? 'Regular Driver';
                    $priorityColor = 'info';
                    $priorityIcon = 'fa-shield';
                }
            } elseif($role == 'customer') {
                $clientStats = getClientStats($conn, $u['id'], $u['username']);
            }
            ?>

            <!-- HEADER SECTION -->
            <section class="header-section">
                <div class="top-bar">
                    <div class="user-info">
                        <div class="avatar-circle <?php echo $role; ?>">
                            <?php
                            $headerAvatarUrl = getAvatarUrl($u);
                            if ($headerAvatarUrl): ?>
                                <img src="<?php echo e($headerAvatarUrl); ?>" alt="">
                            <?php else:
                                echo getUserInitials($u);
                            endif; ?>
                        </div>
                        <div class="greeting">
                            <h3><?php echo $t['hello'] ?? 'مرحباً'; ?> <?php echo e($u['full_name'] ?: $u['username']); ?></h3>
                            <span><?php echo $role == 'driver' ? ($t['start_your_day'] ?? 'ابدأ يومك بنشاط!') : ($t['what_need_today'] ?? 'ماذا تحتاج اليوم؟'); ?></span>
                        </div>
                    </div>
                    <a href="?settings=1" class="notif-btn" title="<?php echo $t['settings']; ?>">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                </div>
            </section>

            <?php if($role == 'driver'): ?>
            <!-- DRIVER STATS SCROLL -->
            <div class="stats-scroll">
                <div class="stat-card-new card-purple">
                    <i class="fa-solid fa-wallet stat-icon-new"></i>
                    <div>
                        <div class="stat-num-new" id="driverPoints"><?php echo number_format($u['points'] ?? 0); ?></div>
                        <div class="stat-label-new"><?php echo $t['balance'] ?? 'الأرباح'; ?> (<?php echo $t['pts'] ?? 'نقطة'; ?>)</div>
                    </div>
                    <div style="position:absolute; top:-10px; left:-10px; width:60px; height:60px; background:rgba(255,255,255,0.1); border-radius:50%;"></div>
                </div>

                <!-- Priority Badge Card -->
                <div class="stat-card-new card-new-white priority-badge-card">
                    <i class="fa-solid <?php echo $priorityIcon; ?> stat-icon-new" style="color:var(--<?php echo $priorityColor; ?>)"></i>
                    <div>
                        <div class="stat-label-new text-uppercase" style="margin-bottom: 8px;"><?php echo $t['your_tier'] ?? 'Your Tier'; ?></div>
                        <div class="priority-badge-text" style="font-size: 0.85rem; font-weight: 700; color: var(--<?php echo $priorityColor; ?>);">
                            <?php echo $priorityBadge; ?>
                        </div>
                    </div>
                    <?php if($priorityTier == 1): ?>
                        <div class="sparkle-effect"></div>
                    <?php endif; ?>
                </div>

                <div class="stat-card-new card-new-white">
                    <i class="fa-solid fa-star stat-icon-new" style="color:#FFD700"></i>
                    <div>
                        <div class="stat-num-new" style="color:var(--text-main)">
                            <?php
                            $rating = $u['rating'] ?? 0;
                            echo $rating > 0 ? number_format($rating, 1) : '<span style="font-size:0.9rem;">-</span>';
                            ?>
                        </div>
                        <div class="stat-label-new"><?php echo $t['rating'] ?? 'التقييم'; ?></div>
                    </div>
                </div>

                <div class="stat-card-new card-new-white">
                    <i class="fa-solid fa-route stat-icon-new" style="color:var(--success)"></i>
                    <div>
                        <div class="stat-num-new" style="color:var(--text-main)"><?php echo $driverStats['total_delivered']; ?></div>
                        <div class="stat-label-new"><?php echo $t['completed_orders'] ?? 'تم التوصيل'; ?></div>
                    </div>
                </div>

                <div class="stat-card-new card-new-white">
                    <i class="fa-solid fa-clock stat-icon-new" style="color:var(--primary)"></i>
                    <div>
                        <div class="stat-num-new" style="color:var(--text-main)"><?php echo $driverStats['active_orders']; ?></div>
                        <div class="stat-label-new"><?php echo $t['active_orders'] ?? 'طلبات نشطة'; ?></div>
                    </div>
                </div>
            </div>

            <!-- DRIVER CONTROL PANEL -->
            <div class="orders-container mb-3">
                <div class="ultra-card">
                    <div class="card-inner">
                        <div class="row g-3">
                            <!-- Online/Offline Toggle -->
                            <div class="col-6">
                                <div class="driver-control-btn <?php echo ($u['is_online'] ?? 0) ? 'active' : ''; ?>" onclick="document.getElementById('onlineSwitch').checked = !document.getElementById('onlineSwitch').checked; document.getElementById('onlineToggleForm').submit();">
                                    <div class="control-icon <?php echo ($u['is_online'] ?? 0) ? 'online' : 'offline'; ?>">
                                        <i class="fas fa-power-off"></i>
                                    </div>
                                    <div class="control-info">
                                        <span class="control-label"><?php echo ($u['is_online'] ?? 0) ? ($t['online'] ?? 'Online') : ($t['offline'] ?? 'Offline'); ?></span>
                                        <small class="control-hint"><?php echo ($u['is_online'] ?? 0) ? ($t['receiving_orders'] ?? 'Receiving orders') : ($t['tap_to_go_online'] ?? 'Tap to go online'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <!-- GPS Toggle -->
                            <div class="col-6">
                                <div class="driver-control-btn" id="gpsControlBtn" onclick="toggleDriverGPS()">
                                    <div class="control-icon gps-off" id="gpsControlIcon">
                                        <i class="fas fa-location-crosshairs"></i>
                                    </div>
                                    <div class="control-info">
                                        <span class="control-label" id="gpsControlLabel"><?php echo $t['gps_disabled'] ?? 'GPS Off'; ?></span>
                                        <small class="control-hint" id="gpsControlHint"><?php echo $t['tap_to_enable_gps'] ?? 'Tap to enable GPS'; ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- GPS Accuracy Badge -->
                        <div class="gps-status-bar mt-3" id="gpsStatusBar" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-success small"><i class="fas fa-satellite-dish me-1"></i><span id="gpsStatusText"><?php echo $t['location_active'] ?? 'Location active'; ?></span></span>
                                <span class="badge bg-light text-dark" id="gpsAccuracyBadge" style="display: none;">
                                    <i class="fas fa-signal me-1"></i><span id="gpsAccuracyValue">--</span>m
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden elements for GPS toggle -->
            <div id="gpsToggle" style="display:none;"></div>
            <div id="gpsStatusLabel" style="display:none;"></div>
            <div id="gpsStatusDetail" style="display:none;"></div>

            <!-- Online Toggle Form (Hidden) -->
            <form method="POST" id="onlineToggleForm" style="display:none;">
                <input type="checkbox" id="onlineSwitch" name="is_online" value="1" <?php echo ($u['is_online'] ?? 0) ? 'checked' : ''; ?>>
                <input type="hidden" name="toggle_online" value="1">
            </form>

            <!-- DRIVER RECHARGE DASHBOARD -->
            <div class="orders-container mb-3">
                <div class="recharge-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="recharge-label"><?php echo $t['your_balance'] ?? 'Your Balance'; ?></div>
                            <div class="recharge-balance"><?php echo number_format($u['points'] ?? 0); ?> <small style="font-size: 0.5em;"><?php echo $t['pts'] ?? 'pts'; ?></small></div>
                        </div>
                        <div class="text-end">
                            <small class="d-block opacity-75"><?php echo $t['cost_per_order'] ?? 'Cost per order'; ?></small>
                            <span class="fw-bold"><?php echo $points_cost_per_order; ?> <?php echo $t['pts'] ?? 'pts'; ?></span>
                        </div>
                    </div>
                    <?php if(($u['points'] ?? 0) < $points_cost_per_order): ?>
                    <div class="alert alert-light mb-3 py-2 px-3" style="background: rgba(255,255,255,0.9); border-radius: 10px;">
                        <small class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i><?php echo $t['err_low_bal'] ?? 'Low balance! Recharge to accept orders.'; ?></small>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex gap-2">
                        <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo urlencode('مرحبا، أرغب في شحن رصيد' . "\n" . 'المستخدم: ' . ($u['serial_no'] ?? $u['username']) . "\n" . 'الرقم: ' . ($u['phone'] ?? '')); ?>" target="_blank" class="recharge-btn whatsapp flex-grow-1 justify-content-center">
                            <i class="fab fa-whatsapp"></i>
                            <?php echo $t['whatsapp_recharge'] ?? 'WhatsApp to Recharge'; ?>
                        </a>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="opacity-75">
                            <i class="fas fa-info-circle me-1"></i>
                            <?php echo $t['recharge_note'] ?? 'Contact support to add credits to your account'; ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($role == 'customer'): ?>
            <!-- CUSTOMER STATS SCROLL -->
            <div class="stats-scroll">
                <div class="stat-card-new card-purple">
                    <i class="fa-solid fa-box stat-icon-new"></i>
                    <div>
                        <div class="stat-num-new"><?php echo $clientStats['total_orders']; ?></div>
                        <div class="stat-label-new"><?php echo $t['total_orders'] ?? 'إجمالي الطلبات'; ?></div>
                    </div>
                    <div style="position:absolute; top:-10px; left:-10px; width:60px; height:60px; background:rgba(255,255,255,0.1); border-radius:50%;"></div>
                </div>

                <div class="stat-card-new card-new-white">
                    <i class="fa-solid fa-check-circle stat-icon-new" style="color:var(--success)"></i>
                    <div>
                        <div class="stat-num-new" style="color:var(--text-main)"><?php echo $clientStats['delivered']; ?></div>
                        <div class="stat-label-new"><?php echo $t['delivered'] ?? 'تم التوصيل'; ?></div>
                    </div>
                </div>

                <div class="stat-card-new card-new-white">
                    <i class="fa-solid fa-clock stat-icon-new" style="color:var(--primary)"></i>
                    <div>
                        <div class="stat-num-new" style="color:var(--text-main)"><?php echo $clientStats['active']; ?></div>
                        <div class="stat-label-new"><?php echo $t['active_orders'] ?? 'طلبات نشطة'; ?></div>
                    </div>
                </div>
            </div>

            <!-- NEW ORDER CARD -->
            <div class="orders-container mb-4">
                <div class="ultra-card">
                    <div class="card-inner">
                        <h5 class="fw-bold mb-4">
                            <i class="fas fa-plus-circle text-primary me-2"></i><?php echo $t['new_order']; ?>
                        </h5>
                        <form method="POST" accept-charset="UTF-8" id="newOrderForm">
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-box me-1"></i><?php echo $t['order_details']; ?>
                                </label>
                                <textarea name="details" class="form-control" rows="3" placeholder="<?php echo $t['order_details_placeholder'] ?? 'Describe what you need delivered...'; ?>" required style="border-radius: var(--radius); border: 2px solid var(--gray-200);"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-phone me-1"></i><?php echo $t['phone_ph'] ?? 'Phone'; ?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">+222</span>
                                    <input type="tel" name="client_phone" class="form-control"
                                           value="<?php echo e($u['phone'] ?? ''); ?>"
                                           placeholder="<?php echo $t['phone_example'] ?? '2XXXXXXX'; ?>"
                                           pattern="[234][0-9]{7}" maxlength="8" inputmode="tel"
                                           <?php echo !empty($u['phone']) ? '' : 'required'; ?>>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i><?php echo $t['pickup_location'] ?? 'Pickup Location'; ?>
                                </label>
                                <div class="input-group">
                                    <input type="text" name="address" id="pickupAddress" class="form-control"
                                           placeholder="<?php echo $t['click_gps'] ?? 'Click GPS to set your location'; ?>" readonly>
                                    <button type="button" class="btn btn-success px-4" onclick="getPickupLocation()" id="gpsBtn" title="<?php echo $t['turn_on_gps'] ?? 'Turn on GPS'; ?>">
                                        <i class="fas fa-location-crosshairs"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="pickup_lat" id="pickupLat">
                                <input type="hidden" name="pickup_lng" id="pickupLng">
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i><?php echo $t['gps_required'] ?? 'GPS location is required for drivers to find you'; ?></small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-tag me-1 text-success"></i><?php echo $t['promo_code'] ?? 'Promo Code'; ?> <span class="text-muted">(<?php echo $t['optional'] ?? 'Optional'; ?>)</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" name="promo_code" id="promoCodeInput" class="form-control text-uppercase"
                                           placeholder="<?php echo $t['enter_promo_code'] ?? 'Enter promo code'; ?>" maxlength="50">
                                    <button type="button" class="btn btn-outline-success" onclick="validatePromoCode()" id="validatePromoBtn">
                                        <i class="fas fa-check"></i> <?php echo $t['apply'] ?? 'Apply'; ?>
                                    </button>
                                </div>
                                <small id="promoFeedback" class="text-muted"></small>
                            </div>

                            <button type="submit" name="add_order" class="slider-btn-container w-100">
                                <div class="slider-thumb"><i class="fa-solid fa-paper-plane"></i></div>
                                <div class="slider-text"><?php echo $t['btn_publish']; ?></div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- TABS FOR FILTERING -->
            <div class="tabs-wrapper">
                <div class="tab-new active"><?php echo $t['recent_orders'] ?? 'الطلبات'; ?></div>
                <?php if($role == 'driver'): ?>
                <span class="badge bg-warning text-dark pulse-badge" id="pendingBadge" style="display:none; margin: auto 0;">0</span>
                <?php endif; ?>
            </div>

            <!-- ORDERS AS ULTRA CARDS -->
            <div class="orders-container" id="ordersContainer">
                <?php
                // Get driver's location for distance filtering
                $driverLat = $u['last_lat'] ?? null;
                $driverLng = $u['last_lng'] ?? null;
                $maxDistance = 7; // 7km radius for drivers

                if($role == 'driver') {
                    if ($driverLat && $driverLng) {
                        $sql = "SELECT *,
                                (6371 * acos(cos(radians(?)) * cos(radians(pickup_lat)) * cos(radians(pickup_lng) - radians(?)) + sin(radians(?)) * sin(radians(pickup_lat)))) AS distance
                                FROM orders1
                                WHERE (driver_id = ? AND status IN ('accepted', 'picked_up'))
                                OR (status = 'pending' AND pickup_lat IS NOT NULL
                                    AND (6371 * acos(cos(radians(?)) * cos(radians(pickup_lat)) * cos(radians(pickup_lng) - radians(?)) + sin(radians(?)) * sin(radians(pickup_lat)))) <= ?)
                                ORDER BY CASE WHEN driver_id = ? THEN 0 ELSE 1 END, distance ASC, id DESC
                                LIMIT 50";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$driverLat, $driverLng, $driverLat, $uid, $driverLat, $driverLng, $driverLat, $maxDistance, $uid]);
                        $res = $stmt;
                    } else {
                        $sql = "SELECT * FROM orders1 WHERE driver_id = ? AND status IN ('accepted', 'picked_up') ORDER BY id DESC LIMIT 50";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$uid]);
                        $res = $stmt;
                    }
                } elseif($role == 'customer') {
                    $sql = "SELECT * FROM orders1 WHERE customer_name = ? OR client_id = ? ORDER BY id DESC LIMIT 50";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$u['username'], $uid]);
                    $res = $stmt;
                } else {
                    $sql = "SELECT * FROM orders1 ORDER BY id DESC LIMIT 50";
                    $res = $conn->query($sql);
                }

                if($role == 'driver' && !$driverLat):
                ?>
                <div class="ultra-card">
                    <div class="card-inner">
                        <div class="text-center py-4">
                            <i class="fas fa-location-crosshairs fa-3x text-warning mb-3"></i>
                            <h5 class="fw-bold"><?php echo $t['enable_gps'] ?? 'Enable GPS to see nearby orders'; ?></h5>
                            <p class="text-muted small"><?php echo $t['gps_driver_note'] ?? 'Turn on your GPS to find orders within 7km of your location'; ?></p>
                        </div>
                    </div>
                </div>
                <?php endif;

                if($res->rowCount() == 0): ?>
                <div class="ultra-card">
                    <div class="card-inner text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted"><?php echo $t['no_orders']; ?></h5>
                        <p class="text-muted small mb-0">
                            <?php echo ($role == 'driver') ? ($driverLat ? $t['no_nearby_orders'] ?? 'No orders nearby (7km radius)' : $t['enable_gps_first'] ?? 'Enable GPS first') : $t['check_back_later']; ?>
                        </p>
                    </div>
                </div>
                <?php else: while($row = $res->fetch()):
                    $st = $row['status'];
                    $orderDistance = isset($row['distance']) ? round($row['distance'], 1) : null;
                    $statusTagClass = ($st == 'pending') ? 'tag-pending' : (($st == 'accepted') ? 'tag-accepted' : (($st == 'picked_up') ? 'tag-picked' : (($st == 'cancelled') ? 'tag-cancelled' : 'tag-delivered')));
                ?>
                <div class="ultra-card">
                    <div class="card-inner">
                        <!-- Card Header -->
                        <div class="c-header">
                            <div class="price-tag">
                                #<?php echo $row['id']; ?>
                            </div>
                            <div class="time-tag <?php echo ($orderDistance && $orderDistance < 3) ? '' : 'blue'; ?>">
                                <i class="fa-regular fa-clock"></i>
                                <?php echo fmtDate($row['created_at']); ?>
                            </div>
                        </div>

                        <!-- Meta Tags -->
                        <div class="meta-tags">
                            <div class="tag-new <?php echo $statusTagClass; ?>">
                                <i class="fas fa-<?php echo getStatusIcon($st); ?>"></i>
                                <?php echo $t['st_'.$st] ?? ucfirst($st); ?>
                            </div>
                            <?php if($role == 'driver' && $st == 'pending' && $orderDistance !== null): ?>
                            <div class="tag-new tag-accepted">
                                <i class="fas fa-route"></i>
                                <?php echo $orderDistance; ?> <?php echo $t['km'] ?? 'km'; ?>
                            </div>
                            <?php endif; ?>
                            <?php if($st != 'pending' && $st != 'cancelled' && !empty($row['distance_km'])): ?>
                            <div class="tag-new tag-accepted">
                                <i class="fas fa-route"></i>
                                <?php echo number_format($row['distance_km'], 1); ?> <?php echo $t['km'] ?? 'km'; ?>
                            </div>
                            <div class="tag-new tag-pending">
                                <i class="fas fa-clock"></i>
                                ~<?php echo ceil($row['distance_km'] / 25 * 60); ?> <?php echo $t['min'] ?? 'min'; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if($st != 'pending' && $st != 'cancelled' && $st != 'delivered' && !empty($row['distance_km'])): ?>
                        <!-- Distance/Time Info -->
                        <div class="order-distance-info">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="distance-item">
                                        <div class="distance-icon route">
                                            <i class="fas fa-route"></i>
                                        </div>
                                        <div>
                                            <div class="distance-value"><?php echo number_format($row['distance_km'], 1); ?> km</div>
                                            <div class="distance-label"><?php echo $t['distance'] ?? 'Distance'; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="distance-item">
                                        <div class="distance-icon time">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <div class="distance-value">~<?php echo ceil($row['distance_km'] / 25 * 60); ?> min</div>
                                            <div class="distance-label"><?php echo $t['estimated_time'] ?? 'Est. Time'; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Route Visual -->
                        <div class="route-row">
                            <div class="visual-connector">
                                <div class="dot-circle dot-p"></div>
                                <div class="line-dashed"></div>
                                <div class="dot-circle dot-d"></div>
                            </div>
                            <div class="text-info-route">
                                <div>
                                    <div class="loc-title"><?php echo e($row['details']); ?></div>
                                    <div class="loc-sub"><?php echo e($row['customer_name']); ?></div>
                                </div>
                                <div>
                                    <div class="loc-title"><i class="fas fa-map-marker-alt text-danger me-1"></i><?php echo e($row['address']); ?></div>
                                    <?php if($row['client_phone']): ?>
                                    <div class="loc-sub">
                                        <a href="tel:+222<?php echo $row['client_phone']; ?>" class="text-primary">
                                            <i class="fas fa-phone me-1"></i>+222 <?php echo $row['client_phone']; ?>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if($role == 'customer' && $st != 'delivered' && $st != 'cancelled'): ?>
                        <!-- PIN Code for Customer -->
                        <div class="alert alert-warning mb-3 py-2">
                            <small class="d-block fw-bold mb-1"><?php echo $t['pin_label']; ?>:</small>
                            <span class="pin-box text-dark fs-5"><?php echo $row['delivery_code']; ?></span>
                            <div class="small text-muted mt-1"><?php echo $t['pin_note']; ?></div>
                        </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <?php if($role == 'driver'): ?>

                            <?php if($st == 'pending'): ?>
                            <form method="POST" onsubmit="this.querySelector('.slider-btn-container').classList.add('loading')">
                                <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="accept_order" class="slider-btn-container w-100" onclick="return confirm('<?php echo $t['confirm_accept']; ?>\n<?php echo $t['cost_per_order']; ?>: <?php echo $points_cost_per_order; ?> <?php echo $t['pts']; ?>')">
                                    <div class="slider-thumb"><i class="fa-solid fa-check"></i></div>
                                    <div class="slider-text"><?php echo $t['driver_accept']; ?></div>
                                </button>
                            </form>

                            <?php elseif($st == 'accepted' && $row['driver_id'] == $uid): ?>
                            <div class="d-flex gap-2 w-100">
                                <form method="POST" class="flex-grow-1">
                                    <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="pickup_order" class="slider-btn-container info w-100">
                                        <div class="slider-thumb"><i class="fa-solid fa-box"></i></div>
                                        <div class="slider-text"><?php echo $t['driver_pickup'] ?? 'Picked Up'; ?></div>
                                    </button>
                                </form>
                                <a href="?driver_cancel=<?php echo $row['id']; ?>" class="btn btn-outline-warning" onclick="return confirm('<?php echo $t['confirm_release'] ?? 'Release this order? Points will be refunded.'; ?>')" style="border-radius: 12px; padding: 12px 16px;">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>

                            <?php elseif($st == 'picked_up' && $row['driver_id'] == $uid): ?>
                            <form method="POST" class="pin-input-row">
                                <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                <input type="text" name="pin" placeholder="<?php echo $t['enter_pin'] ?? 'Enter PIN'; ?>" required pattern="[0-9]{4}" maxlength="4" inputmode="numeric">
                                <button type="submit" name="finish_job" class="btn btn-success px-4 py-3 fw-bold" style="border-radius: 12px;">
                                    <i class="fas fa-check-double me-1"></i><?php echo $t['driver_finish'] ?? 'Finish'; ?>
                                </button>
                            </form>
                            <?php endif; ?>

                        <?php elseif($role == 'customer'): ?>

                            <?php if($st == 'pending'): ?>
                            <a href="?customer_cancel=<?php echo $row['id']; ?>" class="slider-btn-container muted w-100" onclick="return confirm('<?php echo $t['confirm_cancel'] ?? 'Cancel this order?'; ?>')">
                                <div class="slider-thumb"><i class="fa-solid fa-times"></i></div>
                                <div class="slider-text"><?php echo $t['cancel_order'] ?? 'Cancel Order'; ?></div>
                            </a>
                            <?php elseif($st == 'accepted'): ?>
                            <div class="d-flex gap-2 w-100">
                                <div class="slider-btn-container info flex-grow-1" onclick="showOrderTracking(<?php echo htmlspecialchars(json_encode($row)); ?>)" style="cursor:pointer;">
                                    <div class="slider-thumb"><i class="fa-solid fa-location-dot"></i></div>
                                    <div class="slider-text"><?php echo $t['track_order'] ?? 'Track'; ?></div>
                                </div>
                                <a href="?customer_cancel=<?php echo $row['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('<?php echo $t['confirm_cancel'] ?? 'Cancel this order?'; ?>')" style="border-radius: 12px; padding: 12px 16px;">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                            <?php elseif($st == 'picked_up'): ?>
                            <div class="slider-btn-container info w-100" onclick="showOrderTracking(<?php echo htmlspecialchars(json_encode($row)); ?>)" style="cursor:pointer;">
                                <div class="slider-thumb"><i class="fa-solid fa-location-dot"></i></div>
                                <div class="slider-text"><?php echo $t['track_order'] ?? 'Track Order'; ?></div>
                            </div>
                            <?php elseif($st == 'delivered' && empty($row['rated_by_customer'])): ?>
                            <form method="POST" class="order-actions-row">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <select name="score" class="form-select" required style="border-radius: 12px;">
                                    <option value=""><?php echo $t['rate_driver'] ?? 'Rate Driver'; ?></option>
                                    <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                    <option value="4">⭐⭐⭐⭐ (4)</option>
                                    <option value="3">⭐⭐⭐ (3)</option>
                                    <option value="2">⭐⭐ (2)</option>
                                    <option value="1">⭐ (1)</option>
                                </select>
                                <button type="submit" name="submit_rating" class="btn btn-warning fw-bold" style="border-radius: 12px;">
                                    <i class="fas fa-star"></i>
                                </button>
                            </form>
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; endif; ?>
            </div>

            <!-- GLASS NAVIGATION BAR -->
            <?php if($role == 'driver'): ?>
            <!-- Driver Navigation -->
            <nav class="glass-nav">
                <a href="index.php" class="nav-icon active"><i class="fa-solid fa-house"></i></a>
                <a href="?settings=1" class="nav-icon"><i class="fa-solid fa-chart-simple"></i></a>

                <div class="nav-center-btn <?php echo $u['is_online'] ? 'online' : 'offline'; ?>" onclick="document.getElementById('onlineSwitch').checked = !document.getElementById('onlineSwitch').checked; document.getElementById('onlineToggleForm').submit();" title="<?php echo $u['is_online'] ? ($t['go_offline'] ?? 'Go Offline') : ($t['go_online'] ?? 'Go Online'); ?>">
                    <i class="fa-solid fa-power-off"></i>
                </div>

                <a href="?settings=1" class="nav-icon"><i class="fa-solid fa-gear"></i></a>
                <a href="?settings=1" class="nav-icon"><i class="fa-regular fa-user"></i></a>
            </nav>
            <?php elseif($role == 'customer'): ?>
            <!-- Customer Navigation -->
            <nav class="glass-nav">
                <a href="index.php" class="nav-icon active"><i class="fa-solid fa-house"></i></a>
                <a href="#" class="nav-icon" onclick="document.getElementById('newOrderForm').scrollIntoView({behavior: 'smooth'})"><i class="fa-solid fa-plus"></i></a>

                <div class="nav-center-btn">
                    <i class="fa-solid fa-box"></i>
                </div>

                <a href="?settings=1" class="nav-icon"><i class="fa-solid fa-gear"></i></a>
                <a href="?settings=1" class="nav-icon"><i class="fa-regular fa-user"></i></a>
            </nav>
            <?php else: ?>
            <!-- Admin Navigation -->
            <nav class="glass-nav">
                <a href="index.php" class="nav-icon active"><i class="fa-solid fa-house"></i></a>
                <a href="?settings=1" class="nav-icon"><i class="fa-solid fa-chart-bar"></i></a>

                <div class="nav-center-btn" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                    <i class="fa-solid fa-crown"></i>
                </div>

                <a href="?settings=1" class="nav-icon"><i class="fa-solid fa-gear"></i></a>
                <a href="?settings=1" class="nav-icon"><i class="fa-regular fa-user"></i></a>
            </nav>
            <?php endif; ?>

        <?php endif; ?>

        <?php if($role == 'customer'): ?>
        <!-- Order Tracking Modal -->
        <div class="modal fade" id="orderTrackingModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"><i class="fas fa-map-marker-alt me-2"></i><?php echo $t['track_order'] ?? 'Track Order'; ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Order Progress -->
                        <div class="order-progress mb-4">
                            <div class="progress-track">
                                <div class="progress-step completed" id="step-pending">
                                    <div class="step-icon"><i class="fas fa-receipt"></i></div>
                                    <div class="step-label"><?php echo $t['st_pending'] ?? 'Pending'; ?></div>
                                </div>
                                <div class="progress-step" id="step-accepted">
                                    <div class="step-icon"><i class="fas fa-truck"></i></div>
                                    <div class="step-label"><?php echo $t['st_accepted'] ?? 'Accepted'; ?></div>
                                </div>
                                <div class="progress-step" id="step-picked_up">
                                    <div class="step-icon"><i class="fas fa-box"></i></div>
                                    <div class="step-label"><?php echo $t['st_picked_up'] ?? 'Picked Up'; ?></div>
                                </div>
                                <div class="progress-step" id="step-delivered">
                                    <div class="step-icon"><i class="fas fa-check-double"></i></div>
                                    <div class="step-label"><?php echo $t['st_delivered'] ?? 'Delivered'; ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Driver Info -->
                        <div class="driver-info-card bg-light rounded-3 p-3 mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="driver-avatar-lg" id="tracking-driver-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="mb-0 fw-bold" id="tracking-driver-name">Driver</h6>
                                        <span class="badge bg-success" id="tracking-verified-badge" style="display:none;">
                                            <i class="fas fa-check-circle"></i> <?php echo $t['verified'] ?? 'Verified'; ?>
                                        </span>
                                    </div>
                                    <div class="text-warning small" id="tracking-driver-rating">
                                        <i class="fas fa-star"></i> 5.0
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="#" id="tracking-call-btn" class="btn btn-success btn-sm w-100 rounded-pill">
                                    <i class="fas fa-phone me-1"></i> <?php echo $t['call_driver'] ?? 'Call'; ?>
                                </a>
                            </div>
                        </div>

                        <!-- Order Details -->
                        <div class="order-details-card border rounded-3 p-3">
                            <h6 class="fw-bold mb-2"><i class="fas fa-info-circle text-primary me-2"></i><?php echo $t['order_details'] ?? 'Order Details'; ?></h6>
                            <p class="mb-2 small" id="tracking-order-details">-</p>
                            <p class="mb-0 small text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i> <span id="tracking-order-address">-</span></p>
                        </div>

                        <!-- Time Info -->
                        <div class="mt-3 text-center">
                            <small class="text-muted" id="tracking-time-info"></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/app.js"></script>
<script>
// PHP-based translations and initialization
const AppTranslations = {
    // Status translations
    st_pending: '<?php echo $t['st_pending'] ?? 'Pending'; ?>',
    st_accepted: '<?php echo $t['st_accepted'] ?? 'Accepted'; ?>',
    st_picked_up: '<?php echo $t['st_picked_up'] ?? 'Picked Up'; ?>',
    st_delivered: '<?php echo $t['st_delivered'] ?? 'Delivered'; ?>',
    st_cancelled: '<?php echo $t['st_cancelled'] ?? 'Cancelled'; ?>',

    // Common translations
    order_number: '<?php echo $t['order_number'] ?? 'Order'; ?>',
    close: '<?php echo $t['close'] ?? 'Close'; ?>',
    success: '<?php echo $t['success'] ?? 'Success'; ?>',
    error: '<?php echo $t['error'] ?? 'Error'; ?>',
    try_again: '<?php echo $t['try_again'] ?? 'Please try again'; ?>',
    accept: '<?php echo $t['accept'] ?? 'Accept'; ?>',
    decline: '<?php echo $t['decline'] ?? 'Decline'; ?>',

    // GPS translations
    geolocation_not_supported: '<?php echo $t['geolocation_not_supported'] ?? 'Geolocation is not supported by your browser'; ?>',
    location_error: '<?php echo $t['location_error'] ?? 'Error getting location'; ?>',
    location_denied: '<?php echo $t['location_denied'] ?? 'Location access denied. Please enable GPS.'; ?>',
    gps_disabled: '<?php echo $t['gps_disabled'] ?? 'GPS Disabled'; ?>',
    gps_enabled: '<?php echo $t['gps_enabled'] ?? 'GPS Enabled'; ?>',
    gps_driver_note: '<?php echo $t['gps_driver_note'] ?? 'Enable GPS to see nearby orders'; ?>',
    updating_location: '<?php echo $t['updating_location'] ?? 'Updating location...'; ?>',
    location_updated: '<?php echo $t['location_updated'] ?? 'Location updated'; ?>',

    // Order translations
    new_order_nearby: '<?php echo $t['new_order_nearby'] ?? 'New Order Nearby!'; ?>',
    new_order_alert: '<?php echo $t['new_order_alert'] ?? 'New Order Alert'; ?>',
    new_order: '<?php echo $t['new_order'] ?? 'new order(s)'; ?>',
    order_accepted: '<?php echo $t['order_accepted'] ?? 'Order accepted!'; ?>',
    order_status_changed: '<?php echo $t['order_status_changed'] ?? 'Order Status Changed'; ?>',
    no_phone: '<?php echo $t['no_phone'] ?? 'No phone'; ?>',

    // Time/Distance translations
    km: '<?php echo $t['km'] ?? 'km'; ?>',
    min: '<?php echo $t['min'] ?? 'min'; ?>',
    driver_assigned: '<?php echo $t['driver_assigned'] ?? 'Driver assigned'; ?>'
};

const AppConfig = {
    lang: '<?php echo $lang; ?>',
    userRole: '<?php echo isset($_SESSION['user']) ? $role : ''; ?>',
    isLoggedIn: <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>,
    hasExistingLocation: <?php echo (isset($_SESSION['user']) && $role === 'driver' && !empty($u['last_lat']) && !empty($u['last_lng'])) ? 'true' : 'false'; ?>
};

// Override functions that need translations
window.getPickupLocation = function() {
    _getPickupLocation(AppTranslations, AppConfig.lang);
};

window.toggleDriverGPS = function() {
    _toggleDriverGPS(AppTranslations);
};

window.acceptOrderFromBubble = function(orderId, btn) {
    _acceptOrderFromBubble(orderId, btn, AppTranslations);
};

window.showOrderTracking = function(order) {
    _showOrderTracking(order, AppTranslations);
};

<?php if(isset($_SESSION['user']) && $role === 'driver'): ?>
// Initialize driver features
initDriverFeatures(AppTranslations, AppConfig.hasExistingLocation);
<?php endif; ?>

<?php if(isset($_SESSION['user']) && !isset($_GET['settings'])): ?>
// Initialize real-time polling
initRealtimePolling(AppConfig.userRole, AppTranslations);
<?php endif; ?>
</script>
</body>
</html>
