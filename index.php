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
            /* Barq Ultra Premium Color Palette */
            --primary: #584BF6;
            --primary-dark: #4338ca;
            --primary-light: #818cf8;
            --primary-glow: rgba(88, 75, 246, 0.5);
            --secondary: #ec4899;
            --secondary-light: #f9a8d4;
            --accent: #06b6d4;
            --accent-light: #67e8f9;

            --success: #00C851;
            --success-light: #86efac;
            --warning: #f59e0b;
            --warning-light: #fcd34d;
            --danger: #ef4444;
            --danger-light: #fca5a5;

            --dark: #0f121e;
            --dark-soft: #1e293b;
            --text-main: #1A1D26;
            --text-sub: #9499A8;
            --bg-body: #F2F4F8;
            --gray-900: #111827;
            --gray-800: #1f2937;
            --gray-700: #374151;
            --gray-600: #4b5563;
            --gray-500: #6b7280;
            --gray-400: #9ca3af;
            --gray-300: #d1d5db;
            --gray-200: #e5e7eb;
            --gray-100: #f3f4f6;
            --gray-50: #f9fafb;
            --white: #ffffff;

            /* Glassmorphism */
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: 1px solid rgba(255, 255, 255, 0.6);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);

            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            --shadow-glow: 0 0 40px var(--primary-glow);

            /* Transitions */
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.15s ease;
            --transition-bounce: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);

            /* Border Radius */
            --radius-sm: 8px;
            --radius: 12px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --radius-full: 9999px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            padding-bottom: 120px;
            /* Subtle Mesh Gradient Background */
            background-image:
                radial-gradient(circle at 0% 0%, rgba(88, 75, 246, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 100% 0%, rgba(0, 200, 81, 0.05) 0%, transparent 40%);
        }

        /* Remove animated background for cleaner look */
        body::before, body::after {
            display: none;
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

        /* RTL: Phone numbers - always display LTR */
        [dir="rtl"] .phone-display,
        [dir="rtl"] .phone-number,
        [dir="rtl"] [data-phone],
        [dir="rtl"] a[href^="tel:"],
        [dir="rtl"] a[href^="https://wa.me"] {
            direction: ltr;
            unicode-bidi: embed;
            display: inline-block;
            text-align: left;
        }

        /* Force phone inputs to always be LTR aligned */
        input[type="tel"],
        input[name*="phone"] {
            direction: ltr !important;
            text-align: left !important;
        }

        /* RTL: Ensure numbers in badges and stats display correctly */
        [dir="rtl"] .badge,
        [dir="rtl"] .mini-stat-value,
        [dir="rtl"] .stats-box h3 {
            direction: ltr;
            unicode-bidi: isolate;
        }

        /* RTL: Order ID and PIN codes */
        [dir="rtl"] .order-id,
        [dir="rtl"] .pin-code,
        [dir="rtl"] .delivery-code {
            direction: ltr;
            unicode-bidi: embed;
            font-family: 'Courier New', monospace;
        }

        /* ==========================================
           ANIMATIONS
           ========================================== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px var(--primary-glow); }
            50% { box-shadow: 0 0 40px var(--primary-glow), 0 0 60px var(--primary-glow); }
        }
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        @keyframes ripple {
            0% { transform: scale(0); opacity: 1; }
            100% { transform: scale(4); opacity: 0; }
        }

        .animate-fadeInUp { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
        .animate-fadeInDown { animation: fadeInDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
        .animate-fadeIn { animation: fadeIn 0.5s ease forwards; }
        .animate-slideInRight { animation: slideInRight 0.5s ease forwards; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-pulse { animation: pulse 2s ease-in-out infinite; }
        .animate-glow { animation: glow 2s ease-in-out infinite; }

        /* ==========================================
           GLASSMORPHISM CARDS
           ========================================== */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: var(--radius-xl);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255,255,255,0.5) inset;
            transition: var(--transition);
        }

        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255,255,255,0.6) inset;
        }

        .glass-card-dark {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* Login Card - Glass Design */
        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            margin: 0 auto;
        }
        .login-card {
            max-width: 420px;
            margin: 0 auto;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            box-shadow: 0 20px 60px rgba(99, 102, 241, 0.2);
            overflow: hidden;
            position: relative;
            padding: 40px 30px;
            text-align: center;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        /* Login Logo Area */
        .login-logo-area {
            margin-bottom: 30px;
        }
        .login-logo-icon {
            font-size: 2.5rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            display: inline-block;
        }
        .login-app-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--gray-900);
            margin: 0;
            line-height: 1.2;
        }
        .login-app-subtitle {
            font-size: 0.9rem;
            color: var(--gray-500);
            margin-top: 5px;
        }

        /* Login Input Groups */
        .login-input-group {
            margin-bottom: 20px;
            position: relative;
            text-align: right;
        }
        .login-input-icon {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 1.1rem;
            z-index: 2;
        }
        [dir="ltr"] .login-input-icon {
            right: auto;
            left: 20px;
        }
        .login-form-control {
            width: 100%;
            padding: 16px 55px 16px 20px;
            border-radius: 16px;
            border: 2px solid #EBEBF0;
            background: #F9FAFC;
            font-size: 1rem;
            font-family: inherit;
            color: var(--gray-900);
            outline: none;
            transition: 0.3s;
            font-weight: 600;
        }
        [dir="ltr"] .login-form-control {
            padding: 16px 20px 16px 55px;
        }
        .login-form-control:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        /* Login Buttons */
        .btn-login-main {
            width: 100%;
            padding: 18px;
            border-radius: 18px;
            border: none;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
            transition: 0.3s;
            margin-bottom: 15px;
        }
        .btn-login-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
            color: white;
        }
        .btn-login-main:active {
            transform: scale(0.98);
        }
        .btn-register-main {
            width: 100%;
            padding: 18px;
            border-radius: 18px;
            border: none;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
            transition: 0.3s;
            margin-bottom: 15px;
        }
        .btn-register-main:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
            color: white;
        }

        /* Login Divider */
        .login-divider {
            position: relative;
            margin: 25px 0;
            color: var(--gray-500);
            font-size: 0.85rem;
        }
        .login-divider::before, .login-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: #EBEBF0;
        }
        .login-divider::before { left: 0; }
        .login-divider::after { right: 0; }

        /* Login Contact Hub */
        .login-contact-hub {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .login-social-btn {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            background: white;
            color: var(--gray-500);
            border: 1px solid #EBEBF0;
            transition: 0.3s;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }
        .login-social-btn:hover {
            transform: translateY(-5px);
        }
        .login-social-btn.whatsapp:hover {
            background: #25D366;
            color: white;
            border-color: #25D366;
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.3);
        }
        .login-social-btn.phone:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        }
        .login-social-btn.email:hover {
            background: #EA4335;
            color: white;
            border-color: #EA4335;
            box-shadow: 0 8px 20px rgba(234, 67, 53, 0.3);
        }

        /* Login Footer Text */
        .login-footer-text {
            font-size: 0.85rem;
            color: var(--gray-500);
            margin-top: 10px;
        }
        .login-footer-text a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }
        .login-footer-text a:hover {
            text-decoration: underline;
        }

        /* App Navbar - Enhanced */
        .app-navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.12);
            transition: var(--transition);
            position: relative;
            z-index: 100;
            padding: 16px 0;
        }
        .app-navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent), var(--primary));
            background-size: 300% 100%;
            animation: gradientShift 8s ease infinite;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .app-navbar.scrolled {
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.18);
            padding: 12px 0;
        }
        .app-navbar .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: var(--transition);
        }
        .app-navbar .navbar-brand:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }
        .app-navbar .navbar-brand img {
            transition: var(--transition);
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15));
        }
        .app-navbar .navbar-brand:hover img {
            transform: scale(1.08) rotate(2deg);
        }

        /* Content Cards */
        .content-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: var(--radius-lg);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(255,255,255,0.4) inset;
            overflow: hidden;
            transition: var(--transition);
            position: relative;
        }
        .content-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.02) 0%, rgba(236, 72, 153, 0.02) 100%);
            pointer-events: none;
        }
        .content-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18), 0 0 0 1px rgba(255,255,255,0.6) inset;
        }

        /* Stats Box */
        .stats-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--radius-xl);
            padding: 30px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255,255,255,0.3) inset;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
        }
        .stats-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.04) 0%, rgba(236, 72, 153, 0.04) 100%);
            pointer-events: none;
        }
        .stats-box::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stats-box:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255,255,255,0.5) inset;
        }
        .stats-box:hover::after {
            transform: scaleX(1);
        }
        .stats-box h3 {
            font-size: 2.2rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }
        .stats-box .icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            transition: var(--transition);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(236, 72, 153, 0.1));
        }
        .stats-box:hover .icon-wrapper {
            transform: scale(1.15) rotate(8deg);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(236, 72, 153, 0.15));
        }

        /* ==========================================
           STATUS BADGES
           ========================================== */
        .badge {
            font-weight: 600;
            padding: 8px 16px;
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            letter-spacing: 0.3px;
        }
        .badge-pending {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            box-shadow: 0 2px 10px rgba(245, 158, 11, 0.3);
        }
        .badge-accepted {
            background: linear-gradient(135deg, #dbeafe, #93c5fd);
            color: #1e40af;
            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.3);
        }
        .badge-picked_up {
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            color: #4338ca;
            box-shadow: 0 2px 10px rgba(99, 102, 241, 0.3);
        }
        .badge-delivered {
            background: linear-gradient(135deg, #d1fae5, #86efac);
            color: #065f46;
            box-shadow: 0 2px 10px rgba(34, 197, 94, 0.3);
        }
        .badge-cancelled {
            background: linear-gradient(135deg, #fee2e2, #fca5a5);
            color: #991b1b;
            box-shadow: 0 2px 10px rgba(239, 68, 68, 0.3);
        }

        /* PIN Box */
        .pin-box {
            font-family: 'Courier New', monospace;
            letter-spacing: 8px;
            font-weight: 800;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            padding: 14px 24px;
            border-radius: var(--radius);
            user-select: all;
            display: inline-block;
            border: 2px dashed var(--warning);
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
        }
        .pin-box:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
        }

        /* ==========================================
           BUTTONS
           ========================================== */
        .btn {
            font-weight: 700;
            border-radius: var(--radius-lg);
            padding: 14px 28px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border: none;
            letter-spacing: 0.3px;
        }
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        .btn:active::before {
            width: 400px;
            height: 400px;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 10px 35px rgba(99, 102, 241, 0.5);
            color: white;
        }
        .btn-success {
            background: linear-gradient(135deg, var(--success), #16a34a);
            color: white;
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
        }
        .btn-success:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 10px 35px rgba(34, 197, 94, 0.5);
            color: white;
        }
        .btn-warning {
            background: linear-gradient(135deg, var(--warning), #d97706);
            color: white;
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }
        .btn-warning:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 10px 35px rgba(245, 158, 11, 0.5);
            color: white;
        }
        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #dc2626);
            color: white;
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }
        .btn-danger:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 10px 35px rgba(239, 68, 68, 0.5);
            color: white;
        }
        .btn-info {
            background: linear-gradient(135deg, var(--accent), #0891b2);
            color: white;
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.4);
        }
        .btn-info:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 10px 35px rgba(6, 182, 212, 0.5);
            color: white;
        }
        .btn-light {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(255, 255, 255, 0.4);
            color: var(--dark);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .btn-light:hover {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }
        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }
        .btn-outline-success {
            border: 2px solid var(--success);
            color: var(--success);
            background: transparent;
        }
        .btn-outline-success:hover {
            background: var(--success);
            color: white;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
        }

        /* WhatsApp Button */
        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
            border: none;
        }
        .btn-whatsapp:hover {
            background: linear-gradient(135deg, #128C7E, #25D366);
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 10px 35px rgba(37, 211, 102, 0.5);
            color: white;
        }
        .btn-whatsapp i {
            font-size: 1.2em;
            transition: transform 0.3s ease;
        }
        .btn-whatsapp:hover i {
            transform: scale(1.2) rotate(5deg);
        }

        /* Gradient Button */
        .btn-gradient {
            background: linear-gradient(135deg, var(--primary), var(--secondary), var(--accent));
            background-size: 200% 100%;
            color: white;
            box-shadow: 0 6px 25px rgba(99, 102, 241, 0.35);
            border: none;
            position: relative;
            z-index: 1;
        }
        .btn-gradient:hover {
            background-position: 100% 0;
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.45);
            color: white;
        }

        /* Enhanced Button Focus States */
        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25);
        }
        .btn-success:focus {
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.25);
        }
        .btn-danger:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.25);
        }
        .btn-whatsapp:focus {
            box-shadow: 0 0 0 4px rgba(37, 211, 102, 0.25);
        }

        /* Button with Icon */
        .btn-icon {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-icon i {
            transition: transform 0.3s ease;
        }
        .btn-icon:hover i {
            transform: scale(1.15);
        }

        /* Large Button */
        .btn-lg {
            padding: 16px 32px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: var(--radius-lg);
        }

        /* Small Button Enhancement */
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.85rem;
        }

        /* ==========================================
           FORM CONTROLS
           ========================================== */
        .form-control, .form-select {
            border-radius: var(--radius);
            border: 2px solid var(--gray-200);
            padding: 14px 18px;
            transition: var(--transition);
            font-size: 1rem;
            background: var(--white);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            outline: none;
        }
        .form-control::placeholder {
            color: var(--gray-400);
        }
        .input-group-text {
            border: 2px solid var(--gray-200);
            border-right: none;
            background: var(--gray-50);
            color: var(--gray-600);
            font-weight: 600;
        }
        .input-group .form-control {
            border-left: none;
        }
        .input-group:focus-within .input-group-text {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
            color: var(--primary);
        }
        .input-group:focus-within .form-control {
            border-color: var(--primary);
        }
        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
        }

        /* Auth Toggle */
        .auth-toggle {
            display: flex;
            background: var(--gray-100);
            border-radius: var(--radius);
            padding: 6px;
            margin-bottom: 28px;
        }
        .auth-toggle button {
            flex: 1;
            border: none;
            background: transparent;
            padding: 14px;
            border-radius: var(--radius-sm);
            font-weight: 700;
            transition: var(--transition);
            color: var(--gray-500);
        }
        .auth-toggle button:hover:not(.active) {
            background: rgba(255, 255, 255, 0.5);
            color: var(--gray-700);
        }
        .auth-toggle button.active {
            background: var(--white);
            box-shadow: var(--shadow);
            color: var(--primary);
        }
        .auth-form {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        .auth-form.active {
            display: block;
        }

        /* ==========================================
           TABLES
           ========================================== */
        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table thead th {
            background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
            border-bottom: 2px solid var(--gray-200);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: var(--gray-600);
            padding: 18px;
        }
        .table tbody tr {
            transition: var(--transition);
        }
        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.03), rgba(236, 72, 153, 0.03));
        }
        .table tbody td {
            padding: 18px;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-100);
        }

        /* Order Row Animation */
        .order-row {
            animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }
        .order-row:nth-child(1) { animation-delay: 0.05s; }
        .order-row:nth-child(2) { animation-delay: 0.1s; }
        .order-row:nth-child(3) { animation-delay: 0.15s; }
        .order-row:nth-child(4) { animation-delay: 0.2s; }
        .order-row:nth-child(5) { animation-delay: 0.25s; }

        /* ==========================================
           MODALS
           ========================================== */
        .modal-content {
            border-radius: var(--radius-xl);
            border: none;
            box-shadow: var(--shadow-lg);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
        }
        .modal-header {
            border-bottom: 1px solid var(--gray-100);
            padding: 24px;
        }
        .modal-body {
            padding: 24px;
        }
        .modal-footer {
            border-top: 1px solid var(--gray-100);
            padding: 20px 24px;
        }

        /* ==========================================
           NAV TABS
           ========================================== */
        .nav-tabs {
            border-bottom: 2px solid var(--gray-200);
            gap: 8px;
        }
        .nav-tabs .nav-link {
            border: none;
            border-radius: var(--radius) var(--radius) 0 0;
            padding: 14px 24px;
            color: var(--gray-500);
            font-weight: 600;
            transition: var(--transition);
            position: relative;
        }
        .nav-tabs .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 3px 3px 0 0;
        }
        .nav-tabs .nav-link:hover {
            background: rgba(99, 102, 241, 0.05);
            color: var(--primary);
        }
        .nav-tabs .nav-link.active {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
        }
        .nav-tabs .nav-link.active::after {
            transform: scaleX(1);
        }

        /* ==========================================
           AVATARS
           ========================================== */
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            border: 4px solid rgba(255, 255, 255, 0.5);
        }
        .profile-avatar:hover {
            transform: scale(1.08);
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
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            font-size: 0.75rem;
            padding: 10px 6px 6px;
            cursor: pointer;
            opacity: 0;
            transition: var(--transition);
        }
        .profile-avatar:hover .profile-avatar-edit {
            opacity: 1;
        }

        /* Role-based avatar colors */
        .avatar-admin {
            background: linear-gradient(135deg, #f43f5e, #ec4899);
            box-shadow: 0 8px 30px rgba(244, 63, 94, 0.4);
        }
        .avatar-driver {
            background: linear-gradient(135deg, #10b981, #06b6d4);
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.4);
        }
        .avatar-customer {
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4);
        }

        .avatar-sm { width: 48px; height: 48px; font-size: 1.2rem; border-width: 3px; }
        .avatar-md { width: 64px; height: 64px; font-size: 1.6rem; }
        .avatar-lg { width: 120px; height: 120px; font-size: 3rem; }

        /* Role Badges */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .role-badge-admin {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #e11d48;
        }
        .role-badge-driver {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #059669;
        }
        .role-badge-customer {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            color: var(--primary);
        }

        .avatar-with-badge { position: relative; display: inline-block; }

        .verified-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
        }
        .verified-badge-sm { width: 22px; height: 22px; font-size: 0.65rem; border-width: 2px; }

        .not-verified-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            background: linear-gradient(135deg, var(--warning), #d97706);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
        }

        /* ==========================================
           STATS & BADGES
           ========================================== */
        .serial-badge {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            color: var(--primary);
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            font-weight: 700;
        }

        .mini-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 20px;
        }
        .mini-stat {
            background: linear-gradient(135deg, var(--gray-50), var(--white));
            padding: 16px;
            border-radius: var(--radius);
            text-align: center;
            border: 1px solid var(--gray-100);
            transition: var(--transition);
        }
        .mini-stat:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        .mini-stat-value {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .mini-stat-label {
            font-size: 0.7rem;
            color: var(--gray-500);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .rating-stars { color: #fbbf24; }
        .rating-value { font-weight: 700; margin-left: 4px; }

        /* Online Toggle */
        .online-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background: linear-gradient(135deg, var(--gray-50), var(--white));
            border-radius: var(--radius);
            margin-bottom: 20px;
            border: 1px solid var(--gray-100);
        }
        .online-toggle .form-check-input {
            width: 54px;
            height: 28px;
            cursor: pointer;
        }
        .online-toggle .form-check-input:checked {
            background-color: var(--success);
            border-color: var(--success);
        }
        .online-status { font-weight: 700; }
        .online-status.online { color: var(--success); }
        .online-status.offline { color: var(--gray-400); }

        /* GPS Toggle & Status */
        .gps-toggle {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px 24px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: var(--transition);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255,255,255,0.3) inset;
        }
        .gps-toggle:hover {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255,255,255,0.4) inset;
            transform: translateY(-2px);
        }
        .gps-toggle.active {
            background: linear-gradient(135deg, rgba(236, 253, 245, 0.98), rgba(209, 250, 229, 0.98));
            backdrop-filter: blur(20px);
            border-color: var(--success);
            box-shadow: 0 8px 30px rgba(34, 197, 94, 0.2), 0 0 0 1px rgba(34, 197, 94, 0.3) inset;
        }
        .gps-toggle.active:hover {
            box-shadow: 0 12px 40px rgba(34, 197, 94, 0.25), 0 0 0 1px rgba(34, 197, 94, 0.4) inset;
        }
        .gps-toggle-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            flex-shrink: 0;
        }
        .gps-toggle-btn.off {
            background: linear-gradient(135deg, var(--gray-300), var(--gray-400));
            color: var(--gray-600);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .gps-toggle-btn.off:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, var(--gray-400), var(--gray-500));
        }
        .gps-toggle-btn.on {
            background: linear-gradient(135deg, var(--success), #16a34a);
            color: white;
            box-shadow: 0 6px 25px rgba(34, 197, 94, 0.5);
            animation: gpsPulse 2.5s infinite;
        }
        .gps-toggle-btn.on:hover {
            transform: scale(1.05);
        }
        .gps-toggle-btn.loading {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 6px 25px rgba(99, 102, 241, 0.5);
        }
        .gps-toggle-btn.loading i {
            animation: spin 1s linear infinite;
        }
        @keyframes gpsPulse {
            0%, 100% {
                box-shadow: 0 6px 25px rgba(34, 197, 94, 0.5);
            }
            50% {
                box-shadow: 0 6px 35px rgba(34, 197, 94, 0.7), 0 0 0 12px rgba(34, 197, 94, 0.15);
            }
        }
        .gps-status-info {
            flex-grow: 1;
        }
        .gps-status-label {
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 4px;
        }
        .gps-status-label.on {
            color: var(--success);
            background: linear-gradient(135deg, var(--success), #16a34a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .gps-status-label.off {
            color: var(--gray-500);
        }
        .gps-status-detail {
            font-size: 0.85rem;
            color: var(--gray-500);
            margin-top: 4px;
            font-weight: 500;
        }
        .gps-accuracy-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, #dbeafe, #93c5fd);
            color: #1e40af;
            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
        }

        /* ==========================================
           MISC COMPONENTS
           ========================================== */
        .settings-btn {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            border-radius: 50%;
        }
        .settings-btn:hover {
            transform: scale(1.1);
            background: var(--gray-100);
        }
        .settings-btn.text-danger:hover {
            background: #fef2f2;
        }

        .pulse-badge { animation: pulse 2s infinite; }
        .points-badge {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            animation: bounce 2s infinite;
        }

        .btn.loading {
            pointer-events: none;
            color: transparent !important;
        }
        .btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .empty-state {
            padding: 80px 20px;
            text-align: center;
        }
        .empty-state i {
            font-size: 5rem;
            background: linear-gradient(135deg, var(--gray-200), var(--gray-300));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 24px;
            display: block;
        }
        .empty-state h5 {
            color: var(--gray-500);
            font-weight: 700;
        }

        .demo-box {
            background: linear-gradient(135deg, var(--gray-50), var(--white));
            border: 2px dashed var(--gray-200);
            border-radius: var(--radius);
            font-size: 0.85rem;
        }

        .lang-switcher .btn {
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .lang-switcher .btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .notification-toast {
            position: fixed;
            top: 90px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }
        [dir="rtl"] .notification-toast { right: auto; left: 20px; }
        .notification-toast .toast {
            border-radius: var(--radius);
            border: none;
            box-shadow: var(--shadow-lg);
            animation: slideInRight 0.4s ease;
        }

        /* ==========================================
           ORDER STATUS POPUP
           ========================================== */
        .order-status-popup {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
            backdrop-filter: blur(8px);
        }
        .order-status-popup.fade-out {
            animation: fadeOut 0.5s ease forwards;
        }
        @keyframes fadeOut { to { opacity: 0; } }

        .status-popup-content {
            background: var(--white);
            border-radius: var(--radius-xl);
            padding: 48px;
            text-align: center;
            max-width: 380px;
            width: 90%;
            box-shadow: var(--shadow-lg);
            animation: scaleIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .status-icon-wrapper {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 48px;
        }
        .status-icon-wrapper.pulse { animation: pulse 2s ease-in-out infinite; }
        .status-icon-wrapper.bounce { animation: bounce 1s ease infinite; }
        .status-icon-wrapper.celebrate { animation: scaleIn 0.6s ease; }
        .status-icon-wrapper.shake { animation: shake 0.5s ease; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        .status-title { font-size: 1.6rem; font-weight: 800; margin-bottom: 8px; color: var(--dark); }
        .status-order-id { color: var(--gray-500); margin-bottom: 8px; }
        .status-driver { color: var(--gray-500); font-size: 0.9rem; }
        .status-progress {
            height: 8px;
            background: var(--gray-200);
            border-radius: var(--radius-full);
            margin-top: 24px;
            overflow: hidden;
        }
        .progress-bar-animated {
            height: 100%;
            border-radius: var(--radius-full);
            transition: width 0.5s ease;
            animation: shimmer 2s infinite;
            background-size: 200% 100%;
        }

        /* Floating Action Button */
        .fab {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            cursor: pointer;
            transition: var(--transition-bounce);
            z-index: 1000;
        }
        .fab:hover {
            transform: scale(1.15) rotate(90deg);
            box-shadow: 0 12px 35px rgba(99, 102, 241, 0.5);
        }
        [dir="rtl"] .fab { right: auto; left: 24px; }

        /* ==========================================
           FLOATING ORDER NOTIFICATION BUBBLE
           ========================================== */
        .order-notification-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 400px;
            width: calc(100% - 40px);
        }
        [dir="rtl"] .order-notification-container {
            right: auto;
            left: 20px;
        }

        .order-bubble {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(99, 102, 241, 0.1);
            overflow: hidden;
            animation: bubbleSlideIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
        }
        .order-bubble.fade-out {
            animation: bubbleSlideOut 0.4s ease forwards;
        }
        @keyframes bubbleSlideIn {
            from { opacity: 0; transform: translateX(100px) scale(0.8); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes bubbleSlideOut {
            to { opacity: 0; transform: translateX(100px) scale(0.8); }
        }
        [dir="rtl"] .order-bubble {
            animation-name: bubbleSlideInRtl;
        }
        [dir="rtl"] .order-bubble.fade-out {
            animation-name: bubbleSlideOutRtl;
        }
        @keyframes bubbleSlideInRtl {
            from { opacity: 0; transform: translateX(-100px) scale(0.8); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes bubbleSlideOutRtl {
            to { opacity: 0; transform: translateX(-100px) scale(0.8); }
        }

        .order-bubble-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .order-bubble-header .new-order-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .order-bubble-header .new-order-badge i {
            animation: bellRing 0.5s ease infinite;
        }
        @keyframes bellRing {
            0%, 100% { transform: rotate(0); }
            25% { transform: rotate(15deg); }
            75% { transform: rotate(-15deg); }
        }
        .order-bubble-timer {
            background: rgba(255,255,255,0.2);
            padding: 4px 10px;
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            font-weight: 700;
        }

        .order-bubble-body {
            padding: 16px;
        }
        .order-bubble-distance {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #dbeafe, #93c5fd);
            color: #1e40af;
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 12px;
        }
        .order-bubble-details {
            font-size: 0.95rem;
            color: var(--dark);
            margin-bottom: 12px;
            line-height: 1.5;
            max-height: 60px;
            overflow: hidden;
        }
        .order-bubble-address {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            color: var(--gray-600);
            font-size: 0.85rem;
            margin-bottom: 12px;
        }
        .order-bubble-address i {
            color: var(--danger);
            margin-top: 2px;
        }
        .order-bubble-phone {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--gray-50);
            padding: 8px 12px;
            border-radius: var(--radius);
            margin-bottom: 16px;
        }
        .order-bubble-phone i {
            color: var(--success);
        }
        .order-bubble-phone a {
            color: var(--dark);
            font-weight: 600;
            text-decoration: none;
            direction: ltr;
        }

        .order-bubble-actions {
            display: flex;
            gap: 10px;
        }
        .order-bubble-actions .btn {
            flex: 1;
            padding: 12px;
            font-weight: 700;
            border-radius: var(--radius);
        }
        .btn-accept {
            background: linear-gradient(135deg, var(--success), #16a34a);
            color: white;
            border: none;
        }
        .btn-accept:hover {
            background: linear-gradient(135deg, #16a34a, var(--success));
            color: white;
            transform: translateY(-2px);
        }
        .btn-decline {
            background: var(--gray-100);
            color: var(--gray-600);
            border: 1px solid var(--gray-200);
        }
        .btn-decline:hover {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .order-bubble-progress {
            height: 4px;
            background: var(--gray-200);
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .order-bubble-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: width 0.1s linear;
        }

        @media (max-width: 480px) {
            .order-notification-container {
                top: 70px;
                right: 10px;
                left: 10px;
                width: auto;
                max-width: none;
            }
            .order-bubble-body { padding: 12px; }
            .order-bubble-actions .btn { padding: 10px; font-size: 0.9rem; }
        }

        /* ==========================================
           RESPONSIVE
           ========================================== */
        @media (max-width: 992px) {
            .stats-box { padding: 20px; }
            .content-card { border-radius: var(--radius); }
        }

        @media (max-width: 768px) {
            body { background-size: 200% 200%; }
            .container { padding-left: 16px; padding-right: 16px; }
            .login-card { margin: 16px; border-radius: var(--radius-lg); }
            .stats-box { padding: 16px; }
            .stats-box h3 { font-size: 1.5rem; }
            .nav-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .nav-tabs .nav-link { padding: 12px 16px; font-size: 0.85rem; white-space: nowrap; }
            .content-card { border-radius: var(--radius); }
            .form-control, .form-select { padding: 12px 16px; font-size: 16px; }
            .btn { padding: 12px 20px; }
            .pin-box { font-size: 1.2rem; letter-spacing: 5px; padding: 12px 18px; }
            .profile-avatar.avatar-lg { width: 100px; height: 100px; font-size: 2.5rem; }
            .role-badge { font-size: 0.7rem; padding: 6px 12px; }
            .app-navbar { padding: 10px 0; }
            .mini-stats { gap: 10px; }
            .mini-stat { padding: 14px; }
            .mini-stat-value { font-size: 1.3rem; }
            .status-popup-content { padding: 32px; max-width: 320px; }
            .status-icon-wrapper { width: 90px; height: 90px; font-size: 40px; }
            .status-title { font-size: 1.4rem; }
        }

        @media (max-width: 480px) {
            .container { padding-left: 12px; padding-right: 12px; }
            .profile-avatar.avatar-sm { width: 40px; height: 40px; font-size: 1rem; }
            .profile-avatar.avatar-lg { width: 90px; height: 90px; font-size: 2.2rem; }
            .stats-box { padding: 14px; }
            .stats-box h3 { font-size: 1.3rem; }
            .badge { font-size: 0.7rem; padding: 6px 12px; }
            .btn-sm { padding: 6px 12px; font-size: 0.75rem; }
            .table td, .table th { padding: 12px 10px; font-size: 0.85rem; }
            .pin-box { font-size: 1.1rem; letter-spacing: 4px; padding: 10px 14px; }
            .fab { width: 56px; height: 56px; font-size: 1.4rem; bottom: 16px; right: 16px; }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gray-400); }

        /* Phone Verification */
        .phone-verified { color: var(--success); }
        .phone-not-verified { color: var(--warning); }

        /* Section Divider */
        .section-divider {
            display: flex;
            align-items: center;
            margin: 28px 0;
            color: var(--gray-400);
            font-size: 0.9rem;
            font-weight: 600;
        }
        .section-divider::before, .section-divider::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gray-200), transparent);
        }
        .section-divider::before { margin-right: 16px; }
        .section-divider::after { margin-left: 16px; }

        /* ==========================================
           BARQ ULTRA PREMIUM - HEADER WIDGET
           ========================================== */
        .header-section {
            padding: 20px 20px 0 20px;
            margin-bottom: 10px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .user-info { display: flex; align-items: center; gap: 12px; }
        .avatar-circle {
            width: 48px; height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, #FF9966, #FF5E62);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: bold; font-size: 1.2rem;
            box-shadow: 0 8px 16px rgba(255, 94, 98, 0.2);
            overflow: hidden;
        }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-circle.driver { background: linear-gradient(135deg, #10b981, #06b6d4); }
        .avatar-circle.customer { background: linear-gradient(135deg, var(--primary), #8b5cf6); }
        .avatar-circle.admin { background: linear-gradient(135deg, #f43f5e, #ec4899); }
        .greeting h3 { font-size: 1.1rem; font-weight: 800; line-height: 1.1; color: var(--text-main); }
        .greeting span { font-size: 0.8rem; color: var(--text-sub); font-weight: 600; }

        .notif-btn {
            width: 44px; height: 44px;
            background: white; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: var(--text-main);
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            position: relative;
            text-decoration: none;
            transition: var(--transition);
        }
        .notif-btn:hover { transform: scale(1.05); }
        .notif-badge {
            position: absolute; top: 10px; right: 12px;
            width: 8px; height: 8px; background: #FF4444;
            border-radius: 50%; border: 2px solid white;
        }

        /* ==========================================
           DASHBOARD SUMMARY CARDS
           ========================================== */
        .stats-scroll {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding: 0 20px 20px 20px;
            scrollbar-width: none;
        }
        .stats-scroll::-webkit-scrollbar { display: none; }

        .stat-card-new {
            min-width: 140px;
            padding: 18px;
            border-radius: var(--radius-lg);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 130px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s;
            flex-shrink: 0;
        }
        .stat-card-new:active { transform: scale(0.95); }

        /* Card Styles */
        .card-purple {
            background: linear-gradient(135deg, #667EEA, #764BA2);
            color: white;
            box-shadow: 0 10px 20px rgba(118, 75, 162, 0.3);
        }
        .card-new-white {
            background: white;
            color: var(--text-main);
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        }

        .stat-icon-new { font-size: 1.4rem; opacity: 0.8; margin-bottom: 10px; }
        .stat-num-new { font-size: 1.6rem; font-weight: 800; line-height: 1; }
        .stat-label-new { font-size: 0.75rem; font-weight: 600; opacity: 0.8; margin-top: 5px; }

        /* ==========================================
           FILTER TABS
           ========================================== */
        .tabs-wrapper {
            padding: 0 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            overflow-x: auto;
            scrollbar-width: none;
        }
        .tabs-wrapper::-webkit-scrollbar { display: none; }
        .tab-new {
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 700;
            background: white;
            color: var(--text-sub);
            border: 1px solid transparent;
            transition: 0.3s;
            white-space: nowrap;
            cursor: pointer;
        }
        .tab-new.active {
            background: var(--text-main);
            color: white;
            box-shadow: 0 5px 15px rgba(26, 29, 38, 0.2);
        }

        /* ==========================================
           ULTRA CARDS
           ========================================== */
        .orders-container { padding: 0 20px; }

        .ultra-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            padding: 5px;
            margin-bottom: 25px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.04);
            position: relative;
        }

        .card-inner {
            background: #FAFAFC;
            border-radius: 24px;
            padding: 20px;
            border: 1px solid #F0F0F5;
        }

        /* Header of Card */
        .c-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #E0E0E0;
        }

        .price-tag {
            font-size: 1.2rem;
            font-weight: 900;
            color: var(--text-main);
            display: flex; align-items: center; gap: 5px;
        }
        .price-tag i { color: #FFD700; font-size: 0.9rem; }
        .price-tag .currency { font-size: 0.8rem; font-weight: 600; color: var(--text-sub); }

        .time-tag {
            background: #FFF0F0; color: #FF4444;
            padding: 6px 12px; border-radius: 12px;
            font-size: 0.75rem; font-weight: 700;
            display: flex; align-items: center; gap: 5px;
        }
        .time-tag.blue { background: #E3F2FD; color: #2196F3; }

        /* Route Visual */
        .route-row { display: flex; gap: 15px; margin-bottom: 25px; }

        .visual-connector {
            display: flex; flex-direction: column; align-items: center;
            padding-top: 5px;
        }
        .dot-circle { width: 12px; height: 12px; border-radius: 50%; }
        .dot-p { background: var(--primary); box-shadow: 0 0 0 3px rgba(88, 75, 246, 0.15); }
        .dot-d { background: var(--text-main); }
        .line-dashed {
            width: 2px; height: 40px;
            background: repeating-linear-gradient(to bottom, #dcdde1 0, #dcdde1 4px, transparent 4px, transparent 8px);
            margin: 4px 0;
        }

        .text-info-route { display: flex; flex-direction: column; justify-content: space-between; height: 75px; flex: 1; }
        .loc-title { font-weight: 700; font-size: 0.95rem; color: var(--text-main); }
        .loc-sub { font-size: 0.8rem; color: var(--text-sub); }

        /* Meta Tags */
        .meta-tags {
            display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;
        }
        .tag-new {
            font-size: 0.7rem; padding: 6px 10px; border-radius: 8px; font-weight: 700;
            display: flex; align-items: center; gap: 4px;
        }
        .tag-cash { background: #E8F5E9; color: #2E7D32; }
        .tag-heavy { background: #FFF3E0; color: #EF6C00; }
        .tag-bank { background: #F3E5F5; color: #9C27B0; }
        .tag-pending { background: #FFF8E1; color: #F57F17; }
        .tag-accepted { background: #E3F2FD; color: #1565C0; }
        .tag-picked { background: #EDE7F6; color: #5E35B1; }
        .tag-delivered { background: #E8F5E9; color: #2E7D32; }

        /* Slider Button */
        .slider-btn-container {
            position: relative;
            background: var(--text-main);
            height: 56px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .slider-btn-container.success { background: var(--success); }
        .slider-btn-container.info { background: #2196F3; }
        .slider-btn-container.muted { background: var(--text-sub); }

        .slider-text {
            color: rgba(255,255,255,0.9);
            font-weight: 700;
            font-size: 0.95rem;
            z-index: 1;
        }

        .slider-thumb {
            position: absolute;
            right: 4px;
            top: 4px;
            bottom: 4px;
            width: 48px;
            background: white;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-main);
            font-size: 1.2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: 0.3s;
        }
        [dir="rtl"] .slider-thumb { right: auto; left: 4px; }
        .slider-btn-container:active .slider-thumb {
            transform: translateX(-10px);
        }
        [dir="rtl"] .slider-btn-container:active .slider-thumb {
            transform: translateX(10px);
        }

        /* ==========================================
           GLASS NAV
           ========================================== */
        .glass-nav {
            position: fixed;
            bottom: 25px;
            left: 20px;
            right: 20px;
            height: 75px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 25px;
            border: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            z-index: 100;
        }

        .nav-icon {
            font-size: 1.4rem;
            color: #C2C6D1;
            transition: 0.3s;
            cursor: pointer;
            padding: 10px;
        }
        .nav-icon.active { color: var(--text-main); }
        .nav-icon:hover { color: var(--primary); }

        /* Central Floating Button */
        .nav-center-btn {
            width: 65px; height: 65px;
            background: var(--primary);
            border-radius: 50%;
            margin-top: -35px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 10px 25px var(--primary-glow);
            border: 4px solid var(--bg-body);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }
        .nav-center-btn:hover {
            transform: scale(1.1);
            color: white;
        }
        .nav-center-btn.online { background: var(--success); box-shadow: 0 10px 25px rgba(0,200,81,0.5); }
        .nav-center-btn.offline { background: var(--gray-400); }

        /* Order actions in ultra card */
        .order-actions-row {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .order-actions-row .btn { flex: 1; }

        /* PIN input in card */
        .pin-input-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .pin-input-row input {
            flex: 1;
            height: 48px;
            border: 2px solid var(--success);
            border-radius: 12px;
            text-align: center;
            font-weight: bold;
            font-size: 1.1rem;
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
                <i class="fa-solid fa-bolt login-logo-icon"></i>
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
                <a href="https://wa.me/<?php echo $whatsapp_number; ?>" class="login-social-btn whatsapp" title="WhatsApp" target="_blank">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>

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
                    <i class="fas fa-coins"></i> <span id="currentPoints"><?php echo $u['points']; ?></span>
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
                        <div class="stat-num-new" id="driverPoints"><?php echo number_format($u['points']); ?></div>
                        <div class="stat-label-new"><?php echo $t['balance'] ?? 'الأرباح'; ?> (<?php echo $t['pts'] ?? 'نقطة'; ?>)</div>
                    </div>
                    <div style="position:absolute; top:-10px; left:-10px; width:60px; height:60px; background:rgba(255,255,255,0.1); border-radius:50%;"></div>
                </div>

                <?php if(!empty($u['rating'])): ?>
                <div class="stat-card-new card-new-white">
                    <i class="fa-solid fa-star stat-icon-new" style="color:#FFD700"></i>
                    <div>
                        <div class="stat-num-new" style="color:var(--text-main)"><?php echo number_format($u['rating'], 1); ?></div>
                        <div class="stat-label-new"><?php echo $t['rating'] ?? 'التقييم'; ?></div>
                    </div>
                </div>
                <?php endif; ?>

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

            <!-- GPS Toggle (Hidden - controlled by nav button) -->
            <div id="gpsToggle" style="display:none;">
                <span class="gps-accuracy-badge" id="gpsAccuracyBadge" style="display: none;">
                    <i class="fas fa-signal"></i>
                    <span id="gpsAccuracyValue">--</span>m
                </span>
            </div>
            <div id="gpsStatusLabel" style="display:none;"></div>
            <div id="gpsStatusDetail" style="display:none;"></div>

            <!-- Online Toggle Form (Hidden) -->
            <form method="POST" id="onlineToggleForm" style="display:none;">
                <input type="checkbox" id="onlineSwitch" name="is_online" value="1" <?php echo $u['is_online'] ? 'checked' : ''; ?>>
                <input type="hidden" name="toggle_online" value="1">
            </form>

            <?php if($u['points'] < $points_cost_per_order): ?>
            <div class="orders-container mb-3">
                <div class="alert alert-danger d-flex align-items-center gap-2" id="lowBalanceWarning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?php echo $t['err_low_bal']; ?></span>
                    <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=Recharge%20User:%20<?php echo $u['username']; ?>" target="_blank" class="btn btn-sm btn-success ms-auto">
                        <i class="fab fa-whatsapp me-1"></i><?php echo $t['recharge_wa']; ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
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
                                           placeholder="<?php echo $t['click_gps'] ?? 'Click GPS to set your location'; ?>" required readonly>
                                    <button type="button" class="btn btn-success px-4" onclick="getPickupLocation()" id="gpsBtn" title="<?php echo $t['turn_on_gps'] ?? 'Turn on GPS'; ?>">
                                        <i class="fas fa-location-crosshairs"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="pickup_lat" id="pickupLat" required>
                                <input type="hidden" name="pickup_lng" id="pickupLng" required>
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i><?php echo $t['gps_required'] ?? 'GPS location is required for drivers to find you'; ?></small>
                            </div>

                            <div class="slider-btn-container" onclick="document.getElementById('newOrderForm').submit();">
                                <div class="slider-thumb"><i class="fa-solid fa-paper-plane"></i></div>
                                <div class="slider-text"><?php echo $t['btn_publish']; ?></div>
                                <button name="add_order" style="display:none;"></button>
                            </div>
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
                    $limit = "WHERE customer_name='{$u['username']}' OR client_id='$uid'";
                    $sql = "SELECT * FROM orders1 $limit ORDER BY id DESC LIMIT 50";
                    $res = $conn->query($sql);
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
                    $statusTagClass = ($st == 'pending') ? 'tag-pending' : (($st == 'accepted') ? 'tag-accepted' : (($st == 'picked_up') ? 'tag-picked' : 'tag-delivered'));
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
                            <form method="POST">
                                <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="pickup_order" class="slider-btn-container info w-100">
                                    <div class="slider-thumb"><i class="fa-solid fa-box"></i></div>
                                    <div class="slider-text"><?php echo $t['driver_pickup'] ?? 'Picked Up'; ?></div>
                                </button>
                            </form>

                            <?php elseif($st == 'picked_up' && $row['driver_id'] == $uid): ?>
                            <form method="POST" class="pin-input-row">
                                <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                <input type="text" name="entered_pin" placeholder="<?php echo $t['enter_pin'] ?? 'Enter PIN'; ?>" required pattern="[0-9]{4}" maxlength="4" inputmode="numeric">
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
                            <?php elseif($st == 'accepted' || $st == 'picked_up'): ?>
                            <div class="slider-btn-container info w-100" onclick="showOrderTracking(<?php echo htmlspecialchars(json_encode($row)); ?>)" style="cursor:pointer;">
                                <div class="slider-thumb"><i class="fa-solid fa-location-dot"></i></div>
                                <div class="slider-text"><?php echo $t['track_order'] ?? 'Track Order'; ?></div>
                            </div>
                            <?php elseif($st == 'delivered' && empty($row['rating'])): ?>
                            <form method="POST" class="order-actions-row">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <select name="rating" class="form-select" required style="border-radius: 12px;">
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
            <nav class="glass-nav">
                <a href="index.php" class="nav-icon active"><i class="fa-solid fa-house"></i></a>
                <a href="?settings=1" class="nav-icon"><i class="fa-solid fa-chart-simple"></i></a>

                <div class="nav-center-btn <?php echo $u['is_online'] ? 'online' : 'offline'; ?>" onclick="document.getElementById('onlineSwitch').checked = !document.getElementById('onlineSwitch').checked; document.getElementById('onlineToggleForm').submit();" title="<?php echo $u['is_online'] ? ($t['go_offline'] ?? 'Go Offline') : ($t['go_online'] ?? 'Go Online'); ?>">
                    <i class="fa-solid fa-power-off"></i>
                </div>

                <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=Recharge%20User:%20<?php echo $u['username']; ?>" target="_blank" class="nav-icon"><i class="fa-solid fa-wallet"></i></a>
                <a href="?settings=1" class="nav-icon"><i class="fa-regular fa-user"></i></a>
            </nav>
            <?php else: ?>
            <nav class="glass-nav">
                <a href="index.php" class="nav-icon active"><i class="fa-solid fa-house"></i></a>
                <a href="#" class="nav-icon" onclick="document.getElementById('newOrderForm').scrollIntoView({behavior: 'smooth'})"><i class="fa-solid fa-plus"></i></a>

                <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo urlencode($t['need_help'] ?? 'Hello, I need help'); ?>" target="_blank" class="nav-center-btn">
                    <i class="fab fa-whatsapp"></i>
                </a>

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

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ==========================================
// RTL Support - Phone Numbers Display
// ==========================================
const isRTL = document.documentElement.dir === 'rtl';

// Keep phone and number inputs LTR for correct display
document.addEventListener('DOMContentLoaded', function() {
    // Make phone inputs LTR for proper number display (always left-aligned)
    document.querySelectorAll('input[type="tel"], input[name*="phone"]').forEach(input => {
        input.style.direction = 'ltr';
        input.style.textAlign = 'left';
    });

    // Make number inputs LTR (always left-aligned for proper number entry)
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.style.direction = 'ltr';
        input.style.textAlign = 'left';
    });

    // Ensure phone number displays stay LTR
    document.querySelectorAll('.phone-display, [data-phone]').forEach(el => {
        el.style.direction = 'ltr';
        el.style.unicodeBidi = 'embed';
        el.style.display = 'inline-block';
        el.style.textAlign = 'left';
    });
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
    const loginFooterText = document.getElementById('loginFooterText');
    const registerFooterText = document.getElementById('registerFooterText');

    if (!loginForm || !registerForm) return;

    loginForm.classList.remove('active');
    registerForm.classList.remove('active');
    loginToggle.classList.remove('active');
    registerToggle.classList.remove('active');

    document.getElementById(form + 'Form').classList.add('active');
    document.getElementById(form + 'Toggle').classList.add('active');

    // Toggle footer text
    if (loginFooterText && registerFooterText) {
        if (form === 'login') {
            loginFooterText.style.display = 'block';
            registerFooterText.style.display = 'none';
        } else {
            loginFooterText.style.display = 'none';
            registerFooterText.style.display = 'block';
        }
    }
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

// Get pickup location for customer orders
function getPickupLocation() {
    if (!navigator.geolocation) {
        alert('<?php echo $t['geolocation_not_supported'] ?? 'Geolocation is not supported by your browser'; ?>');
        return;
    }

    const btn = document.getElementById('gpsBtn');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            document.getElementById('pickupLat').value = lat;
            document.getElementById('pickupLng').value = lng;

            // Reverse geocode to get address
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=<?php echo $lang; ?>`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        let address = data.display_name.split(', ').slice(0, 3).join(', ');
                        document.getElementById('pickupAddress').value = address;
                    } else {
                        document.getElementById('pickupAddress').value = lat.toFixed(5) + ', ' + lng.toFixed(5);
                    }
                    btn.innerHTML = '<i class="fas fa-check"></i>';
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-success');
                        btn.disabled = false;
                    }, 2000);
                })
                .catch(() => {
                    document.getElementById('pickupAddress').value = lat.toFixed(5) + ', ' + lng.toFixed(5);
                    btn.innerHTML = '<i class="fas fa-check"></i>';
                    btn.disabled = false;
                });
        },
        (error) => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            let msg = '<?php echo $t['location_error'] ?? 'Error getting location'; ?>';
            if (error.code === error.PERMISSION_DENIED) {
                msg = '<?php echo $t['location_denied'] ?? 'Location access denied. Please enable GPS.'; ?>';
            }
            alert(msg);
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
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

// ==========================================
// DRIVER GPS TOGGLE FUNCTIONALITY
// ==========================================
let gpsEnabled = false;
let gpsWatchId = null;

function toggleDriverGPS() {
    const btn = document.getElementById('gpsToggleBtn');
    const toggle = document.getElementById('gpsToggle');
    const label = document.getElementById('gpsStatusLabel');
    const detail = document.getElementById('gpsStatusDetail');
    const accuracyBadge = document.getElementById('gpsAccuracyBadge');

    if (!navigator.geolocation) {
        alert('<?php echo $t['geolocation_not_supported'] ?? 'Geolocation is not supported by your browser'; ?>');
        return;
    }

    if (gpsEnabled) {
        // Disable GPS
        if (gpsWatchId !== null) {
            navigator.geolocation.clearWatch(gpsWatchId);
            gpsWatchId = null;
        }
        gpsEnabled = false;

        btn.classList.remove('on', 'loading');
        btn.classList.add('off');
        toggle.classList.remove('active');
        label.classList.remove('on');
        label.classList.add('off');
        label.innerHTML = '<i class="fas fa-satellite-dish me-1"></i><?php echo $t['gps_disabled'] ?? 'GPS Disabled'; ?>';
        detail.textContent = '<?php echo $t['gps_driver_note'] ?? 'Enable GPS to see nearby orders'; ?>';
        accuracyBadge.style.display = 'none';

        // Reset driver location variables
        driverLat = null;
        driverLng = null;
    } else {
        // Enable GPS
        btn.classList.remove('off', 'on');
        btn.classList.add('loading');
        btn.innerHTML = '<i class="fas fa-spinner"></i>';
        label.innerHTML = '<i class="fas fa-satellite-dish me-1"></i><?php echo $t['updating_location'] ?? 'Updating location...'; ?>';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                gpsEnabled = true;
                driverLat = position.coords.latitude;
                driverLng = position.coords.longitude;
                const accuracy = Math.round(position.coords.accuracy);

                btn.classList.remove('loading');
                btn.classList.add('on');
                btn.innerHTML = '<i class="fas fa-location-crosshairs"></i>';
                toggle.classList.add('active');
                label.classList.remove('off');
                label.classList.add('on');
                label.innerHTML = '<i class="fas fa-check-circle me-1"></i><?php echo $t['gps_enabled'] ?? 'GPS Enabled'; ?>';
                detail.textContent = '<?php echo $t['location_updated'] ?? 'Location updated'; ?>';
                accuracyBadge.style.display = 'inline-flex';
                document.getElementById('gpsAccuracyValue').textContent = accuracy;

                // Update location on server
                updateMyLocation();

                // Start watching position
                gpsWatchId = navigator.geolocation.watchPosition(
                    (pos) => {
                        driverLat = pos.coords.latitude;
                        driverLng = pos.coords.longitude;
                        const acc = Math.round(pos.coords.accuracy);
                        document.getElementById('gpsAccuracyValue').textContent = acc;
                        updateMyLocation();
                    },
                    () => {},
                    { enableHighAccuracy: true, timeout: 30000, maximumAge: 5000 }
                );

                // Start fetching nearby orders
                fetchNearbyOrders();
            },
            (error) => {
                btn.classList.remove('loading');
                btn.classList.add('off');
                btn.innerHTML = '<i class="fas fa-location-crosshairs"></i>';
                label.innerHTML = '<i class="fas fa-exclamation-triangle me-1 text-warning"></i><?php echo $t['location_error'] ?? 'Location error'; ?>';

                let msg = '<?php echo $t['location_error'] ?? 'Error getting location'; ?>';
                if (error.code === error.PERMISSION_DENIED) {
                    msg = '<?php echo $t['location_denied'] ?? 'Location access denied. Please enable GPS.'; ?>';
                    detail.textContent = msg;
                }
                alert(msg);
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }
}

// Start location updates for drivers
<?php if(isset($_SESSION['user']) && $role === 'driver'): ?>
setInterval(updateMyLocation, 60000); // Update every minute
updateMyLocation(); // Initial update

// Auto-enable GPS if driver has existing location
<?php if(!empty($u['last_lat']) && !empty($u['last_lng'])): ?>
// Driver has previous location, auto-enable GPS
setTimeout(() => {
    if (!gpsEnabled) {
        toggleDriverGPS();
    }
}, 1000);
<?php endif; ?>

// ==========================================
// DRIVER ORDER NOTIFICATION BUBBLES
// ==========================================

// Track displayed order IDs to prevent duplicates
let displayedOrderIds = new Set();
let driverLat = null;
let driverLng = null;

// Play notification ring sound
function playOrderRingSound() {
    try {
        const ctx = getAudioContext();
        const now = ctx.currentTime;

        // Create a pleasant ring tone
        for (let i = 0; i < 3; i++) {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, now + i * 0.3); // A5 note
            osc.frequency.setValueAtTime(1100, now + i * 0.3 + 0.1); // C#6 note

            gain.gain.setValueAtTime(0.3, now + i * 0.3);
            gain.gain.exponentialRampToValueAtTime(0.01, now + i * 0.3 + 0.25);

            osc.start(now + i * 0.3);
            osc.stop(now + i * 0.3 + 0.3);
        }
    } catch(e) {
        console.log('Sound not available');
    }
}

// Create order bubble HTML
function createOrderBubble(order) {
    const bubble = document.createElement('div');
    bubble.className = 'order-bubble';
    bubble.id = `order-bubble-${order.id}`;
    bubble.dataset.orderId = order.id;

    const distanceText = order.distance ? `${parseFloat(order.distance).toFixed(1)} <?php echo $t['km'] ?? 'km'; ?>` : '---';
    const phone = order.client_phone || '<?php echo $t['no_phone'] ?? 'No phone'; ?>';

    bubble.innerHTML = `
        <div class="order-bubble-header">
            <div class="new-order-badge">
                <i class="fas fa-bell"></i>
                <?php echo $t['new_order_nearby'] ?? 'New Order Nearby!'; ?>
            </div>
            <div class="order-bubble-timer"><span class="timer-seconds">10</span>s</div>
        </div>
        <div class="order-bubble-body">
            <div class="order-bubble-distance">
                <i class="fas fa-route"></i>
                ${distanceText}
            </div>
            <div class="order-bubble-details">${escapeHtml(order.details)}</div>
            <div class="order-bubble-address">
                <i class="fas fa-map-marker-alt"></i>
                <span>${escapeHtml(order.address)}</span>
            </div>
            <div class="order-bubble-phone">
                <i class="fas fa-phone"></i>
                <a href="tel:+222${phone}" dir="ltr">+222 ${phone}</a>
            </div>
            <div class="order-bubble-actions">
                <button class="btn btn-accept" onclick="acceptOrderFromBubble(${order.id}, this)">
                    <i class="fas fa-check me-2"></i><?php echo $t['accept'] ?? 'Accept'; ?>
                </button>
                <button class="btn btn-decline" onclick="declineOrderBubble(${order.id})">
                    <i class="fas fa-times me-2"></i><?php echo $t['decline'] ?? 'Decline'; ?>
                </button>
            </div>
        </div>
        <div class="order-bubble-progress">
            <div class="order-bubble-progress-bar" style="width: 100%"></div>
        </div>
    `;

    return bubble;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

// Show order bubble with countdown
function showOrderBubble(order) {
    if (displayedOrderIds.has(order.id)) return;

    displayedOrderIds.add(order.id);

    const container = document.getElementById('orderBubbleContainer');
    if (!container) return;

    const bubble = createOrderBubble(order);
    container.appendChild(bubble);

    // Play ring sound
    playOrderRingSound();

    // Start countdown
    let secondsLeft = 10;
    const timerSpan = bubble.querySelector('.timer-seconds');
    const progressBar = bubble.querySelector('.order-bubble-progress-bar');

    const countdown = setInterval(() => {
        secondsLeft--;
        if (timerSpan) timerSpan.textContent = secondsLeft;
        if (progressBar) progressBar.style.width = (secondsLeft / 10 * 100) + '%';

        if (secondsLeft <= 0) {
            clearInterval(countdown);
            removeBubble(order.id);
        }
    }, 1000);

    // Store countdown reference
    bubble.dataset.countdown = countdown;
}

// Accept order from bubble
function acceptOrderFromBubble(orderId, btn) {
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    // Submit accept request
    fetch('actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `accept_order=1&oid=${orderId}`
    })
    .then(response => {
        if (response.redirected || response.ok) {
            // Success - remove bubble and reload page
            removeBubble(orderId);
            showNotification('<?php echo $t['success'] ?? 'Success'; ?>', '<?php echo $t['order_accepted'] ?? 'Order accepted!'; ?>', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(() => {
        btn.innerHTML = '<i class="fas fa-check me-2"></i><?php echo $t['accept'] ?? 'Accept'; ?>';
        btn.disabled = false;
        showNotification('<?php echo $t['error'] ?? 'Error'; ?>', '<?php echo $t['try_again'] ?? 'Please try again'; ?>', 'warning');
    });
}

// Decline order bubble (just dismiss it)
function declineOrderBubble(orderId) {
    removeBubble(orderId);
    // Keep in set for this session to avoid showing again
}

// Remove bubble with animation
function removeBubble(orderId) {
    const bubble = document.getElementById(`order-bubble-${orderId}`);
    if (bubble) {
        // Clear countdown
        if (bubble.dataset.countdown) {
            clearInterval(parseInt(bubble.dataset.countdown));
        }

        bubble.classList.add('fade-out');
        setTimeout(() => bubble.remove(), 400);
    }
}

// Fetch nearby orders for driver
function fetchNearbyOrders() {
    if (!driverLat || !driverLng) {
        // Get current position first
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    driverLat = pos.coords.latitude;
                    driverLng = pos.coords.longitude;
                    doFetchNearbyOrders();
                },
                () => {}
            );
        }
        return;
    }
    doFetchNearbyOrders();
}

function doFetchNearbyOrders() {
    fetch(`api.php?action=get_nearby_orders&lat=${driverLat}&lng=${driverLng}&max_distance=7`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.orders && data.orders.length > 0) {
                data.orders.forEach(order => {
                    showOrderBubble(order);
                });
            }
        })
        .catch(() => {});
}

// Initialize driver GPS and start polling
navigator.geolocation.getCurrentPosition(
    (pos) => {
        driverLat = pos.coords.latitude;
        driverLng = pos.coords.longitude;

        // Start polling for new orders every 15 seconds
        fetchNearbyOrders();
        setInterval(fetchNearbyOrders, 15000);
    },
    () => {
        console.log('GPS not available - order notifications disabled');
    },
    { enableHighAccuracy: true }
);

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
