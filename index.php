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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $t['app_name']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.<?php echo $lang=='ar'?'rtl.':''; ?>min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: #eef2ff;
            --secondary-color: #818cf8;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --bg-color: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --card-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --transition-fast: 0.15s ease;
            --transition-normal: 0.3s ease;
            --border-radius: 16px;
            --border-radius-sm: 10px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--bg-color) 0%, #e2e8f0 100%);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .animate-fadeInUp { animation: fadeInUp 0.5s ease forwards; }
        .animate-fadeInDown { animation: fadeInDown 0.5s ease forwards; }
        .animate-fadeIn { animation: fadeIn 0.4s ease forwards; }
        .animate-slideInRight { animation: slideInRight 0.4s ease forwards; }

        /* Login Card */
        .login-card {
            max-width: 440px;
            margin: 30px auto;
            border-radius: var(--border-radius);
            border: none;
            animation: fadeInUp 0.6s ease;
            backdrop-filter: blur(10px);
        }

        /* App Navbar */
        .app-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: var(--transition-normal);
        }
        .app-navbar.scrolled {
            box-shadow: var(--card-shadow);
        }

        /* Cards */
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: var(--border-radius);
            border: none;
            transition: transform var(--transition-normal), box-shadow var(--transition-normal);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-shadow-hover);
        }

        .content-card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            background: white;
            overflow: hidden;
            transition: transform var(--transition-normal), box-shadow var(--transition-normal);
        }
        .content-card:hover {
            box-shadow: var(--card-shadow-lg);
        }

        .stats-box {
            background: white;
            border-radius: var(--border-radius-sm);
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: transform var(--transition-normal), box-shadow var(--transition-normal);
            position: relative;
            overflow: hidden;
        }
        .stats-box::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transition: transform var(--transition-normal);
        }
        .stats-box:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-lg);
        }
        .stats-box:hover::after {
            transform: scaleX(1);
        }
        .stats-box i {
            transition: transform var(--transition-normal);
        }
        .stats-box:hover i {
            transform: scale(1.1);
        }

        /* Status Badges */
        .badge-pending {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border: none;
            font-weight: 600;
        }
        .badge-accepted {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            border: none;
            font-weight: 600;
        }
        .badge-delivered {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border: none;
            font-weight: 600;
        }
        .badge-cancelled {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border: none;
            font-weight: 600;
        }

        /* PIN Box */
        .pin-box {
            font-family: 'Courier New', monospace;
            letter-spacing: 6px;
            font-weight: bold;
            font-size: 1.4rem;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            padding: 10px 16px;
            border-radius: 8px;
            user-select: all;
            display: inline-block;
            border: 2px dashed #f59e0b;
            transition: transform var(--transition-fast);
        }
        .pin-box:hover {
            transform: scale(1.02);
        }

        /* Buttons */
        .btn {
            font-weight: 600;
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
        }
        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        .btn:active::after {
            width: 300px;
            height: 300px;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5);
        }
        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #34d399);
            border: none;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
        }
        .btn-warning {
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
        }
        .btn-warning:hover {
            transform: translateY(-2px);
        }
        .btn-outline-primary:hover, .btn-outline-success:hover {
            transform: translateY(-2px);
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: var(--border-radius-sm);
            border: 2px solid #e2e8f0;
            padding: 12px 16px;
            transition: all var(--transition-normal);
            font-size: 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }
        .form-floating > .form-control {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }
        .form-floating > label {
            padding: 1rem 1rem;
            color: var(--text-secondary);
        }

        /* Input Group */
        .input-group-text {
            border: 2px solid #e2e8f0;
            border-right: none;
        }
        .input-group .form-control {
            border-left: none;
        }
        .input-group:focus-within .input-group-text {
            border-color: var(--primary-color);
        }
        .input-group:focus-within .form-control {
            border-color: var(--primary-color);
        }

        /* Auth form toggle */
        .auth-toggle {
            display: flex;
            background: #e5e7eb;
            border-radius: var(--border-radius-sm);
            padding: 5px;
            margin-bottom: 24px;
        }
        .auth-toggle button {
            flex: 1;
            border: none;
            background: transparent;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            transition: all var(--transition-normal);
            color: var(--text-secondary);
        }
        .auth-toggle button:hover:not(.active) {
            background: rgba(255, 255, 255, 0.5);
        }
        .auth-toggle button.active {
            background: white;
            box-shadow: var(--card-shadow);
            color: var(--primary-color);
        }
        .auth-form {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        .auth-form.active {
            display: block;
        }

        /* Modals */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--card-shadow-lg);
        }
        .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 24px;
        }
        .modal-body {
            padding: 24px;
        }
        .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 16px 24px;
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            padding: 16px;
        }
        .table tbody tr {
            transition: background-color var(--transition-fast);
        }
        .table tbody tr:hover {
            background-color: #f8fafc;
        }
        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-actions .btn {
            margin: 2px;
            padding: 6px 10px;
        }

        /* Notification toast */
        .notification-toast {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            max-width: 380px;
        }
        .notification-toast.rtl {
            right: auto;
            left: 20px;
        }
        .notification-toast .toast {
            border-radius: var(--border-radius-sm);
            border: none;
            box-shadow: var(--card-shadow-lg);
            animation: slideInRight 0.4s ease;
        }

        /* Nav Tabs */
        .nav-tabs {
            border-bottom: 2px solid #e2e8f0;
            gap: 8px;
        }
        .nav-tabs .nav-link {
            border: none;
            border-radius: var(--border-radius-sm) var(--border-radius-sm) 0 0;
            padding: 12px 20px;
            color: var(--text-secondary);
            font-weight: 600;
            transition: all var(--transition-normal);
            position: relative;
        }
        .nav-tabs .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-color);
            transform: scaleX(0);
            transition: transform var(--transition-normal);
            border-radius: 3px 3px 0 0;
        }
        .nav-tabs .nav-link:hover {
            background: var(--primary-light);
            color: var(--primary-color);
        }
        .nav-tabs .nav-link.active {
            background: var(--primary-light);
            color: var(--primary-color);
        }
        .nav-tabs .nav-link.active::after {
            transform: scaleX(1);
        }

        /* Notification badge pulse */
        .pulse-badge {
            animation: pulse 2s infinite;
        }

        /* Settings button */
        .settings-btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-normal);
        }
        .settings-btn:hover {
            transform: rotate(30deg);
        }
        .settings-btn.text-danger:hover {
            transform: scale(1.1) rotate(0);
        }

        /* Points Badge */
        .points-badge {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            animation: bounce 2s infinite;
        }

        /* Loading state for buttons */
        .btn.loading {
            pointer-events: none;
            position: relative;
            color: transparent !important;
        }
        .btn.loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Empty state */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }
        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }
        .empty-state h5 {
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* Order row animations */
        .order-row {
            animation: fadeInUp 0.4s ease forwards;
            opacity: 0;
        }
        .order-row:nth-child(1) { animation-delay: 0.05s; }
        .order-row:nth-child(2) { animation-delay: 0.1s; }
        .order-row:nth-child(3) { animation-delay: 0.15s; }
        .order-row:nth-child(4) { animation-delay: 0.2s; }
        .order-row:nth-child(5) { animation-delay: 0.25s; }

        /* Mobile improvements */
        @media (max-width: 768px) {
            body {
                background: var(--bg-color);
            }
            .login-card {
                margin: 15px;
                border-radius: var(--border-radius-sm);
            }
            .stats-box {
                padding: 16px;
            }
            .stats-box h3 {
                font-size: 1.4rem;
            }
            .table-actions .btn {
                padding: 0.3rem 0.5rem;
                font-size: 0.8rem;
            }
            .nav-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 2px;
            }
            .nav-tabs .nav-link {
                padding: 10px 14px;
                font-size: 0.85rem;
                white-space: nowrap;
            }
            .content-card {
                border-radius: var(--border-radius-sm);
            }
            .form-control, .form-select {
                padding: 10px 14px;
            }
            .btn {
                padding: 10px 16px;
            }
            .pin-box {
                font-size: 1.2rem;
                letter-spacing: 4px;
                padding: 8px 12px;
            }
        }

        /* Dark scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Language switcher */
        .lang-switcher .btn {
            padding: 6px 14px;
            font-size: 0.85rem;
        }
        .lang-switcher .btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Demo accounts box */
        .demo-box {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border: 1px dashed #cbd5e1;
            border-radius: var(--border-radius-sm);
            font-size: 0.85rem;
        }

        /* Profile avatar */
        .profile-avatar {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
            position: relative;
            overflow: hidden;
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-avatar-edit {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.6);
            color: white;
            font-size: 0.7rem;
            padding: 4px;
            cursor: pointer;
            opacity: 0;
            transition: opacity var(--transition-normal);
        }
        .profile-avatar:hover .profile-avatar-edit {
            opacity: 1;
        }

        /* Online toggle switch */
        .online-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: var(--border-radius-sm);
            margin-bottom: 16px;
        }
        .online-toggle .form-check-input {
            width: 50px;
            height: 26px;
            cursor: pointer;
        }
        .online-toggle .form-check-input:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        .online-status {
            font-weight: 600;
        }
        .online-status.online {
            color: var(--success-color);
        }
        .online-status.offline {
            color: var(--text-secondary);
        }

        /* Serial number badge */
        .serial-badge {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            color: var(--primary-color);
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        /* Verified badge */
        .verified-badge {
            position: absolute;
            bottom: -2px;
            right: -2px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
        }
        .verified-badge-sm {
            width: 20px;
            height: 20px;
            font-size: 0.6rem;
            border-width: 2px;
        }
        .avatar-with-badge {
            position: relative;
            display: inline-block;
        }
        .not-verified-badge {
            position: absolute;
            bottom: -2px;
            right: -2px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.4);
        }

        /* Phone verification badge */
        .phone-verified {
            color: var(--success-color);
        }
        .phone-not-verified {
            color: var(--warning-color);
        }

        /* Stats grid for driver/client */
        .mini-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 16px;
        }
        .mini-stat {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 12px;
            border-radius: var(--border-radius-sm);
            text-align: center;
        }
        .mini-stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .mini-stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        /* Rating stars */
        .rating-stars {
            color: #fbbf24;
        }
        .rating-value {
            font-weight: 700;
            margin-left: 4px;
        }

        /* Order status picked_up */
        .badge-picked_up {
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            color: #3730a3;
            border: none;
            font-weight: 600;
        }

        /* Section divider */
        .section-divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        .section-divider::before { margin-right: 16px; }
        .section-divider::after { margin-left: 16px; }

        /* Footer */
        .app-footer {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

<!-- Audio for notifications -->
<audio id="notificationSound" preload="auto">
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleAMCP6XS2qNrCwJEoc/YoWkKAU2gzNadZggBVJ/J1JhkBwFen8bRlWMGAWSexs+SYQUBap/Ez49fBAFwn8LNjF0DABZ4tsW/fEIAAneyyLt3PgADd7DHuHY8AAR2r8W2dDoABXatw7RzOAAFdazCtHI3AAZ0q8GsaTIABnOqv6tnLwAGc6m+qGUtAAdzqL2mZCwAB3KnvKRjKgAHcqW6o2IpAAdxpLmhYCgAB3GjuJ9fJwAHcKK3nl4mAAdwobacXSUAB2+gtZtcJAAHb5+0mVsjAAdun7OYWiIAB26espdZIQAHbZ2xlVggAAdsm6+SVB0ABmqZrY9RGgAFZ5aqi04XAAVllaeHTxQABWKSpIZNEQAEYI+hg0oOAANdjaB/RwwAA1qLnnxFCgACWImceEMIAAJVhpl1QAYAAVKDlnI9BAABUICTbzoCAA=='"/>
</audio>

<!-- Notification Toast Container -->
<div id="notificationContainer" class="notification-toast <?php echo $dir == 'rtl' ? 'rtl' : ''; ?>"></div>

<?php if (!isset($_SESSION['user'])): ?>
    <!-- ================= LOGIN/REGISTER SCREEN ================= -->
    <div class="container py-4">
        <div class="card login-card content-card shadow-lg">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <img src="logo.png" alt="<?php echo $t['app_name']; ?>" style="max-width: 100px; height: auto;" onerror="this.style.display='none'">
                    </div>
                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);"><?php echo $t['app_name']; ?></h3>
                    <p class="text-muted small mb-3"><?php echo $t['app_desc']; ?></p>
                    <div class="btn-group btn-group-sm lang-switcher" role="group">
                        <a href="?lang=ar" class="btn btn-outline-secondary <?php echo $lang=='ar'?'active':''; ?>">العربية</a>
                        <a href="?lang=fr" class="btn btn-outline-secondary <?php echo $lang=='fr'?'active':''; ?>">Français</a>
                    </div>
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
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary"><?php echo $t['login_identifier'] ?? 'Phone or Username'; ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="<?php echo $t['login_identifier_ph'] ?? 'Phone number or username'; ?>" required autocomplete="username">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary"><?php echo $t['pass_ph']; ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="<?php echo $t['pass_ph']; ?>" required autocomplete="current-password">
                        </div>
                        <small class="text-muted"><?php echo $t['new_users_phone_password'] ?? 'New users: use your phone number as password'; ?></small>
                    </div>
                    <button name="do_login" class="btn btn-primary w-100 py-3 fw-bold rounded-pill">
                        <?php echo $t['btn_login']; ?> <i class="fas fa-arrow-<?php echo ($lang=='ar')?'left':'right'; ?> ms-2"></i>
                    </button>
                </form>

                <!-- Register Form (Phone-Only) -->
                <form method="POST" id="registerForm" class="auth-form" onsubmit="this.querySelector('button').classList.add('loading')">
                    <div class="alert alert-info border-0 small mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo $t['register_phone_info'] ?? 'Register with your phone number. You can complete your profile later.'; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary"><?php echo $t['phone_ph']; ?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                            <input type="tel" name="reg_phone" class="form-control" placeholder="<?php echo $t['phone_example'] ?? '06XXXXXXXX'; ?>" required minlength="8" inputmode="tel">
                        </div>
                        <small class="text-muted"><?php echo $t['phone_password_note'] ?? 'Your phone number will be your initial password'; ?></small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary"><?php echo $t['full_name_ph']; ?> <span class="text-muted fw-normal">(<?php echo $t['optional']; ?>)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="reg_full_name" class="form-control" placeholder="<?php echo $t['full_name_ph']; ?>">
                        </div>
                    </div>

                    <button name="do_register" class="btn btn-success w-100 py-3 fw-bold rounded-pill">
                        <?php echo $t['btn_register']; ?> <i class="fas fa-user-plus ms-2"></i>
                    </button>

                    <div class="text-center mt-3">
                        <small class="text-muted"><?php echo $t['complete_profile_later'] ?? 'You can set a custom username and password after registration'; ?></small>
                    </div>
                </form>

                <div class="mt-4 text-center demo-box p-3">
                    <div class="fw-bold text-secondary mb-2"><i class="fas fa-info-circle me-1"></i> <?php echo $t['demo_accounts']; ?></div>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <span class="badge bg-danger">admin / 123</span>
                        <span class="badge bg-info">driver / 123</span>
                        <span class="badge bg-success">client / 123</span>
                    </div>
                </div>
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
                <img src="logo.png" alt="<?php echo $t['app_name']; ?>" style="height: 40px; width: auto;">
                <span class="text-primary d-none d-sm-inline"><?php echo $t['app_name']; ?></span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <?php if($role == 'driver'): ?>
                <span class="badge bg-warning text-dark px-3 py-2" id="pointsBadge">
                    <i class="fas fa-coins"></i> <span id="currentPoints"><?php echo $u['points']; ?></span>
                </span>
                <?php endif; ?>
                <div class="d-none d-md-block text-end lh-1 me-2">
                    <span class="d-block fw-bold small"><?php echo e($u['full_name'] ?: $u['username']); ?></span>
                    <span class="badge bg-secondary rounded-pill" style="font-size:0.6rem"><?php echo strtoupper($role); ?></span>
                </div>
                <a href="?settings=1" class="btn btn-light settings-btn rounded-circle shadow-sm" title="<?php echo $t['settings']; ?>">
                    <i class="fas fa-cog"></i>
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
                                            <div class="profile-avatar mb-3">
                                                <?php
                                                $avatarUrl = getAvatarUrl($u);
                                                if ($avatarUrl): ?>
                                                    <img src="<?php echo e($avatarUrl); ?>" alt="Avatar">
                                                <?php else: ?>
                                                    <span style="font-size: 2rem;"><?php echo getUserInitials($u); ?></span>
                                                <?php endif; ?>
                                                <div class="profile-avatar-edit">
                                                    <i class="fas fa-camera"></i> <?php echo $t['change_photo'] ?? 'Change'; ?>
                                                </div>
                                            </div>
                                            <?php if($role == 'driver' && !empty($u['is_verified'])): ?>
                                                <div class="verified-badge" title="<?php echo $t['driver_verified'] ?? 'Verified Driver'; ?>">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                    <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="this.form.submit()">

                                    <h5 class="fw-bold mb-1"><?php echo e($u['full_name'] ?: $u['username']); ?></h5>
                                    <span class="badge bg-primary rounded-pill px-3"><?php echo $t[$role]; ?></span>

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
                                            <i class="fas fa-coins me-1"></i> <?php echo $u['points']; ?> <?php echo $t['pts']; ?>
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
                                    $clientStats = getClientStats($conn, $u['username']);
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
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stats-box text-center">
                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                        <h3 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) FROM users1 WHERE role='customer'")->fetchColumn(); ?></h3>
                        <small class="text-muted"><?php echo $t['customer']; ?>s</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-box text-center">
                        <i class="fas fa-motorcycle fa-2x text-info mb-2"></i>
                        <h3 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) FROM users1 WHERE role='driver'")->fetchColumn(); ?></h3>
                        <small class="text-muted"><?php echo $t['driver']; ?>s</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-box text-center">
                        <i class="fas fa-box fa-2x text-success mb-2"></i>
                        <h3 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) FROM orders1")->fetchColumn(); ?></h3>
                        <small class="text-muted"><?php echo $t['total_orders']; ?></small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stats-box text-center">
                        <i class="fas fa-check-circle fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) FROM users1 WHERE role='driver' AND status='active'")->fetchColumn(); ?></h3>
                        <small class="text-muted"><?php echo $t['active_drivers']; ?></small>
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
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo $t['username']; ?></th>
                                        <th class="d-none d-md-table-cell"><?php echo $t['phone_ph']; ?></th>
                                        <th><?php echo $t['status']; ?></th>
                                        <th class="text-end"><?php echo $t['action']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $users = $conn->query("SELECT * FROM users1 WHERE role='customer' ORDER BY id DESC");
                                    while($user = $users->fetch()):
                                    ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td>
                                            <strong><?php echo e($user['username']); ?></strong>
                                            <?php if($user['full_name']): ?>
                                            <br><small class="text-muted"><?php echo e($user['full_name']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="d-none d-md-table-cell"><?php echo e($user['phone']); ?></td>
                                        <td>
                                            <?php if($user['status'] == 'active'): ?>
                                                <span class="badge bg-success"><?php echo $t['active']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><?php echo $t['banned']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end table-actions">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?toggle_ban=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-<?php echo $user['status']=='active'?'warning':'success'; ?>" onclick="return confirm('Confirm?')">
                                                <i class="fas fa-<?php echo $user['status']=='active'?'ban':'check'; ?>"></i>
                                            </a>
                                            <a href="?delete_user=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete permanently?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- DRIVERS TAB -->
                <div class="tab-pane fade" id="drivers">
                    <div class="card content-card">
                        <div class="card-header bg-white py-3 d-flex justify-content-between flex-wrap gap-2">
                            <h5 class="mb-0"><i class="fas fa-motorcycle text-info"></i> <?php echo $t['manage_drivers']; ?></h5>
                            <button class="btn btn-sm btn-info text-white" onclick="showAddUserModal('driver')">
                                <i class="fas fa-plus"></i> <?php echo $t['add_user']; ?>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th><?php echo $t['driver'] ?? 'Driver'; ?></th>
                                        <th><?php echo $t['phone_ph']; ?></th>
                                        <th><?php echo $t['points']; ?></th>
                                        <th><?php echo $t['verification'] ?? 'Verification'; ?></th>
                                        <th><?php echo $t['status']; ?></th>
                                        <th class="text-end"><?php echo $t['action']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $drivers = $conn->query("SELECT * FROM users1 WHERE role='driver' ORDER BY id DESC");
                                    while($driver = $drivers->fetch()):
                                        $driverAvatarUrl = getAvatarUrl($driver);
                                        $isVerified = !empty($driver['is_verified']);
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-with-badge">
                                                    <div style="width:40px;height:40px;border-radius:50%;background:<?php echo getAvatarColor('driver'); ?>;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:0.9rem;overflow:hidden;">
                                                        <?php if($driverAvatarUrl): ?>
                                                            <img src="<?php echo e($driverAvatarUrl); ?>" style="width:100%;height:100%;object-fit:cover;">
                                                        <?php else: ?>
                                                            <?php echo getUserInitials($driver); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if($isVerified): ?>
                                                        <div class="verified-badge verified-badge-sm" title="<?php echo $t['driver_verified'] ?? 'Verified Driver'; ?>">
                                                            <i class="fas fa-check"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <strong><?php echo e($driver['username']); ?></strong>
                                                    <?php if($driver['full_name']): ?>
                                                    <br><small class="text-muted"><?php echo e($driver['full_name']); ?></small>
                                                    <?php endif; ?>
                                                    <?php if(!empty($driver['serial_no'])): ?>
                                                    <br><small class="text-primary"><?php echo e($driver['serial_no']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if(!empty($driver['phone'])): ?>
                                                <span class="text-dark"><?php echo e($driver['phone']); ?></span>
                                                <?php if($driver['phone_verified']): ?>
                                                    <i class="fas fa-check-circle text-success ms-1" title="<?php echo $t['phone_verified'] ?? 'Verified'; ?>"></i>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-warning text-dark"><?php echo $driver['points']; ?> pts</span></td>
                                        <td>
                                            <?php if($isVerified): ?>
                                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i><?php echo $t['verified'] ?? 'Verified'; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i><?php echo $t['pending_verification'] ?? 'Pending'; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($driver['status'] == 'active'): ?>
                                                <span class="badge bg-success"><?php echo $t['active']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><?php echo $t['banned']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end table-actions">
                                            <!-- Verify/Unverify Button -->
                                            <a href="?toggle_verify=<?php echo $driver['id']; ?>" class="btn btn-sm btn-<?php echo $isVerified ? 'success' : 'outline-success'; ?>" onclick="return confirm('<?php echo $isVerified ? ($t['confirm_unverify'] ?? 'Remove verification?') : ($t['confirm_verify'] ?? 'Verify this driver?'); ?>')" title="<?php echo $isVerified ? ($t['unverify'] ?? 'Remove Verification') : ($t['verify_driver'] ?? 'Verify Driver'); ?>">
                                                <i class="fas fa-<?php echo $isVerified ? 'certificate' : 'user-check'; ?>"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editUser(<?php echo htmlspecialchars(json_encode($driver)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?toggle_ban=<?php echo $driver['id']; ?>" class="btn btn-sm btn-outline-<?php echo $driver['status']=='active'?'warning':'success'; ?>" onclick="return confirm('Confirm?')">
                                                <i class="fas fa-<?php echo $driver['status']=='active'?'ban':'check'; ?>"></i>
                                            </a>
                                            <a href="?delete_user=<?php echo $driver['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete permanently?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
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
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo $t['order_details']; ?></th>
                                        <th class="d-none d-md-table-cell">Customer</th>
                                        <th><?php echo $t['status']; ?></th>
                                        <th>PIN</th>
                                        <th class="text-end"><?php echo $t['action']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $orders = $conn->query("SELECT o.*, u.username as driver_name FROM orders1 o LEFT JOIN users1 u ON o.driver_id=u.id ORDER BY o.id DESC LIMIT 100");
                                    while($order = $orders->fetch()):
                                        $st = $order['status'];
                                        $badge = ($st=='pending')?'badge-pending':(($st=='accepted')?'badge-accepted':(($st=='cancelled')?'badge-cancelled':'badge-delivered'));
                                    ?>
                                    <tr>
                                        <td><?php echo $order['id']; ?></td>
                                        <td>
                                            <div class="fw-bold text-truncate" style="max-width:150px;"><?php echo e($order['details']); ?></div>
                                            <small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?php echo e($order['address']); ?></small>
                                        </td>
                                        <td class="d-none d-md-table-cell"><?php echo e($order['customer_name']); ?></td>
                                        <td><span class="badge <?php echo $badge; ?>"><?php echo $t['st_'.$st]; ?></span></td>
                                        <td><code><?php echo $order['delivery_code']; ?></code></td>
                                        <td class="text-end table-actions">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if($st == 'pending' || $st == 'accepted'): ?>
                                                <a href="?cancel_order=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-warning" onclick="return confirm('Cancel this order?')">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="?delete_order=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete permanently?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- POINTS TAB -->
                <div class="tab-pane fade" id="points">
                    <div class="card content-card" style="max-width: 500px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4"><i class="fas fa-coins text-warning"></i> <?php echo $t['add_points']; ?></h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Driver</label>
                                    <select name="driver_id" class="form-select" required>
                                        <option value="">Select Driver</option>
                                        <?php
                                        $ds = $conn->query("SELECT id, username, points FROM users1 WHERE role='driver' ORDER BY username");
                                        while($d=$ds->fetch()) {
                                            echo "<option value='{$d['id']}'>{$d['username']} (Current: {$d['points']} pts)</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Amount</label>
                                    <input type="number" name="amount" class="form-control" placeholder="20" min="1" required>
                                </div>
                                <button name="recharge" class="btn btn-warning w-100 fw-bold">
                                    <i class="fas fa-plus"></i> Add Points
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="admin_edit_user" class="btn btn-primary">Update</button>
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="admin_edit_order" class="btn btn-primary">Update Order</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- ================= DRIVER/CUSTOMER DASHBOARD ================= -->
            <div class="row g-4">
                <!-- LEFT COLUMN -->
                <div class="col-lg-4 order-lg-last">
                    <?php if($role == 'driver'): ?>
                    <!-- Online/Offline Toggle -->
                    <div class="online-toggle mb-3">
                        <form method="POST" id="onlineToggleForm" class="d-flex align-items-center gap-3 w-100">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="onlineSwitch" name="is_online" value="1"
                                    <?php echo $u['is_online'] ? 'checked' : ''; ?>
                                    onchange="document.getElementById('onlineToggleForm').submit();"
                                    <?php echo !isPhoneVerified($u) ? 'disabled' : ''; ?>>
                            </div>
                            <div class="flex-grow-1">
                                <span class="online-status <?php echo $u['is_online'] ? 'online' : 'offline'; ?>">
                                    <?php echo $u['is_online'] ? ($t['online'] ?? 'Online') : ($t['offline'] ?? 'Offline'); ?>
                                </span>
                                <?php if(!isPhoneVerified($u)): ?>
                                    <div class="small text-warning"><i class="fas fa-exclamation-circle"></i> <?php echo $t['verify_phone_first'] ?? 'Add phone number to go online'; ?></div>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="toggle_online" value="1">
                        </form>
                    </div>

                    <div class="card stat-card mb-3">
                        <div class="card-body text-center p-4">
                            <h6 class="opacity-75 mb-2"><?php echo $t['balance']; ?></h6>
                            <h1 class="display-4 fw-bold mb-0" id="driverPoints"><?php echo $u['points']; ?></h1>
                            <span class="opacity-75"><?php echo $t['points']; ?></span>

                            <?php if(!empty($u['rating'])): ?>
                            <div class="mt-2 opacity-75">
                                <span class="rating-stars"><i class="fas fa-star"></i></span>
                                <span class="rating-value"><?php echo number_format($u['rating'], 1); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if($u['points'] < $points_cost_per_order): ?>
                                <div class="mt-3 bg-white text-danger rounded p-2 small fw-bold" id="lowBalanceWarning">
                                    <i class="fas fa-exclamation-triangle"></i> <?php echo $t['err_low_bal']; ?>
                                </div>
                            <?php endif; ?>
                            <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=Recharge%20User:%20<?php echo $u['username']; ?>" target="_blank" class="btn btn-light text-success w-100 mt-3 fw-bold rounded-pill">
                                <i class="fab fa-whatsapp"></i> <?php echo $t['recharge_wa']; ?>
                            </a>
                        </div>
                    </div>

                    <!-- Driver Quick Stats -->
                    <?php $driverStats = getDriverStats($conn, $uid); ?>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="stats-box text-center py-3">
                                <h4 class="mb-0 text-primary"><?php echo $driverStats['active_orders']; ?></h4>
                                <small class="text-muted"><?php echo $t['active_orders'] ?? 'Active'; ?></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-box text-center py-3">
                                <h4 class="mb-0 text-success"><?php echo $driverStats['total_delivered']; ?></h4>
                                <small class="text-muted"><?php echo $t['completed_orders'] ?? 'Completed'; ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($role == 'customer'): ?>
                    <div class="card content-card">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4"><i class="fas fa-plus-circle text-primary"></i> <?php echo $t['new_order']; ?></h5>
                            <form method="POST" accept-charset="UTF-8">
                                <div class="mb-3">
                                    <label class="small text-muted mb-1"><?php echo $t['order_details']; ?></label>
                                    <textarea name="details" class="form-control bg-light border-0" rows="3" required></textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="small text-muted mb-1"><?php echo $t['address']; ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-map-marker-alt text-danger"></i></span>
                                        <input type="text" name="address" class="form-control bg-light border-0" required>
                                    </div>
                                </div>
                                <button name="add_order" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                                    <?php echo $t['btn_publish']; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="col-lg-8">
                    <div class="card content-card h-100">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-primary">
                                <i class="fas fa-list-ul"></i> <?php echo $t['recent_orders']; ?>
                                <?php if($role == 'driver'): ?>
                                <span class="badge bg-warning text-dark ms-2 pulse-badge" id="pendingBadge" style="display:none;">0</span>
                                <?php endif; ?>
                            </h5>
                            <?php if($role == 'customer'): ?>
                                <span class="badge bg-light text-dark border"><?php echo $u['username']; ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive" id="ordersContainer">
                            <table class="table align-middle mb-0 table-hover">
                                <thead class="bg-light">
                                    <tr class="text-secondary small text-uppercase">
                                        <th class="ps-4" style="min-width:200px"><?php echo $t['order_details']; ?></th>
                                        <th><?php echo $t['status']; ?></th>
                                        <th class="text-end pe-4"><?php echo $t['action']; ?></th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    <?php
                                    $limit = "";
                                    if($role == 'driver') $limit = "WHERE status IN ('pending', 'accepted', 'picked_up') OR driver_id='$uid'";
                                    if($role == 'customer') $limit = "WHERE customer_name='{$u['username']}' OR client_id='$uid'";

                                    $sql = "SELECT * FROM orders1 $limit ORDER BY id DESC LIMIT 50";
                                    $res = $conn->query($sql);

                                    if($res->rowCount() == 0):
                                    ?>
                                    <tr>
                                        <td colspan="3" class="empty-state">
                                            <i class="fas fa-box-open"></i>
                                            <h5><?php echo $t['no_orders']; ?></h5>
                                            <p class="text-muted small mb-0">
                                                <?php echo ($role == 'driver') ? $t['no_pending_orders'] : $t['check_back_later']; ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <?php else: while($row = $res->fetch()):
                                        $st = $row['status'];
                                        $badge = getStatusBadge($st);
                                        $icon = getStatusIcon($st);
                                    ?>
                                    <tr class="order-row">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="mt-1"><i class="fas fa-clock text-muted small"></i></div>
                                                <div>
                                                    <small class="text-muted"><?php echo fmtDate($row['created_at']); ?></small>
                                                    <div class="fw-bold text-dark text-break"><?php echo e($row['details']); ?></div>
                                                    <small class="text-secondary"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo e($row['address']); ?></small>

                                                    <?php if($role == 'customer' && $st != 'delivered' && $st != 'cancelled'): ?>
                                                        <div class="mt-2 bg-warning bg-opacity-10 p-2 rounded border border-warning border-opacity-25">
                                                            <small class="d-block text-warning fw-bold mb-1"><?php echo $t['pin_label']; ?>:</small>
                                                            <span class="pin-box text-dark"><?php echo $row['delivery_code']; ?></span>
                                                            <div class="small text-muted mt-1" style="font-size:0.75rem"><?php echo $t['pin_note']; ?></div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if($role == 'driver' && ($st == 'accepted' || $st == 'picked_up') && $row['driver_id'] == $uid): ?>
                                                        <div class="mt-2 text-muted small">
                                                            <i class="fas fa-user me-1"></i> <?php echo e($row['customer_name']); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2">
                                                <i class="fas fa-<?php echo $icon; ?> me-1"></i>
                                                <?php echo $t['st_'.$st] ?? ucfirst($st); ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <?php if($role == 'driver'): ?>

                                                <?php if($st == 'pending'): ?>
                                                    <form method="POST" onsubmit="this.querySelector('button').classList.add('loading')">
                                                        <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                                        <button name="accept_order" class="btn btn-sm btn-primary rounded-pill px-3" onclick="return confirm('<?php echo $t['confirm_accept']; ?>\n<?php echo $t['cost_per_order']; ?>: <?php echo $points_cost_per_order; ?> <?php echo $t['pts']; ?>')">
                                                            <i class="fas fa-hand-pointer me-1"></i> <?php echo $t['driver_accept']; ?>
                                                        </button>
                                                    </form>

                                                <?php elseif($st == 'accepted' && $row['driver_id'] == $uid): ?>
                                                    <form method="POST" onsubmit="this.querySelector('button').classList.add('loading')">
                                                        <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                                        <button type="submit" name="pickup_order" class="btn btn-sm btn-info text-white rounded-pill px-3">
                                                            <i class="fas fa-box me-1"></i> <?php echo $t['driver_pickup'] ?? 'Picked Up'; ?>
                                                        </button>
                                                    </form>

                                                <?php elseif($st == 'picked_up' && $row['driver_id'] == $uid): ?>
                                                    <form method="POST" class="d-flex justify-content-end align-items-center gap-2" onsubmit="this.querySelector('button').classList.add('loading')">
                                                        <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                                        <input type="text" name="pin" class="form-control form-control-sm text-center fw-bold" style="width:85px; border-color: var(--success-color);" placeholder="<?php echo $t['verify_ph']; ?>" required maxlength="4" pattern="[0-9]{4}" inputmode="numeric">
                                                        <button type="submit" name="finish_job" class="btn btn-sm btn-success rounded-pill px-3" title="<?php echo $t['finish_delivery']; ?>">
                                                            <i class="fas fa-check-double me-1"></i> <?php echo $t['verify_fin']; ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                            <?php elseif($role == 'customer'): ?>

                                                <?php if($st == 'pending'): ?>
                                                    <form method="POST" onsubmit="return confirm('<?php echo $t['confirm_cancel'] ?? 'Cancel this order?'; ?>');">
                                                        <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                                        <button type="submit" name="customer_cancel" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                            <i class="fas fa-times me-1"></i> <?php echo $t['cancel'] ?? 'Cancel'; ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="app-footer text-center text-muted py-4 mt-5">
        <div class="container">
            <p class="mb-0 small">
                <i class="fas fa-bolt text-primary me-1"></i>
                &copy; <?php echo date('Y'); ?> <?php echo $t['app_name']; ?>. <?php echo $t['all_rights']; ?>.
            </p>
        </div>
    </footer>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.app-navbar');
    if (navbar) {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
});

// Auth form toggle
function showAuthForm(form) {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginToggle = document.getElementById('loginToggle');
    const registerToggle = document.getElementById('registerToggle');

    if (!loginForm || !registerForm) return;

    loginForm.classList.remove('active');
    registerForm.classList.remove('active');
    loginToggle.classList.remove('active');
    registerToggle.classList.remove('active');

    document.getElementById(form + 'Form').classList.add('active');
    document.getElementById(form + 'Toggle').classList.add('active');
}

// Form validation feedback
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const inputs = this.querySelectorAll('input[required]');
        let valid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                valid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        // Password match check for registration
        const password = this.querySelector('input[name="reg_password"]');
        const confirm = this.querySelector('input[name="reg_confirm_password"]');
        if (password && confirm && password.value !== confirm.value) {
            valid = false;
            confirm.classList.add('is-invalid');
        }

        if (!valid) {
            e.preventDefault();
            this.querySelector('button').classList.remove('loading');
        }
    });
});

// Remove invalid class on input
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});

// Admin modals
function showAddUserModal(role) {
    document.getElementById('addUserRole').value = role;
    var modal = new bootstrap.Modal(document.getElementById('addUserModal'));
    modal.show();
}

function editUser(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_points').value = user.points;

    var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();
}

function editOrder(order) {
    document.getElementById('edit_order_id').value = order.id;
    document.getElementById('edit_order_customer').value = order.customer_name;
    document.getElementById('edit_order_details').value = order.details;
    document.getElementById('edit_order_address').value = order.address;
    document.getElementById('edit_order_status').value = order.status;
    document.getElementById('edit_order_driver').value = order.driver_id || '';

    var modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
    modal.show();
}

// Notification functions
function showNotification(title, message, type = 'info') {
    const container = document.getElementById('notificationContainer');
    const id = 'toast-' + Date.now();

    const bgClass = type === 'success' ? 'bg-success' : (type === 'warning' ? 'bg-warning' : 'bg-primary');
    const textClass = type === 'warning' ? 'text-dark' : 'text-white';

    const toast = document.createElement('div');
    toast.id = id;
    toast.className = `toast show ${bgClass} ${textClass} mb-2`;
    toast.innerHTML = `
        <div class="toast-header ${bgClass} ${textClass}">
            <i class="fas fa-bell me-2"></i>
            <strong class="me-auto">${title}</strong>
            <button type="button" class="btn-close btn-close-white" onclick="this.closest('.toast').remove()"></button>
        </div>
        <div class="toast-body">${message}</div>
    `;

    container.appendChild(toast);

    // Play notification sound
    playNotificationSound();

    // Auto remove after 5 seconds
    setTimeout(() => {
        const el = document.getElementById(id);
        if (el) el.remove();
    }, 5000);
}

function playNotificationSound() {
    const sound = document.getElementById('notificationSound');
    if (sound) {
        sound.currentTime = 0;
        sound.play().catch(() => {});
    }
}

<?php if(isset($_SESSION['user']) && !isset($_GET['settings'])): ?>
// Real-time notifications polling
let lastCheck = Math.floor(Date.now() / 1000);
const userRole = '<?php echo $role; ?>';

function checkForUpdates() {
    fetch(`api.php?action=check_orders&last_check=${lastCheck}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                lastCheck = data.timestamp;

                if (userRole === 'driver' && data.should_notify && data.new_orders > 0) {
                    showNotification(
                        '<?php echo $t['new_order_alert']; ?>',
                        `${data.new_orders} <?php echo $t['new_order']; ?>`,
                        'warning'
                    );

                    // Update pending badge
                    const badge = document.getElementById('pendingBadge');
                    if (badge && data.pending_count > 0) {
                        badge.textContent = data.pending_count;
                        badge.style.display = 'inline';
                    }

                    // Refresh page to show new orders
                    setTimeout(() => location.reload(), 2000);
                }

                if (userRole === 'customer' && data.should_notify && data.changed_orders.length > 0) {
                    data.changed_orders.forEach(order => {
                        showNotification(
                            '<?php echo $t['order_status_changed']; ?>',
                            `Order #${order.order_id}: ${order.status}`,
                            'success'
                        );
                    });

                    // Refresh page to show updated status
                    setTimeout(() => location.reload(), 2000);
                }
            }
        })
        .catch(err => console.log('Check failed:', err));
}

// Check every 10 seconds
setInterval(checkForUpdates, 10000);

// Initial check after 3 seconds
setTimeout(checkForUpdates, 3000);
<?php endif; ?>
</script>
</body>
</html>
