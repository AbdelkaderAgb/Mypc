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

        /* ==========================================
           RTL (Right-to-Left) Support for Arabic
           ========================================== */
        [dir="rtl"] {
            text-align: right;
        }

        /* RTL: Numbers display */
        [dir="rtl"] .ltr-numbers {
            direction: ltr;
            display: inline-block;
        }

        /* RTL: Flip margins and paddings */
        [dir="rtl"] .me-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
        [dir="rtl"] .me-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
        [dir="rtl"] .me-3 { margin-right: 0 !important; margin-left: 1rem !important; }
        [dir="rtl"] .ms-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
        [dir="rtl"] .ms-2 { margin-left: 0 !important; margin-right: 0.5rem !important; }
        [dir="rtl"] .ms-3 { margin-left: 0 !important; margin-right: 1rem !important; }
        [dir="rtl"] .ms-auto { margin-left: 0 !important; margin-right: auto !important; }
        [dir="rtl"] .me-auto { margin-right: 0 !important; margin-left: auto !important; }

        [dir="rtl"] .pe-1 { padding-right: 0 !important; padding-left: 0.25rem !important; }
        [dir="rtl"] .pe-2 { padding-right: 0 !important; padding-left: 0.5rem !important; }
        [dir="rtl"] .pe-3 { padding-right: 0 !important; padding-left: 1rem !important; }
        [dir="rtl"] .ps-1 { padding-left: 0 !important; padding-right: 0.25rem !important; }
        [dir="rtl"] .ps-2 { padding-left: 0 !important; padding-right: 0.5rem !important; }
        [dir="rtl"] .ps-3 { padding-left: 0 !important; padding-right: 1rem !important; }
        [dir="rtl"] .ps-4 { padding-left: 0 !important; padding-right: 1.5rem !important; }

        /* RTL: Border radius */
        [dir="rtl"] .rounded-start { border-radius: 0 var(--bs-border-radius) var(--bs-border-radius) 0 !important; }
        [dir="rtl"] .rounded-end { border-radius: var(--bs-border-radius) 0 0 var(--bs-border-radius) !important; }
        [dir="rtl"] .rounded-start-3 { border-radius: 0 0.5rem 0.5rem 0 !important; }
        [dir="rtl"] .rounded-end-3 { border-radius: 0.5rem 0 0 0.5rem !important; }

        /* RTL: Border sides */
        [dir="rtl"] .border-start { border-left: none !important; border-right: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important; }
        [dir="rtl"] .border-end { border-right: none !important; border-left: var(--bs-border-width) var(--bs-border-style) var(--bs-border-color) !important; }

        /* RTL: Text alignment */
        [dir="rtl"] .text-start { text-align: right !important; }
        [dir="rtl"] .text-end { text-align: left !important; }

        /* RTL: Flexbox */
        [dir="rtl"] .flex-row { flex-direction: row-reverse !important; }
        [dir="rtl"] .flex-row-reverse { flex-direction: row !important; }

        /* RTL: Input groups */
        [dir="rtl"] .input-group { flex-direction: row-reverse; }
        [dir="rtl"] .input-group > .form-control,
        [dir="rtl"] .input-group > .form-select {
            text-align: right;
        }
        [dir="rtl"] .input-group-text { border-radius: 0 0.375rem 0.375rem 0; }
        [dir="rtl"] .input-group > .form-control:last-child { border-radius: 0.375rem 0 0 0.375rem; }
        [dir="rtl"] .input-group > .form-control:first-child { border-radius: 0 0.375rem 0.375rem 0; }

        /* RTL: Dropdown */
        [dir="rtl"] .dropdown-menu { text-align: right; }
        [dir="rtl"] .dropdown-menu-end { right: auto !important; left: 0 !important; }

        /* RTL: Icons positioning */
        [dir="rtl"] .fa-arrow-left:before { content: "\f061"; }
        [dir="rtl"] .fa-arrow-right:before { content: "\f060"; }
        [dir="rtl"] .fa-chevron-left:before { content: "\f054"; }
        [dir="rtl"] .fa-chevron-right:before { content: "\f053"; }

        /* RTL: Tables */
        [dir="rtl"] table { direction: rtl; }
        [dir="rtl"] th, [dir="rtl"] td { text-align: right; }

        /* RTL: Lists */
        [dir="rtl"] ul, [dir="rtl"] ol { padding-right: 2rem; padding-left: 0; }

        /* RTL: Form labels */
        [dir="rtl"] .form-label { text-align: right; display: block; }
        [dir="rtl"] .form-check { padding-left: 0; padding-right: 1.5em; }
        [dir="rtl"] .form-check-input { float: right; margin-left: 0.5em; margin-right: -1.5em; }

        /* RTL: Modal close button */
        [dir="rtl"] .btn-close { margin-left: 0; margin-right: auto; }
        [dir="rtl"] .modal-header .btn-close { margin: -0.5rem auto -0.5rem -0.5rem; }

        /* RTL: Alerts */
        [dir="rtl"] .alert-dismissible { padding-right: 1rem; padding-left: 3rem; }
        [dir="rtl"] .alert-dismissible .btn-close { right: auto; left: 0; }

        /* RTL: Badge positioning */
        [dir="rtl"] .position-absolute.top-0.end-0 { right: auto !important; left: 0 !important; }
        [dir="rtl"] .position-absolute.top-0.start-0 { left: auto !important; right: 0 !important; }

        /* RTL: Gap utilities - keep consistent */
        [dir="rtl"] .gap-1, [dir="rtl"] .gap-2, [dir="rtl"] .gap-3 { gap: inherit; }

        /* RTL: Card headers */
        [dir="rtl"] .card-header { text-align: right; }

        /* RTL: Progress bars */
        [dir="rtl"] .progress-bar { transform-origin: right; }
        [dir="rtl"] .progress { direction: rtl; }

        /* RTL: Order tracking steps */
        [dir="rtl"] .progress-track { flex-direction: row-reverse; }
        [dir="rtl"] .progress-track::before { left: 10%; right: 10%; }

        /* RTL: Navbar brand */
        [dir="rtl"] .navbar-brand { margin-right: 0; margin-left: 1rem; }

        /* RTL: Stats and mini-stats */
        [dir="rtl"] .mini-stats { flex-direction: row-reverse; }

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

        /* Order Status Popup */
        .order-status-popup {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
            backdrop-filter: blur(4px);
        }
        .order-status-popup.fade-out {
            animation: fadeOut 0.5s ease forwards;
        }
        @keyframes fadeOut {
            to { opacity: 0; }
        }
        .status-popup-content {
            background: white;
            border-radius: 24px;
            padding: 40px;
            text-align: center;
            max-width: 340px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes popIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .status-icon-wrapper {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        .status-icon-wrapper.pulse {
            animation: pulseIcon 2s ease-in-out infinite;
        }
        .status-icon-wrapper.bounce {
            animation: bounceIcon 1s ease infinite;
        }
        .status-icon-wrapper.celebrate {
            animation: celebrateIcon 0.6s ease;
        }
        .status-icon-wrapper.shake {
            animation: shakeIcon 0.5s ease;
        }
        @keyframes pulseIcon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        @keyframes bounceIcon {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes celebrateIcon {
            0% { transform: scale(0.5) rotate(-10deg); }
            50% { transform: scale(1.2) rotate(10deg); }
            100% { transform: scale(1) rotate(0); }
        }
        @keyframes shakeIcon {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .status-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        .status-order-id {
            color: var(--text-secondary);
            margin-bottom: 5px;
        }
        .status-driver {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .status-progress {
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            margin-top: 20px;
            overflow: hidden;
        }
        .progress-bar-animated {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
            animation: progressGlow 1.5s ease-in-out infinite;
        }
        @keyframes progressGlow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Enhanced Button Styles */
        .btn-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .btn-icon:hover {
            transform: translateY(-2px);
        }
        .btn-icon-sm {
            width: 36px;
            height: 36px;
            border-radius: 10px;
        }

        /* Enhanced Form Inputs */
        .form-control-modern {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.2s ease;
        }
        .form-control-modern:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        /* GPS Location Button Animation */
        .location-btn {
            transition: all 0.3s ease;
        }
        .location-btn:hover {
            transform: scale(1.05);
        }
        .location-btn.locating {
            animation: locatingPulse 1s ease infinite;
        }
        @keyframes locatingPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        }

        /* Enhanced Card Hover Effects */
        .card-hover-lift {
            transition: all 0.3s ease;
        }
        .card-hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
        }

        /* Status Indicator Animations */
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        .status-dot.active {
            animation: statusPulse 1.5s ease-in-out infinite;
        }
        @keyframes statusPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        /* Modern Icon Buttons */
        .icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-light), #fff);
            color: var(--primary-color);
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        .icon-circle:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        /* Floating Action Button */
        .fab {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .fab:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5);
        }
        [dir="rtl"] .fab {
            right: auto;
            left: 24px;
        }
    </style>
</head>
<body>

<!-- Audio for notifications - Using Web Audio API for better sound -->
<script>
// Web Audio API notification sound generator
let audioContext = null;
function getAudioContext() {
    if (!audioContext) {
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
    }
    return audioContext;
}

function createNotificationSound() {
    try {
        const ctx = getAudioContext();

        // Resume context if suspended (needed for user interaction requirement)
        if (ctx.state === 'suspended') {
            ctx.resume();
        }

        const currentTime = ctx.currentTime;

        // Create oscillator for the main tone
        const osc1 = ctx.createOscillator();
        const osc2 = ctx.createOscillator();
        const gainNode = ctx.createGain();

        // Connect nodes
        osc1.connect(gainNode);
        osc2.connect(gainNode);
        gainNode.connect(ctx.destination);

        // Configure sound - pleasant notification tone
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(880, currentTime); // A5
        osc1.frequency.setValueAtTime(1046.5, currentTime + 0.1); // C6

        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(659.25, currentTime); // E5
        osc2.frequency.setValueAtTime(783.99, currentTime + 0.1); // G5

        // Envelope
        gainNode.gain.setValueAtTime(0, currentTime);
        gainNode.gain.linearRampToValueAtTime(0.3, currentTime + 0.02);
        gainNode.gain.linearRampToValueAtTime(0.2, currentTime + 0.1);
        gainNode.gain.linearRampToValueAtTime(0.3, currentTime + 0.12);
        gainNode.gain.linearRampToValueAtTime(0, currentTime + 0.3);

        // Start and stop
        osc1.start(currentTime);
        osc2.start(currentTime);
        osc1.stop(currentTime + 0.3);
        osc2.stop(currentTime + 0.3);

        return true;
    } catch(e) {
        console.log('Audio not available:', e);
        return false;
    }
}
</script>

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

                <!-- Login Form (Phone + Password) -->
                <form method="POST" id="loginForm" class="auth-form active" onsubmit="this.querySelector('button').classList.add('loading')">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary"><?php echo $t['phone_ph']; ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                            <input type="tel" name="phone" class="form-control" placeholder="<?php echo $t['phone_example'] ?? '2XXXXXXX'; ?>" required inputmode="tel" maxlength="8" pattern="[234][0-9]{7}">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary"><?php echo $t['pass_ph']; ?></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="<?php echo $t['pass_ph']; ?>" required autocomplete="current-password">
                        </div>
                    </div>
                    <button name="do_login" class="btn btn-primary w-100 py-3 fw-bold rounded-pill">
                        <?php echo $t['btn_login']; ?> <i class="fas fa-arrow-<?php echo ($lang=='ar')?'left':'right'; ?> ms-2"></i>
                    </button>
                </form>

                <!-- Register Form (Phone + Password) -->
                <form method="POST" id="registerForm" class="auth-form" onsubmit="this.querySelector('button').classList.add('loading')">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary"><?php echo $t['full_name_ph']; ?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                            <input type="text" name="reg_full_name" class="form-control" placeholder="<?php echo $t['full_name_ph']; ?>" required minlength="2">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary"><?php echo $t['phone_ph']; ?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-phone text-muted"></i></span>
                            <input type="tel" name="reg_phone" class="form-control" placeholder="<?php echo $t['phone_example'] ?? '2XXXXXXX'; ?>" required inputmode="tel" maxlength="8" minlength="8" pattern="[234][0-9]{7}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary"><?php echo $t['pass_ph']; ?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="reg_password" class="form-control" placeholder="<?php echo $t['pass_ph']; ?>" required minlength="4" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary"><?php echo $t['confirm_pass_ph']; ?> <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="reg_confirm_password" class="form-control" placeholder="<?php echo $t['confirm_pass_ph']; ?>" required autocomplete="new-password">
                        </div>
                    </div>

                    <button name="do_register" class="btn btn-success w-100 py-3 fw-bold rounded-pill">
                        <?php echo $t['btn_register']; ?> <i class="fas fa-user-plus ms-2"></i>
                    </button>
                </form>

                <!-- Help Contact Info -->
                <div class="mt-4 text-center p-3">
                    <small class="text-muted">
                        <?php echo $t['need_help'] ?? 'Need help?'; ?>
                        <a href="mailto:<?php echo $help_email; ?>" class="text-primary"><?php echo $help_email; ?></a>
                        <br>
                        <a href="https://wa.me/<?php echo $whatsapp_number; ?>" class="text-success"><i class="fab fa-whatsapp"></i> <?php echo $help_phone; ?></a>
                    </small>
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
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-plus-circle text-primary me-2"></i><?php echo $t['new_order']; ?>
                            </h5>
                            <form method="POST" accept-charset="UTF-8" id="newOrderForm">
                                <!-- Order Details -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">
                                        <i class="fas fa-box me-1"></i><?php echo $t['order_details']; ?>
                                    </label>
                                    <textarea name="details" class="form-control bg-light border-0 rounded-3" rows="3" placeholder="<?php echo $t['order_details_placeholder'] ?? 'Describe what you need delivered...'; ?>" required></textarea>
                                </div>

                                <!-- Phone Number -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">
                                        <i class="fas fa-phone me-1"></i><?php echo $t['phone_ph'] ?? 'Phone'; ?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 rounded-start-3">+222</span>
                                        <input type="tel" name="client_phone" class="form-control bg-light border-0 rounded-end-3"
                                               value="<?php echo e($u['phone'] ?? ''); ?>"
                                               placeholder="<?php echo $t['phone_example'] ?? '2XXXXXXX'; ?>"
                                               pattern="[234][0-9]{7}" maxlength="8" inputmode="tel"
                                               <?php echo !empty($u['phone']) ? '' : 'required'; ?>>
                                    </div>
                                </div>

                                <!-- Pickup Location -->
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">
                                        <i class="fas fa-store me-1 text-success"></i><?php echo $t['pickup_location'] ?? 'Pickup Location'; ?>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" name="pickup_address" id="pickupAddress" class="form-control bg-light border-0"
                                               placeholder="<?php echo $t['pickup_placeholder'] ?? 'Where to pick up from...'; ?>" required>
                                        <button type="button" class="btn btn-outline-success border-0 bg-light" onclick="getLocation('pickup')">
                                            <i class="fas fa-location-crosshairs"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="pickup_lat" id="pickupLat">
                                    <input type="hidden" name="pickup_lng" id="pickupLng">
                                </div>

                                <!-- Delivery Address -->
                                <div class="mb-4">
                                    <label class="form-label small text-muted mb-1">
                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i><?php echo $t['delivery_address'] ?? 'Delivery Address'; ?>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" name="address" id="deliveryAddress" class="form-control bg-light border-0"
                                               placeholder="<?php echo $t['delivery_placeholder'] ?? 'Where to deliver...'; ?>" required>
                                        <button type="button" class="btn btn-outline-danger border-0 bg-light" onclick="getLocation('delivery')">
                                            <i class="fas fa-location-crosshairs"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="delivery_lat" id="deliveryLat">
                                    <input type="hidden" name="delivery_lng" id="deliveryLng">
                                </div>

                                <!-- Distance Preview (shown when both locations are set) -->
                                <div id="distancePreview" class="mb-3 p-3 bg-primary bg-opacity-10 rounded-3 text-center" style="display:none;">
                                    <div class="d-flex justify-content-around align-items-center">
                                        <div>
                                            <i class="fas fa-route fa-lg text-primary"></i>
                                            <div class="small text-muted"><?php echo $t['distance'] ?? 'Distance'; ?></div>
                                            <div class="fw-bold" id="estimatedDistance">--</div>
                                        </div>
                                        <div class="border-start ps-4">
                                            <i class="fas fa-clock fa-lg text-warning"></i>
                                            <div class="small text-muted"><?php echo $t['eta'] ?? 'ETA'; ?></div>
                                            <div class="fw-bold" id="estimatedTime">--</div>
                                        </div>
                                    </div>
                                </div>

                                <button name="add_order" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i><?php echo $t['btn_publish']; ?>
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
                                                    <a href="?customer_cancel=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('<?php echo $t['confirm_cancel'] ?? 'Cancel this order?'; ?>');">
                                                        <i class="fas fa-times me-1"></i> <?php echo $t['cancel'] ?? 'Cancel'; ?>
                                                    </a>

                                                <?php elseif($st == 'accepted' || $st == 'picked_up'): ?>
                                                    <?php
                                                    // Get driver info for this order
                                                    $driverStmt = $conn->prepare("SELECT id, full_name, phone, avatar_url, rating, is_verified FROM users1 WHERE id = ?");
                                                    $driverStmt->execute([$row['driver_id']]);
                                                    $orderDriver = $driverStmt->fetch();
                                                    $driverAvatarUrl = $orderDriver ? getAvatarUrl($orderDriver) : null;
                                                    ?>
                                                    <button type="button" class="btn btn-sm btn-info text-white rounded-pill px-3"
                                                            onclick="showOrderTracking(<?php echo htmlspecialchars(json_encode([
                                                                'id' => $row['id'],
                                                                'status' => $st,
                                                                'details' => $row['details'],
                                                                'address' => $row['address'],
                                                                'driver_name' => $orderDriver['full_name'] ?? 'Driver',
                                                                'driver_phone' => $orderDriver['phone'] ?? '',
                                                                'driver_rating' => $orderDriver['rating'] ?? 5,
                                                                'driver_verified' => $orderDriver['is_verified'] ?? 0,
                                                                'driver_avatar' => $driverAvatarUrl,
                                                                'accepted_at' => $row['accepted_at'],
                                                                'picked_at' => $row['picked_at']
                                                            ])); ?>)">
                                                        <i class="fas fa-map-marker-alt me-1"></i> <?php echo $t['track_order'] ?? 'Track'; ?>
                                                    </button>
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
                            <div class="mt-3 d-flex gap-2">
                                <a href="#" id="tracking-call-btn" class="btn btn-success btn-sm flex-grow-1 rounded-pill">
                                    <i class="fas fa-phone me-1"></i> <?php echo $t['call_driver'] ?? 'Call'; ?>
                                </a>
                                <a href="#" id="tracking-whatsapp-btn" class="btn btn-outline-success btn-sm flex-grow-1 rounded-pill">
                                    <i class="fab fa-whatsapp me-1"></i> WhatsApp
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

        <style>
        .order-progress { padding: 0 10px; }
        .progress-track { display: flex; justify-content: space-between; position: relative; }
        .progress-track::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 3px;
            background: #e0e0e0;
            z-index: 0;
        }
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 0.9rem;
            margin-bottom: 8px;
            transition: all 0.3s;
        }
        .progress-step.completed .step-icon,
        .progress-step.active .step-icon {
            background: var(--primary-color, #0d6efd);
            color: white;
        }
        .progress-step.active .step-icon {
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2); }
            50% { box-shadow: 0 0 0 8px rgba(13, 110, 253, 0.1); }
        }
        .step-label { font-size: 0.7rem; color: #666; text-align: center; }
        .driver-avatar-lg {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0891b2, #0d6efd);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            overflow: hidden;
        }
        .driver-avatar-lg img { width: 100%; height: 100%; object-fit: cover; }
        </style>
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
// ==========================================
// RTL & Arabic Number Support
// ==========================================
const isRTL = document.documentElement.dir === 'rtl';
const currentLang = '<?php echo $lang; ?>';

// Arabic-Indic numerals
const arabicNumerals = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

// Convert Western numbers to Arabic numerals
function toArabicNumbers(str) {
    if (currentLang !== 'ar') return str;
    return String(str).replace(/[0-9]/g, d => arabicNumerals[d]);
}

// Convert Arabic numerals to Western numbers
function toWesternNumbers(str) {
    return String(str).replace(/[٠-٩]/g, d => arabicNumerals.indexOf(d));
}

// Format number with Arabic numerals if Arabic language
function formatNumber(num) {
    if (currentLang === 'ar') {
        return toArabicNumbers(num);
    }
    return num;
}

// Keep phone inputs as LTR for easier input
document.addEventListener('DOMContentLoaded', function() {
    // Make phone inputs LTR
    document.querySelectorAll('input[type="tel"], input[name*="phone"]').forEach(input => {
        input.style.direction = 'ltr';
        input.style.textAlign = isRTL ? 'right' : 'left';
    });

    // Make number inputs LTR
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.style.direction = 'ltr';
        input.style.textAlign = isRTL ? 'right' : 'left';
    });

    // Update number displays with Arabic numerals if needed
    if (currentLang === 'ar') {
        document.querySelectorAll('.arabic-number').forEach(el => {
            el.textContent = toArabicNumbers(el.textContent);
        });
    }
});

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

// Order tracking function for customers
function showOrderTracking(order) {
    // Reset all steps
    document.querySelectorAll('.progress-step').forEach(step => {
        step.classList.remove('completed', 'active');
    });

    // Mark completed and active steps based on status
    const steps = ['pending', 'accepted', 'picked_up', 'delivered'];
    const currentIndex = steps.indexOf(order.status);

    steps.forEach((step, index) => {
        const stepEl = document.getElementById('step-' + step);
        if (stepEl) {
            if (index < currentIndex) {
                stepEl.classList.add('completed');
            } else if (index === currentIndex) {
                stepEl.classList.add('completed', 'active');
            }
        }
    });

    // Set driver info
    document.getElementById('tracking-driver-name').textContent = order.driver_name;
    document.getElementById('tracking-driver-rating').innerHTML = '<i class="fas fa-star"></i> ' + parseFloat(order.driver_rating).toFixed(1);

    // Set avatar
    const avatarEl = document.getElementById('tracking-driver-avatar');
    if (order.driver_avatar) {
        avatarEl.innerHTML = '<img src="' + order.driver_avatar + '" alt="Driver">';
    } else {
        avatarEl.innerHTML = '<i class="fas fa-user"></i>';
    }

    // Show verified badge
    const verifiedBadge = document.getElementById('tracking-verified-badge');
    verifiedBadge.style.display = order.driver_verified ? 'inline-block' : 'none';

    // Set contact buttons
    if (order.driver_phone) {
        document.getElementById('tracking-call-btn').href = 'tel:+222' + order.driver_phone;
        document.getElementById('tracking-whatsapp-btn').href = 'https://wa.me/222' + order.driver_phone;
    }

    // Set order details
    document.getElementById('tracking-order-details').textContent = order.details;
    document.getElementById('tracking-order-address').textContent = order.address;

    // Calculate and show time info
    let timeInfo = '';
    if (order.accepted_at) {
        const acceptedTime = new Date(order.accepted_at);
        const now = new Date();
        const diffMins = Math.floor((now - acceptedTime) / 60000);
        if (diffMins < 60) {
            timeInfo = '<?php echo $t['driver_assigned'] ?? 'Driver assigned'; ?> ' + diffMins + ' <?php echo $t['min'] ?? 'min'; ?> ago';
        } else {
            const diffHours = Math.floor(diffMins / 60);
            timeInfo = '<?php echo $t['driver_assigned'] ?? 'Driver assigned'; ?> ' + diffHours + 'h ago';
        }
    }
    document.getElementById('tracking-time-info').textContent = timeInfo;

    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('orderTrackingModal'));
    modal.show();
}

// ==========================================
// GPS & LOCATION FUNCTIONS
// ==========================================

// Get current location
function getLocation(type) {
    if (!navigator.geolocation) {
        alert('<?php echo $t['geolocation_not_supported'] ?? 'Geolocation is not supported by your browser'; ?>');
        return;
    }

    const btn = event.target.closest('button');
    const originalIcon = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            if (type === 'pickup') {
                document.getElementById('pickupLat').value = lat;
                document.getElementById('pickupLng').value = lng;
                reverseGeocode(lat, lng, 'pickupAddress');
            } else {
                document.getElementById('deliveryLat').value = lat;
                document.getElementById('deliveryLng').value = lng;
                reverseGeocode(lat, lng, 'deliveryAddress');
            }

            btn.innerHTML = '<i class="fas fa-check text-success"></i>';
            setTimeout(() => {
                btn.innerHTML = originalIcon;
                btn.disabled = false;
            }, 2000);

            calculateDistance();
        },
        (error) => {
            btn.innerHTML = originalIcon;
            btn.disabled = false;
            let errorMsg = '<?php echo $t['location_error'] ?? 'Error getting location'; ?>';
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    errorMsg = '<?php echo $t['location_denied'] ?? 'Location access denied'; ?>';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMsg = '<?php echo $t['location_unavailable'] ?? 'Location unavailable'; ?>';
                    break;
                case error.TIMEOUT:
                    errorMsg = '<?php echo $t['location_timeout'] ?? 'Location request timed out'; ?>';
                    break;
            }
            showNotification('<?php echo $t['error'] ?? 'Error'; ?>', errorMsg, 'warning');
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
    );
}

// Reverse geocode coordinates to address
function reverseGeocode(lat, lng, inputId) {
    // Use Nominatim for reverse geocoding (free, no API key needed)
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=<?php echo $lang; ?>`)
        .then(response => response.json())
        .then(data => {
            if (data.display_name) {
                // Shorten the address
                let address = data.display_name;
                const parts = address.split(', ');
                if (parts.length > 3) {
                    address = parts.slice(0, 3).join(', ');
                }
                document.getElementById(inputId).value = address;
            }
        })
        .catch(() => {
            // If geocoding fails, just show coordinates
            document.getElementById(inputId).value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
        });
}

// Calculate distance between pickup and delivery
function calculateDistance() {
    const pickupLat = parseFloat(document.getElementById('pickupLat')?.value);
    const pickupLng = parseFloat(document.getElementById('pickupLng')?.value);
    const deliveryLat = parseFloat(document.getElementById('deliveryLat')?.value);
    const deliveryLng = parseFloat(document.getElementById('deliveryLng')?.value);

    if (pickupLat && pickupLng && deliveryLat && deliveryLng) {
        const distance = haversineDistance(pickupLat, pickupLng, deliveryLat, deliveryLng);
        const time = Math.ceil(distance / 30 * 60); // Estimate: 30 km/h average speed

        const preview = document.getElementById('distancePreview');
        if (preview) {
            preview.style.display = 'block';
            document.getElementById('estimatedDistance').textContent = distance.toFixed(1) + ' <?php echo $t['km'] ?? 'km'; ?>';
            document.getElementById('estimatedTime').textContent = time + ' <?php echo $t['min'] ?? 'min'; ?>';
        }
    }
}

// Haversine formula for distance calculation
function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth's radius in kilometers
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Live tracking for driver location (called periodically)
let trackingInterval = null;

function startLiveTracking(orderId, driverId) {
    if (trackingInterval) clearInterval(trackingInterval);

    trackingInterval = setInterval(() => {
        fetch(`api.php?action=get_driver_location&driver_id=${driverId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.lat && data.lng) {
                    updateDriverDistance(data.lat, data.lng);
                }
            })
            .catch(() => {});
    }, 30000); // Update every 30 seconds
}

function stopLiveTracking() {
    if (trackingInterval) {
        clearInterval(trackingInterval);
        trackingInterval = null;
    }
}

function updateDriverDistance(driverLat, driverLng) {
    // Get client's current position for distance calculation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const clientLat = position.coords.latitude;
                const clientLng = position.coords.longitude;
                const distance = haversineDistance(clientLat, clientLng, driverLat, driverLng);
                const time = Math.ceil(distance / 25 * 60); // 25 km/h in city

                const distanceEl = document.getElementById('live-distance');
                const timeEl = document.getElementById('live-eta');

                if (distanceEl) distanceEl.textContent = distance.toFixed(1) + ' <?php echo $t['km'] ?? 'km'; ?>';
                if (timeEl) timeEl.textContent = time + ' <?php echo $t['min'] ?? 'min'; ?>';
            },
            () => {}
        );
    }
}

// Update driver location (for drivers)
function updateMyLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                fetch('api.php?action=update_location', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ lat, lng })
                }).catch(() => {});
            },
            () => {}
        );
    }
}

// Start location updates for drivers
<?php if(isset($_SESSION['user']) && $role === 'driver'): ?>
setInterval(updateMyLocation, 60000); // Update every minute
updateMyLocation(); // Initial update
<?php endif; ?>

// ==========================================
// ANIMATED ORDER STATUS POPUP
// ==========================================

function showOrderStatusPopup(order) {
    // Remove existing popup
    const existingPopup = document.getElementById('orderStatusPopup');
    if (existingPopup) existingPopup.remove();

    const statusConfig = {
        'pending': { icon: 'clock', color: '#f59e0b', text: '<?php echo $t['st_pending'] ?? 'Pending'; ?>', animation: 'pulse' },
        'accepted': { icon: 'truck', color: '#3b82f6', text: '<?php echo $t['st_accepted'] ?? 'Accepted'; ?>', animation: 'bounce' },
        'picked_up': { icon: 'box', color: '#8b5cf6', text: '<?php echo $t['st_picked_up'] ?? 'Picked Up'; ?>', animation: 'bounce' },
        'delivered': { icon: 'check-double', color: '#10b981', text: '<?php echo $t['st_delivered'] ?? 'Delivered'; ?>', animation: 'celebrate' },
        'cancelled': { icon: 'times-circle', color: '#ef4444', text: '<?php echo $t['st_cancelled'] ?? 'Cancelled'; ?>', animation: 'shake' }
    };

    const config = statusConfig[order.status] || statusConfig['pending'];

    const popup = document.createElement('div');
    popup.id = 'orderStatusPopup';
    popup.className = 'order-status-popup';
    popup.innerHTML = `
        <div class="status-popup-content">
            <div class="status-icon-wrapper ${config.animation}">
                <i class="fas fa-${config.icon}" style="color: ${config.color}"></i>
            </div>
            <h4 class="status-title">${config.text}</h4>
            <p class="status-order-id"><?php echo $t['order_number'] ?? 'Order'; ?> #${order.id}</p>
            ${order.driver_name ? `<p class="status-driver"><i class="fas fa-user"></i> ${order.driver_name}</p>` : ''}
            <div class="status-progress">
                <div class="progress-bar-animated" style="width: ${getProgressPercent(order.status)}%; background: ${config.color}"></div>
            </div>
            <button class="btn btn-light btn-sm mt-3" onclick="this.closest('.order-status-popup').remove()">
                <i class="fas fa-times"></i> <?php echo $t['close'] ?? 'Close'; ?>
            </button>
        </div>
    `;

    document.body.appendChild(popup);

    // Auto-close after 5 seconds
    setTimeout(() => {
        popup.classList.add('fade-out');
        setTimeout(() => popup.remove(), 500);
    }, 5000);
}

function getProgressPercent(status) {
    const progress = { 'pending': 25, 'accepted': 50, 'picked_up': 75, 'delivered': 100, 'cancelled': 0 };
    return progress[status] || 0;
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
    // Use Web Audio API for notification sound
    createNotificationSound();
}

// Initialize audio context on first user interaction (required by browsers)
document.addEventListener('click', function initAudio() {
    getAudioContext();
    document.removeEventListener('click', initAudio);
}, { once: true });

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
