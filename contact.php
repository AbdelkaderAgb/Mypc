<?php
include 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - CYN TURIZM</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Acme&family=Playfair+Display:ital,wght@0,675;1,675&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #8A6D3B;
            --secondary-color: #f9f7f4;
            --accent-color: #D4AF37;
            --text-color: #2c2c2c;
            --border-color: #e6e1d8;
            --light-text: #6c6c6c;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0,0,0,0.05);
            --hover-shadow: 0 15px 40px rgba(0,0,0,0.1);
            --transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--secondary-color);
            color: var(--text-color);
            line-height: 1.8;
            overflow-x: hidden;
        }

        h2, h3, h4 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
        }

        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-top: -5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .carousel-inner img {
            border-radius: 0 0 15px 15px;
        }

        .content-section {
            padding: 20px;
            background-color: #f8f9fa;
        }

        h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5rem;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }

        h2::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background-color: var(--accent-color);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        .navbar-nav .nav-link {
            color: var(--text-color);
            font-weight: 500;
            margin: 0 12px;
            position: relative;
            padding: 8px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--accent-color);
            transition: width 0.3s ease;
        }

        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-item.active .nav-link::after {
            width: 100%;
        }

        .navbar-nav .nav-item.active .nav-link {
            color: var(--primary-color);
            font-weight: 600;
        }

        .navbar {
            background-color: var(--white) !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            padding: 1rem 1.5rem;
            transition: var(--transition);
        }

        .navbar.scrolled {
            padding: 0.75rem 1.5rem;
        }

        .navbar-brand img {
    height: 100px; /* Increased size */
    width: auto;
    transition: var(--transition);
}

        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        /* Hero Image */
        .hero-image {
            width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 0 0 8px 8px;
            box-shadow: var(--shadow);
        }

        /* Content Container */
        .content-container {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 2.5rem;
            margin-top: 30px;
            margin-bottom: 3rem;
            position: relative;
            z-index: 10;
        }

        .content-container::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 1px solid var(--border-color);
            opacity: 0;
            border-radius: 4px;
            transition: var(--transition);
            pointer-events: none;
        }

        .content-container:hover {
            box-shadow: var(--hover-shadow);
        }

        .content-container:hover::before {
            opacity: 1;
        }

        /* Sidebar Card */
        .sidebar-card {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .sidebar-card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-5px);
        }

        .sidebar-card img {
            margin-bottom: 1.5rem;
        }

        .sidebar-card p {
            color: var(--text-color);
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .sidebar-card .btn-success {
            background-color: var(--primary-color);
            border: none;
            color: var(--white);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .sidebar-card .btn-success:hover {
            background-color: var(--accent-color);
            transform: translateY(-2px);
        }

        /* Contact Info */
        .contact-info {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 2rem;
            transition: var(--transition);
        }

        .contact-info:hover {
            box-shadow: var(--hover-shadow);
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .contact-item i {
            color: var(--accent-color);
            font-size: 1.2rem;
            margin-right: 1rem;
            width: 1.5rem;
            text-align: center;
        }

        .contact-item a {
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .contact-item a:hover {
            color: var(--primary-color);
        }

        /* Footer */
        .footer {
            background-color: #262626;
            color: var(--white);
            padding: 3rem 0 1.5rem;
            position: relative;
            margin-top: 40px;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
        }

        .copyright {
            margin-top: 2rem;
            opacity: 0.7;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
        }

        /* Back to top button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: var(--primary-color);
            color: var(--white);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            opacity: 0;
            transform: translateY(20px);
            transition: var(--transition);
            z-index: 100;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .back-to-top.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .back-to-top:hover {
            background-color: var(--accent-color);
            transform: translateY(-5px);
        }

        /* Responsive styles */
        @media (max-width: 991px) {
            .navbar-nav {
                text-align: center;
            }
            .navbar-nav .nav-item {
                display: inline-block;
            }
            h2 {
                font-size: 2rem;
            }
            .content-container {
                padding: 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .sidebar-card, .contact-info {
                margin-bottom: 1.5rem;
            }
            h2 {
                font-size: 1.8rem;
            }
            .navbar-brand img {
                height: 50px;
            }
        }

        @media (max-width: 575px) {
            .content-container {
                padding: 1.25rem;
            }
            .footer {
                padding: 2rem 0 1rem;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="img/logo.png" alt="Cyntourism Logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
           <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Dashboard</a>
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
                            <a class="nav-link" href="login.php">Login</a>
                          </li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Image Section -->
<section>
    <img src="slide45.jpg" alt="About CYN TURIZM" class="hero-image">
</section>

<!-- Main Content -->
<div class="container content-container">
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 class="section-title">About Us</h2>
            <p class="lead">Your trusted partner for premium travel services in Turkey since 2006</p>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <p>CYN Tourism, founded by Cüneyt Yedikardeş, has distinguished itself as a premier provider in Türkiye, focusing on hotel reservation services and transfer solutions from airports. With a commitment to reliability, we specialize in facilitating seamless airport transfers, including private and shuttle services, from Ataturk and Sabiha Gokcen Airports to hotels and vice versa.</p>
            
            <p>Leveraging our vast experience and a fleet of modern vehicles, we ensure a safe and comfortable journey, managed by knowledgeable local drivers. Additionally, our Meet and Greet services streamline the travel experience, offering a personal touch from the moment you arrive.</p>
            
            <p>Since 2006, our dedication to delivering top-tier transfer and accommodation arrangements has made us a preferred choice for international travelers visiting Türkiye.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="sidebar-card d-flex flex-column align-items-center">
                <img class="sidebar-card-illustration mb-2" src="img/tursab-seeklogo-removebg.png" alt="TURSAB Certification" style="width: 230px; height: 150px;">
                <p class="text-center mb-3"><strong>CYN TURIZM</strong> company is certified by Tursab under address BELGE NO: 11738</p>
                <a class="btn btn-success btn-sm" href="https://www.tursab.org.tr/acenta-arama">Check here!</a>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="contact-info">
                <h3 class="text-center mb-4">Contact Information</h3>
                <div class="contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <span>+90531 817 67 70</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>info@cyntour.com</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span>sales@cyntourim.com</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Molla Gürani, Karakoyunlu Sokağı No:2 D:4, 34093 Fatih/İstanbul</span>
                </div>
                <div class="contact-item">
                    <i class="fab fa-instagram"></i>
                    <a href="https://www.instagram.com/cyn__turizm/">cyn__turizm</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h4>CYN TURIZM</h4>
                <p>Your trusted partner for premium travel services in Turkey since 2006.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h4>Quick Links</h4>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-white">Home</a></li>
                    <li><a href="tours.php" class="text-white">Tours</a></li>
                    <li><a href="transfer.php" class="text-white">Transfer</a></li>
                    <li><a href="contact.php" class="text-white">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h4>Contact Us</h4>
                <p><i class="fas fa-phone-alt mr-2"></i> +90531 817 67 70</p>
                <p><i class="fas fa-envelope mr-2"></i> info@cyntour.com</p>
            </div>
        </div>
        <div class="copyright">
            © 2006-2024 CYN TURIZM All Rights Reserved
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<div class="back-to-top" id="backToTop">
    <i class="fas fa-arrow-up"></i>
</div>

<!-- Bootstrap core JavaScript-->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Back to top button visibility
    window.addEventListener('scroll', function() {
        const backToTop = document.getElementById('backToTop');
        if (window.scrollY > 300) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });
    
    // Back to top functionality
    document.getElementById('backToTop').addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>
</body>
</html>