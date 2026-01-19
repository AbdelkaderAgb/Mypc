<?php
// Include authentication (auth.php starts the session and verifies authentication)
include 'auth.php';

// Redirect to login page if user is not authenticated
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    // Try auto-login with cookie first
    if (isset($_COOKIE['remember_me'])) {
        // Retrieve the token from the cookie
        $token = $_COOKIE['remember_me'];
        
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
        
        // Use a prepared statement to safely query the user with this token
        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $resultToken = $stmt->get_result();
        
        if ($resultToken->num_rows > 0) {
            $user = $resultToken->fetch_assoc();
            // Set session variables for the authenticated user
            $_SESSION['auth'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
        } else {
            // If auto-login failed, redirect to login page
            header("Location: login.php");
            exit();
        }
        $stmt->close();
        $conn->close();
    } else {
        // No cookie found, redirect to login page
        header("Location: login.php");
        exit();
    }
}
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

// Auto-login using the "remember me" cookie if the user is not already authenticated
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    if (isset($_COOKIE['remember_me'])) {
        // Retrieve the token from the cookie
        $token = $_COOKIE['remember_me'];
        
        // Use a prepared statement to safely query the user with this token
        $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $resultToken = $stmt->get_result();
        
        if ($resultToken->num_rows > 0) {
            $user = $resultToken->fetch_assoc();
            // Set session variables for the authenticated user
            $_SESSION['auth'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
        }
        $stmt->close();
    }
}

// Get current page number from query string (default is 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$hotelsPerPage = 10;
$offset = ($page - 1) * $hotelsPerPage;

// Query to fetch hotel names with pagination
$sql = "SELECT DISTINCT hotel_name FROM pricing_data LIMIT $offset, $hotelsPerPage";
$result = $conn->query($sql);

// Query to get total number of hotels for pagination
$totalHotelsResult = $conn->query("SELECT COUNT(DISTINCT hotel_name) as total FROM pricing_data");
$totalHotelsRow = $totalHotelsResult->fetch_assoc();
$totalHotels = $totalHotelsRow['total'];
$totalPages = ceil($totalHotels / $hotelsPerPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Cyntourism - Your premier tourism partner in Turkey">
    <meta name="author" content="Cyntourism">
    <title>Cyntourism - Hotel Listings</title>
    <!-- Custom fonts and styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #CA8C05;
            --primary-light: #FFD700;
            --primary-dark: #A06000;
            --secondary: #2A4D69;
            --light: #F8F9FA;
            --dark: #212529;
            --gray: #6c757d;
            --border-radius: 12px;
            --box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.7;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
        }
        
        /* Navbar Styling */
        .navbar {
            background-color: #fff !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-brand img {
            height: 80px;
            width: auto;
            transition: var(--transition);
        }
        
        .navbar-brand img:hover {
            transform: scale(1.05);
        }
        
        .nav-link {
            font-weight: 500;
            padding: 10px 15px !important;
            color: var(--dark) !important;
            border-bottom: 2px solid transparent;
            transition: var(--transition);
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
            border-bottom: 2px solid var(--primary);
        }
        
        /* Hotel List Container */
        .section-container {
            padding: 40px 20px;
            background-color: #fff;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
            margin: 30px auto;
        }
        
        .section-title {
            font-size: 2.5rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--primary);
        }
        
        /* Search Bar */
        .search-container {
            max-width: 700px;
            margin: 0 auto 30px;
            position: relative;
        }
        
        .search-input {
            border-radius: 50px;
            padding: 15px 25px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            font-size: 16px;
            transition: var(--transition);
        }
        
        .search-input:focus {
            box-shadow: 0 5px 15px rgba(202, 140, 5, 0.2);
            border-color: var(--primary-light);
        }
        
        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        /* Hotel List */
        .hotel-list {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hotel-item {
            margin: 12px 0;
            padding: 16px 20px;
            background-color: var(--light);
            border-radius: var(--border-radius);
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 4px solid var(--primary);
        }
        
        .hotel-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            background-color: #fff;
        }
        
        .hotel-name {
            color: var(--secondary);
            font-weight: 600;
            font-size: 18px;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .hotel-name:hover {
            color: var(--primary);
        }
        
        .view-details {
            padding: 6px 15px;
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 30px;
            font-size: 14px;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }
        
        .view-details:hover {
            background: linear-gradient(to right, var(--primary-dark), var(--primary));
            transform: scale(1.05);
            color: white;
        }
        
        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }
        
        .pagination .page-link {
            margin: 0 5px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            border: none;
            transition: var(--transition);
        }
        
        .pagination .page-link:hover {
            background-color: var(--primary-light);
            color: white;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary);
            color: white;
        }
        
        /* Featured Section */
        .feature-heading {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 40px;
            text-align: center;
            color: var(--dark);
        }
        
        .feature-card {
            border: none;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .card-img-top {
            height: 280px;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .feature-card:hover .card-img-top {
            transform: scale(1.05);
        }
        
        .card-body {
            padding: 25px;
        }
        
        .card-title {
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 15px;
        }
        
        .card-text {
            color: var(--gray);
            font-size: 15px;
            line-height: 1.6;
        }
        
        /* Info Cards */
        .info-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--box-shadow);
            height: 100%;
            transition: var(--transition);
        }
        
        .info-card:hover {
            transform: translateY(-5px);
        }
        
        .certification-img {
            max-width: 200px;
            margin-bottom: 20px;
            transition: var(--transition);
        }
        
        .info-card:hover .certification-img {
            transform: scale(1.05);
        }
        
        .contact-info p {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        
        .contact-info i {
            color: var(--primary);
            margin-right: 10px;
            font-size: 18px;
            width: 25px;
            text-align: center;
        }
        
        .contact-info a {
            color: var(--secondary);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .contact-info a:hover {
            color: var(--primary);
        }
        
        .btn-check-certificate {
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            color: white;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-check-certificate:hover {
            background: linear-gradient(to right, var(--primary-dark), var(--primary));
            transform: scale(1.05);
            color: white;
        }
        
        /* Footer */
        .footer {
            background-color: var(--primary);
            color: white;
            padding: 20px 0;
            text-align: center;
            font-weight: 500;
            margin-top: 60px;
        }
        
        /* Back to top button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 45px;
            height: 45px;
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            transition: var(--transition);
            z-index: 99;
        }
        
        .back-to-top:hover {
            background-color: var(--primary-dark);
            transform: translateY(-5px);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: white;
                border-radius: var(--border-radius);
                padding: 20px;
                box-shadow: var(--box-shadow);
                margin-top: 15px;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .feature-heading {
                font-size: 1.8rem;
            }
            
            .nav-link {
                padding: 10px !important;
            }
            
            .navbar-nav {
                align-items: center;
            }
        }
        
        @media (max-width: 767px) {
            .section-container {
                padding: 30px 15px;
            }
            
            .hotel-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .view-details {
                align-self: flex-end;
            }
            
            .card-img-top {
                height: 220px;
            }
        }
    </style>
</head>
<body>
    <!-- Back to Top Button -->
    <a href="#" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="img/logo.png" alt="Cyntourism Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tours.php">Tours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transfer.php">Transfer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact Us</a>
                    </li>
                    <?php
                    // Check if user is logged in using the session variable
                    if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
                        // If the user is an admin, show the Dashboard link
                        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                            echo '<li class="nav-item">
                                    <a class="nav-link" href="admin.php">Dashboard</a>
                                  </li>';
                        }
                        // Display Logout link for any logged-in user
                        echo '<li class="nav-item">
                                <a class="nav-link" href="logout.php">Logout</a>
                              </li>';
                    } else {
                        // For non-logged in users, display the Login link
                        echo '<li class="nav-item">
                                <a class="nav-link" href="login.php">logout</a>
                              </li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
        
    <!-- Hotel Listings Section -->
    <div class="container section-container">
        <h2 class="section-title">Premium Hotel Collection</h2>
        
        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="hotelSearch" class="form-control search-input" placeholder="Find your perfect stay...">
            <i class="fas fa-search search-icon"></i>
        </div>
        
        <!-- Hotel List -->
        <div class="hotel-list">
            <ul id="hotelList" class="list-unstyled">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li class="hotel-item">
                                <a href="hotel.php?name=' . urlencode($row['hotel_name']) . '" class="hotel-name">' . htmlspecialchars($row['hotel_name']) . '</a>
                                <a href="hotel.php?name=' . urlencode($row['hotel_name']) . '" class="view-details">View Details</a>
                              </li>';
                    }
                } else {
                    echo '<li class="hotel-item">No hotels found.</li>';
                }
                ?>
            </ul>
            
            <!-- Pagination -->
            <div class="pagination-container">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Featured Destinations -->
    <div class="container my-5">
        <h2 class="feature-heading">Premium Turkish Experiences</h2>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <a href="transfer.php">
                        <img src="tra.webp" class="card-img-top" alt="Luxury Transfer Services">
                    </a>
                    <div class="card-body">
                        <h4 class="card-title">Luxury Transfer Services</h4>
                        <p class="card-text">Experience premium transportation from all major airports in Turkey. Our fleet of luxury vehicles ensures comfortable and reliable travel to your destination.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <a href="tours.php">
                        <img src="tour.webp" class="card-img-top" alt="Exclusive Turkish Tours">
                    </a>
                    <div class="card-body">
                        <h4 class="card-title">Exclusive Tours</h4>
                        <p class="card-text">Discover Turkey's hidden gems with our carefully curated tours. From Istanbul's historic wonders to Cappadocia's magical landscapes, create unforgettable memories.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mx-auto">
                <div class="feature-card">
                    <a href="index.php">
                        <img src="img/images (25)_LE_auto_x2_colored_light_ai.jpg" class="card-img-top" alt="Luxury Hotels & Resorts">
                    </a>
                    <div class="card-body">
                        <h4 class="card-title">Luxury Accommodations</h4>
                        <p class="card-text">Indulge in Turkey's finest hotels and resorts. From beachfront villas to historic boutique hotels, we offer a curated selection of premium accommodations.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Info Section -->
    <div class="container my-5">
        <div class="row g-4">
            <!-- Certification Block -->
            <div class="col-lg-6">
                <div class="info-card text-center">
                    <img class="certification-img" src="img/tursab-seeklogo-removebg.png" alt="Tursab Certification">
                    <h4 class="mb-3">Official Certification</h4>
                    <p class="mb-4"><strong>CYN TURIZM</strong> is proud to be certified by TURSAB (Association of Turkish Travel Agencies) under license no: 11738</p>
                    <a class="btn btn-check-certificate" href="https://www.tursab.org.tr/acenta-arama" target="_blank">
                        Verify Certificate <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-6">
                <div class="info-card">
                    <h4 class="mb-4 text-center">Get In Touch</h4>
                    <div class="contact-info">
                        <p><i class="fas fa-phone-alt"></i> +90531 817 67 70</p>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:info@cyntour.com">info@cyntour.com</a></p>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:sales@cyntourim.com">sales@cyntourim.com</a></p>
                        <p><i class="fas fa-map-marker-alt"></i> Molla Gürani, Karakoyunlu Sokağı No:2 D:4, 34093 Fatih/İstanbul</p>
                        <p><i class="fab fa-instagram"></i> <a href="https://www.instagram.com/cyn__turizm/" target="_blank">@cyn__turizm</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <span>© 2006-2024 CYN TURIZM | All Rights Reserved</span>
        </div>
    </footer>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Hotel search functionality
            $("#hotelSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#hotelList li").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
            
            // Back to top button functionality
            $(window).scroll(function() {
                if ($(this).scrollTop() > 200) {
                    $('.back-to-top').fadeIn();
                } else {
                    $('.back-to-top').fadeOut();
                }
            });
            
            $('.back-to-top').click(function(e) {
                e.preventDefault();
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });
            
            // Add animation when scrolling
            function revealOnScroll() {
                var reveals = document.querySelectorAll('.feature-card, .info-card');
                
                for (var i = 0; i < reveals.length; i++) {
                    var windowHeight = window.innerHeight;
                    var elementTop = reveals[i].getBoundingClientRect().top;
                    var elementVisible = 150;
                    
                    if (elementTop < windowHeight - elementVisible) {
                        reveals[i].classList.add('show');
                    }
                }
            }
            
            window.addEventListener('scroll', revealOnScroll);
            revealOnScroll();
        });
    </script>
</body>
</html>
