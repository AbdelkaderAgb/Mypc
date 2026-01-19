<?php
include 'auth.php'; // Include auth.php to restrict access
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYN Tours - Explore Beautiful Destinations</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary-color: #b8860b;
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
        height: 80px;
        width: auto;
        transition: var(--transition);
    }

    .navbar-brand img:hover {
        transform: scale(1.05);
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

    .page-header {
        background: linear-gradient(to right, rgba(0,0,0,0.7), rgba(0,0,0,0.3)), url('img/istanbul.png');
        background-size: cover;
        background-position: center;
        color: var(--white);
        padding: 100px 0 60px;
        margin-bottom: 50px;
        position: relative;
    }

    .header-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .header-title {
        font-size: 3.5rem;
        margin-bottom: 20px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .header-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        max-width: 700px;
        margin: 0 auto;
    }

    .feature-heading {
        font-family: 'Cormorant Garamond', serif;
        color: var(--primary-color);
        font-size: 2.5rem;
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        font-weight: 700;
    }

    .feature-heading::after {
        content: '';
        position: absolute;
        width: 80px;
        height: 3px;
        background-color: var(--accent-color);
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
    }

    .feature-card {
        background-color: var(--white);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: var(--transition);
        height: 100%;
        border: 1px solid var(--border-color);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-shadow);
    }

    .feature-card .card-img-top {
        height: 220px;
        object-fit: cover;
        transition: var(--transition);
    }

    .feature-card:hover .card-img-top {
        transform: scale(1.05);
    }

    .feature-card .card-body {
        padding: 1.5rem;
    }

    .feature-card .card-title {
        color: var(--primary-color);
        font-size: 1.4rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .feature-card .card-text {
        color: var(--light-text);
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    .feature-card .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), #D4AF37);
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        font-weight: 500;
        transition: var(--transition);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
    }

    .feature-card .btn-primary:hover {
        background: linear-gradient(135deg, #D4AF37, var(--primary-color));
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }

    .sidebar-card {
        background-color: var(--white);
        border-radius: 8px;
        padding: 2rem;
        box-shadow: var(--shadow);
        transition: var(--transition);
        border: 1px solid var(--border-color);
    }

    .sidebar-card:hover {
        box-shadow: var(--hover-shadow);
    }

    .sidebar-card img {
        transition: var(--transition);
    }

    .sidebar-card:hover img {
        transform: scale(1.05);
    }

    .sidebar-card p {
        color: var(--light-text);
        font-size: 0.95rem;
    }

    .sidebar-card .btn-success {
        background: linear-gradient(135deg, var(--primary-color), #D4AF37);
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        font-weight: 500;
        transition: var(--transition);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
    }

    .sidebar-card .btn-success:hover {
        background: linear-gradient(135deg, #D4AF37, var(--primary-color));
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }

    .contact-info p {
        margin-bottom: 0.75rem;
        color: var(--text-color);
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }

    .contact-info i {
        color: var(--accent-color);
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }

    .contact-info a {
        color: var(--primary-color);
        text-decoration: none;
        transition: var(--transition);
    }

    .contact-info a:hover {
        color: var(--accent-color);
        text-decoration: underline;
    }

    .footer {
        background-color: #262626;
        color: var(--white);
        padding: 2rem 0;
        position: relative;
        margin-top: 4rem;
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
        .header-title {
            font-size: 2.5rem;
        }
        
        .feature-heading {
            font-size: 2rem;
        }
        
        .sidebar-card {
            margin-bottom: 2rem;
        }
    }

    @media (max-width: 767px) {
        .navbar-brand img {
            height: 60px;
        }
        
        .page-header {
            padding: 80px 0 40px;
        }
        
        .header-title {
            font-size: 2rem;
        }
        
        .feature-card .card-img-top {
            height: 180px;
        }
    }

    @media (max-width: 575px) {
        .feature-card {
            margin-bottom: 1.5rem;
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index-2.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tours-2.php">Tours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transfer-2.php">Transfer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact-2.php">Contact Us</a>
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

<!-- Header Section -->
<section class="page-header">
    <div class="container">
        <div class="header-content">
            <h1 class="header-title">Discover Amazing Tours</h1>
            <p class="header-subtitle">Explore the beauty of Turkey with our carefully curated tours</p>
        </div>
    </div>
</section>

<!-- Tours Section -->
<div class="container">
    <h2 class="feature-heading">Our Tours</h2>
    
    <div class="row g-4">
        <!-- Istanbul Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/istanbul.png" class="card-img-top" alt="Istanbul">
                <div class="card-body">
                    <h4 class="card-title">Istanbul Tour</h4>
                    <p class="card-text">Explore the historical beauty of Istanbul with our amazing tour.</p>
                    <a href="pdf/Istanbultour.pdf" class="btn btn-primary" download="IstanbulTour.pdf">Tour Programme</a>
                </div>
            </div>
        </div>
        
        <!-- Bursa Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/65854f24-4f9e-4b19-a024-cf444eedad2e.jpg" class="card-img-top" alt="Bursa">
                <div class="card-body">
                    <h4 class="card-title">Bursa Tour</h4>
                    <p class="card-text">Experience the green Bursa with its historic sites and nature.</p>
                    <a href="pdf/bursatour.pdf" class="btn btn-primary" download="BursaTour.pdf">Tour Programme</a>
                </div>
            </div>
        </div>
        
        <!-- Yalova Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/images (28)-fotor-enhance-20240330174124.png" class="card-img-top" alt="Yalova">
                <div class="card-body">
                    <h4 class="card-title">Yalova Tour</h4>
                    <p class="card-text">Relax in the thermal springs and explore the natural beauty of Yalova.</p>
                    <a href="pdf/Yalovatour.pdf" class="btn btn-primary" download="YalovaTour.pdf">Tour Programme</a>
                </div>
            </div>
        </div>
        
        <!-- Şile Ağva Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/images (29)-fotor-enhance-20240330174338.png" class="card-img-top" alt="Şile Ağva">
                <div class="card-body">
                    <h4 class="card-title">Şile Ağva Tour</h4>
                    <p class="card-text">Discover the serene beaches and beautiful landscapes of Şile and Ağva.</p>
                    <a href="pdf/sile.pdf" class="btn btn-primary" download="SileAgvaTour.pdf">Tour Programme</a>
                </div>
            </div>
        </div>
        
        <!-- Sapanca Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/images (30)-fotor-enhance-20240330174554.png" class="card-img-top" alt="Sapanca">
                <div class="card-body">
                    <h4 class="card-title">Sapanca Maşukiye Tour</h4>
                    <p class="card-text">Enjoy the lush greenery and peaceful lake of Sapanca.</p>
                    <a href="pdf/Sapancatour.pdf" class="btn btn-primary" download="SapancaTour.pdf">Tour Programme</a>
                </div>
            </div>
        </div>
        
        <!-- Princes' Island Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/output_image.png" class="card-img-top" alt="Princes' Island">
                <div class="card-body">
                    <h4 class="card-title">Princesses Island</h4>
                    <p class="card-text">Experience the charm of Istanbul's Princes' Islands, with their rich history and tranquil scenery.</p>
                    <a href="pdf/Princessisland.pdf" class="btn btn-primary" download="PrincesIslandTour.pdf">Tour Programme</a>
                </div>
            </div>
        </div>

        <!-- Cappadocia Red Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/cappadocia-5270797.jpg" class="card-img-top" alt="Cappadocia Red Tour">
                <div class="card-body">
                    <h4 class="card-title">Cappadocia Red Tour</h4>
                    <p class="card-text">Explore the breathtaking landscapes and fairy chimneys of Cappadocia on our Red Tour.</p>
                    <a href="pdf/RED TOUR (2).pdf" class="btn btn-primary" download="CappadociaRedTour.pdf">Tour Programme</a>
                </div>
            </div>
        </div>

        <!-- ATV Quadbike Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/enhanced.jpg" class="card-img-top" alt="ATV Quadbike Tour">
                <div class="card-body">
                    <h4 class="card-title">Cappadocia ATV Quadbike</h4>
                    <p class="card-text">Experience the thrill of riding through Cappadocia's unique landscapes on an ATV quadbike.</p>
                    <a href="pdf/ATV QUATBİKE TOURS.pdf" class="btn btn-primary" download="ATVQuadbikeTour.pdf">Tour Programme</a>
                </div>
            </div>
        </div>

        <!-- Horse Back Riding Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/enhanced (1).jpg" class="card-img-top" alt="Horse Back Riding">
                <div class="card-body">
                    <h4 class="card-title">Horseback Riding</h4>
                    <p class="card-text">Connect with nature and explore scenic trails on horseback in Cappadocia.</p>
                    <a href="pdf/HORSE BACK RİDİNG.pdf" class="btn btn-primary" download="HorseBackRidingTour.pdf">Tour Programme</a>
                </div>
            </div>
        </div>

        <!-- Green Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/enhanced (2).jpg" class="card-img-top" alt="Green Tour">
                <div class="card-body">
                    <h4 class="card-title">Green Tour</h4>
                    <p class="card-text">Immerse yourself in the lush green valleys and historical sites of Cappadocia.</p>
                    <a href="pdf/GREEN TOUR.pdf" class="btn btn-primary" download="GreenTour.pdf">Tour Programme</a>
                </div>
            </div>
        </div>

        <!-- Cappadocia Turkish Night Tour Card -->
        <div class="col-sm-6 col-md-4">
            <div class="feature-card">
                <img src="img/enhanced (3).jpg" class="card-img-top" alt="Cappadocia Turkish Night">
                <div class="card-body">
                    <h4 class="card-title">Cappadocia Turkish Night</h4>
                    <p class="card-text">Experience a magical evening of Turkish culture with music, dance, and delicious cuisine.</p>
                    <a href="pdf/cappadocia-turkish-night.pdf" class="btn btn-primary" download="CappadociaTurkishNight.pdf">Tour Programme</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Info Section -->
<div class="container mt-5">
    <div class="row">
        <!-- Certification Block -->
        <div class="col-md-6 mb-4">
            <div class="sidebar-card h-100">
                <div class="text-center">
                    <img src="img/tursab-seeklogo-removebg.png" alt="Tursab Certification" style="width: 230px; height: 150px;" class="mb-3">
                    <p><strong>CYN TURIZM</strong> company is certified by Tursab under address BELGE NO: 11738</p>
                    <a class="btn btn-success btn-sm" href="https://www.tursab.org.tr/acenta-arama" target="_blank">Check here!</a>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-md-6 mb-4">
            <div class="sidebar-card h-100">
                <h4 class="text-center mb-4" style="color: var(--primary-color);">Contact Information</h4>
                <div class="contact-info">
                    <p><i class="fas fa-phone-alt"></i> +90531 817 67 70</p>
                    <p><i class="fas fa-envelope"></i> info@cyntour.com</p>
                    <p><i class="fas fa-envelope"></i> sales@cyntourim.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> Molla Gürani, Karakoyunlu Sokağı No:2 D:4, 34093 Fatih/İstanbul</p>
                    <p><i class="fab fa-instagram"></i> <a href="https://www.instagram.com/cyn__turizm/" target="_blank">cyn__turizm</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Back to Top Button -->
<div class="back-to-top">
    <i class="fas fa-chevron-up"></i>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="container text-center">
        <span>© 2006-2024 CYN TURIZM All Rights Reserved</span>
    </div>
</footer>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Back to top button functionality
    const backToTopBtn = document.querySelector('.back-to-top');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
        
        // Add scrolled class to navbar for smaller padding
        if (window.pageYOffset > 50) {
            document.querySelector('.navbar').classList.add('scrolled');
        } else {
            document.querySelector('.navbar').classList.remove('scrolled');
        }
    });
    
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
</script>

</body>
</html>