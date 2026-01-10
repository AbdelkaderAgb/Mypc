<?php
/**
 * Helper Functions & Translations
 * This file contains utility functions and language translations
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

    if ($lang == 'ar') {
        $months_ar = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                      'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        $day = date('d', $timestamp);
        $month = $months_ar[(int)date('m', $timestamp)];
        $year = date('Y', $timestamp);
        $time = date('h:i A', $timestamp);
        return "$day $month $year - $time";
    }

    return date('d/m/Y h:i A', $timestamp);
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
        $icon = ($f['type'] == 'success') ? 'check-circle' : 'exclamation-triangle';
        $cls = ($f['type'] == 'error') ? 'danger' : $f['type'];
        return "
        <div class='alert alert-{$cls} alert-dismissible fade show shadow-sm border-0 mb-4' role='alert'>
            <i class='fas fa-{$icon} me-2'></i> {$f['msg']}
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
    return '';
}

// ==========================================
// TRANSLATIONS
// ==========================================
$text = [
    'ar' => [
        'app_name' => 'نظام التوصيل برو',
        'login_title' => 'تسجيل الدخول',
        'user_ph' => 'اسم المستخدم',
        'pass_ph' => 'كلمة المرور',
        'btn_login' => 'دخول آمن',
        'logout' => 'تسجيل خروج',
        'dashboard' => 'لوحة التحكم',
        'balance' => 'رصيد المحفظة',
        'points' => 'نقطة',
        'recharge_wa' => 'شحن الرصيد',
        'new_order' => 'طلب جديد',
        'order_details' => 'تفاصيل الطلب',
        'address' => 'العنوان / الموقع',
        'btn_publish' => 'نشر الطلب الآن',
        'recent_orders' => 'سجل الطلبات',
        'status' => 'الحالة',
        'action' => 'الإجراءات',
        'st_pending' => 'بانتظار سائق',
        'st_accepted' => 'قيد التوصيل',
        'st_delivered' => 'تم التسليم',
        'st_cancelled' => 'ملغي',
        'pin_label' => 'كود التسليم (PIN)',
        'pin_note' => 'شارك هذا الكود مع السائق عند الاستلام فقط',
        'driver_accept' => 'قبول الطلب',
        'driver_cost' => 'خصم',
        'verify_fin' => 'إنهاء',
        'verify_ph' => 'PIN',
        'err_low_bal' => 'عفواً، رصيدك لا يكفي لقبول الطلبات.',
        'err_auth' => 'بيانات الدخول غير صحيحة',
        'err_banned' => 'حسابك محظور. تواصل مع الإدارة.',
        'err_pin' => 'كود التسليم غير صحيح!',
        'success_add' => 'تم نشر الطلب بنجاح',
        'success_acc' => 'تم قبول الطلب وخصم النقاط بنجاح',
        'success_fin' => 'تم توصيل الطلب بنجاح. أحسنت!',
        'empty_list' => 'لا توجد طلبات حالياً',
        'admin_panel' => 'لوحة الإدارة',
        'manage_users' => 'إدارة العملاء',
        'manage_drivers' => 'إدارة السائقين',
        'manage_orders' => 'إدارة الطلبات',
        'add_user' => 'إضافة مستخدم',
        'add_order' => 'إضافة طلب',
        'edit_order' => 'تعديل الطلب',
        'add_points' => 'إضافة نقاط',
        'username' => 'اسم المستخدم',
        'password' => 'كلمة المرور',
        'role' => 'الدور',
        'admin' => 'مدير',
        'driver' => 'سائق',
        'customer' => 'عميل',
        'active' => 'نشط',
        'banned' => 'محظور',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'ban' => 'حظر',
        'unban' => 'إلغاء الحظر',
        'cancel_order' => 'إلغاء',
        'delete_order' => 'حذف',
        'total_users' => 'إجمالي المستخدمين',
        'total_orders' => 'إجمالي الطلبات',
        'active_drivers' => 'السائقين النشطين',
        'customer_name' => 'اسم العميل',
        'assign_driver' => 'تعيين سائق',
        'no_driver' => 'بدون سائق'
    ],
    'fr' => [
        'app_name' => 'Delivery Pro',
        'login_title' => 'Connexion',
        'user_ph' => 'Nom d\'utilisateur',
        'pass_ph' => 'Mot de passe',
        'btn_login' => 'Connexion',
        'logout' => 'Déconnexion',
        'dashboard' => 'Tableau de bord',
        'balance' => 'Mon Solde',
        'points' => 'Pts',
        'recharge_wa' => 'Recharger',
        'new_order' => 'Nouvelle Commande',
        'order_details' => 'Détails',
        'address' => 'Adresse',
        'btn_publish' => 'Publier',
        'recent_orders' => 'Commandes Récentes',
        'status' => 'Statut',
        'action' => 'Action',
        'st_pending' => 'En attente',
        'st_accepted' => 'En cours',
        'st_delivered' => 'Terminé',
        'st_cancelled' => 'Annulé',
        'pin_label' => 'Code PIN',
        'pin_note' => 'Donnez ce code au livreur',
        'driver_accept' => 'Accepter',
        'driver_cost' => 'Coût',
        'verify_fin' => 'Finir',
        'verify_ph' => 'PIN',
        'err_low_bal' => 'Solde insuffisant.',
        'err_auth' => 'Identifiants incorrects',
        'err_banned' => 'Compte banni. Contactez admin.',
        'err_pin' => 'Code PIN incorrect!',
        'success_add' => 'Commande publiée',
        'success_acc' => 'Commande acceptée',
        'success_fin' => 'Commande livrée!',
        'empty_list' => 'Aucune commande',
        'admin_panel' => 'Admin Panel',
        'manage_users' => 'Gérer Clients',
        'manage_drivers' => 'Gérer Chauffeurs',
        'manage_orders' => 'Gérer Commandes',
        'add_user' => 'Ajouter Utilisateur',
        'add_order' => 'Ajouter Commande',
        'edit_order' => 'Modifier Commande',
        'add_points' => 'Ajouter Points',
        'username' => 'Utilisateur',
        'password' => 'Mot de passe',
        'role' => 'Rôle',
        'admin' => 'Admin',
        'driver' => 'Chauffeur',
        'customer' => 'Client',
        'active' => 'Actif',
        'banned' => 'Banni',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'ban' => 'Bannir',
        'unban' => 'Débannir',
        'cancel_order' => 'Annuler',
        'delete_order' => 'Supprimer',
        'total_users' => 'Total Utilisateurs',
        'total_orders' => 'Total Commandes',
        'active_drivers' => 'Chauffeurs Actifs',
        'customer_name' => 'Nom Client',
        'assign_driver' => 'Assigner Chauffeur',
        'no_driver' => 'Sans chauffeur'
    ]
];
$t = $text[$lang];
?>
