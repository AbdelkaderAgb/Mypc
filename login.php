<?php
session_start();
require_once "db.php"; // Include your database connection file here

$errors = [];

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
            if ($user['role'] === 'admin') {
                header("Location: index.php");
                exit();
            } else {
                header("Location: index-2.php");
                exit();
            }
        } else {
            $errors[] = "Invalid login credentials.";
        }
    } catch(PDOException $e) {
        $errors[] = "Connection failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="CYN Login Page">
    <meta name="author" content="CYN">
    <title>CYN - Login</title>
    <!-- Custom fonts and styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom additional styles -->
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #224abe;
            border-color: #224abe;
            transform: translateY(-1px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
        }
        .bg-login-image {
            border-radius: 10px 0 0 10px;
            background-position: center;
        }
        .form-control-user {
            border-radius: 100px;
            padding: 1.2rem 1rem;
        }
        .form-control-user:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        .alert-danger {
            border-radius: 10px;
            background-color: #ffebee;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .login-logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 1.5rem;
        }
        .animated {
            animation-duration: 0.5s;
            animation-fill-mode: both;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .fadeIn {
            animation-name: fadeIn;
        }
    </style>
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5 animated fadeIn">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image" style="background-image: url('img/istanbul1.png'); background-size: cover;"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <img src="img/logo.png" alt="CYN Logo" class="img-fluid login-logo">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <?php if (!empty($errors)): ?>
                                        <div class="alert alert-danger animated fadeIn" role="alert">
                                            <?php foreach ($errors as $error): ?>
                                                <p class="mb-0"><i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?></p>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <form class="user" action="" method="post">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" 
                                                id="exampleInputEmail" name="email" 
                                                aria-describedby="emailHelp" 
                                                placeholder="Enter Email Address..." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" 
                                                id="exampleInputPassword" name="password" 
                                                placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block" name="login">
                                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">
                                            <i class="fas fa-key mr-1"></i>Forgot Password?
                                        </a>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a class="small" href="register.php">
                                            <i class="fas fa-user-plus mr-1"></i>Create an Account!
                                        </a>
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
    
    <!-- Additional animations and effects -->
    <script>
    $(document).ready(function() {
        // Add smooth transitions
        $('.form-control').focus(function() {
            $(this).parent().addClass('focused');
        });
        
        $('.form-control').blur(function() {
            if ($(this).val() === '') {
                $(this).parent().removeClass('focused');
            }
        });
        
        // Form submission animation
        $('.user').submit(function() {
            $('.btn-user').html('<i class="fas fa-spinner fa-spin mr-2"></i>Logging in...');
        });
    });
    </script>
</body>
</html>