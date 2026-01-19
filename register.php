<?php
$errors = array(); // Initialize an array to store validation errors

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $servername = "localhost";
    $username = "cyntzsrb_cyn";
    $password = "Qj!d$}Zh,-~m";
    $dbname = "cyntzsrb_cyn";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Validate and sanitize input fields
    $company_name = trim(mysqli_real_escape_string($conn, $_POST['company_name']));
    $first_name = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $last_name = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $phone_number = trim(mysqli_real_escape_string($conn, $_POST['phone_number']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($company_name)) { array_push($errors, "Company name is required"); }
    if (empty($first_name)) { array_push($errors, "First name is required"); }
    if (empty($last_name)) { array_push($errors, "Last name is required"); }
    if (empty($email)) { array_push($errors, "Email is required"); }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { array_push($errors, "Invalid email format"); }
    if (empty($phone_number)) { array_push($errors, "Phone number is required"); }
    if (empty($password)) { array_push($errors, "Password is required"); }
    elseif (strlen($password) < 8) { array_push($errors, "Password must be at least 8 characters"); }
    if ($password != $confirm_password) { array_push($errors, "Passwords do not match"); }

    // Check if email or company name already exist using prepared statement
    $check_stmt = $conn->prepare("SELECT email, company_name FROM users WHERE email = ? OR company_name = ?");
    $check_stmt->bind_param("ss", $email, $company_name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $user = $check_result->fetch_assoc();
        if ($user['email'] === $email) { array_push($errors, "Email already exists"); }
        if ($user['company_name'] === $company_name) { array_push($errors, "Company name already exists"); }
    }
    
    $check_stmt->close();

    // If no errors, insert into database
    if (count($errors) == 0) {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (company_name, first_name, last_name, email, phone_number, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $company_name, $first_name, $last_name, $email, $phone_number, $password_hash);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // Set session variable for success message
            session_start();
            $_SESSION['registration_success'] = true;
            // Redirect to login.php
            header("Location: login.php");
            exit();
        } else {
            array_push($errors, "Registration failed: " . $stmt->error);
        }

        $stmt->close();
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="CYN Registration Page - Create your account">
    <meta name="author" content="CYN">

    <title>CYN - Create Account</title>

    <!-- Custom fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    
    <!-- Custom additional styles -->
    <style>
        .bg-register-image {
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            transition: all 0.3s ease;
        }
        
        .btn-user {
            transition: all 0.3s ease;
        }
        
        .btn-user:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .logo-container img {
            max-height: 80px;
            transition: all 0.3s ease;
        }
        
        .logo-container img:hover {
            transform: scale(1.05);
        }
        
        .form-control-user:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .password-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            color: #6c757d;
        }
        
        .error-message {
            color: #e74a3b;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .card {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .strength-meter {
            height: 5px;
            width: 100%;
            background-color: #eee;
            border-radius: 3px;
            margin-top: 5px;
        }
        
        .strength-meter div {
            height: 100%;
            border-radius: 3px;
            transition: all 0.3s ease;
        }
        
        .password-strength-text {
            font-size: 0.8rem;
            margin-top: 5px;
        }
        
        .password-weak { background-color: #e74a3b; width: 30%; }
        .password-medium { background-color: #f6c23e; width: 60%; }
        .password-strong { background-color: #1cc88a; width: 100%; }
    </style>
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <!-- Display validation errors -->
        <?php if (count($errors) > 0) : ?>
            <div class="error-message mt-4">
                <?php foreach ($errors as $error) : ?>
                    <p class="mb-0"><i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?></p>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"
                        style="background-image: url('img/istanbul.png');">
                    </div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <!-- Logo container -->
                                <div class="logo-container">
                                    <img src="img/logo.png" alt="CYN Logo" class="img-fluid">
                                </div>
                                <h1 class="h4 text-gray-900 mb-4">Create Your Account</h1>
                                <p class="text-muted mb-4">Join our platform and start managing your business efficiently</p>
                            </div>
                            
                            <form class="user" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="registrationForm">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" name="company_name"
                                        id="companyName" placeholder="Company Name" value="<?php echo isset($company_name) ? htmlspecialchars($company_name) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" name="first_name"
                                            id="firstName" placeholder="First Name" value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" name="last_name"
                                            id="lastName" placeholder="Last Name" value="<?php echo isset($last_name) ? htmlspecialchars($last_name) : ''; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" name="email"
                                        id="emailAddress" placeholder="Email Address" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <input type="tel" class="form-control form-control-user" name="phone_number"
                                        id="phoneNumber" placeholder="Phone Number" value="<?php echo isset($phone_number) ? htmlspecialchars($phone_number) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <div class="password-container">
                                            <input type="password" class="form-control form-control-user"
                                                name="password" id="password" placeholder="Password" required>
                                            <i class="fas fa-eye toggle-password" data-target="password"></i>
                                        </div>
                                        <div class="strength-meter">
                                            <div id="strength-bar"></div>
                                        </div>
                                        <p class="password-strength-text" id="strength-text"></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="password-container">
                                            <input type="password" class="form-control form-control-user"
                                                name="confirm_password" id="confirmPassword" placeholder="Confirm Password" required>
                                            <i class="fas fa-eye toggle-password" data-target="confirmPassword"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-user btn-block" name="register">
                                    <i class="fas fa-user-plus mr-2"></i>Register Account
                                </button>
                                
                                <hr>
                            </form>
                            
                            <div class="text-center mt-3">
                                <a class="small" href="forgot-password.html">
                                    <i class="fas fa-lock mr-1"></i>Forgot Password?
                                </a>
                            </div>
                            <div class="text-center mt-2">
                                <a class="small" href="login.php">
                                    <i class="fas fa-sign-in-alt mr-1"></i>Already have an account? Login!
                                </a>
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
    
    <!-- Custom additional scripts -->
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('.toggle-password').click(function() {
                const targetId = $(this).data('target');
                const input = $('#' + targetId);
                
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    $(this).removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    $(this).removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
            // Password strength meter
            $('#password').keyup(function() {
                const password = $(this).val();
                const strengthBar = $('#strength-bar');
                const strengthText = $('#strength-text');
                
                // Remove all classes
                strengthBar.removeClass('password-weak password-medium password-strong');
                
                if (password.length === 0) {
                    strengthBar.css('width', '0%');
                    strengthText.text('');
                } else if (password.length < 8) {
                    strengthBar.addClass('password-weak');
                    strengthText.text('Weak - Use at least 8 characters');
                    strengthText.css('color', '#e74a3b');
                } else if (password.length >= 8 && 
                          (!password.match(/[A-Z]/) || !password.match(/[0-9]/) || !password.match(/[^A-Za-z0-9]/))) {
                    strengthBar.addClass('password-medium');
                    strengthText.text('Medium - Add uppercase, numbers and symbols');
                    strengthText.css('color', '#f6c23e');
                } else {
                    strengthBar.addClass('password-strong');
                    strengthText.text('Strong');
                    strengthText.css('color', '#1cc88a');
                }
            });
            
            // Form validation
            $('#registrationForm').submit(function(e) {
                let valid = true;
                const password = $('#password').val();
                const confirmPassword = $('#confirmPassword').val();
                
                // Clear previous validation messages
                $('.validation-message').remove();
                
                // Check password match
                if (password !== confirmPassword) {
                    e.preventDefault();
                    $('#confirmPassword').after('<p class="validation-message text-danger small mt-1">Passwords do not match</p>');
                    valid = false;
                }
                
                // Check password strength
                if (password.length < 8) {
                    e.preventDefault();
                    $('#password').after('<p class="validation-message text-danger small mt-1">Password must be at least 8 characters</p>');
                    valid = false;
                }
                
                return valid;
            });
        });
    </script>
</body>
</html>