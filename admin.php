<?php
include 'auth.php'; // Include auth.php to restrict access

// Database connection
$host = 'localhost'; // Adjust if necessary
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$database = 'cyntzsrb_cyn';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total number of unique hotels
$hotelsQuery = "SELECT COUNT(DISTINCT hotel_name) AS total_hotels FROM pricing_data";
$hotelsResult = $conn->query($hotelsQuery);
$hotelsRow = $hotelsResult->fetch_assoc();
$totalHotels = $hotelsRow['total_hotels'] ?? 'N/A';

// Fetch total number of users
$usersQuery = "SELECT COUNT(*) AS total_users FROM users"; // Adjust if necessary for your table structure
$usersResult = $conn->query($usersQuery);
$usersRow = $usersResult->fetch_assoc();
$totalUsers = $usersRow['total_users'] ?? 'N/A';

// Fetch total number of cities
$citiesQuery = "SELECT COUNT(DISTINCT city) AS total_cities FROM hotels"; // Adjust if necessary for your table structure
$citiesResult = $conn->query($citiesQuery);
$citiesRow = $citiesResult->fetch_assoc();
$totalCities = $citiesRow['total_cities'] ?? 'N/A';

// Fetch hotel names
$hotelNamesQuery = "SELECT DISTINCT hotel_name FROM pricing_data";
$hotelNamesResult = $conn->query($hotelNamesQuery);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Cyntourism Admin Dashboard">
    <meta name="author" content="CYN Turizm">

    <title>Cyntourism - Dashboard</title>

    <!-- Custom fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
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
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
        }
        
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
        
        .topbar {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .card-big {
            font-size: 1.1rem;
            padding: 1.25rem;
        }
        
        .card-big .h5 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-top: 0.5rem;
        }
        
        .border-left-primary, .border-left-success, .border-left-info {
            border-left: 0.25rem solid;
            position: relative;
            overflow: hidden;
        }
        
        .border-left-primary:before, .border-left-success:before, .border-left-info:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0.25rem;
            height: 100%;
            background: inherit;
            opacity: 0.2;
        }
        
        .stat-icon {
            font-size: 2.5rem!important;
            opacity: 0.3;
            transition: transform 0.3s ease;
        }
        
        .card:hover .stat-icon {
            transform: scale(1.2);
        }
        
        .list-group-item {
            border: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 1.25rem;
            transition: background-color 0.3s ease;
        }
        
        .list-group-item:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .list-group-item:last-child {
            border-bottom: none;
        }
        
        .btn-danger {
            background-color: #e74a3b;
            border-color: #e74a3b;
            padding: 0.375rem 0.75rem;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background-color: #d52a1a;
            border-color: #d52a1a;
            transform: scale(1.05);
        }
        
        .scroll-to-top {
            bottom: 2rem;
            right: 2rem;
            background-color: rgba(90, 92, 105, 0.5);
            transition: all 0.3s ease;
        }
        
        .scroll-to-top:hover {
            background-color: rgba(90, 92, 105, 0.8);
        }
        
        footer {
            padding: 1.5rem 0;
            font-size: 0.85rem;
        }
        
        /* Enhanced Stats Cards */
        .stats-card {
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        
        .stats-card .card-body {
            z-index: 2;
            position: relative;
        }
        
        .stats-card:after {
            content: '';
            position: absolute;
            bottom: -10px;
            right: -10px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: inherit;
            opacity: 0.1;
            z-index: 1;
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
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .stats-row .col-xl-6 {
                margin-bottom: 1rem;
            }
            
            .card-big .h5 {
                font-size: 1.5rem;
            }
            
            .navbar-nav.ml-auto {
                display: flex;
                flex-direction: row;
                align-items: center;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-card {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .animate-card:nth-child(1) { animation-delay: 0.1s; }
        .animate-card:nth-child(2) { animation-delay: 0.2s; }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <img src="img/icon123.ico" alt="Icon" style="width: 60px; height: 60px;">
                </div>
                <div class="sidebar-brand-text mx-3">CYN<sup>2024</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="admin.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Management
            </div>

            <!-- Nav Item - Hotels Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-hotel"></i>
                    <span>Hotels</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Hotel Management:</h6>
                        <a class="collapse-item" href="upload.php">
                            <i class="fas fa-plus-circle fa-sm fa-fw mr-1"></i>Add Hotel
                        </a>
                        <a class="collapse-item" href="increase_prices.php">
                            <i class="fas fa-tag fa-sm fa-fw mr-1"></i>Pricing
                        </a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Users -->
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

            <!-- Certification -->
            <div class="sidebar-card d-none d-lg-flex bg-gradient-primary text-white mt-4">
                <img class="sidebar-card-illustration mb-2" src="img/Tursab.png" alt="Icon" style="width: 100px; height: 100px; filter: brightness(1.2);">
                <p class="text-center mb-2"><strong>CYN TURIZM</strong> is certified by TURSAB under BELGE NO: 11738</p>
                <a class="btn btn-light btn-sm" href="https://www.tursab.org.tr/acenta-arama">Verify</a>
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
                    
                    <!-- Page Title -->
                    <div class="d-none d-sm-inline-block mr-auto ml-md-3 my-2 my-md-0">
                        <h1 class="h5 mb-0 text-gray-800">Admin Dashboard</h1>
                    </div>
                    
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">CYN Manager</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <?php endif; ?>
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
                    
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
                        <div>
                            <a href="upload.php" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                                <i class="fas fa-plus fa-sm text-white-50"></i> New Hotel
                            </a>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row stats-row">
                        
                        <!-- Hotels Card -->
                        <div class="col-xl-6 col-md-6 mb-4 animate-card">
                            <div class="card border-left-primary shadow h-100 py-3 stats-card">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Hotels</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalHotels; ?></div>
                                            <div class="mt-2 text-xs text-gray-500">
                                                <i class="fas fa-arrow-up text-success mr-1"></i>
                                                <span>12% increase this month</span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-hotel fa-3x text-gray-300 stat-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Users Card -->
                        <div class="col-xl-6 col-md-6 mb-4 animate-card">
                            <div class="card border-left-success shadow h-100 py-3 stats-card">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Registered Users</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalUsers; ?></div>
                                            <div class="mt-2 text-xs text-gray-500">
                                                <i class="fas fa-arrow-up text-success mr-1"></i>
                                                <span>8% increase this month</span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-3x text-gray-300 stat-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Content Row -->

                    <!-- Hotel List Section -->
                    <div class="row">
                        <div class="col-lg-12 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Hotel Management</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Actions:</div>
                                            <a class="dropdown-item" href="upload.php">Add New Hotel</a>
                                            <a class="dropdown-item" href="increase_prices.php">Update Prices</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Export List</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Hotel Name</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($hotel = $hotelNamesResult->fetch_assoc()): ?>
                                                <tr>
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-hotel text-primary mr-2"></i>
                                                            <span><?php echo htmlspecialchars($hotel['hotel_name']); ?></span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="badge badge-success px-2 py-1">Active</span>
                                                    </td>
                                                    <td class="text-right align-middle">
                                                        <button class="btn btn-sm btn-outline-primary mr-1 edit-hotel" data-hotel="<?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger remove-hotel" data-hotel="<?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of Hotel List Section -->

                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; CYN TURIZM 2024</span>
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

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <script>
        // Initialize animations
        $(document).ready(function() {
            // Animation for cards
            setTimeout(function() {
                $('.animate-card').css('opacity', '1');
            }, 100);
            
            // Handle edit button clicks - redirect to increase_prices.php
            $('.edit-hotel').on('click', function() {
                var hotelName = $(this).data('hotel');
                window.location.href = 'increase_prices.php?hotel=' + encodeURIComponent(hotelName);
            });
            
            // Handle hotel removal
            $('.remove-hotel').on('click', function() {
                var hotelName = $(this).data('hotel');
                var $row = $(this).closest('tr');
                
                // Confirmation dialog
                if (confirm('Are you sure you want to remove "' + hotelName + '"?')) {
                    $.ajax({
                        url: 'remove_hotel.php',
                        type: 'POST',
                        data: { hotel_name: hotelName },
                        success: function(response) {
                            if(response === 'success') {
                                // Animate row removal
                                $row.fadeOut(300, function() {
                                    $(this).remove();
                                });
                                
                                // Show success toast (you would need to add a toast component)
                                showToast('Hotel successfully removed', 'success');
                            } else {
                                // Show error toast
                                showToast('Failed to remove hotel', 'error');
                            }
                        },
                        error: function() {
                            showToast('Server error occurred', 'error');
                        }
                    });
                }
            });
            
            // Simple toast function - you can replace this with a proper toast component
            function showToast(message, type) {
                // Create toast element if it doesn't exist
                if (!$('#toast-container').length) {
                    $('body').append('<div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;"></div>');
                }
                
                // Set color based on type
                var bgColor = type === 'success' ? '#1cc88a' : '#e74a3b';
                
                // Create toast HTML
                var toast = $('<div class="toast-notification" style="background-color: ' + bgColor + '; color: white; padding: 15px 25px; border-radius: 5px; margin-top: 10px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); max-width: 350px;">' + message + '</div>');
                
                // Add to container with animation
                $('#toast-container').append(toast);
                toast.css('transform', 'translateX(100%)');
                
                setTimeout(function() {
                    toast.css({
                        'transition': 'transform 0.3s ease',
                        'transform': 'translateX(0)'
                    });
                }, 10);
                
                // Auto remove after 3 seconds
                setTimeout(function() {
                    toast.css({
                        'transition': 'transform 0.3s ease, opacity 0.3s ease',
                        'transform': 'translateX(100%)',
                        'opacity': '0'
                    });
                    
                    setTimeout(function() {
                        toast.remove();
                    }, 300);
                }, 3000);
            }
        });
    </script>
</body>

</html>