<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>VIP Transfer Services - CYN TURIZM</title>
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
    height: 60px;
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

.hero-image {
    width: 100%;
    height: 60vh;
    object-fit: cover;
    border-radius: 0 0 8px 8px;
    box-shadow: var(--shadow);
}

.page-header {
    background: linear-gradient(to right, rgba(0,0,0,0.7), rgba(0,0,0,0.3)), url('img/file-AshJUchc3ssKgKVRAzK0OOFH.webp');
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

.content-container {
    background-color: var(--white);
    border-radius: 8px;
    box-shadow: var(--shadow);
    padding: 2.5rem;
    margin-top: -70px;
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

.section-title {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    font-size: 2.2rem;
    letter-spacing: 0.5px;
    position: relative;
    display: inline-block;
    padding-bottom: 10px;
}

.section-title::after {
    content: '';
    position: absolute;
    width: 50px;
    height: 3px;
    background-color: var(--accent-color);
    bottom: 0;
    left: 0;
}

.service-card {
    background-color: var(--white);
    border-radius: 8px;
    box-shadow: var(--shadow);
    padding: 1.5rem;
    margin-bottom: 2rem;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}

.service-card:hover {
    box-shadow: var(--hover-shadow);
    transform: translateY(-5px);
}

.service-card h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.service-card i {
    color: var(--accent-color);
    font-size: 2.5rem;
    margin-bottom: 1rem;
    display: block;
}

.contact-info {
    background-color: var(--white);
    border-radius: 8px;
    box-shadow: var(--shadow);
    padding: 2rem;
    margin-bottom: 3rem;
}

.contact-info h3 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
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

.footer {
    background-color: #262626;
    color: var(--white);
    padding: 3rem 0 1.5rem;
    position: relative;
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
    
    .section-title {
        font-size: 1.8rem;
    }
}

@media (max-width: 767px) {
    .header-title {
        font-size: 2rem;
    }
    
    .content-container {
        padding: 1.5rem;
        margin-top: -50px;
    }
    
    .section-title {
        font-size: 1.6rem;
    }
    
    .hero-image {
        height: 40vh;
    }
}

@media (max-width: 575px) {
    .header-title {
        font-size: 1.8rem;
    }
    
    .content-container {
        padding: 1.25rem;
        margin-top: -30px;
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

<section>
    <img src="slide45.jpg" alt="VIP Transfer Service" class="hero-image" style="width: 100%; height: auto; max-height: none; object-fit: contain;">
</section>

    <!-- Main Content -->
    <div class="container content-container">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="section-title">VIP Transfer Services</h2>
                <p class="lead">Experience luxury and comfort with our premium transfer solutions</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <p>CYN TURIZM offers a wide range of transfer services to and from the airports, including Istanbul International Airport, Sabiha Gokcen Airport, and Antalya Airport, to facilitate the movements of travelers and provide a comfortable journey to and from the airport.</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="service-card text-center">
                    <i class="fas fa-plane-departure"></i>
                    <h3>Istanbul International Airport</h3>
                    <p>One of the largest airports in the world, located on the European side of the city and serves millions of passengers annually. Transfer services are available via wide range of luxury cars such as Mercedes van, Mercedes vito, buses offering comfortable and direct trips to various parts of the city and surrounding areas.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="service-card text-center">
                    <i class="fas fa-plane"></i>
                    <h3>Sabiha Gokcen Airport</h3>
                    <p>Located on the Asian side of Istanbul and is a preferred choice for many domestic flights and some international ones. Travelers to and from Sabiha Gokcen can choose from a variety of transport services, including buses, Mercedes vito, Mercedes van providing convenient and efficient transportation.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="service-card text-center">
                    <i class="fas fa-plane-arrival"></i>
                    <h3>Antalya Airport</h3>
                    <p>The gateway to Turkey's Riviera, serves travelers heading to the beautiful beaches and resorts in the area. This airport features multiple transport services including buses, Mercedes van, Mercedes vito, ensuring that travelers reach their destinations with ease and comfort.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="service-card">
                    <h3>Our Fleet</h3>
                    <p>We pride ourselves on maintaining a modern fleet of luxury vehicles that cater to all your transfer needs:</p>
                    <ul>
                        <li>Mercedes Vito - Perfect for small groups and families</li>
                        <li>Mercedes Van - Spacious and comfortable for larger groups</li>
                        <li>Luxury Sedans - For executive and VIP transfers</li>
                        <li>Modern Buses - Ideal for group transfers and excursions</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="service-card">
                    <h3>Why Choose Us</h3>
                    <ul>
                        <li>Professional, multilingual drivers</li>
                        <li>24/7 service availability</li>
                        <li>Punctual pickups and drop-offs</li>
                        <li>Competitive pricing with no hidden fees</li>
                        <li>Comfortable, air-conditioned vehicles</li>
                        <li>Door-to-door service</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-8 mx-auto">
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
                        <li><a href="index-2.php" class="text-white">Home</a></li>
                        <li><a href="tours-2.php" class="text-white">Tours</a></li>
                        <li><a href="transfer-2.php" class="text-white">Transfer</a></li>
                        <li><a href="contact-2.php" class="text-white">Contact Us</a></li>
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

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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