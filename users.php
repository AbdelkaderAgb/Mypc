<?php
include 'auth.php'; // Include auth.php to restrict access
$servername = "localhost"; // Adjust as necessary
$username = "cyntzsrb_cyn"; // Your DB username
$password = "Qj!d$}Zh,-~m"; // Your DB password
$dbname = "cyntzsrb_cyn"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handling the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userId'], $_POST['email'], $_POST['password'])) {
    $userId = $conn->real_escape_string($_POST['userId']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']); // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "UPDATE users SET email = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $email, $hashedPassword, $userId);
    $stmt->execute();
    
    // Redirect to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$sql = "SELECT id, company_name, first_name FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Cyntourism - Users</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

<style>
    /* Root variables */
    :root {
        --primary: #4e73df;
        --secondary: #858796;
        --success: #1cc88a;
        --info: #36b9cc;
        --warning: #f6c23e;
        --danger: #e74a3b;
        --light: #f8f9fc;
        --dark: #5a5c69;
    }
    
    /* General body style */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fc;
    }
    
    /* Sidebar styling */
    .sidebar {
        background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .sidebar-brand {
        padding: 1.5rem 1rem;
    }
    
    .sidebar-brand-icon img {
        transition: transform 0.3s ease;
    }
    
    .sidebar-brand-icon img:hover {
        transform: rotate(15deg);
    }
    
    .nav-item .nav-link {
        padding: 1rem;
        font-weight: 500;
        font-size: 0.85rem;
        border-radius: 0.35rem;
        margin: 0 0.5rem;
    }
    
    .nav-item .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .nav-item .nav-link i {
        margin-right: 0.5rem;
        font-size: 0.85rem;
    }
    
    /* Table styling */
    .table {
        background-color: #ffffff;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        border-radius: 0.5rem;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .table:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .table thead th {
        background-color: var(--primary);
        color: white;
        border-top: none;
        padding: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        transition: background-color 0.3s ease;
    }
    
    .table tbody tr:hover td {
        background-color: rgba(78, 115, 223, 0.05);
    }
    
    /* User section styling */
    .user-section {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
        animation: fadeIn 0.5s ease forwards;
    }
    
    .user-section h2 {
        color: var(--dark);
        font-weight: 700;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--primary);
        padding-bottom: 0.75rem;
        display: inline-block;
    }
    
    /* Modal customization */
    .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        overflow: hidden;
    }
    
    .modal-header {
        background: linear-gradient(90deg, var(--primary) 0%, #224abe 100%);
        color: white;
        border-bottom: none;
        padding: 1.25rem;
    }
    
    .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }
    
    /* Form styling */
    .form-control {
        border-radius: 0.35rem;
        padding: 0.75rem 1rem;
        border: 1px solid rgba(0, 0, 0, 0.1);
        font-size: 0.9rem;
        box-shadow: none;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }
    
    .form-group label {
        font-weight: 500;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }
    
    /* Button styling */
    .btn {
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        border-radius: 0.35rem;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(90deg, var(--primary) 0%, #224abe 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(90deg, #224abe 0%, var(--primary) 100%);
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(78, 115, 223, 0.4);
    }
    
    .btn-secondary {
        background-color: var(--secondary);
        border: none;
    }
    
    .btn-secondary:hover {
        background-color: #6c757d;
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(108, 117, 125, 0.4);
    }
    
    /* Clickable elements */
    .clickable {
        cursor: pointer;
        position: relative;
        transition: color 0.3s ease;
    }
    
    .clickable:hover {
        color: var(--primary);
    }
    
    .clickable:after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background-color: var(--primary);
        transition: width 0.3s ease;
    }
    
    .clickable:hover:after {
        width: 100%;
    }
    
    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .user-section {
            padding: 1rem;
        }
        
        .user-section h2 {
            font-size: 1.5rem;
        }
        
        .table thead th {
            padding: 0.75rem;
        }
        
        .table tbody td {
            padding: 0.75rem;
        }
    }
    
    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
</style>

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
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">
            
            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
               <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                   aria-expanded="true" aria-controls="collapseUtilities">
                   <i class="fas fa-hotel"></i>
                   <span>Hotels</span>
               </a>

                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Hotels:</h6>
                        <a class="collapse-item" href="upload.php">Add hotel</a>
                        <a class="collapse-item" href="increase_prices.php">Change prices</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Nav Item - Users -->
            <li class="nav-item active">
                <a class="nav-link" href="users.php">
                    <i class="fas fa-users"></i>
                    <span>Users</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->
            <div class="sidebar-card d-none d-lg-flex">
                <img class="sidebar-card-illustration mb-2" src="img/Tursab.png"  alt="Icon" style="width: 120px; height: 120px;">
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
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">cyn manager</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
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
                <div class="container-fluid">
                    <div class="user-section animate-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2>User Management</h2>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>First Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td class='clickable' data-toggle='modal' data-target='#userModal' onclick='showPopup(" . $row["id"] . ")'>" . 
                                                htmlspecialchars($row["company_name"]) . 
                                            "</td>
                                            <td>" . htmlspecialchars($row["first_name"]) . "</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2' class='text-center'>No users found</td></tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
            
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Cyntourism 2024</span>
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

    <!-- User Update Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Update User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="userId" id="userId">
                        <div class="form-group">
                            <label for="email">Change Email:</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Change Password:</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>
                        <div class="text-right mt-4">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary ml-2">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

    <script>
    function showPopup(userId) {
        document.getElementById("userId").value = userId;
        // Triggering Bootstrap's modal via JavaScript
        $('#userModal').modal('show');
    }

    // Optional: jQuery for handling the modal close event
    $('#userModal').on('hidden.bs.modal', function (e) {
        // You could clear the form or perform other actions.
        document.getElementById("email").value = '';
        document.getElementById("password").value = '';
    });
    
    // Initialize animation on page load
    $(document).ready(function() {
        // Delayed table row animation
        $('.table tbody tr').each(function(index) {
            $(this).css({
                'opacity': '0',
                'transform': 'translateY(20px)'
            });
            setTimeout(function(el) {
                el.css({
                    'opacity': '1',
                    'transform': 'translateY(0)',
                    'transition': 'all 0.3s ease'
                });
            }, 100 + (index * 50), $(this));
        });
    });
    </script>
</body>

</html>
<?php
$conn->close();
?>