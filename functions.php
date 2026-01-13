<?php
/**
 * Helper Functions & Translations
 * Enhanced Delivery Pro System v2.0
 */

// ==========================================
// LANGUAGE SETTINGS
// ==========================================
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'fr'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'ar';
$dir = ($lang == 'ar') ? 'rtl' : 'ltr';

// ==========================================
// HELPER FUNCTIONS
// ==========================================

/**
 * Escape HTML entities for safe output
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format date according to current language
 */
function fmtDate($date) {
    global $lang;
    $timestamp = strtotime($date);
    $now = time();
    $diff = $now - $timestamp;

    // Show relative time for recent dates
    if ($diff < 60) {
        return $lang == 'ar' ? 'الآن' : 'Maintenant';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $lang == 'ar' ? "منذ {$mins} دقيقة" : "Il y a {$mins} min";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $lang == 'ar' ? "منذ {$hours} ساعة" : "Il y a {$hours}h";
    }

    if ($lang == 'ar') {
        $months_ar = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                      'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        $day = date('d', $timestamp);
        $month = $months_ar[(int)date('m', $timestamp)];
        $year = date('Y', $timestamp);
        $time = date('h:i A', $timestamp);
        return "$day $month $year - $time";
    }

    return date('d/m/Y H:i', $timestamp);
}

/**
 * Set flash message in session
 */
function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

/**
 * Get and display flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $icon = ($f['type'] == 'success') ? 'check-circle' : (($f['type'] == 'warning') ? 'exclamation-circle' : 'exclamation-triangle');
        $cls = ($f['type'] == 'error') ? 'danger' : $f['type'];
        return "
        <div class='alert alert-{$cls} alert-dismissible fade show shadow-sm border-0 mb-4 animate__animated animate__fadeInDown' role='alert'>
            <div class='d-flex align-items-center'>
                <i class='fas fa-{$icon} fa-lg me-3'></i>
                <div>{$f['msg']}</div>
            </div>
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
    return '';
}

/**
 * Get status badge class
 */
function getStatusBadge($status) {
    $badges = [
        'pending' => 'badge-pending',
        'accepted' => 'badge-accepted',
        'picked_up' => 'badge-picked_up',
        'delivered' => 'badge-delivered',
        'cancelled' => 'badge-cancelled'
    ];
    return $badges[$status] ?? 'bg-secondary';
}

/**
 * Get status icon
 */
function getStatusIcon($status) {
    $icons = [
        'pending' => 'clock',
        'accepted' => 'truck',
        'picked_up' => 'box',
        'delivered' => 'check-double',
        'cancelled' => 'times-circle'
    ];
    return $icons[$status] ?? 'circle';
}

/**
 * Get user avatar URL or generate initials avatar
 */
function getAvatarUrl($user) {
    if (!empty($user['avatar_url'])) {
        $fullPath = __DIR__ . '/' . $user['avatar_url'];
        if (file_exists($fullPath)) {
            // Add cache-busting parameter based on file modification time
            $mtime = filemtime($fullPath);
            return $user['avatar_url'] . '?v=' . $mtime;
        }
    }
    // Return null to use initials avatar
    return null;
}

/**
 * Get user initials for avatar
 */
function getUserInitials($user) {
    $name = $user['full_name'] ?? $user['username'] ?? 'U';
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    }
    return mb_strtoupper(mb_substr($name, 0, 2));
}

/**
 * Get avatar background color based on role
 */
function getAvatarColor($role) {
    $colors = [
        'admin' => '#dc2626',
        'driver' => '#0891b2',
        'customer' => '#059669'
    ];
    return $colors[$role] ?? '#6366f1';
}

/**
 * Handle avatar upload
 */
function uploadAvatar($file, $userId) {
    global $uploads_dir;

    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error'];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File too large (max 5MB)'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!in_array($mime, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    // Create user directory
    $user_dir = $uploads_dir . '/avatars/' . $userId;
    if (!is_dir($user_dir)) {
        mkdir($user_dir, 0755, true);
    }

    // Generate filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . time() . '.' . $ext;
    $filepath = $user_dir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'path' => 'uploads/avatars/' . $userId . '/' . $filename
        ];
    }

    return ['success' => false, 'error' => 'Failed to save file'];
}

/**
 * Format rating stars
 */
function formatRating($rating, $showNumber = true) {
    $rating = floatval($rating);
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    $html = '<span class="rating-stars">';
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="fas fa-star text-warning"></i>';
    }
    if ($halfStar) {
        $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="far fa-star text-warning"></i>';
    }
    if ($showNumber) {
        $html .= ' <small class="text-muted">(' . number_format($rating, 1) . ')</small>';
    }
    $html .= '</span>';

    return $html;
}

/**
 * Get driver stats
 */
function getDriverStats($conn, $driverId) {
    $stats = [
        'total_orders' => 0,
        'completed_today' => 0,
        'earnings_today' => 0,
        'earnings_week' => 0,
        'earnings_month' => 0,
        'rating' => 5.0
    ];

    // Total completed orders
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders1 WHERE driver_id = ? AND status = 'delivered'");
    $stmt->execute([$driverId]);
    $stats['total_orders'] = $stmt->fetchColumn();

    // Completed today
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders1 WHERE driver_id = ? AND status = 'delivered' AND DATE(delivered_at) = CURDATE()");
    $stmt->execute([$driverId]);
    $stats['completed_today'] = $stmt->fetchColumn();

    // Earnings (points spent by driver for orders)
    $stmt = $conn->prepare("SELECT COALESCE(SUM(points_cost), 0) FROM orders1 WHERE driver_id = ? AND status = 'delivered' AND DATE(delivered_at) = CURDATE()");
    $stmt->execute([$driverId]);
    $stats['earnings_today'] = $stmt->fetchColumn();

    // This week
    $stmt = $conn->prepare("SELECT COALESCE(SUM(points_cost), 0) FROM orders1 WHERE driver_id = ? AND status = 'delivered' AND YEARWEEK(delivered_at) = YEARWEEK(NOW())");
    $stmt->execute([$driverId]);
    $stats['earnings_week'] = $stmt->fetchColumn();

    // This month
    $stmt = $conn->prepare("SELECT COALESCE(SUM(points_cost), 0) FROM orders1 WHERE driver_id = ? AND status = 'delivered' AND MONTH(delivered_at) = MONTH(NOW()) AND YEAR(delivered_at) = YEAR(NOW())");
    $stmt->execute([$driverId]);
    $stats['earnings_month'] = $stmt->fetchColumn();

    // Average rating
    $stmt = $conn->prepare("SELECT AVG(score) FROM ratings WHERE ratee_id = ?");
    $stmt->execute([$driverId]);
    $avgRating = $stmt->fetchColumn();
    if ($avgRating) {
        $stats['rating'] = round($avgRating, 2);
    }

    return $stats;
}

/**
 * Get client stats
 */
function getClientStats($conn, $clientId, $username) {
    $stats = [
        'total_orders' => 0,
        'active' => 0,
        'delivered' => 0,
        'this_month' => 0
    ];

    // Total orders
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders1 WHERE client_id = ? OR customer_name = ?");
    $stmt->execute([$clientId, $username]);
    $stats['total_orders'] = $stmt->fetchColumn();

    // Active orders (pending, accepted, picked_up)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders1 WHERE (client_id = ? OR customer_name = ?) AND status IN ('pending', 'accepted', 'picked_up')");
    $stmt->execute([$clientId, $username]);
    $stats['active'] = $stmt->fetchColumn();

    // Delivered
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders1 WHERE (client_id = ? OR customer_name = ?) AND status = 'delivered'");
    $stmt->execute([$clientId, $username]);
    $stats['delivered'] = $stmt->fetchColumn();

    // This month
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders1 WHERE (client_id = ? OR customer_name = ?) AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
    $stmt->execute([$clientId, $username]);
    $stats['this_month'] = $stmt->fetchColumn();

    return $stats;
}

/**
 * Count active orders for a driver
 */
function countActiveOrders($conn, $driverId) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders1 WHERE driver_id = ? AND status IN ('accepted', 'picked_up')");
    $stmt->execute([$driverId]);
    return $stmt->fetchColumn();
}

/**
 * Check if phone is verified
 */
function isPhoneVerified($user) {
    return !empty($user['phone']) && !empty($user['phone_verified']);
}

// ==========================================
// COMPLETE TRANSLATIONS
// ==========================================
$text = [
    'ar' => [
        // App
        'app_name' => 'نظام التوصيل برق',
        'app_desc' => 'خدمة توصيل سريعة وموثوقة',
        'welcome' => 'مرحباً',
        'welcome_back' => 'مرحباً بعودتك',

        // Auth
        'login_title' => 'تسجيل الدخول',
        'register_title' => 'إنشاء حساب جديد',
        'user_ph' => 'اسم المستخدم',
        'pass_ph' => 'كلمة المرور',
        'confirm_pass_ph' => 'تأكيد كلمة المرور',
        'full_name_ph' => 'الاسم الكامل',
        'phone_ph' => 'رقم الهاتف',
        'email_ph' => 'البريد الإلكتروني',
        'btn_login' => 'تسجيل الدخول',
        'btn_register' => 'إنشاء حساب',
        'have_account' => 'لديك حساب بالفعل؟',
        'no_account' => 'ليس لديك حساب؟',
        'login_here' => 'سجل دخولك',
        'register_here' => 'أنشئ حساباً',
        'logout' => 'تسجيل الخروج',
        'logout_confirm' => 'هل تريد تسجيل الخروج؟',

        // Dashboard
        'dashboard' => 'لوحة التحكم',
        'home' => 'الرئيسية',
        'settings' => 'الإعدادات',
        'profile' => 'الملف الشخصي',
        'my_account' => 'حسابي',

        // Balance & Points
        'balance' => 'رصيدي',
        'points' => 'نقطة',
        'pts' => 'نقطة',
        'current_balance' => 'الرصيد الحالي',
        'recharge_wa' => 'شحن عبر واتساب',
        'recharge_now' => 'اشحن الآن',
        'low_balance' => 'رصيد منخفض',

        // Orders
        'new_order' => 'طلب جديد',
        'create_order' => 'إنشاء طلب',
        'order_details' => 'تفاصيل الطلب',
        'order_info' => 'معلومات الطلب',
        'address' => 'العنوان',
        'delivery_address' => 'عنوان التوصيل',
        'btn_publish' => 'نشر الطلب',
        'recent_orders' => 'الطلبات',
        'my_orders' => 'طلباتي',
        'all_orders' => 'جميع الطلبات',
        'available_orders' => 'الطلبات المتاحة',
        'order_number' => 'رقم الطلب',
        'order_date' => 'تاريخ الطلب',

        // Status
        'status' => 'الحالة',
        'action' => 'الإجراءات',
        'actions' => 'الإجراءات',
        'st_pending' => 'بانتظار سائق',
        'st_accepted' => 'قيد التوصيل',
        'st_delivered' => 'تم التسليم',
        'st_cancelled' => 'ملغي',

        // PIN
        'pin_label' => 'كود التسليم',
        'pin_code' => 'رمز PIN',
        'pin_note' => 'أعطِ هذا الكود للسائق عند الاستلام فقط',
        'pin_warning' => 'لا تشارك هذا الكود إلا عند استلام طلبك',
        'enter_pin' => 'أدخل كود التسليم',

        // Driver
        'driver_accept' => 'قبول',
        'driver_pickup' => 'تم الاستلام',
        'accept_order' => 'قبول الطلب',
        'driver_cost' => 'التكلفة',
        'cost_per_order' => 'تكلفة الطلب',
        'verify_fin' => 'إتمام التسليم',
        'verify_ph' => 'PIN',
        'finish_delivery' => 'إنهاء التوصيل',
        'my_deliveries' => 'توصيلاتي',
        'accepted_orders' => 'الطلبات المقبولة',

        // Errors
        'err_low_bal' => 'رصيدك غير كافٍ لقبول طلبات جديدة',
        'err_auth' => 'اسم المستخدم أو كلمة المرور غير صحيحة',
        'err_banned' => 'تم إيقاف حسابك. تواصل مع الإدارة',
        'err_pin' => 'كود التسليم غير صحيح',
        'err_username_short' => 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل',
        'err_password_short' => 'كلمة المرور يجب أن تكون 4 أحرف على الأقل',
        'err_password_mismatch' => 'كلمتا المرور غير متطابقتين',
        'err_username_exists' => 'اسم المستخدم مستخدم بالفعل',
        'err_register' => 'فشل إنشاء الحساب. حاول مرة أخرى',
        'err_order_taken' => 'هذا الطلب تم قبوله من سائق آخر',
        'err_general' => 'حدث خطأ. حاول مرة أخرى',

        // Success
        'success_add' => 'تم نشر طلبك بنجاح',
        'success_acc' => 'تم قبول الطلب بنجاح',
        'success_fin' => 'تم تسليم الطلب بنجاح. أحسنت!',
        'success_register' => 'تم إنشاء حسابك بنجاح. يمكنك الآن تسجيل الدخول',
        'success_profile' => 'تم تحديث ملفك الشخصي بنجاح',
        'success_order_cancelled' => 'تم إلغاء الطلب بنجاح',
        'success_user_added' => 'تمت إضافة المستخدم بنجاح',
        'success_user_updated' => 'تم تحديث المستخدم بنجاح',
        'success_user_deleted' => 'تم حذف المستخدم بنجاح',
        'success_points_added' => 'تمت إضافة النقاط بنجاح',

        // Empty states
        'empty_list' => 'لا توجد طلبات حالياً',
        'no_orders' => 'لا توجد طلبات',
        'no_pending_orders' => 'لا توجد طلبات متاحة حالياً',
        'no_users' => 'لا يوجد مستخدمين',
        'check_back_later' => 'تحقق لاحقاً',

        // Admin
        'admin_panel' => 'لوحة الإدارة',
        'manage_users' => 'إدارة العملاء',
        'manage_drivers' => 'إدارة السائقين',
        'manage_orders' => 'إدارة الطلبات',
        'add_user' => 'إضافة مستخدم',
        'edit_user' => 'تعديل المستخدم',
        'add_order' => 'إضافة طلب',
        'edit_order' => 'تعديل الطلب',
        'add_points' => 'إضافة نقاط',
        'recharge_points' => 'شحن النقاط',

        // User fields
        'username' => 'اسم المستخدم',
        'password' => 'كلمة المرور',
        'new_password' => 'كلمة مرور جديدة',
        'current_password' => 'كلمة المرور الحالية',
        'confirm_new_password' => 'تأكيد كلمة المرور الجديدة',
        'role' => 'الدور',
        'user_type' => 'نوع المستخدم',

        // Roles
        'admin' => 'مدير',
        'driver' => 'سائق',
        'customer' => 'عميل',
        'drivers' => 'السائقين',
        'customers' => 'العملاء',

        // Status labels
        'active' => 'نشط',
        'banned' => 'محظور',
        'online' => 'متصل',
        'offline' => 'غير متصل',

        // Actions
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'confirm' => 'تأكيد',
        'close' => 'إغلاق',
        'back' => 'رجوع',
        'submit' => 'إرسال',
        'ban' => 'حظر',
        'unban' => 'إلغاء الحظر',
        'cancel_order' => 'إلغاء الطلب',
        'delete_order' => 'حذف الطلب',
        'view_details' => 'عرض التفاصيل',

        // Statistics
        'total_users' => 'إجمالي المستخدمين',
        'total_orders' => 'إجمالي الطلبات',
        'total_drivers' => 'إجمالي السائقين',
        'total_customers' => 'إجمالي العملاء',
        'active_drivers' => 'السائقين النشطين',
        'pending_orders' => 'الطلبات المعلقة',
        'completed_orders' => 'الطلبات المكتملة',
        'statistics' => 'الإحصائيات',

        // Order fields
        'customer_name' => 'اسم العميل',
        'assign_driver' => 'تعيين سائق',
        'no_driver' => 'بدون سائق',
        'select_driver' => 'اختر سائق',
        'select_status' => 'اختر الحالة',

        // Settings
        'save_changes' => 'حفظ التغييرات',
        'leave_empty_password' => 'اتركها فارغة للإبقاء على كلمة المرور الحالية',
        'profile_updated' => 'تم تحديث الملف الشخصي',
        'change_password' => 'تغيير كلمة المرور',
        'account_settings' => 'إعدادات الحساب',
        'personal_info' => 'المعلومات الشخصية',
        'security' => 'الأمان',

        // Notifications
        'new_order_alert' => 'طلب جديد!',
        'order_status_changed' => 'تم تحديث حالة طلبك',
        'notification' => 'إشعار',
        'notifications' => 'الإشعارات',
        'new_notification' => 'إشعار جديد',

        // Confirmations
        'confirm_delete' => 'هل أنت متأكد من الحذف؟',
        'confirm_cancel' => 'هل تريد إلغاء هذا الطلب؟',
        'confirm_ban' => 'هل تريد حظر هذا المستخدم؟',
        'confirm_accept' => 'هل تريد قبول هذا الطلب؟',
        'action_irreversible' => 'لا يمكن التراجع عن هذا الإجراء',

        // Misc
        'loading' => 'جاري التحميل...',
        'please_wait' => 'يرجى الانتظار...',
        'search' => 'بحث',
        'filter' => 'تصفية',
        'refresh' => 'تحديث',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'id' => 'الرقم',
        'details' => 'التفاصيل',
        'amount' => 'المبلغ',
        'select' => 'اختر',
        'optional' => 'اختياري',
        'required' => 'مطلوب',
        'demo_accounts' => 'حسابات تجريبية',
        'all_rights' => 'جميع الحقوق محفوظة',
        'copyright' => 'حقوق النشر',

        // Time
        'now' => 'الآن',
        'today' => 'اليوم',
        'yesterday' => 'أمس',
        'minutes_ago' => 'منذ دقائق',
        'hours_ago' => 'منذ ساعات',
        'days_ago' => 'منذ أيام',

        // Enhanced v2.0
        'serial_no' => 'الرقم التسلسلي',
        'user_id' => 'معرف المستخدم',
        'profile_picture' => 'صورة الملف الشخصي',
        'upload_photo' => 'رفع صورة',
        'change_photo' => 'تغيير الصورة',
        'remove_photo' => 'إزالة الصورة',
        'phone_required' => 'رقم الهاتف مطلوب للمتابعة',
        'phone_not_verified' => 'يرجى إضافة رقم هاتفك للمتابعة',
        'verify_phone' => 'تأكيد رقم الهاتف',
        'phone_verified' => 'رقم الهاتف مؤكد',
        'add_phone_first' => 'أضف رقم هاتفك أولاً',

        // Driver Verification
        'driver_verified' => 'سائق موثق',
        'driver_not_verified' => 'يجب أن يتم التحقق من حسابك من قبل المدير قبل قبول الطلبات',
        'pending_verification' => 'في انتظار التحقق',
        'verified' => 'موثق',
        'verification' => 'التحقق',
        'verify_driver' => 'توثيق السائق',
        'unverify' => 'إلغاء التوثيق',
        'confirm_verify' => 'هل تريد توثيق هذا السائق؟',
        'confirm_unverify' => 'هل تريد إلغاء التوثيق؟',
        'driver_verified_success' => 'تم توثيق السائق بنجاح! يمكنه الآن قبول الطلبات.',
        'driver_unverified' => 'تم إلغاء توثيق السائق.',

        // Phone Registration (Mauritania: 8 digits starting with 2, 3, or 4)
        'phone_example' => '2XXXXXXX',
        'phone_format_hint' => '8 أرقام تبدأ بـ 2 أو 3 أو 4',
        'err_phone_invalid' => 'الهاتف يجب أن يكون 8 أرقام تبدأ بـ 2 أو 3 أو 4',
        'err_phone_exists' => 'رقم الهاتف مسجل بالفعل. الرجاء تسجيل الدخول.',
        'demo_phone_login' => 'حسابات تجريبية (الهاتف / كلمة المرور):',
        'profile_completed' => 'تم تحديث الملف الشخصي بنجاح!',

        // Driver Enhanced
        'go_online' => 'أصبح متصلاً',
        'go_offline' => 'أصبح غير متصل',
        'you_are_online' => 'أنت متصل الآن',
        'you_are_offline' => 'أنت غير متصل',
        'earnings' => 'الأرباح',
        'my_earnings' => 'أرباحي',
        'today_earnings' => 'أرباح اليوم',
        'week_earnings' => 'أرباح الأسبوع',
        'month_earnings' => 'أرباح الشهر',
        'completed_deliveries' => 'التوصيلات المكتملة',
        'active_deliveries' => 'التوصيلات النشطة',
        'my_rating' => 'تقييمي',
        'max_orders_reached' => 'لقد وصلت للحد الأقصى من الطلبات النشطة',

        // Client Enhanced
        'active_orders' => 'الطلبات النشطة',
        'track_order' => 'تتبع الطلب',
        'order_history' => 'سجل الطلبات',
        'rate_driver' => 'قيّم السائق',
        'rate_delivery' => 'قيّم التوصيل',
        'your_rating' => 'تقييمك',
        'leave_comment' => 'اترك تعليقاً',
        'submit_rating' => 'إرسال التقييم',
        'thanks_for_rating' => 'شكراً لتقييمك!',

        // Order Status Enhanced
        'st_picked_up' => 'تم الاستلام',
        'finding_driver' => 'جاري البحث عن سائق...',
        'driver_assigned' => 'تم تعيين سائق',
        'driver_on_way' => 'السائق في الطريق',
        'driver_arrived' => 'السائق وصل',
        'package_picked' => 'تم استلام الطرد',
        'on_the_way' => 'في الطريق إليك',

        // Tracking
        'live_tracking' => 'التتبع المباشر',
        'distance' => 'المسافة',
        'eta' => 'الوقت المتوقع',
        'km' => 'كم',
        'min' => 'دقيقة',
        'call_driver' => 'اتصل بالسائق',
        'message_driver' => 'راسل السائق',

        // Stats
        'this_week' => 'هذا الأسبوع',
        'this_month' => 'هذا الشهر',
        'orders_count' => 'عدد الطلبات',
        'delivery_count' => 'عدد التوصيلات',

        // Help & Footer
        'need_help' => 'تحتاج مساعدة؟',
        'contact_us' => 'اتصل بنا',
        'connect_with_us' => 'تواصل معنا',
        'call_us' => 'اتصل بنا',
        'fast_delivery' => 'خدمة توصيل سريعة وموثوقة لجميع احتياجاتك.',
        'available_24_7' => 'متاح على مدار الساعة',
        'location_mauritania' => 'نواكشوط، موريتانيا',
        'secure_service' => 'خدمة آمنة',

        // GPS & Location
        'gps_status' => 'حالة GPS',
        'gps_enabled' => 'GPS مفعّل',
        'gps_disabled' => 'GPS معطّل',
        'enable_gps' => 'تفعيل GPS',
        'disable_gps' => 'إيقاف GPS',
        'turn_on_gps' => 'تشغيل GPS',
        'gps_required' => 'موقع GPS مطلوب ليجدك السائقون',
        'gps_driver_note' => 'فعّل GPS لرؤية الطلبات القريبة منك (7 كم)',
        'enable_gps_first' => 'فعّل GPS أولاً',
        'no_nearby_orders' => 'لا توجد طلبات قريبة (7 كم)',
        'geolocation_not_supported' => 'متصفحك لا يدعم تحديد الموقع',
        'location_error' => 'خطأ في تحديد الموقع',
        'location_denied' => 'تم رفض الوصول للموقع. يرجى تفعيل GPS.',
        'click_gps' => 'انقر على GPS لتحديد موقعك',
        'pickup_location' => 'موقع الاستلام',
        'updating_location' => 'جاري تحديث الموقع...',
        'location_updated' => 'تم تحديث الموقع',
        'gps_accuracy' => 'دقة الموقع',
        'last_update' => 'آخر تحديث',
        'new_order_nearby' => 'طلب جديد قريب!',
        'accept' => 'قبول',
        'decline' => 'رفض',
        'no_phone' => 'لا يوجد هاتف',
        'update_order' => 'تحديث الطلب'
    ],

    'fr' => [
        // App
        'app_name' => 'Barq Delivery',
        'app_desc' => 'Service de livraison rapide et fiable',
        'welcome' => 'Bienvenue',
        'welcome_back' => 'Bon retour',

        // Auth
        'login_title' => 'Connexion',
        'register_title' => 'Créer un compte',
        'user_ph' => 'Nom d\'utilisateur',
        'pass_ph' => 'Mot de passe',
        'confirm_pass_ph' => 'Confirmer le mot de passe',
        'full_name_ph' => 'Nom complet',
        'phone_ph' => 'Téléphone',
        'email_ph' => 'Email',
        'btn_login' => 'Se connecter',
        'btn_register' => 'S\'inscrire',
        'have_account' => 'Déjà un compte?',
        'no_account' => 'Pas de compte?',
        'login_here' => 'Connectez-vous',
        'register_here' => 'Inscrivez-vous',
        'logout' => 'Déconnexion',
        'logout_confirm' => 'Voulez-vous vous déconnecter?',

        // Dashboard
        'dashboard' => 'Tableau de bord',
        'home' => 'Accueil',
        'settings' => 'Paramètres',
        'profile' => 'Profil',
        'my_account' => 'Mon compte',

        // Balance & Points
        'balance' => 'Mon solde',
        'points' => 'points',
        'pts' => 'pts',
        'current_balance' => 'Solde actuel',
        'recharge_wa' => 'Recharger via WhatsApp',
        'recharge_now' => 'Recharger',
        'low_balance' => 'Solde faible',

        // Orders
        'new_order' => 'Nouvelle commande',
        'create_order' => 'Créer une commande',
        'order_details' => 'Détails de la commande',
        'order_info' => 'Informations',
        'address' => 'Adresse',
        'delivery_address' => 'Adresse de livraison',
        'btn_publish' => 'Publier',
        'recent_orders' => 'Commandes',
        'my_orders' => 'Mes commandes',
        'all_orders' => 'Toutes les commandes',
        'available_orders' => 'Commandes disponibles',
        'order_number' => 'N° commande',
        'order_date' => 'Date de commande',

        // Status
        'status' => 'Statut',
        'action' => 'Action',
        'actions' => 'Actions',
        'st_pending' => 'En attente',
        'st_accepted' => 'En livraison',
        'st_delivered' => 'Livrée',
        'st_cancelled' => 'Annulée',

        // PIN
        'pin_label' => 'Code de livraison',
        'pin_code' => 'Code PIN',
        'pin_note' => 'Donnez ce code au livreur à la réception',
        'pin_warning' => 'Ne partagez ce code qu\'à la réception',
        'enter_pin' => 'Entrez le code PIN',

        // Driver
        'driver_accept' => 'Accepter',
        'driver_pickup' => 'Récupéré',
        'accept_order' => 'Accepter la commande',
        'driver_cost' => 'Coût',
        'cost_per_order' => 'Coût par commande',
        'verify_fin' => 'Terminer',
        'verify_ph' => 'PIN',
        'finish_delivery' => 'Terminer la livraison',
        'my_deliveries' => 'Mes livraisons',
        'accepted_orders' => 'Commandes acceptées',

        // Errors
        'err_low_bal' => 'Solde insuffisant pour accepter des commandes',
        'err_auth' => 'Nom d\'utilisateur ou mot de passe incorrect',
        'err_banned' => 'Compte suspendu. Contactez l\'admin',
        'err_pin' => 'Code PIN incorrect',
        'err_username_short' => 'Nom d\'utilisateur: minimum 3 caractères',
        'err_password_short' => 'Mot de passe: minimum 4 caractères',
        'err_password_mismatch' => 'Les mots de passe ne correspondent pas',
        'err_username_exists' => 'Ce nom d\'utilisateur existe déjà',
        'err_register' => 'Échec de l\'inscription. Réessayez',
        'err_order_taken' => 'Commande déjà prise par un autre livreur',
        'err_general' => 'Une erreur s\'est produite. Réessayez',

        // Success
        'success_add' => 'Commande publiée avec succès',
        'success_acc' => 'Commande acceptée avec succès',
        'success_fin' => 'Commande livrée avec succès!',
        'success_register' => 'Compte créé avec succès. Connectez-vous',
        'success_profile' => 'Profil mis à jour avec succès',
        'success_order_cancelled' => 'Commande annulée avec succès',
        'success_user_added' => 'Utilisateur ajouté avec succès',
        'success_user_updated' => 'Utilisateur mis à jour avec succès',
        'success_user_deleted' => 'Utilisateur supprimé avec succès',
        'success_points_added' => 'Points ajoutés avec succès',

        // Empty states
        'empty_list' => 'Aucune commande pour le moment',
        'no_orders' => 'Aucune commande',
        'no_pending_orders' => 'Aucune commande disponible',
        'no_users' => 'Aucun utilisateur',
        'check_back_later' => 'Revenez plus tard',

        // Admin
        'admin_panel' => 'Panneau Admin',
        'manage_users' => 'Gérer les clients',
        'manage_drivers' => 'Gérer les livreurs',
        'manage_orders' => 'Gérer les commandes',
        'add_user' => 'Ajouter utilisateur',
        'edit_user' => 'Modifier utilisateur',
        'add_order' => 'Ajouter commande',
        'edit_order' => 'Modifier commande',
        'add_points' => 'Ajouter points',
        'recharge_points' => 'Recharger points',

        // User fields
        'username' => 'Nom d\'utilisateur',
        'password' => 'Mot de passe',
        'new_password' => 'Nouveau mot de passe',
        'current_password' => 'Mot de passe actuel',
        'confirm_new_password' => 'Confirmer le nouveau mot de passe',
        'role' => 'Rôle',
        'user_type' => 'Type d\'utilisateur',

        // Roles
        'admin' => 'Admin',
        'driver' => 'Livreur',
        'customer' => 'Client',
        'drivers' => 'Livreurs',
        'customers' => 'Clients',

        // Status labels
        'active' => 'Actif',
        'banned' => 'Banni',
        'online' => 'En ligne',
        'offline' => 'Hors ligne',

        // Actions
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'confirm' => 'Confirmer',
        'close' => 'Fermer',
        'back' => 'Retour',
        'submit' => 'Envoyer',
        'ban' => 'Bannir',
        'unban' => 'Débannir',
        'cancel_order' => 'Annuler commande',
        'delete_order' => 'Supprimer commande',
        'view_details' => 'Voir détails',

        // Statistics
        'total_users' => 'Total utilisateurs',
        'total_orders' => 'Total commandes',
        'total_drivers' => 'Total livreurs',
        'total_customers' => 'Total clients',
        'active_drivers' => 'Livreurs actifs',
        'pending_orders' => 'Commandes en attente',
        'completed_orders' => 'Commandes terminées',
        'statistics' => 'Statistiques',

        // Order fields
        'customer_name' => 'Nom du client',
        'assign_driver' => 'Assigner livreur',
        'no_driver' => 'Sans livreur',
        'select_driver' => 'Choisir livreur',
        'select_status' => 'Choisir statut',

        // Settings
        'save_changes' => 'Enregistrer',
        'leave_empty_password' => 'Laisser vide pour garder le mot de passe actuel',
        'profile_updated' => 'Profil mis à jour',
        'change_password' => 'Changer mot de passe',
        'account_settings' => 'Paramètres du compte',
        'personal_info' => 'Informations personnelles',
        'security' => 'Sécurité',

        // Notifications
        'new_order_alert' => 'Nouvelle commande!',
        'order_status_changed' => 'Statut de commande mis à jour',
        'notification' => 'Notification',
        'notifications' => 'Notifications',
        'new_notification' => 'Nouvelle notification',

        // Confirmations
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer?',
        'confirm_cancel' => 'Voulez-vous annuler cette commande?',
        'confirm_ban' => 'Voulez-vous bannir cet utilisateur?',
        'confirm_accept' => 'Voulez-vous accepter cette commande?',
        'action_irreversible' => 'Cette action est irréversible',

        // Misc
        'loading' => 'Chargement...',
        'please_wait' => 'Veuillez patienter...',
        'search' => 'Rechercher',
        'filter' => 'Filtrer',
        'refresh' => 'Actualiser',
        'date' => 'Date',
        'time' => 'Heure',
        'created_at' => 'Créé le',
        'updated_at' => 'Mis à jour le',
        'id' => 'ID',
        'details' => 'Détails',
        'amount' => 'Montant',
        'select' => 'Sélectionner',
        'optional' => 'Optionnel',
        'required' => 'Requis',
        'demo_accounts' => 'Comptes démo',
        'all_rights' => 'Tous droits réservés',
        'copyright' => 'Copyright',

        // Time
        'now' => 'Maintenant',
        'today' => 'Aujourd\'hui',
        'yesterday' => 'Hier',
        'minutes_ago' => 'Il y a quelques minutes',
        'hours_ago' => 'Il y a quelques heures',
        'days_ago' => 'Il y a quelques jours',

        // Enhanced v2.0
        'serial_no' => 'Numéro de série',
        'user_id' => 'ID utilisateur',
        'profile_picture' => 'Photo de profil',
        'upload_photo' => 'Télécharger photo',
        'change_photo' => 'Changer la photo',
        'remove_photo' => 'Supprimer la photo',
        'phone_required' => 'Numéro de téléphone requis',
        'phone_not_verified' => 'Veuillez ajouter votre téléphone',
        'verify_phone' => 'Vérifier le téléphone',
        'phone_verified' => 'Téléphone vérifié',
        'add_phone_first' => 'Ajoutez d\'abord votre téléphone',

        // Driver Verification
        'driver_verified' => 'Chauffeur vérifié',
        'driver_not_verified' => 'Votre compte doit être vérifié par l\'admin avant d\'accepter des commandes',
        'pending_verification' => 'En attente de vérification',
        'verified' => 'Vérifié',
        'verification' => 'Vérification',
        'verify_driver' => 'Vérifier le chauffeur',
        'unverify' => 'Retirer la vérification',
        'confirm_verify' => 'Vérifier ce chauffeur?',
        'confirm_unverify' => 'Retirer la vérification?',
        'driver_verified_success' => 'Chauffeur vérifié avec succès! Il peut maintenant accepter des commandes.',
        'driver_unverified' => 'Vérification du chauffeur retirée.',

        // Phone Registration (Mauritania: 8 digits starting with 2, 3, or 4)
        'phone_example' => '2XXXXXXX',
        'phone_format_hint' => '8 chiffres commençant par 2, 3 ou 4',
        'err_phone_invalid' => 'Le téléphone doit être 8 chiffres commençant par 2, 3 ou 4',
        'err_phone_exists' => 'Ce numéro est déjà enregistré. Veuillez vous connecter.',
        'demo_phone_login' => 'Comptes démo (Téléphone / Mot de passe):',
        'profile_completed' => 'Profil mis à jour avec succès!',

        // Driver Enhanced
        'go_online' => 'Passer en ligne',
        'go_offline' => 'Passer hors ligne',
        'you_are_online' => 'Vous êtes en ligne',
        'you_are_offline' => 'Vous êtes hors ligne',
        'earnings' => 'Gains',
        'my_earnings' => 'Mes gains',
        'today_earnings' => 'Gains du jour',
        'week_earnings' => 'Gains de la semaine',
        'month_earnings' => 'Gains du mois',
        'completed_deliveries' => 'Livraisons effectuées',
        'active_deliveries' => 'Livraisons actives',
        'my_rating' => 'Ma note',
        'max_orders_reached' => 'Maximum de commandes actives atteint',

        // Client Enhanced
        'active_orders' => 'Commandes actives',
        'track_order' => 'Suivre la commande',
        'order_history' => 'Historique',
        'rate_driver' => 'Noter le livreur',
        'rate_delivery' => 'Noter la livraison',
        'your_rating' => 'Votre note',
        'leave_comment' => 'Laisser un commentaire',
        'submit_rating' => 'Envoyer la note',
        'thanks_for_rating' => 'Merci pour votre note!',

        // Order Status Enhanced
        'st_picked_up' => 'Récupéré',
        'finding_driver' => 'Recherche d\'un livreur...',
        'driver_assigned' => 'Livreur assigné',
        'driver_on_way' => 'Livreur en route',
        'driver_arrived' => 'Livreur arrivé',
        'package_picked' => 'Colis récupéré',
        'on_the_way' => 'En route vers vous',

        // Tracking
        'live_tracking' => 'Suivi en direct',
        'distance' => 'Distance',
        'eta' => 'Temps estimé',
        'km' => 'km',
        'min' => 'min',
        'call_driver' => 'Appeler le livreur',
        'message_driver' => 'Envoyer un message',

        // Stats
        'this_week' => 'Cette semaine',
        'this_month' => 'Ce mois',
        'orders_count' => 'Nombre de commandes',
        'delivery_count' => 'Nombre de livraisons',

        // Help & Footer
        'need_help' => 'Besoin d\'aide?',
        'contact_us' => 'Contactez-nous',
        'connect_with_us' => 'Connectez avec nous',
        'call_us' => 'Appelez-nous',
        'fast_delivery' => 'Service de livraison rapide et fiable pour tous vos besoins.',
        'available_24_7' => 'Disponible 24h/24',
        'location_mauritania' => 'Nouakchott, Mauritanie',
        'secure_service' => 'Service sécurisé',

        // GPS & Location
        'gps_status' => 'Statut GPS',
        'gps_enabled' => 'GPS activé',
        'gps_disabled' => 'GPS désactivé',
        'enable_gps' => 'Activer GPS',
        'disable_gps' => 'Désactiver GPS',
        'turn_on_gps' => 'Allumer GPS',
        'gps_required' => 'La localisation GPS est requise pour que les livreurs vous trouvent',
        'gps_driver_note' => 'Activez le GPS pour voir les commandes à proximité (7 km)',
        'enable_gps_first' => 'Activez le GPS d\'abord',
        'no_nearby_orders' => 'Pas de commandes à proximité (7 km)',
        'geolocation_not_supported' => 'La géolocalisation n\'est pas supportée par votre navigateur',
        'location_error' => 'Erreur de localisation',
        'location_denied' => 'Accès à la localisation refusé. Veuillez activer le GPS.',
        'click_gps' => 'Cliquez sur GPS pour définir votre position',
        'pickup_location' => 'Lieu de ramassage',
        'updating_location' => 'Mise à jour de la position...',
        'location_updated' => 'Position mise à jour',
        'gps_accuracy' => 'Précision GPS',
        'last_update' => 'Dernière mise à jour',
        'new_order_nearby' => 'Nouvelle commande à proximité!',
        'accept' => 'Accepter',
        'decline' => 'Refuser',
        'no_phone' => 'Pas de téléphone',
        'update_order' => 'Mettre à jour'
    ]
];
$t = $text[$lang];
?>
