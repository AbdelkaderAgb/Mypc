<?php
include 'auth.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Prices</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            color: #000;
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
        
        .container {
            padding: 20px;
            animation: fadeIn 0.5s ease forwards;
        }
        
        h2 {
            color: var(--dark);
            margin-bottom: 30px;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }
        
        h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 50px;
            background: var(--primary);
        }
        
        form {
            background: #fff;
            padding: 25px;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            width: 100%;
            max-width: 500px;
            margin: auto;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        form:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
        }
        
        input[type="file"] {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 5px;
            width: calc(100% - 24px);
            margin-bottom: 15px;
            transition: border-color 0.3s ease;
        }
        
        input[type="file"]:hover {
            border-color: var(--primary);
        }
        
        button[type="submit"] {
            background: linear-gradient(to right, var(--primary), #3a5fcc);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: block;
            width: 100%;
        }
        
        button[type="submit"]:hover {
            background: linear-gradient(to right, #3a5fcc, var(--primary));
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }
        
        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            color: #fff;
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-weight: 500;
            position: relative;
            padding-left: 45px;
        }
        
        .message:before {
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }
        
        .error {
            background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%);
        }
        
        .error:before {
            content: "\f071"; /* warning icon */
        }
        
        .success {
            background: linear-gradient(135deg, #1cc88a 0%, #27ae60 100%);
        }
        
        .success:before {
            content: "\f00c"; /* check icon */
        }
        
        .info {
            background: linear-gradient(135deg, #36b9cc 0%, #2980b9 100%);
        }
        
        .info:before {
            content: "\f05a"; /* info icon */
        }
        
        .debug {
            background-color: #f8f9fc;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px auto;
            border-radius: 8px;
            color: #333;
            max-width: 500px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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
        
        .animate-card {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .animate-card:nth-child(1) { animation-delay: 0.1s; }
        .animate-card:nth-child(2) { animation-delay: 0.2s; }
        
        footer {
            padding: 1.5rem 0;
            font-size: 0.85rem;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            .message {
                padding: 12px 12px 12px 40px;
                font-size: 0.9rem;
            }
            
            .message:before {
                font-size: 16px;
                left: 12px;
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
                <div class="sidebar-brand-icon rotate-n-5">
                    <img src="img/icon123.ico" alt="Icon" style="width: 80px; height: 80px;">
                </div>
                <div class="sidebar-brand-text mx-3">©<sup>2024</sup></div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="admin.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span></a>
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
                        <a class="collapse-item" href="increase_prices.php">Change prices</a>
                    </div>
                </div>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Users -->
            <li class="nav-item">
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
                <img class="sidebar-card-illustration mb-2" src="img/Tursab.png" alt="Icon" style="width: 120px; height: 120px;">
                <p class="text-center mb-2"><strong>CYN TURIZM</strong> company is certified by tursab under address BELGE NO: 11738</p>
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
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">cyn manager</span>
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
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <h2 class="animate-card">Import Hotel's Prices from Excel</h2>
                            <form method="post" enctype="multipart/form-data" class="animate-card">
                                <div class="form-group">
                                    <label for="file"><i class="fas fa-file-excel mr-2"></i>Select Excel File</label>
                                    <input type="file" name="file" id="file" accept=".xlsx" class="form-control-file">
                                </div>
                                <button type="submit" name="submit"><i class="fas fa-upload mr-2"></i>Upload</button>
                            </form>
                            <div class="message info animate-card" style="margin-top: 20px;">
                                Excel format should include columns: Hotel Name, Room Type, Accommodation, Start Date, End Date, Price, Currency
                            </div>
                            <?php
                            $hotel_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

                            if (isset($_POST['submit'])) {
                                $servername = "localhost";
                                $username = "cyntzsrb_cyn";
                                $password = "Qj!d$}Zh,-~m";
                                $dbname = "cyntzsrb_cyn";

                                // Create connection
                                $conn = new mysqli($servername, $username, $password, $dbname);

                                // Check connection
                                if ($conn->connect_error) {
                                    die("<div class='message error animate-card'>Connection failed: " . $conn->connect_error . "</div>");
                                }

                                // Function to check if currency column exists, if not add it
                                function ensureCurrencyColumnExists($conn) {
                                    $checkSql = "SHOW COLUMNS FROM pricing_data LIKE 'currency'";
                                    $result = $conn->query($checkSql);
                                    if ($result->num_rows == 0) {
                                        $sql = "ALTER TABLE pricing_data ADD COLUMN currency VARCHAR(10) DEFAULT 'EUR'";
                                        if ($conn->query($sql) === TRUE) {
                                            echo "<div class='message success animate-card'>Currency column added successfully.</div>";
                                        } else {
                                            echo "<div class='message error animate-card'>Error adding currency column: " . $conn->error . "</div>";
                                        }
                                    }
                                }

                                // Function to ensure price column is VARCHAR
                                function ensurePriceColumnIsVarchar($conn) {
                                    $checkSql = "SHOW COLUMNS FROM pricing_data LIKE 'price'";
                                    $result = $conn->query($checkSql);
                                    if ($result->num_rows > 0) {
                                        $row = $result->fetch_assoc();
                                        if (strpos(strtolower($row['Type']), 'varchar') === false) {
                                            $sql = "ALTER TABLE pricing_data MODIFY price VARCHAR(50)";
                                            if ($conn->query($sql) === TRUE) {
                                                echo "<div class='message success animate-card'>Price column structure updated to VARCHAR(50).</div>";
                                            } else {
                                                echo "<div class='message error animate-card'>Error updating column structure: " . $conn->error . "</div>";
                                            }
                                        }
                                    }
                                }

                                // Check and update database structure
                                ensurePriceColumnIsVarchar($conn);
                                ensureCurrencyColumnExists($conn);

                                $allowedFileType = [
                                    'application/vnd.ms-excel',
                                    'text/xls',
                                    'text/xlsx',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                ];

                                if (in_array($_FILES["file"]["type"], $allowedFileType)) {

                                    $targetPath = 'uploads/' . $_FILES['file']['name'];
                                    move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

                                    $Reader = new Xlsx();

                                    $spreadSheet = $Reader->load($targetPath);
                                    $excelSheet = $spreadSheet->getActiveSheet();
                                    $spreadSheetAry = $excelSheet->toArray();
                                    $sheetCount = count($spreadSheetAry);

                                    // Initialize counters
                                    $successCount = 0;
                                    $errorCount = 0;

                                    for ($i = 1; $i < $sheetCount; $i++) {
                                        if (isset($spreadSheetAry[$i][0])) {
                                            // Process and sanitize input data
                                            $hotel_name = mysqli_real_escape_string($conn, trim($spreadSheetAry[$i][0]));
                                            $room_type = mysqli_real_escape_string($conn, trim($spreadSheetAry[$i][1]));
                                            $accommodation = mysqli_real_escape_string($conn, trim($spreadSheetAry[$i][2]));
                                            $start_date = mysqli_real_escape_string($conn, trim($spreadSheetAry[$i][3]));
                                            $end_date = mysqli_real_escape_string($conn, trim($spreadSheetAry[$i][4]));
                                            $price = mysqli_real_escape_string($conn, trim($spreadSheetAry[$i][5]));
                                            // Get currency from the 7th column (index 6) or default to EUR
                                            $currency = isset($spreadSheetAry[$i][6]) ? mysqli_real_escape_string($conn, trim($spreadSheetAry[$i][6])) : 'EUR';
                                            
                                            // If currency is empty, set default
                                            if (empty($currency)) {
                                                $currency = 'EUR';
                                            }
                                            
                                            // Skip rows with empty essential data
                                            if (empty($hotel_name) || empty($room_type)) {
                                                $errorCount++;
                                                continue;
                                            }
                                            
                                            // Debug the processed values for the first few rows
                                            if ($i < 4) {
                                                echo "<div class='debug animate-card'>";
                                                echo "<strong>Processed values for row $i:</strong><br>";
                                                echo "<i class='fas fa-hotel mr-2'></i> Hotel: <span class='text-primary'>$hotel_name</span><br>";
                                                echo "<i class='fas fa-door-open mr-2'></i> Room: <span class='text-primary'>$room_type</span><br>";
                                                echo "<i class='fas fa-bed mr-2'></i> Accommodation: <span class='text-primary'>$accommodation</span><br>";
                                                echo "<i class='fas fa-calendar-alt mr-2'></i> Start date: <span class='text-primary'>$start_date</span><br>";
                                                echo "<i class='fas fa-calendar-alt mr-2'></i> End date: <span class='text-primary'>$end_date</span><br>";
                                                echo "<i class='fas fa-tag mr-2'></i> Price: <span class='text-primary'>$price</span><br>";
                                                echo "<i class='fas fa-money-bill-wave mr-2'></i> Currency: <span class='text-primary'>$currency</span><br>";
                                                echo "</div>";
                                            }
                                            
                                            // Prepare SQL with proper NULL and string handling
                                            $sql = "INSERT INTO pricing_data (hotel_name, room_type, accommodation, start_date, end_date, price, currency) VALUES (
                                                '$hotel_name',
                                                '$room_type',
                                                " . (!empty($accommodation) ? "'$accommodation'" : "NULL") . ",
                                                " . ($start_date ? "'$start_date'" : "NULL") . ",
                                                " . ($end_date ? "'$end_date'" : "NULL") . ",
                                                " . (!empty($price) ? "'$price'" : "NULL") . ",
                                                '$currency'
                                            )";
                                            
                                            // Execute query and track success/failure
                                            if ($conn->query($sql)) {
                                                $successCount++;
                                            } else {
                                                $errorCount++;
                                                // Display detailed error for debugging
                                                echo "<div class='message error animate-card'>Error on row $i: " . $conn->error . "<br>SQL: $sql</div>";
                                            }
                                        }
                                    }
                                    
                                    // Summary message
                                    echo "<div class='message success animate-card'>
                                        <strong>Import completed!</strong><br>
                                        <i class='fas fa-check-circle mr-2'></i> Successfully imported: $successCount records<br>
                                        <i class='fas fa-times-circle mr-2'></i> Failed to import: $errorCount records
                                    </div>";
                                    
                                } else {
                                    echo "<div class='message error animate-card'><i class='fas fa-exclamation-triangle mr-2'></i> Invalid File Type. Upload Excel File.</div>";
                                }

                                // Close connection
                                $conn->close();
                            }
                            ?>
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
                    <a class="btn btn-primary" href="login.html">Logout</a>
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