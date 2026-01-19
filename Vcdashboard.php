<?php
session_start();
require_once "db.php"; // Include your database connection file here

// Handle logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Unset all session variables
    $_SESSION = array();
    // Destroy the session
    session_destroy();
    // Redirect to login page (same page without logged in session)
    header("Location: Vcdashboard.php");
    exit();
}

$errors = [];
$isLoggedIn = false;
$isAdmin = false;

// Check if user is already logged in
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    $isLoggedIn = true;
    $user = $_SESSION['user'];
    $isAdmin = ($user['role'] === 'admin');
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    try {
        $email = $_POST['email'];
        $userPassword = $_POST['password'];
        
        // Attempt to find the user in the 'users' table
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($userPassword, $user['password'])) {
            $_SESSION['user'] = $user;
            $_SESSION['auth'] = true;
            $isLoggedIn = true;
            $isAdmin = ($user['role'] === 'admin');
            // Redirect to the same page to refresh
            header("Location: Vcdashboard.php");
            exit();
        } else {
            $errors[] = "Invalid login credentials.";
        }
    } catch(PDOException $e) {
        $errors[] = "Connection failed: " . $e->getMessage();
    }
}

// Determine which HTML to display based on login status
if (!$isLoggedIn) {
    // Show login form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>CYN - Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block" style="background-image: url('img/istanbul1.png'); background-size: cover;"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <img src="img/logo.png" alt="Logo" class="img-fluid mb-4">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <?php if (!empty($errors)): ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php foreach ($errors as $error): ?>
                                                <p><?php echo $error; ?></p>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <form class="user" action="" method="post">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="exampleInputEmail" name="email" aria-describedby="emailHelp" placeholder="Enter Email Address..." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="exampleInputPassword" name="password" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block" name="login">Login</button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
<?php
} else {
    // Show dashboard for logged in users
?>
<!DOCTYPE html>
<html lang="tr" <?php if(isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') echo 'data-theme="dark"'; ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYN TURIZM Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #4f46e5;
            --accent: #818cf8;
            --background: #f8fafc;
            --card: #ffffff;
            --text: #1e293b;
            --hover: #4338ca;
        }

        [data-theme="dark"] {
            --primary: #818cf8;
            --secondary: #6366f1;
            --accent: #4f46e5;
            --background: #0f172a;
            --card: #1e293b;
            --text: #f8fafc;
            --hover: #6366f1;
        }

        body {
            background-color: var(--background);
            color: var(--text);
            transition: all 0.3s ease;
            background-image: 
                radial-gradient(at 100% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(129, 140, 248, 0.05) 0px, transparent 50%);
            font-family: 'Inter', sans-serif;
        }

        .dashboard-card {
            background: var(--card);
            border-radius: 16px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(99, 102, 241, 0.1);
            backdrop-filter: blur(8px);
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.15);
            border-color: var(--primary);
        }

        .icon-box {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(129, 140, 248, 0.1));
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .nav-button {
            background: var(--primary);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-button:hover {
            background: var(--hover);
        }

        .nav-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }

        .nav-button:hover::before {
            left: 100%;
        }

        .header-text {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.5s ease-out forwards;
        }

        .icon-scale {
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover .icon-scale {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="p-6">
    <!-- User Info & Logout -->
    <div class="fixed top-6 left-6 flex items-center p-2 px-4 bg-white dark:bg-gray-800 rounded-full shadow-lg z-50 transition-all duration-300">
        <i class="fas fa-user-circle mr-2 text-primary"></i>
        <span class="mr-2"><?php echo htmlspecialchars($user['email']); ?></span>
        <a href="Vcdashboard.php?action=logout" class="ml-2 text-red-500 hover:text-red-700" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>

    <!-- Theme Toggle -->
    <button onclick="toggleTheme()" title="Temayı değiştir" class="fixed top-6 right-6 p-4 rounded-full bg-white dark:bg-gray-800 shadow-lg z-50 transition-all duration-300 hover:scale-110">
        <i class="fas fa-moon text-gray-800 dark:hidden"></i>
        <i class="fas fa-sun text-white hidden dark:block"></i>
    </button>

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-16 animate-slide-in">
            <img src="logo.png" alt="CYN Logo" class="mx-auto h-24 mb-8">
            <h1 class="text-5xl font-bold header-text mb-4">CYN TURIZM</h1>
            <p class="text-xl text-gray-600 dark:text-gray-300">Yönetim Sistemi</p>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Transfer Takvimi Card -->
            <div class="dashboard-card p-8" style="animation: slideIn 0.5s ease-out forwards; animation-delay: 0.1s; opacity: 0;">
                <div class="icon-box w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-calendar-alt text-3xl text-primary icon-scale"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Transfer Takvimi</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Transfer operasyonlarınızı planlayın ve yönetin</p>
                <a href="Calendar.php" class="nav-button block py-3 px-6 rounded-xl text-white text-center font-medium">
                    Takvimi Aç
                </a>
            </div>
<!-- 2️⃣ YENİ  kart  – şehir turu takviminize bağlı  -->
<div class="dashboard-card p-8" style="animation: slideIn 0.5s ease-out forwards; animation-delay: 0.1s; opacity: 0;">
    <div class="icon-box w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
        <i class="fas fa-calendar-alt text-3xl text-primary icon-scale"></i>
    </div>
    <h3 class="text-xl font-bold mb-3">Tur Takvimi</h3>
    <p class="text-gray-600 dark:text-gray-300 mb-6">
        Şehir turlarınızı tek sayfada görüntüleyin ve yönetin
    </p>
    <!-- 🔗 takvimin gerçek dosya adını burada kullanın -->
    <a href="tour_calendar.php" class="nav-button block py-3 px-6 rounded-xl text-white text-center font-medium">
        Takvimi Aç
    </a>
</div>
            <!-- Hotel Takvimi Card -->
            <div class="dashboard-card p-8" style="animation: slideIn 0.5s ease-out forwards; animation-delay: 0.2s; opacity: 0;">
                <div class="icon-box w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-building text-3xl text-primary icon-scale"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Hotel Takvimi</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Otel rezervasyonlarını takip edin</p>
                <a href="cal.php" class="nav-button block py-3 px-6 rounded-xl text-white text-center font-medium">
                    Takvimi Aç
                </a>
            </div>

            <!-- Otel Fatura Card -->
            <div class="dashboard-card p-8" style="animation: slideIn 0.5s ease-out forwards; animation-delay: 0.3s; opacity: 0;">
                <div class="icon-box w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-file-invoice-dollar text-3xl text-primary icon-scale"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Otel Fatura</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Otel faturalarını oluşturun ve yönetin</p>
                <a href="invoice_form.php" class="nav-button block py-3 px-6 rounded-xl text-white text-center font-medium">
                    Fatura Oluştur
                </a>
            </div>

            <!-- Otel Voucher Card -->
            <div class="dashboard-card p-8" style="animation: slideIn 0.5s ease-out forwards; animation-delay: 0.4s; opacity: 0;">
                <div class="icon-box w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-ticket-alt text-3xl text-primary icon-scale"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Otel Voucher</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Otel voucher'larını hazırlayın</p>
                <a href="form.php" class="nav-button block py-3 px-6 rounded-xl text-white text-center font-medium">
                    Voucher Oluştur
                </a>
            </div>

            <!-- Transfer Voucher Card -->
            <div class="dashboard-card p-8" style="animation: slideIn 0.5s ease-out forwards; animation-delay: 0.5s; opacity: 0;">
                <div class="icon-box w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-exchange-alt text-3xl text-primary icon-scale"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Transfer Voucher</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Transfer voucher'larını yönetin</p>
                <a href="transfer-voucher-form.php" class="nav-button block py-3 px-6 rounded-xl text-white text-center font-medium">
                    Voucher Oluştur
                </a>
            </div>

            <!-- Transfer Fatura Card -->
            <div class="dashboard-card p-8" style="animation: slideIn 0.5s ease-out forwards; animation-delay: 0.6s; opacity: 0;">
                <div class="icon-box w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-file-invoice text-3xl text-primary icon-scale"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Transfer Fatura</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Transfer faturalarını düzenleyin</p>
                <a href="transfer-invoice-form.php" class="nav-button block py-3 px-6 rounded-xl text-white text-center font-medium">
                    Fatura Oluştur
                </a>
            </div>

            <!-- Makbuz İşlemleri Card -->
            <div class="dashboard-card p-8" style="animation: slideIn 0.5s ease-out forwards; animation-delay: 0.7s; opacity: 0;">
                <div class="icon-box w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-receipt text-3xl text-primary icon-scale"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Makbuz İşlemleri</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Makbuz oluşturun ve düzenleyin</p>
                <a href="receipt-form.php" class="nav-button block py-3 px-6 rounded-xl text-white text-center font-medium">
                    Makbuz Oluştur
                </a>
            </div>

            <!-- Tur Voucher Card -->
            <div class="dashboard-card p-8" style="animation: slideIn 0.5s ease-out forwards; animation-delay: 0.8s; opacity: 0;">
                <div class="icon-box w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-route text-3xl text-primary icon-scale"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Tur Voucher</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Tur voucher'larını yönetin</p>
                <a href="tour_voucher_form.php" class="nav-button block py-3 px-6 rounded-xl text-white text-center font-medium">
                    Voucher Oluştur
                </a>
            </div>

            <?php if ($isAdmin): ?>
            <!-- Antetli Kağıt Card - Only visible to admin users -->
            <div class="dashboard-card p-8" style="animation: slideIn 0.5s ease-out forwards; animation-delay: 0.9s; opacity: 0;">
                <div class="icon-box w-16 h-16 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-file-alt text-3xl text-primary icon-scale"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Antetli Kağıt</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6">Kurumsal dokümanlar oluşturun</p>
                <a href="letterhead.php" class="nav-button block py-3 px-6 rounded-xl text-white text-center font-medium">
                    Doküman Oluştur
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer class="mt-20 text-center pb-8">
            <div class="mb-6">
                <div class="h-px w-32 mx-auto bg-gradient-to-r from-transparent via-primary to-transparent"></div>
            </div>
            <h2 class="text-2xl font-bold header-text mb-3">CYN TURIZM</h2>
            <p class="text-gray-500 dark:text-gray-400">© 2024 Tüm hakları saklıdır</p>
        </footer>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            
            if (html.getAttribute('data-theme') === 'dark') {
                html.removeAttribute('data-theme');
                document.cookie = "theme=light; path=/; max-age=31536000";
            } else {
                html.setAttribute('data-theme', 'dark');
                document.cookie = "theme=dark; path=/; max-age=31536000";
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Theme is now handled by PHP based on cookie at the HTML tag level
            
            // Animate cards sequentially
            const cards = document.querySelectorAll('.dashboard-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = 1;
                }, index * 100 + 100);
            });
        });
    </script>
</body>
</html>
<?php
}
?>