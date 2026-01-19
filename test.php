<?php
// Force UTF-8 encoding from the start
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

session_start();
date_default_timezone_set('Africa/Nouakchott');

// ==========================================
// 1. CONFIGURATION & DATABASE CONNECTION
// ==========================================
$db_config = [
    'host' => 'localhost',
    'name' => 'cyntzsrb_delivery_system',
    'user' => 'cyntzsrb_delivery_system',
    'pass' => 'cyntzsrb_delivery_system',
    'charset' => 'utf8mb4'
];

$whatsapp_number = "22241312931"; 
$points_cost_per_order = 20;

try {
    $dsn = "mysql:host={$db_config['host']};dbname={$db_config['name']};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    $conn = new PDO($dsn, $db_config['user'], $db_config['pass'], $options);
    
    // Additional UTF-8 enforcement
    $conn->exec("SET NAMES utf8mb4");
    $conn->exec("SET CHARACTER SET utf8mb4");
    $conn->exec("SET character_set_connection=utf8mb4");
    $conn->exec("SET character_set_client=utf8mb4");
    $conn->exec("SET character_set_results=utf8mb4");

    // ==========================================
    // DATABASE INSTALLER / REPAIR
    // ==========================================
    
    // 1. Create Users Table with banned status
    $conn->exec("CREATE TABLE IF NOT EXISTS users1 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','driver','customer') NOT NULL,
        points INT DEFAULT 0,
        status ENUM('active','banned') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 2. Create Orders Table
    $conn->exec("CREATE TABLE IF NOT EXISTS orders1 (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(50) NOT NULL,
        details TEXT NOT NULL,
        address VARCHAR(255) NOT NULL,
        status ENUM('pending','accepted','delivered','cancelled') DEFAULT 'pending',
        driver_id INT DEFAULT NULL,
        delivery_code VARCHAR(10) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_driver (driver_id),
        INDEX idx_customer (customer_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Check if status column exists in users1, if not add it
    $check = $conn->query("SHOW COLUMNS FROM users1 LIKE 'status'")->rowCount();
    if($check == 0) {
        $conn->exec("ALTER TABLE users1 ADD COLUMN status ENUM('active','banned') DEFAULT 'active' AFTER points");
    }

    // 3. Create Default Users
    $check_users = $conn->query("SELECT count(*) FROM users1")->fetchColumn();
    if ($check_users == 0) {
        $conn->prepare("INSERT INTO users1 (username, password, role, points, status) VALUES (?, ?, ?, ?, 'active')")
             ->execute(['admin', '123', 'admin', 0]);
        $conn->prepare("INSERT INTO users1 (username, password, role, points, status) VALUES (?, ?, ?, ?, 'active')")
             ->execute(['driver', '123', 'driver', 50]);
        $conn->prepare("INSERT INTO users1 (username, password, role, points, status) VALUES (?, ?, ?, ?, 'active')")
             ->execute(['client', '123', 'customer', 0]);
    }

} catch(PDOException $e) {
    die("
    <div style='font-family:sans-serif; text-align:center; padding:50px; color:#721c24; background:#f8d7da;'>
        <h3>Database Connection Failed</h3>
        <p>Please check your config variables at the top of the file.</p>
        <small>Error: ".$e->getMessage()."</small>
    </div>");
}

// ==========================================
// 2. HELPER FUNCTIONS
// ==========================================
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'fr'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = $_SESSION['lang'] ?? 'ar';
$dir = ($lang == 'ar') ? 'rtl' : 'ltr';

function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

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

function setFlash($type, $msg) { 
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg]; 
}

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
// 3. TRANSLATIONS
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

// ==========================================
// 4. LOGIC CONTROLLER
// ==========================================

// --- LOGIN ---
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
            header("Location: index.php"); exit();
        }
    } else {
        setFlash('error', $t['err_auth']);
    }
}

// --- LOGOUT ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php"); exit();
}

// --- AUTHENTICATED ACTIONS ---
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

    // ========== ADMIN ACTIONS ==========
    if($u['role'] == 'admin') {
        
        // Add User
        if(isset($_POST['admin_add_user'])) {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $role = $_POST['role'];
            $points = (int)$_POST['points'];
            
            try {
                $stmt = $conn->prepare("INSERT INTO users1 (username, password, role, points, status) VALUES (?, ?, ?, ?, 'active')");
                $stmt->execute([$username, $password, $role, $points]);
                setFlash('success', 'User added successfully');
            } catch(PDOException $e) {
                setFlash('error', 'Username already exists');
            }
            header("Location: index.php"); exit();
        }
        
        // Edit User
        if(isset($_POST['admin_edit_user'])) {
            $user_id = (int)$_POST['user_id'];
            $password = trim($_POST['password']);
            $role = $_POST['role'];
            $points = (int)$_POST['points'];
            
            if(!empty($password)) {
                $conn->prepare("UPDATE users1 SET password=?, role=?, points=? WHERE id=?")
                     ->execute([$password, $role, $points, $user_id]);
            } else {
                $conn->prepare("UPDATE users1 SET role=?, points=? WHERE id=?")
                     ->execute([$role, $points, $user_id]);
            }
            setFlash('success', 'User updated successfully');
            header("Location: index.php"); exit();
        }
        
        // Ban/Unban User
        if(isset($_GET['toggle_ban'])) {
            $user_id = (int)$_GET['toggle_ban'];
            $stmt = $conn->prepare("SELECT status FROM users1 WHERE id=?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            $new_status = ($user['status'] == 'active') ? 'banned' : 'active';
            $conn->prepare("UPDATE users1 SET status=? WHERE id=?")->execute([$new_status, $user_id]);
            setFlash('success', 'User status updated');
            header("Location: index.php"); exit();
        }
        
        // Delete User
        if(isset($_GET['delete_user'])) {
            $user_id = (int)$_GET['delete_user'];
            $conn->prepare("DELETE FROM users1 WHERE id=? AND id!=?")->execute([$user_id, $uid]);
            setFlash('success', 'User deleted');
            header("Location: index.php"); exit();
        }
        
        // Recharge Points
        if (isset($_POST['recharge'])) {
            $amt = (int)$_POST['amount'];
            $did = (int)$_POST['driver_id'];
            if ($amt > 0 && $did > 0) {
                $conn->prepare("UPDATE users1 SET points = points + ? WHERE id=?")->execute([$amt, $did]);
                setFlash('success', "Points added successfully.");
                header("Location: index.php"); exit();
            }
        }
        
        // Add Order (Admin)
        if(isset($_POST['admin_add_order'])) {
            $customer_name = trim($_POST['customer_name']);
            $details = mb_convert_encoding(trim($_POST['details']), 'UTF-8', 'UTF-8');
            $address = mb_convert_encoding(trim($_POST['address']), 'UTF-8', 'UTF-8');
            $status = $_POST['status'];
            $driver_id = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : NULL;
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            
            $stmt = $conn->prepare("INSERT INTO orders1 (customer_name, details, address, status, driver_id, delivery_code) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customer_name, $details, $address, $status, $driver_id, $otp]);
            setFlash('success', 'Order added successfully');
            header("Location: index.php"); exit();
        }
        
        // Edit Order (Admin)
        if(isset($_POST['admin_edit_order'])) {
            $order_id = (int)$_POST['order_id'];
            $customer_name = trim($_POST['customer_name']);
            $details = mb_convert_encoding(trim($_POST['details']), 'UTF-8', 'UTF-8');
            $address = mb_convert_encoding(trim($_POST['address']), 'UTF-8', 'UTF-8');
            $status = $_POST['status'];
            $driver_id = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : NULL;
            
            $conn->prepare("UPDATE orders1 SET customer_name=?, details=?, address=?, status=?, driver_id=? WHERE id=?")
                 ->execute([$customer_name, $details, $address, $status, $driver_id, $order_id]);
            setFlash('success', 'Order updated successfully');
            header("Location: index.php"); exit();
        }
        
        // Cancel Order
        if(isset($_GET['cancel_order'])) {
            $order_id = (int)$_GET['cancel_order'];
            $conn->prepare("UPDATE orders1 SET status='cancelled' WHERE id=?")->execute([$order_id]);
            setFlash('success', 'Order cancelled');
            header("Location: index.php"); exit();
        }
        
        // Delete Order
        if(isset($_GET['delete_order'])) {
            $order_id = (int)$_GET['delete_order'];
            $conn->prepare("DELETE FROM orders1 WHERE id=?")->execute([$order_id]);
            setFlash('success', 'Order deleted');
            header("Location: index.php"); exit();
        }
    }

    // ========== CUSTOMER ACTIONS ==========
    if (isset($_POST['add_order']) && $u['role'] == 'customer') {
        $details = mb_convert_encoding(trim($_POST['details']), 'UTF-8', 'UTF-8');
        $address = mb_convert_encoding(trim($_POST['address']), 'UTF-8', 'UTF-8');
        
        if($details && $address) {
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            
            $stmt = $conn->prepare("INSERT INTO orders1 (customer_name, details, address, status, delivery_code) VALUES (?, ?, ?, 'pending', ?)");
            $stmt->execute([$u['username'], $details, $address, $otp]);
            setFlash('success', $t['success_add']);
            header("Location: index.php"); exit();
        }
    }

    // ========== DRIVER ACTIONS ==========
    if (isset($_POST['accept_order']) && $u['role'] == 'driver') {
        $oid = (int)$_POST['oid'];
        
        if ($u['points'] < $points_cost_per_order) {
            setFlash('error', $t['err_low_bal']);
        } else {
            try {
                $conn->beginTransaction();

                $chk = $conn->prepare("SELECT id FROM orders1 WHERE id=? AND status='pending' FOR UPDATE");
                $chk->execute([$oid]);
                
                if ($chk->rowCount() > 0) {
                    $upd = $conn->prepare("UPDATE orders1 SET status='accepted', driver_id=? WHERE id=?");
                    $upd->execute([$uid, $oid]);

                    $deduct = $conn->prepare("UPDATE users1 SET points = points - ? WHERE id=?");
                    $deduct->execute([$points_cost_per_order, $uid]);

                    $conn->commit();
                    setFlash('success', $t['success_acc']);
                } else {
                    $conn->rollBack();
                    setFlash('error', "Order already taken");
                }
            } catch (Exception $e) {
                $conn->rollBack();
                setFlash('error', "System Error");
            }
        }
        header("Location: index.php"); exit();
    }

    if (isset($_POST['finish_job']) && $u['role'] == 'driver') {
        $oid = (int)$_POST['oid'];
        $pin = str_pad(trim($_POST['pin']), 4, '0', STR_PAD_LEFT);
        
        $chk = $conn->prepare("SELECT delivery_code FROM orders1 WHERE id=? AND driver_id=? AND status='accepted'");
        $chk->execute([$oid, $uid]);
        $order = $chk->fetch();

        if ($order && $order['delivery_code'] === $pin) {
            $conn->prepare("UPDATE orders1 SET status='delivered' WHERE id=?")->execute([$oid]);
            setFlash('success', $t['success_fin']);
        } else {
            setFlash('error', $t['err_pin']);
        }
        header("Location: index.php"); exit();
    }
}
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
            --secondary-color: #818cf8;
            --bg-color: #f3f4f6;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        body { font-family: 'Tajawal', sans-serif; background: var(--bg-color); color: #333; }
        .login-card { max-width: 400px; margin: 60px auto; border-radius: 20px; border:none; }
        .app-navbar { background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-card { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border-radius: 15px; border: none; }
        .content-card { border: none; border-radius: 15px; box-shadow: var(--card-shadow); background: white; overflow: hidden; }
        .badge-pending { background-color: #fef3c7; color: #d97706; border: 1px solid #fcd34d; }
        .badge-accepted { background-color: #dbeafe; color: #2563eb; border: 1px solid #93c5fd; }
        .badge-delivered { background-color: #d1fae5; color: #059669; border: 1px solid #6ee7b7; }
        .badge-cancelled { background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .pin-box { font-family: monospace; letter-spacing: 4px; font-weight: bold; font-size: 1.2rem; background: #eee; padding: 5px 10px; border-radius: 5px; user-select: all; display: inline-block;}
        .btn-primary { background-color: var(--primary-color); border: none; }
        .btn-primary:hover { background-color: #4338ca; }
        .stats-box { background: white; border-radius: 10px; padding: 20px; box-shadow: var(--card-shadow); }
        .modal-content { border-radius: 15px; border: none; }
        .table-actions .btn { margin: 2px; }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['user'])): ?>
    <!-- ================= LOGIN SCREEN ================= -->
    <div class="container">
        <div class="card login-card content-card shadow-lg">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <img src="logo.png" alt="<?php echo $t['app_name']; ?>" style="max-width: 150px; height: auto;">
                    </div>
                    <h4 class="fw-bold"><?php echo $t['app_name']; ?></h4>
                    <div class="btn-group btn-group-sm mt-2" role="group">
                        <a href="?lang=ar" class="btn btn-outline-secondary <?php echo $lang=='ar'?'active':''; ?>">عربي</a>
                        <a href="?lang=fr" class="btn btn-outline-secondary <?php echo $lang=='fr'?'active':''; ?>">Français</a>
                    </div>
                </div>
                <?php echo getFlash(); ?>
                <form method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" name="username" class="form-control" id="floatingInput" placeholder="User" required>
                        <label for="floatingInput"><?php echo $t['user_ph']; ?></label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" name="password" class="form-control" id="floatingPass" placeholder="Pass" required>
                        <label for="floatingPass"><?php echo $t['pass_ph']; ?></label>
                    </div>
                    <button name="do_login" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm">
                        <?php echo $t['btn_login']; ?> <i class="fas fa-arrow-<?php echo ($lang=='ar')?'left':'right'; ?>"></i>
                    </button>
                    <div class="mt-4 text-center small text-muted bg-light p-2 rounded">
                        <strong>Demo Users:</strong><br>
                        admin / 123<br>
                        driver / 123<br>
                        client / 123
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php else: 
    $u = $_SESSION['user'];
    $role = $u['role'];
?>
    <!-- ================= DASHBOARD ================= -->
    <nav class="navbar app-navbar sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <img src="logo.png" alt="<?php echo $t['app_name']; ?>" style="height: 40px; width: auto;">
                <span class="text-primary"><?php echo $t['app_name']; ?></span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-md-block text-end lh-1">
                    <span class="d-block fw-bold small"><?php echo e($u['username']); ?></span>
                    <span class="badge bg-secondary rounded-pill" style="font-size:0.6rem"><?php echo strtoupper($role); ?></span>
                </div>
                <a href="?logout=1" class="btn btn-light text-danger rounded-circle shadow-sm" title="<?php echo $t['logout']; ?>">
                    <i class="fas fa-power-off"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <?php echo getFlash(); ?>

        <?php if($role == 'admin'): ?>
            <!-- ================= ADMIN DASHBOARD ================= -->
            
            <!-- Statistics -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="stats-box text-center">
                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                        <h3 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) FROM users1 WHERE role='customer'")->fetchColumn(); ?></h3>
                        <small class="text-muted"><?php echo $t['customer']; ?>s</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box text-center">
                        <i class="fas fa-motorcycle fa-2x text-info mb-2"></i>
                        <h3 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) FROM users1 WHERE role='driver'")->fetchColumn(); ?></h3>
                        <small class="text-muted"><?php echo $t['driver']; ?>s</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box text-center">
                        <i class="fas fa-box fa-2x text-success mb-2"></i>
                        <h3 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) FROM orders1")->fetchColumn(); ?></h3>
                        <small class="text-muted"><?php echo $t['total_orders']; ?></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box text-center">
                        <i class="fas fa-check-circle fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0"><?php echo $conn->query("SELECT COUNT(*) FROM users1 WHERE role='driver' AND status='active'")->fetchColumn(); ?></h3>
                        <small class="text-muted"><?php echo $t['active_drivers']; ?></small>
                    </div>
                </div>
            </div>

            <!-- Admin Tabs -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#customers"><i class="fas fa-users"></i> <?php echo $t['manage_users']; ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#drivers"><i class="fas fa-motorcycle"></i> <?php echo $t['manage_drivers']; ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#orders"><i class="fas fa-box"></i> <?php echo $t['manage_orders']; ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#points"><i class="fas fa-coins"></i> <?php echo $t['add_points']; ?></a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- CUSTOMERS TAB -->
                <div class="tab-pane fade show active" id="customers">
                    <div class="card content-card">
                        <div class="card-header bg-white py-3 d-flex justify-content-between">
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
                                        <th><?php echo $t['points']; ?></th>
                                        <th><?php echo $t['status']; ?></th>
                                        <th>Date</th>
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
                                        <td><strong><?php echo e($user['username']); ?></strong></td>
                                        <td><?php echo $user['points']; ?></td>
                                        <td>
                                            <?php if($user['status'] == 'active'): ?>
                                                <span class="badge bg-success"><?php echo $t['active']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><?php echo $t['banned']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></small></td>
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
                        <div class="card-header bg-white py-3 d-flex justify-content-between">
                            <h5 class="mb-0"><i class="fas fa-motorcycle text-info"></i> <?php echo $t['manage_drivers']; ?></h5>
                            <button class="btn btn-sm btn-info text-white" onclick="showAddUserModal('driver')">
                                <i class="fas fa-plus"></i> <?php echo $t['add_user']; ?>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th><?php echo $t['username']; ?></th>
                                        <th><?php echo $t['points']; ?></th>
                                        <th><?php echo $t['status']; ?></th>
                                        <th>Date</th>
                                        <th class="text-end"><?php echo $t['action']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $drivers = $conn->query("SELECT * FROM users1 WHERE role='driver' ORDER BY id DESC");
                                    while($driver = $drivers->fetch()):
                                    ?>
                                    <tr>
                                        <td><?php echo $driver['id']; ?></td>
                                        <td><strong><?php echo e($driver['username']); ?></strong></td>
                                        <td><span class="badge bg-warning text-dark"><?php echo $driver['points']; ?> pts</span></td>
                                        <td>
                                            <?php if($driver['status'] == 'active'): ?>
                                                <span class="badge bg-success"><?php echo $t['active']; ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><?php echo $t['banned']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><?php echo date('d/m/Y', strtotime($driver['created_at'])); ?></small></td>
                                        <td class="text-end table-actions">
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
                        <div class="card-header bg-white py-3 d-flex justify-content-between">
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
                                        <th>Customer</th>
                                        <th>Driver</th>
                                        <th><?php echo $t['status']; ?></th>
                                        <th>PIN</th>
                                        <th>Date</th>
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
                                            <div class="fw-bold"><?php echo e($order['details']); ?></div>
                                            <small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?php echo e($order['address']); ?></small>
                                        </td>
                                        <td><?php echo e($order['customer_name']); ?></td>
                                        <td><?php echo $order['driver_name'] ? e($order['driver_name']) : '<span class="text-muted">-</span>'; ?></td>
                                        <td><span class="badge <?php echo $badge; ?>"><?php echo $t['st_'.$st]; ?></span></td>
                                        <td><code><?php echo $order['delivery_code']; ?></code></td>
                                        <td><small><?php echo date('d/m H:i', strtotime($order['created_at'])); ?></small></td>
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
                    <div class="card stat-card mb-3">
                        <div class="card-body text-center p-4">
                            <h6 class="opacity-75 mb-2"><?php echo $t['balance']; ?></h6>
                            <h1 class="display-4 fw-bold mb-0"><?php echo $u['points']; ?></h1>
                            <span class="opacity-75"><?php echo $t['points']; ?></span>
                            <?php if($u['points'] < $points_cost_per_order): ?>
                                <div class="mt-3 bg-white text-danger rounded p-2 small fw-bold">
                                    <i class="fas fa-exclamation-triangle"></i> <?php echo $t['err_low_bal']; ?>
                                </div>
                            <?php endif; ?>
                            <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=Recharge%20User:%20<?php echo $u['username']; ?>" target="_blank" class="btn btn-light text-success w-100 mt-3 fw-bold rounded-pill">
                                <i class="fab fa-whatsapp"></i> <?php echo $t['recharge_wa']; ?>
                            </a>
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
                            <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-list-ul"></i> <?php echo $t['recent_orders']; ?></h5>
                            <?php if($role == 'customer'): ?>
                                <span class="badge bg-light text-dark border"><?php echo $u['username']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="table-responsive">
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
                                    if($role == 'driver') $limit = "WHERE status IN ('pending', 'accepted') OR driver_id='$uid'";
                                    if($role == 'customer') $limit = "WHERE customer_name='{$u['username']}'";
                                    
                                    $sql = "SELECT * FROM orders1 $limit ORDER BY id DESC LIMIT 50";
                                    $res = $conn->query($sql);
                                    
                                    if($res->rowCount() == 0): 
                                    ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i><br>
                                            <?php echo $t['empty_list']; ?>
                                        </td>
                                    </tr>
                                    <?php else: while($row = $res->fetch()): 
                                        $st = $row['status'];
                                        $badge = ($st=='pending')?'badge-pending':(($st=='accepted')?'badge-accepted':(($st=='cancelled')?'badge-cancelled':'badge-delivered'));
                                    ?>
                                    <tr>
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
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2 fw-normal">
                                                <?php echo $t['st_'.$st]; ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <?php if($role == 'driver'): ?>
                                                
                                                <?php if($st == 'pending'): ?>
                                                    <form method="POST">
                                                        <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                                        <button name="accept_order" class="btn btn-sm btn-outline-primary fw-bold" onclick="return confirm('<?php echo $t['driver_cost']; ?>: <?php echo $points_cost_per_order; ?> <?php echo $t['points']; ?>. Confirm?')">
                                                            <?php echo $t['driver_accept']; ?>
                                                        </button>
                                                    </form>
                                                
                                                <?php elseif($st == 'accepted' && $row['driver_id'] == $uid): ?>
                                                    <form method="POST" class="d-flex justify-content-end align-items-center gap-2">
                                                        <input type="hidden" name="oid" value="<?php echo $row['id']; ?>">
                                                        <input type="text" name="pin" class="form-control form-control-sm text-center fw-bold border-success" style="width:80px" placeholder="<?php echo $t['verify_ph']; ?>" required maxlength="4" pattern="[0-9]{4}">
                                                        <button type="submit" name="finish_job" class="btn btn-sm btn-success text-white shadow-sm" title="<?php echo $t['verify_fin']; ?>">
                                                            <i class="fas fa-check"></i>
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
    
    <div class="text-center text-muted py-4 small">
        &copy; <?php echo date('Y'); ?> <?php echo $t['app_name']; ?>. All rights reserved.
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
</script>
</body>
</html>