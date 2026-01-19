<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adjust Hotel Prices</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
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
        
        /* Price adjustment form styling */
        .price-adjustment-container {
            margin: 20px auto;
            max-width: 600px;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            background-color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeIn 0.5s ease forwards;
        }
        
        .price-adjustment-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .price-adjustment-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .price-adjustment-header h1 {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        
        .price-adjustment-form label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .price-adjustment-form select,
        .price-adjustment-form input[type="number"] {
            width: 100%;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            color: #5a5c69; /* Ensure text color is visible */
            background-color: #fff; /* Ensure background is white */
            font-size: 0.9rem; /* Adjust font size */
            height: auto; /* Override any fixed height */
            appearance: auto; /* Ensure native select styling */
        }
        
        /* Fix for select element placeholder text */
        select option {
            color: #5a5c69;
            background-color: #fff;
        }
        
        select option:first-child {
            color: #858796;
            font-style: italic;
        }
        
        .price-adjustment-form select:focus,
        .price-adjustment-form input[type="number"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            outline: none;
        }
        
        .price-adjustment-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.35rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            border: none;
            color: white;
        }
        
        .btn-danger {
            background-color: #e74a3b;
            border-color: #e74a3b;
        }
        
        .btn-primary:hover {
            background: linear-gradient(180deg, #4e73df 30%, #224abe 100%);
        }
        
        .btn-danger:hover {
            background-color: #d52a1a;
            border-color: #d52a1a;
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
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .price-adjustment-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .price-adjustment-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .navbar-nav.ml-auto {
                display: flex;
                flex-direction: row;
                align-items: center;
            }
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
                <div class="sidebar-brand-icon">
                    <img src="img/icon123.ico" alt="Icon" style="width: 80px; height: 80px;" class="rotate-n-5">
                </div>
                <div class="sidebar-brand-text mx-3">CYN<sup>2024</sup></div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="admin.php">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Dashboard</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-hotel"></i>
                    <span>Hotels</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
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
            <li class="nav-item">
                <a class="nav-link" href="users.php">
                    <i class="fas fa-fw fa-users"></i>
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
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">CYN Manager</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-cog fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
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
                        <h1 class="h3 mb-0 text-gray-800">Hotel Price Management</h1>
                    </div>
                    
                    <!-- Price Adjustment Form -->
                    <div class="price-adjustment-container animate-card">
                        <div class="price-adjustment-header">
                            <h1><i class="fas fa-tags mr-2"></i>Adjust Hotel Prices</h1>
                            <p class="text-muted">Increase or decrease prices for selected hotels</p>
                        </div>
                        <form id="adjust-prices-form" class="price-adjustment-form">
                            <div class="form-group">
                                <label for="hotel-select"><i class="fas fa-hotel mr-2"></i>Select Hotel:</label>
                                <select id="hotel-select" name="hotel" class="form-control custom-select">
                                    <option value="" disabled selected>Select a hotel</option>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="percentage"><i class="fas fa-percent mr-2"></i>Percentage:</label>
                                <input type="number" id="percentage" name="percentage" step="0.01" required class="form-control" placeholder="Enter percentage value">
                            </div>
                            
                            <div class="price-adjustment-buttons">
                                <button type="button" class="btn btn-primary btn-block" onclick="adjustPrices('increase')">
                                    <i class="fas fa-arrow-up mr-2"></i>Increase Prices
                                </button>
                                <button type="button" class="btn btn-danger btn-block" onclick="adjustPrices('decrease')">
                                    <i class="fas fa-arrow-down mr-2"></i>Decrease Prices
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End of Page Content -->
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
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
    <!-- Alert Modal -->
    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Operation Result</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id="alertModalBody">Operation completed successfully!</div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-dismiss="modal">OK</button>
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
        function loadHotels() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_hotels.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    var hotelSelect = document.getElementById('hotel-select');
                    
                    // Keep only the default option
                    hotelSelect.innerHTML = '<option value="" disabled selected>Select a hotel</option>';
                    
                    // Add hotel options
                    response.hotels.forEach(function(hotel) {
                        var option = document.createElement('option');
                        option.value = hotel;
                        option.innerText = hotel;
                        hotelSelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        }
        
        function adjustPrices(action) {
            var form = document.getElementById('adjust-prices-form');
            var hotelSelect = document.getElementById('hotel-select');
            var percentage = document.getElementById('percentage').value;
            
            // Basic validation
            if (hotelSelect.value === '') {
                showAlert('Please select a hotel');
                return;
            }
            
            if (!percentage || percentage <= 0) {
                showAlert('Please enter a valid percentage greater than 0');
                return;
            }
            
            var formData = new FormData(form);
            formData.append('action', action);
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'adjust_prices.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        showAlert('Prices ' + (action === 'increase' ? 'increased' : 'decreased') + ' successfully!');
                    } else {
                        showAlert('Error: ' + xhr.responseText);
                    }
                }
            };
            xhr.send(formData);
        }
        
        function showAlert(message) {
            document.getElementById('alertModalBody').innerText = message;
            $('#alertModal').modal('show');
        }
        
        // Make sure the select element is properly styled
        document.addEventListener('DOMContentLoaded', function() {
            loadHotels();
            
            // Add animations to sidebar items
            var navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(function(item, index) {
                item.style.opacity = 0;
                setTimeout(function() {
                    item.style.transition = 'opacity 0.3s ease';
                    item.style.opacity = 1;
                }, 100 * index);
            });
            
            // Ensure select styling is correct
            var selectElements = document.querySelectorAll('select');
            selectElements.forEach(function(select) {
                // Add Bootstrap's custom-select class
                select.classList.add('custom-select');
                
                // Ensure the text color is visible
                select.style.color = '#5a5c69';
            });
        });
    </script>
</body>
</html>