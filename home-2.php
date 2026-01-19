<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سي واي إن للسياحة | خدمات سياحية متكاملة للجزائريين</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
/* Reputable resources:
   - MDN on CSS Variables: https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_custom_properties
   - MDN on Responsive Design: https://developer.mozilla.org/en-US/docs/Learn/CSS/CSS_layout/Responsive_Design
   - CSS-Tricks on Modern CSS: https://css-tricks.com/snippets/css/a-guide-to-flexbox/ */

/* Root Variables */
:root { 
  --primary-color: #b8860b; /* Gold for luxury */
  --secondary-color: #1e3a5f; /* Deep blue */
  --accent-color: #c9a55a; /* Light gold */
  --dark-color: #1a1a1a;
  --light-color: #f9f9f9;
  --text-color: #333;
  --light-text: #fff;
  --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--accent-color));
  --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  --transition: all 0.3s ease;
  --border-radius: 12px;
  --container-width: 1200px;
}

/* Base Styles */
body {
  font-family: 'Cairo', sans-serif;
  line-height: 1.8;
  color: var(--text-color);
  background-color: var(--light-color);
  margin: 0;
  padding: 0;
  overflow-x: hidden;
}

/* Buttons */
.btn-primary {
  background: var(--gradient-primary);
  border: none;
  border-radius: 50px;
  padding: 12px 30px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  box-shadow: 0 5px 15px rgba(184, 134, 11, 0.3);
  transition: var(--transition);
  position: relative;
  overflow: hidden;
  z-index: 1;
}
.btn-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 0;
  height: 100%;
  background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
  transition: width 0.5s ease;
  z-index: -1;
  border-radius: 50px;
}
.btn-primary:hover::before {
  width: 100%;
}
.btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(184, 134, 11, 0.4);
}
.btn-primary:active {
  transform: translateY(0);
  box-shadow: 0 4px 10px rgba(184, 134, 11, 0.3);
}

/* Navigation Bar */
.navbar {
  padding: 20px 0;
  background-color: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(15px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
  transition: var(--transition);
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 1000;
}
.navbar.scrolled {
  padding: 10px 0;
  background-color: rgba(255, 255, 255, 0.98);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}
.navbar-brand img {
  height: 60px;
  transition: var(--transition);
}
.navbar.scrolled .navbar-brand img {
  height: 50px;
}
.nav-link {
  font-weight: 600;
  color: var(--secondary-color) !important;
  margin: 0 12px;
  position: relative;
  padding: 8px 0;
  transition: var(--transition);
}
.nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  right: 0;
  width: 0;
  height: 2px;
  background-color: var(--primary-color);
  transition: var(--transition);
}
.nav-link:hover::after,
.nav-link:focus::after,
.nav-link.active::after {
  width: 100%;
}
.nav-link:hover,
.nav-link:focus,
.nav-link.active {
  color: var(--primary-color) !important;
}

/* Mobile Navigation */
.navbar-toggler {
  border: none;
  padding: 0;
}
.navbar-toggler:focus {
  box-shadow: none;
}
.navbar-toggler-icon {
  width: 24px;
  height: 24px;
  position: relative;
  transition: var(--transition);
}

/* Hero Section with Parallax */
.hero-section {
  background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('/api/placeholder/1200/600');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  color: white;
  padding: 180px 0 140px;
  text-align: center;
  position: relative;
  overflow: hidden;
  margin-top: 80px;
}
.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(30, 58, 95, 0.8), rgba(0, 0, 0, 0.6));
  z-index: 1;
}
.hero-content {
  position: relative;
  z-index: 2;
  max-width: 900px;
  margin: 0 auto;
  opacity: 0;
  transform: translateY(20px);
  animation: fadeInUp 1s ease forwards;
  animation-delay: 0.3s;
}
.hero-badge {
  display: inline-block;
  background-color: var(--primary-color);
  color: white;
  padding: 10px 25px;
  border-radius: 50px;
  font-weight: 600;
  margin-bottom: 20px;
  font-size: 16px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  transform: translateY(10px);
  opacity: 0;
  animation: fadeInUp 0.8s ease forwards;
  animation-delay: 0.5s;
}
.hero-title {
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 800;
  margin-bottom: 25px;
  line-height: 1.2;
  text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  letter-spacing: 1px;
  transform: translateY(10px);
  opacity: 0;
  animation: fadeInUp 0.8s ease forwards;
  animation-delay: 0.7s;
}
.hero-subtitle {
  font-size: clamp(1.2rem, 2vw, 1.6rem);
  margin-bottom: 40px;
  max-width: 700px;
  margin: 0 auto;
  opacity: 0;
  transform: translateY(10px);
  animation: fadeInUp 0.8s ease forwards;
  animation-delay: 0.9s;
}

/* Sections */
.section {
  padding: 100px 0;
  position: relative;
  overflow: hidden;
}
.section-title {
  position: relative;
  font-weight: 700;
  margin-bottom: 60px;
  padding-bottom: 20px;
  text-align: center;
  font-size: clamp(2rem, 4vw, 2.5rem);
}
.section-title::after {
  content: '';
  position: absolute;
  bottom: 0;
  right: 50%;
  transform: translateX(50%);
  width: 80px;
  height: 3px;
  background: var(--gradient-primary);
}

/* Service Cards */
.service-card {
  background-color: #fff;
  border-radius: var(--border-radius);
  padding: 40px 30px;
  margin-bottom: 30px;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
  text-align: center;
  border: 1px solid rgba(0, 0, 0, 0.03);
  position: relative;
  height: 100%;
  overflow: hidden;
}
.service-card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  height: 5px;
  width: 100%;
  background: var(--gradient-primary);
  transform: scaleX(0);
  transform-origin: right;
  transition: transform 0.5s ease;
}
.service-card:hover::before {
  transform: scaleX(1);
}
.service-card:hover {
  transform: translateY(-15px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}
.service-icon {
  font-size: 3rem;
  color: var(--primary-color);
  margin-bottom: 25px;
  display: inline-block;
  background: rgba(184, 134, 11, 0.1);
  width: 100px;
  height: 100px;
  line-height: 100px;
  border-radius: 50%;
  transition: var(--transition);
}
.service-card:hover .service-icon {
  transform: rotateY(360deg);
  background: var(--gradient-primary);
  color: #fff;
}
.service-title {
  font-weight: 700;
  margin-bottom: 20px;
  color: var(--secondary-color);
}

/* Destination Cards */
.destination-card {
  position: relative;
  border-radius: var(--border-radius);
  overflow: hidden;
  margin-bottom: 30px;
  height: 300px;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
}
.destination-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.8s ease;
}
.destination-overlay {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 100%;
  padding: 30px;
  background-image: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
  color: #fff;
  transition: var(--transition);
}
.destination-card:hover .destination-img {
  transform: scale(1.1);
}
.destination-card:hover .destination-overlay {
  padding-bottom: 40px;
}
.destination-title {
  font-weight: 700;
  margin-bottom: 8px;
  font-size: 1.8rem;
}
.destination-subtitle {
  opacity: 0.8;
}

/* Office Cards */
.office-card {
  background-color: #fff;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: var(--box-shadow);
  margin-bottom: 30px;
  transition: var(--transition);
  border: 1px solid rgba(0, 0, 0, 0.03);
  height: 100%;
}
.office-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}
.office-img {
  width: 100%;
  height: 250px;
  object-fit: cover;
  transition: var(--transition);
}
.office-card:hover .office-img {
  transform: scale(1.05);
}
.office-content {
  padding: 30px;
}
.office-title {
  font-weight: 700;
  margin-bottom: 15px;
  color: var(--secondary-color);
  font-size: 1.5rem;
}
.office-info {
  margin-bottom: 12px;
  display: flex;
  align-items: flex-start;
}
.office-info i {
  color: var(--primary-color);
  font-size: 1.2rem;
  margin-left: 10px;
  margin-top: 5px;
  transition: var(--transition);
}
.office-card:hover .office-info i {
  transform: translateX(-5px);
}

/* Testimonial Cards */
.testimonial-card {
  background-color: #fff;
  border-radius: var(--border-radius);
  padding: 40px 30px;
  margin-bottom: 30px;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
  border: 1px solid rgba(0, 0, 0, 0.03);
  height: 100%;
}
.testimonial-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}
.testimonial-content {
  font-style: italic;
  margin-bottom: 30px;
  padding: 0 20px;
  font-size: 1.1rem;
  line-height: 1.8;
  position: relative;
}
.testimonial-content::before {
  content: '\201D';
  font-size: 4rem;
  color: var(--primary-color);
  position: absolute;
  right: -10px;
  top: -30px;
  opacity: 0.2;
  font-family: serif;
}
.testimonial-author {
  display: flex;
  align-items: center;
  border-top: 1px solid rgba(0, 0, 0, 0.05);
  padding-top: 20px;
}
.testimonial-author img {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  object-fit: cover;
  margin-left: 20px;
  border: 4px solid rgba(184, 134, 11, 0.1);
}
.testimonial-author h4 {
  font-weight: 700;
  margin-bottom: 5px;
  color: var(--secondary-color);
}

/* Contact Form */
.contact-form .form-control {
  padding: 15px 20px;
  margin-bottom: 25px;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  font-size: 1rem;
  transition: var(--transition);
  background-color: #f8f9fa;
}
.contact-form .form-control:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(184, 134, 11, 0.25);
  background-color: #fff;
}
.contact-form textarea {
  min-height: 150px;
  resize: vertical;
}

/* Enhanced Footer Design */
.footer {
  background-color: var(--secondary-color);
  color: #fff;
  padding: 80px 0 30px;
  position: relative;
  overflow: hidden;
  text-align: center;
}

.footer::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 5px;
  background: var(--gradient-primary);
  z-index: 1;
}

.footer-content {
  position: relative;
  z-index: 2;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 40px;
  margin-bottom: 50px;
  padding: 0 15px;
}

.footer-logo img {
  height: 70px;
  margin-bottom: 25px;
}

.footer h4 {
  font-weight: 700;
  margin-bottom: 25px;
  padding-bottom: 15px;
  color: #fff;
  position: relative;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.footer h4::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 50px;
  height: 2px;
  background-color: var(--primary-color);
}

.footer-nav {
  list-style: none;
  padding: 0;
  margin: 0;
}
.footer-nav li {
  margin-bottom: 12px;
}
.footer-nav a {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: var(--transition);
  display: inline-block;
  padding: 5px 0;
}
.footer-nav a:hover {
  color: #fff;
  padding-right: 10px;
}

.footer-form {
  margin-top: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}
.footer-form .form-control {
  background-color: rgba(255, 255, 255, 0.1);
  border: none;
  color: #fff;
  padding: 12px 20px;
  border-radius: 50px 0 0 50px;
  outline: none;
}
.footer-form .form-control::placeholder {
  color: rgba(255, 255, 255, 0.6);
}
.footer-form .btn {
  border-radius: 0 50px 50px 0;
  background-color: var(--primary-color);
  border: none;
  padding: 12px 20px;
  color: #fff;
  transition: var(--transition);
}
.footer-form .btn:hover {
  background-color: darken(var(--primary-color), 10%);
}

/* Social Links */
.social-links {
  text-align: center;
  margin-bottom: 30px;
}
.social-links a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 45px;
  height: 45px;
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff;
  border-radius: 50%;
  margin: 0 8px;
  font-size: 1.2rem;
  transition: var(--transition);
}
.social-links a:hover {
  background-color: var(--primary-color);
  transform: translateY(-5px);
}

/* Copyright */
.copyright {
  position: relative;
  z-index: 2;
  text-align: center;
  padding-top: 30px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.9rem;
}

/* Responsive Footer Adjustments */
@media (max-width: 768px) {
  .footer-content {
    flex-direction: column;
    gap: 20px;
  }
  .footer-form {
    flex-direction: column;
  }
  .footer-form .form-control,
  .footer-form .btn {
    width: 100%;
    border-radius: 50px;
  }
  .footer-form .btn {
    margin-top: 10px;
  }
}


/* Social Links */
.social-links {
  text-align: center;
  margin-bottom: 30px;
}
.social-links a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 45px;
  height: 45px;
  background-color: rgba(255, 255, 255, 0.1);
  color: #fff;
  border-radius: 50%;
  margin: 0 8px;
  font-size: 1.2rem;
  transition: var(--transition);
}
.social-links a:hover {
  background-color: var(--primary-color);
  transform: translateY(-5px);
}

/* Copyright */
.copyright {
  text-align: center;
  padding-top: 30px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.9rem;
}

/* Back to Top Button */
.back-to-top {
  position: fixed;
  bottom: 30px;
  left: 30px;
  width: 50px;
  height: 50px;
  background: var(--gradient-primary);
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  visibility: hidden;
  transition: var(--transition);
  z-index: 999;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}
.back-to-top.visible {
  opacity: 1;
  visibility: visible;
}
.back-to-top:hover {
  transform: translateY(-5px);
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive Grid Layout */
.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 30px;
}

/* Media Queries for Responsiveness */

/* Large Devices & Desktops */
@media (max-width: 1200px) {
  .container {
    max-width: 95%;
  }
}

/* Medium Devices */
@media (max-width: 992px) {
  .hero-section {
    padding: 160px 0 120px;
  }
  .section {
    padding: 80px 0;
  }
  .navbar-collapse {
    background-color: #fff;
    padding: 20px;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    max-height: 80vh;
    overflow-y: auto;
  }
}

/* Small Devices & Tablets */
@media (max-width: 768px) {
  .hero-section {
    padding: 140px 0 100px;
  }
  .section {
    padding: 60px 0;
  }
  .section-title {
    margin-bottom: 40px;
  }
  .card-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
  }
  .office-card,
  .testimonial-card,
  .service-card {
    margin-bottom: 20px;
  }
}

/* Extra Small Devices & Phones */
@media (max-width: 576px) {
  .hero-section {
    padding: 120px 0 80px;
  }
  .section {
    padding: 40px 0;
  }
  .btn-primary {
    padding: 10px 25px;
    font-size: 0.9rem;
  }
  .navbar-brand img {
    height: 45px;
  }
  .navbar.scrolled .navbar-brand img {
    height: 40px;
  }
  .testimonial-author {
    flex-direction: column;
    text-align: center;
  }
  .testimonial-author img {
    margin: 0 auto 15px;
  }
  .social-links a {
    width: 40px;
    height: 40px;
    margin: 0 5px;
  }
  .back-to-top {
    width: 40px;
    height: 40px;
    bottom: 20px;
    left: 20px;
  }
}

/* Smooth Scrolling */
html {
  scroll-behavior: smooth;
}

/* Carousel Enhancements */
#hero-carousel {
  margin-top: 80px;
}
#hero-carousel .carousel-item {
  height: 70vh;
}
#hero-carousel .carousel-item img,
#hero-carousel .carousel-item video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
#hero-carousel .carousel-caption {
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(0.5px);
  padding: 20px;
  border-radius: var(--border-radius);
  max-width: 80%;
  margin: 0 auto;
}

/* Video Background */
.video-background {
  position: relative;
  width: 100%;
  height: 70vh;
  overflow: hidden;
}
.video-background video {
  position: absolute;
  top: 50%;
  left: 50%;
  min-width: 100%;
  min-height: 100%;
  width: auto;
  height: auto;
  transform: translate(-50%, -50%);
}
.video-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(30, 58, 95, 0.7), rgba(0, 0, 0, 0.6));
  display: flex;
  align-items: center;
  justify-content: center;
}
/* Enhanced Contact Section "تواصل معنا" Design */
#contact {
  background: #f4f4f4;
  padding: 100px 0;
  position: relative;
  overflow: hidden;
  text-align: center;
}

#contact::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(184,134,11,0.05), rgba(30,58,95,0.05));
  z-index: -1;
}

#contact .section-title {
  font-size: 2.5rem;
  margin-bottom: 40px;
  color: var(--secondary-color);
  text-transform: uppercase;
  letter-spacing: 2px;
  position: relative;
}

#contact .section-title::after {
  content: "";
  display: block;
  width: 60px;
  height: 3px;
  background: var(--primary-color);
  margin: 10px auto 0;
  border-radius: 3px;
}

.contact-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
  justify-items: center;
  padding: 0 15px;
}

.contact-card {
  background: #fff;
  padding: 30px;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  text-align: center;
  position: relative;
}

.contact-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.contact-card .contact-icon {
  font-size: 2.5rem;
  color: var(--primary-color);
  margin-bottom: 15px;
}

.contact-card h3 {
  font-size: 1.5rem;
  margin-bottom: 10px;
  color: var(--secondary-color);
}

.contact-card p {
  font-size: 1rem;
  color: var(--text-color);
  margin: 0;
}

/* Optional: Responsive adjustments for very small devices */
@media (max-width: 576px) {
  #contact {
    padding: 80px 0;
  }
  #contact .section-title {
    font-size: 2rem;
  }
  .contact-card {
    padding: 20px;
  }
  .contact-card .contact-icon {
    font-size: 2rem;
  }
  .contact-card h3 {
    font-size: 1.3rem;
  }
  .contact-card p {
    font-size: 0.9rem;
  }
}
/* Enhanced Gallery Section "معرض الصور" Design */
#gallery {
  background: #fff;
  padding: 100px 0;
  text-align: center;
  position: relative;
  overflow: hidden;
}

#gallery::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(184,134,11,0.05), rgba(30,58,95,0.05));
  z-index: -1;
}

#gallery .section-title {
  font-size: 2.5rem;
  margin-bottom: 40px;
  color: var(--secondary-color);
  text-transform: uppercase;
  letter-spacing: 2px;
  position: relative;
}

#gallery .section-title::after {
  content: "";
  display: block;
  width: 60px;
  height: 3px;
  background: var(--primary-color);
  margin: 10px auto 0;
  border-radius: 3px;
}

/* Gallery Grid */
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  padding: 0 15px;
}

/* Gallery Items */
.gallery-grid img {
  width: 100%;
  height: auto;
  border-radius: var(--border-radius);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  object-fit: cover;
  box-shadow: var(--box-shadow);
}

/* Hover Effects */
.gallery-grid img:hover {
  transform: scale(1.05);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

/* Responsive Adjustments */
@media (max-width: 576px) {
  #gallery {
    padding: 80px 0;
  }
  #gallery .section-title {
    font-size: 2rem;
  }
  .gallery-grid {
    gap: 15px;
  }
}

 </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="logo.png" alt="CYN Turizm Logo">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="#">الرئيسية</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#about">من نحن</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#services">خدماتنا</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#offices">مكاتبنا</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#destinations">وجهاتنا</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#contact">تواصل معنا</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Carousel with Video Background -->
  <section id="hero-carousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <video class="d-block w-100" autoplay loop muted playsinline>
          <source src="slide3.mp4" type="video/mp4">
          متصفحك لا يدعم تشغيل الفيديو.
        </video>
        <div class="carousel-caption">
          <h1>تجربة فاخرة</h1>
          <p>استمتع بأفضل خدمات السفر والإقامة</p>
          <a href="#services" class="btn btn-primary">
            استكشف خدماتنا <i class="fas fa-arrow-left ms-2"></i>
          </a>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">السابق</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">التالي</span>
    </button>
  </section>

  <!-- Cloud Effect Under Carousel -->
  <div class="cloud-effect"></div>

  <!-- About Section -->
  <section id="about" class="section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 order-lg-2">
          <figure class="about-img">
            <img src="slide452.jpg" alt="About CYN Turizm" class="img-fluid">
          </figure>
        </div>
        <div class="col-lg-6 order-lg-1">
          <h2 class="section-title">من نحن</h2>
          <p>
            تأسست شركة سي واي إن للسياحة على يد جنيت يديكارديش عام 2006، وتميزت كمزود رائد في تركيا، مع التركيز على خدمات حجز الفنادق وحلول النقل من المطارات.
          </p>
          <p>
            ونظرًا للطلب المتزايد من المسافرين الجزائريين، قمنا بافتتاح مكتب جديد في الجزائر العاصمة لتقديم خدماتنا بشكل مباشر وأكثر فعالية للعملاء.
          </p>
          <p>
            نفتخر بتقديم تجربة سفر متكاملة تشمل حجوزات الفنادق، تذاكر الطيران، خدمات النقل، والجولات السياحية مع مرشدين يتحدثون العربية، مما يضمن لعملائنا من الجزائر تجربة سفر مريحة وممتعة في تركيا.
          </p>
          <div class="founder-info d-flex align-items-center mt-4">
            <img src="/api/placeholder/100/100" alt="Cüneyt Yedikardeş" class="founder-img rounded-circle me-3">
            <div>
              <h4>جنيت يديكارديش</h4>
              <p class="mb-0">مؤسس سي واي إن للسياحة</p>
            </div>
          </div>
          <div class="certification-badge d-flex align-items-center mt-3">
            <i class="fas fa-certificate fa-2x me-2"></i>
            <div>
              <strong>شركة معتمدة من TURSAB</strong>
              <p class="mb-0">رقم الشهادة: 11738</p>
            </div>
          </div>
          <a href="#contact" class="btn btn-primary mt-4">
            تواصل معنا <i class="fas fa-arrow-left ms-2"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section id="services" class="section services-section">
    <div class="container">
      <header class="text-center mb-5">
        <h2 class="section-title">خدماتنا</h2>
        <p>نقدم مجموعة متكاملة من الخدمات السياحية المصممة خصيصًا للمسافرين الجزائريين</p>
      </header>
      <div class="row g-4">
        <!-- Service Card Example -->
        <div class="col-lg-4 col-md-6">
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-plane-departure"></i>
            </div>
            <h3 class="service-title">حجز تذاكر الطيران</h3>
            <p class="service-desc">
              نوفر أفضل أسعار تذاكر الطيران من الجزائر إلى تركيا وبالعكس مع خيارات متعددة تناسب جميع الميزانيات.
            </p>
          </div>
        </div>
        <!-- Additional service cards follow the same structure -->
        <div class="col-lg-4 col-md-6">
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-hotel"></i>
            </div>
            <h3 class="service-title">حجوزات فندقية</h3>
            <p class="service-desc">
              نوفر حجوزات في أفضل الفنادق والمنتجعات في جميع المدن التركية بأسعار تنافسية وخدمات متميزة.
            </p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-plane-arrival"></i>
            </div>
            <h3 class="service-title">خدمة النقل من المطار</h3>
            <p class="service-desc">
              نقدم خدمات نقل احترافية من جميع المطارات التركية إلى الفنادق والوجهات المختلفة بسيارات حديثة وسائقين محترفين.
            </p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-route"></i>
            </div>
            <h3 class="service-title">جولات سياحية</h3>
            <p class="service-desc">
              نقدم جولات سياحية مميزة بصحبة مرشدين سياحيين يتحدثون العربية لاستكشاف أجمل المعالم التاريخية والطبيعية في تركيا.
            </p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-passport"></i>
            </div>
            <h3 class="service-title">تأشيرات وإقامات</h3>
            <p class="service-desc">
              نوفر المساعدة في إجراءات الحصول على التأشيرات وتجديد الإقامات في تركيا بسهولة وبدون تعقيدات.
            </p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="service-card">
            <div class="service-icon">
              <i class="fas fa-clipboard-check"></i>
            </div>
            <h3 class="service-title">برامج سياحية متكاملة</h3>
            <p class="service-desc">
              نقدم باقات وبرامج سياحية متكاملة تشمل الإقامة والنقل والجولات السياحية بأسعار تنافسية ومناسبة للعائلات.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Offices Section -->
  <section id="offices" class="section offices-section">
    <div class="container">
      <header class="text-center mb-5">
        <h2 class="section-title">مكاتبنا</h2>
        <p>نخدمكم الآن من خلال مكتبين في إسطنبول والجزائر العاصمة</p>
      </header>
      <div class="row g-4">
        <!-- Istanbul Office -->
        <div class="col-lg-6">
          <div class="office-card">
            <img src="/api/placeholder/600/300" alt="Istanbul Office" class="office-img">
            <div class="office-content">
              <h3 class="office-title">
                مكتب إسطنبول <span class="badge bg-secondary">المقر الرئيسي</span>
              </h3>
              <p class="office-info"><i class="fas fa-map-marker-alt me-2"></i>شارع الاستقلال، تقسيم، إسطنبول، تركيا</p>
              <p class="office-info"><i class="fas fa-phone-alt me-2"></i>+90 555 123 4567</p>
              <p class="office-info"><i class="fas fa-envelope me-2"></i>istanbul@cynturizm.com</p>
              <p class="office-info"><i class="fas fa-clock me-2"></i>السبت - الخميس: 9:00 ص - 7:00 م</p>
            </div>
          </div>
        </div>
        <!-- Algiers Office -->
        <div class="col-lg-6">
          <div class="office-card">
            <img src="/api/placeholder/600/300" alt="Algiers Office" class="office-img">
            <div class="office-content">
              <h3 class="office-title">
                مكتب الجزائر العاصمة <span class="highlight-badge">جديد</span>
              </h3>
              <p class="office-info"><i class="fas fa-map-marker-alt me-2"></i>شارع ديدوش مراد، الجزائر العاصمة، الجزائر</p>
              <p class="office-info"><i class="fas fa-phone-alt me-2"></i>+213 21 XXX XXX</p>
              <p class="office-info"><i class="fas fa-envelope me-2"></i>algiers@cynturizm.com</p>
              <p class="office-info"><i class="fas fa-clock me-2"></i>الأحد - الخميس: 9:00 ص - 5:00 م</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Destinations Section -->
  <section id="destinations" class="section">
    <div class="container">
      <header class="text-center mb-5">
        <h2 class="section-title">وجهاتنا</h2>
        <p>استكشف أجمل المدن والوجهات السياحية في تركيا مع برامجنا المخصصة للمسافرين الجزائريين</p>
      </header>
      <div class="row g-4">
        <!-- Destination Card Template -->
        <div class="col-lg-4 col-md-6">
          <div class="destination-card">
            <img src="/api/placeholder/400/300" alt="إسطنبول" class="destination-img">
            <div class="destination-overlay">
              <h3 class="destination-title">إسطنبول</h3>
              <p class="destination-subtitle">مدينة الحضارات وملتقى الشرق والغرب</p>
            </div>
          </div>
        </div>
        <!-- Repeat for additional destinations -->
        <div class="col-lg-4 col-md-6">
          <div class="destination-card">
            <img src="/api/placeholder/400/300" alt="كابادوكيا" class="destination-img">
            <div class="destination-overlay">
              <h3 class="destination-title">كابادوكيا</h3>
              <p class="destination-subtitle">أرض المناطيد والتشكيلات الصخرية الساحرة</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="destination-card">
            <img src="/api/placeholder/400/300" alt="أنطاليا" class="destination-img">
            <div class="destination-overlay">
              <h3 class="destination-title">أنطاليا</h3>
              <p class="destination-subtitle">عاصمة السياحة التركية وجنة الشواطئ</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="destination-card">
            <img src="/api/placeholder/400/300" alt="بورصة" class="destination-img">
            <div class="destination-overlay">
              <h3 class="destination-title">بورصة</h3>
              <p class="destination-subtitle">المدينة الخضراء وأولى العواصم العثمانية</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="destination-card">
            <img src="/api/placeholder/400/300" alt="طرابزون" class="destination-img">
            <div class="destination-overlay">
              <h3 class="destination-title">طرابزون</h3>
              <p class="destination-subtitle">جوهرة البحر الأسود والطبيعة الخلابة</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="destination-card">
            <img src="/api/placeholder/400/300" alt="بودروم" class="destination-img">
            <div class="destination-overlay">
              <h3 class="destination-title">بودروم</h3>
              <p class="destination-subtitle">مدينة الشواطئ الساحرة والحياة الليلية</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Gallery Section -->
  <section id="gallery" class="section gallery-section">
    <div class="container">
      <header class="text-center mb-5">
        <h2 class="section-title">معرض الصور</h2>
        <p>تعرف على بعض اللحظات والوجهات السياحية</p>
      </header>
      <div class="row g-4">
        <div class="col-lg-4 col-md-6">
          <img src="/api/placeholder/400/300?text=Gallery+1" class="img-fluid" alt="Gallery 1">
        </div>
        <div class="col-lg-4 col-md-6">
          <img src="/api/placeholder/400/300?text=Gallery+2" class="img-fluid" alt="Gallery 2">
        </div>
        <div class="col-lg-4 col-md-6">
          <img src="/api/placeholder/400/300?text=Gallery+3" class="img-fluid" alt="Gallery 3">
        </div>
        <div class="col-lg-4 col-md-6">
          <img src="/api/placeholder/400/300?text=Gallery+4" class="img-fluid" alt="Gallery 4">
        </div>
        <div class="col-lg-4 col-md-6">
          <img src="/api/placeholder/400/300?text=Gallery+5" class="img-fluid" alt="Gallery 5">
        </div>
        <div class="col-lg-4 col-md-6">
          <img src="/api/placeholder/400/300?text=Gallery+6" class="img-fluid" alt="Gallery 6">
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="section">
    <div class="container">
      <header class="text-center mb-5">
        <h2 class="section-title">تواصل معنا</h2>
        <p>نحن هنا للإجابة على جميع استفساراتكم وتلبية احتياجاتكم السياحية</p>
      </header>
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="row g-4">
            <div class="col-md-6">
              <div class="contact-card text-center">
                <div class="contact-icon mb-3">
                  <i class="fas fa-map-marker-alt fa-2x"></i>
                </div>
                <h3 class="contact-title">عنواننا</h3>
                <p>شارع ديدوش مراد، الجزائر العاصمة، الجزائر</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="contact-card text-center">
                <div class="contact-icon mb-3">
                  <i class="fas fa-phone-alt fa-2x"></i>
                </div>
                <h3 class="contact-title">اتصل بنا</h3>
                <p>+213 21 XXX XXX</p>
                <p>+90 555 123 4567</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="contact-card text-center">
                <div class="contact-icon mb-3">
                  <i class="fas fa-envelope fa-2x"></i>
                </div>
                <h3 class="contact-title">راسلنا</h3>
                <p>info@cynturizm.com</p>
                <p>algiers@cynturizm.com</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="contact-card text-center">
                <div class="contact-icon mb-3">
                  <i class="fas fa-clock fa-2x"></i>
                </div>
                <h3 class="contact-title">ساعات العمل</h3>
                <p>الأحد - الخميس: 9:00 ص - 5:00 م</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content text-center">
        <div class="social-links mb-3">
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
        <div class="copyright">
          <p>&copy; 2025 سي واي إن للسياحة - جميع الحقوق محفوظة</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Back to Top Button -->
  <div class="back-to-top">
    <i class="fas fa-arrow-up"></i>
  </div>

  <!-- JavaScript Libraries -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script>
    // Navbar scroll effect and back-to-top button visibility
    window.addEventListener('scroll', () => {
      const navbar = document.querySelector('.navbar');
      const backToTop = document.querySelector('.back-to-top');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
      backToTop.classList.toggle('visible', window.scrollY > 300);
    });
    
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          window.scrollTo({
            top: target.offsetTop - 80,
            behavior: 'smooth'
          });
        }
      });
    });
    
    // Back to top button click event
    document.querySelector('.back-to-top').addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  </script>
</body>
</html>
