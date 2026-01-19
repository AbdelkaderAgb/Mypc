<?php
session_start();

// Establishing a connection to the database
$servername = "localhost";
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$database = "cyntzsrb_cyn";

$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch profile information for the admin user from the database
$sql = "SELECT * FROM users WHERE role = 'admin' LIMIT 1"; // Assuming there is only one admin user
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $companyName = $row['company_name'];
    $firstName = $row['first_name'];
    $lastName = $row['last_name'];
    $email = $row['email'];
    $phoneNumber = $row['phone_number'];
} else {
    echo "No admin user found";
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update profile information if form is submitted
    $updateEmail = $_POST['updateEmail'];
    $updatePhoneNumber = $_POST['updatePhoneNumber'];
    $updatePassword = $_POST['updatePassword'];

    $updateEmail = mysqli_real_escape_string($conn, $updateEmail);
    $updatePhoneNumber = mysqli_real_escape_string($conn, $updatePhoneNumber);
    $updatePassword = password_hash(mysqli_real_escape_string($conn, $updatePassword), PASSWORD_DEFAULT);

    // Update the user's profile information in the database
    $updateSql = "UPDATE users SET email = '$updateEmail', phone_number = '$updatePhoneNumber', password = '$updatePassword' WHERE role = 'admin'";
    if (mysqli_query($conn, $updateSql)) {
        echo "Profile information updated successfully";
    } else {
        echo "Error updating profile information: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Cyntourism - Profile</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-5">
                    <img src="img/icon123.ico" alt="Icon" style="width: 80px; height: 80px;">
                </div>
                <div class="sidebar-brand-text mx-3">©<sup>2024</sup></div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="admin.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-hotel"></i>
                    <span>Hotels</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Hotels:</h6>
                        <a class="collapse-item" href="upload.php">Add hotel</a>
                        <a class="collapse-item" href="increase_prices.php">change prices</a>
                       
                    </div>
                </div>
            </li>
            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="users.php">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
            <!-- Sidebar Message -->
            <div class="sidebar-card d-none d-lg-flex">
                <img class="sidebar-card-illustration mb-2" src="img/Tursab.png" alt="Icon" style="width: 120px; height: 120px;">
                <p class="text-center mb-2"><strong>CYN TURIZM </strong> company is certified by tursab under address BELGE NO: 11738</p>
                <a class="btn btn-success btn-sm" href="https://www.tursab.org.tr/acenta-arama">Check here!</a>
            </div>
        </ul>
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">cyn manger</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
 
                                </a>
                                <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                                    <a class="dropdown-item" href="profile.php">
                                        <i class="fas fa-cog fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Profile Settings
                                    </a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#Modal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container">
                    <h1>Profile</h1>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Profile Information</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="companyName">Company Name</label>
                                        <input type="text" id="companyName" value="<?php echo $companyName; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="firstName">First Name</label>
                                        <input type="text" id="firstName" value="<?php echo $firstName; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="lastName">Last Name</label>
                                        <input type="text" id="lastName" value="<?php echo $lastName; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" id="email" value="<?php echo $email; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="phoneNumber">Phone Number</label>
                                        <input type="tel" id="phoneNumber" value="<?php echo $phoneNumber; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Update Profile Information</h3>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="form-group">
                                            <label for="updateEmail">Email Address</label>
                                            <input type="email" id="updateEmail" name="updateEmail" placeholder="Enter new email">
                                        </div>
                                        <div class="form-group">
                                            <label for="updatePhoneNumber">Phone Number</label>
                                            <input type="tel" id="updatePhoneNumber" name="updatePhoneNumber" placeholder="Enter new phone number">
                                        </div>
                                        <div class="form-group">
                                            <label for="updatePassword">Password</label>
                                            <input type="password" id="updatePassword" name="updatePassword" placeholder="Enter new password">
                                        </div>
                                        <button type="submit" class="btn-primary">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="text-center">
                        <span>Cyntourism &copy; 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
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