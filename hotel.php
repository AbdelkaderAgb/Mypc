<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Hotel Pricing Calendar</title>
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

/* Navbar Styles */
.navbar {
    position: static;
    background-color: var(--white) !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    padding: 1rem 1.5rem;
    transition: var(--transition);
}

@media (min-width: 992px) {
    .navbar {
        position: fixed !important;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1030;
    }
    
    body {
        padding-top: 80px;
    }
    
    .navbar.scrolled {
        padding: 0.75rem 1.5rem;
    }
}

.navbar-brand img {
    height: 100px;
    width: auto;
    transition: var(--transition);
}

@media (max-width: 767px) {
    .navbar-brand img {
        height: 90px;
    }
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

/* Page Header Styles */
.page-header {
    background: linear-gradient(to right, rgba(0,0,0,0.7), rgba(0,0,0,0.3)), url('img/hotel-bg.jpg');
    background-size: cover;
    background-position: center;
    color: var(--white);
    padding: 15vh 0 10vh;
    margin-bottom: 50px;
    position: relative;
    min-height: 40vh;
    display: flex;
    align-items: center;
}

.header-content {
    position: relative;
    z-index: 2;
    padding: 2rem;
}

.header-title {
    font-size: clamp(2rem, 6vw, 3.5rem);
    margin-bottom: 1.5rem;
    font-weight: 700;
    letter-spacing: 1px;
    line-height: 1.2;
}

.header-subtitle {
    font-size: clamp(1rem, 1.8vw, 1.2rem);
    opacity: 0.9;
    max-width: 90%;
    margin: 0 auto;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .page-header {
        padding: 25vh 0 15vh;
        min-height: 60vh;
        background-attachment: scroll;
    }
    
    .header-title {
        margin-bottom: 1rem;
    }
    
    .header-subtitle {
        max-width: 100%;
        font-size: 1.1rem;
    }
}

@media (max-width: 480px) {
    .page-header {
        padding: 20vh 0 10vh;
        min-height: 50vh;
    }
    
    .header-title {
        font-size: 2.2rem;
        line-height: 1.1;
    }
    
    .header-subtitle {
        font-size: 1rem;
        line-height: 1.5;
    }
}

.calendar-container {
    background-color: var(--white);
    border-radius: 8px;
    box-shadow: var(--shadow);
    padding: 2.5rem;
    margin-bottom: 3rem;
    transition: var(--transition);
    border: 1px solid var(--border-color);
    position: relative;
}

.calendar-container::before {
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

.calendar-container:hover {
    box-shadow: var(--hover-shadow);
}

.calendar-container:hover::before {
    opacity: 1;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 1.5rem;
}

.calendar-title {
    color: var(--primary-color);
    margin-bottom: 0.5rem;
    font-size: 2.5rem;
    letter-spacing: 0.5px;
    position: relative;
    display: inline-block;
}

.calendar-title::after {
    content: '';
    position: absolute;
    width: 50px;
    height: 3px;
    background-color: var(--accent-color);
    bottom: -8px;
    left: 0;
}

.currency-description {
    color: var(--light-text);
    font-size: 1rem;
    margin-top: 1rem;
    font-style: italic;
}

.btn-navigation {
    background-color: var(--white);
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
    padding: 0.75rem 1.75rem;
    border-radius: 50px;
    font-weight: 500;
    transition: var(--transition);
    margin: 0 5px;
    letter-spacing: 0.5px;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.btn-navigation::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0%;
    height: 100%;
    background-color: var(--primary-color);
    transition: var(--transition);
    z-index: -1;
}

.btn-navigation:hover {
    color: var(--white);
    border-color: var(--primary-color);
    transform: translateY(-3px);
}

.btn-navigation:hover::before {
    width: 100%;
}

.btn-navigation:active {
    transform: translateY(0);
}

.btn-navigation i {
    transition: var(--transition);
}

.btn-navigation:hover i.fa-arrow-left {
    transform: translateX(-5px);
}

.btn-navigation:hover i.fa-arrow-right {
    transform: translateX(5px);
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 15px rgba(0,0,0,0.03);
}

.pricing-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.pricing-table th {
    background: linear-gradient(135deg, var(--primary-color), #9f8656);
    color: var(--white);
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 1px;
    position: sticky;
    top: 0;
    z-index: 10;
}

.pricing-table td {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    transition: var(--transition);
    vertical-align: middle;
}

.pricing-table tbody tr {
    transition: var(--transition);
}

.pricing-table tbody tr:hover {
    background-color: rgba(138, 109, 59, 0.05);
    transform: translateY(-2px);
}

.pricing-table tbody tr:hover td {
    border-bottom-color: rgba(138, 109, 59, 0.2);
}

.pricing-table tbody tr:last-child td {
    border-bottom: none;
}

.room-type {
    font-weight: 600;
    color: var(--text-color);
    font-size: 1.05rem;
}

.accommodation {
    color: var(--light-text);
    font-size: 0.95rem;
    display: flex;
    align-items: center;
}

.date-range {
    color: var(--text-color);
    font-size: 0.95rem;
    font-weight: 500;
}

.price {
    font-weight: 700;
    color: var(--primary-color);
    font-size: 1.15rem;
}

.accommodation-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 12px;
    display: inline-block;
}

.accommodation-1 .accommodation-indicator {
    background-color: #AF9458;
}

.accommodation-2 .accommodation-indicator {
    background-color: #4285f4;
}

.accommodation-3 .accommodation-indicator {
    background-color: #34a853;
}

#loading {
    display: none;
    padding: 3rem;
    text-align: center;
    color: var(--primary-color);
}

.loading-spinner {
    width: 50px;
    height: 50px;
    margin: 0 auto 1.5rem;
    border: 3px solid rgba(138, 109, 59, 0.2);
    border-top-color: var(--primary-color);
    border-radius: 50%;
    animation: spinner 1s linear infinite;
}

@keyframes spinner {
    to {transform: rotate(360deg);}
}

.footer {
    background-color: #262626;
    color: var(--white);
    padding: 4rem 0 2rem;
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

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 2rem;
}

.footer-logo img {
    height: 100px;
    opacity: 0.9;
    transition: var(--transition);
}

.footer-logo img:hover {
    opacity: 1;
    transform: scale(1.05);
}

.footer-nav {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-nav li {
    margin: 0 15px;
}

.footer-nav a {
    color: var(--white);
    text-decoration: none;
    transition: var(--transition);
    font-weight: 500;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    position: relative;
    padding-bottom: 5px;
}

.footer-nav a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    background-color: var(--accent-color);
    bottom: 0;
    left: 0;
    transition: var(--transition);
}

.footer-nav a:hover {
    color: var(--accent-color);
}

.footer-nav a:hover::after {
    width: 100%;
}

.copyright {
    margin-top: 2rem;
    opacity: 0.7;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
    text-align: center;
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 2rem;
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

/* Responsive Styles */
@media (max-width: 991px) {
    .calendar-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .navigation-buttons {
        margin-top: 1.5rem;
        align-self: flex-end;
    }
    
    .footer-content {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .footer-nav {
        margin-top: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .footer-nav li {
        margin: 0.5rem 1rem;
    }

    .header-title {
        font-size: 2.5rem;
    }
}

@media (max-width: 767px) {
    .calendar-container {
        padding: 1.5rem;
    }
    
    .calendar-title {
        font-size: 2rem;
    }
    
    .pricing-table th, 
    .pricing-table td {
        padding: 1rem;
        font-size: 0.9rem;
    }
    
    .navbar-brand img {
        height: 100px;
    }

    .btn-navigation {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }

    .page-header {
        padding: 80px 0 40px;
    }

    .header-title {
        font-size: 2rem;
    }

    .header-subtitle {
        font-size: 1rem;
    }

    .back-to-top {
        width: 40px;
        height: 40px;
        bottom: 20px;
        right: 20px;
    }
    
    /* Hide the original table headers on mobile */
    .pricing-table thead {
        display: none;
    }
    
    /* Change table layout for mobile */
    .pricing-table, 
    .pricing-table tbody, 
    .pricing-table tr {
        display: block;
        width: 100%;
    }
    
    /* Style each row as a card */
    .pricing-table tr {
        margin-bottom: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        background-color: var(--white);
        overflow: hidden;
    }
    
    /* Remove the spacer styling for mobile */
    .pricing-table tr.spacer-row {
        height: 20px !important;
        box-shadow: none;
        background-color: transparent;
        margin-bottom: 0;
    }
    
    /* Style each cell as a flex container */
    .pricing-table td {
        display: flex;
        justify-content: space-between;
        text-align: right;
        padding: 0.8rem 1rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    /* Add headers to each cell using pseudo-elements */
    .pricing-table td:nth-of-type(1):before { content: "Room Type"; font-weight: 600; text-align: left; }
    .pricing-table td:nth-of-type(2):before { content: "Accommodation"; font-weight: 600; text-align: left; }
    .pricing-table td:nth-of-type(3):before { content: "Date Range"; font-weight: 600; text-align: left; }
    .pricing-table td:nth-of-type(4):before { content: "Price"; font-weight: 600; text-align: left; }
    
    /* Improve spacing for room type */
    .pricing-table td.room-type {
        background-color: rgba(138, 109, 59, 0.1);
        color: var(--primary-color);
        font-size: 1rem;
    }
    
    /* Reset some of the desktop styling */
    .accommodation-indicator {
        margin-right: 8px;
    }
    
    /* Add a subtle border to the cards */
    .pricing-table tr:not(.spacer-row) {
        border: 1px solid var(--border-color);
    }
    
    /* Adjust the price styling */
    .price {
        font-weight: 700;
        color: var(--primary-color);
    }
    
    /* Accommodate the new layout in the container */
    .calendar-container {
        padding: 1rem;
    }
}

@media (max-width: 575px) {
    .calendar-container {
        padding: 1.25rem;
    }

    .calendar-title {
        font-size: 1.75rem;
    }

    .pricing-table th, 
    .pricing-table td {
        padding: 0.75rem;
        font-size: 0.85rem;
    }

    .room-type {
        font-size: 0.95rem;
    }

    .price {
        font-size: 1rem;
    }

    .navigation-buttons {
        display: flex;
        width: 100%;
        justify-content: space-between;
    }

    .btn-navigation {
        flex: 1;
        text-align: center;
        padding: 0.6rem 0.8rem;
    }
}

/* For very small screens, make the buttons smaller */
@media (max-width: 375px) {
    .btn-navigation {
        padding: 0.5rem 0.6rem;
        font-size: 0.85rem;
    }
    
    .navigation-buttons {
        gap: 0.5rem;
    }
}

/* Spacer for accommodation groups - mobile adjustment */
@media (max-width: 767px) {
    .spacer-row {
        height: 5px !important;
    }
}

    </style>
</head>
<body>
    <!-- Navbar -->
   <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="img/logo.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Home</a>
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header text-center">
        <div class="container">
            <div class="header-content">
                <h1 class="header-title">Pricing Calendar</h1>
                <p class="header-subtitle">Explore our rates and availability for the perfect stay</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="calendar-container">
            <div class="calendar-header">
                <div class="title-area">
                    <h2 id="hotel-name" class="calendar-title">Luxury Resort</h2>
                    <p id="currency-description" class="currency-description">Prices are in USD</p>
                </div>
                <div class="navigation-buttons">
                    <button id="prevBtn" class="btn btn-navigation">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button id="nextBtn" class="btn btn-navigation">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <div id="loading">
                <div class="loading-spinner"></div>
                <p>Loading pricing data...</p>
            </div>
            
            <div class="table-responsive">
                <table class="pricing-table">
                    <thead>
                        <tr>
                            <th>Room Type</th>
                            <th>Accommodation</th>
                            <th>Date Range</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody id="calendar-body">
                        <!-- Data will be populated here by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <div class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="img/logo.png" alt="Logo">
                </div>
                <ul class="footer-nav">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="tours.php">Tours</a></li>
                    <li><a href="transfer.php">Transfer</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div class="copyright">
                © 2006-2024 CYN TURIZM All Rights Reserved
            </div>
        </div>
    </footer>

    <script>
    var pricingData = {};
    var dateRanges = [];
    var currentIndex = 0;
    var hotelName = '';
    var currency = '';

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            document.querySelector('.navbar').classList.add('scrolled');
        } else {
            document.querySelector('.navbar').classList.remove('scrolled');
        }
    });

    // Back to top button
    window.addEventListener('scroll', function() {
        var backToTopBtn = document.getElementById('backToTop');
        if (window.scrollY > 300) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });

    document.getElementById('backToTop').addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    function getAccommodationClass(accommodation) {
        switch (accommodation) {
            case 'ANEX BUILDING LAND VIEW':
                return 'accommodation-1';
            case 'STANDARD ROOM':
                return 'accommodation-2';
            default:
                return 'accommodation-3';
        }
    }

    function formatPrice(price) {
        return parseFloat(price).toFixed(1);
    }

    function formatDateString(dateStr) {
        dateStr = dateStr.trim();
        if (/^\d{4}-\d{1,2}-\d{1,2}$/.test(dateStr)) {
            var parts = dateStr.split('-');
            if (parts[1].length === 1) parts[1] = '0' + parts[1];
            if (parts[2].length === 1) parts[2] = '0' + parts[2];
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        // Add more date format handling as needed
        return dateStr;
    }

    function updateTable(dateRange) {
        var data = pricingData[dateRange];
        var tbody = document.getElementById('calendar-body');
        tbody.innerHTML = '';

        var groupedData = {};
        data.forEach(function(entry) {
            if (!groupedData[entry.accommodation]) {
                groupedData[entry.accommodation] = [];
            }
            groupedData[entry.accommodation].push(entry);
        });

        function roomTypeComparator(a, b) {
            var isLetterA = /^[A-Za-z]/.test(a.room_type);
            var isLetterB = /^[A-Za-z]/.test(b.room_type);
            if (isLetterA && !isLetterB) return -1;
            if (!isLetterA && isLetterB) return 1;
            return a.room_type.localeCompare(b.room_type);
        }

        var sortedKeys = Object.keys(groupedData).sort();

        sortedKeys.forEach(function(accommodation) {
            let isFirstInGroup = true;
            
            groupedData[accommodation].sort(roomTypeComparator).forEach(function(entry) {
                var row = document.createElement('tr');
                row.classList.add(getAccommodationClass(entry.accommodation));
                
                var roomTypeCell = document.createElement('td');
                var accommodationCell = document.createElement('td');
                var dateRangeCell = document.createElement('td');
                var priceCell = document.createElement('td');
                
                roomTypeCell.classList.add('room-type');
                accommodationCell.classList.add('accommodation');
                dateRangeCell.classList.add('date-range');
                priceCell.classList.add('price');
                
                roomTypeCell.innerText = entry.room_type;
                
                if (isFirstInGroup) {
                    var indicator = document.createElement('span');
                    indicator.classList.add('accommodation-indicator');
                    accommodationCell.appendChild(indicator);
                    accommodationCell.appendChild(document.createTextNode(' ' + entry.accommodation));
                    isFirstInGroup = false;
                } else {
                    accommodationCell.innerHTML = '<span class="accommodation-indicator" style="visibility: hidden;"></span> ' + entry.accommodation;
                }

                var dateParts = dateRange.split(' - ');
                var formattedStartDate = formatDateString(dateParts[0]);
                var formattedEndDate = formatDateString(dateParts[1]);
                dateRangeCell.innerText = formattedStartDate + ' - ' + formattedEndDate;

                priceCell.innerText = formatPrice(entry.price) + ' ' + currency;
                
                row.appendChild(roomTypeCell);
                row.appendChild(accommodationCell);
                row.appendChild(dateRangeCell);
                row.appendChild(priceCell);
                tbody.appendChild(row);
            });
            
            if (sortedKeys.indexOf(accommodation) < sortedKeys.length - 1) {
                var spacerRow = document.createElement('tr');
                spacerRow.classList.add('spacer-row');
                spacerRow.style.height = '10px';
                spacerRow.style.backgroundColor = 'var(--secondary-color)';
                spacerRow.style.border = 'none';
                
                for (let i = 0; i < 4; i++) {
                    let spacerCell = document.createElement('td');
                    spacerCell.style.padding = '5px';
                    spacerCell.style.border = 'none';
                    spacerRow.appendChild(spacerCell);
                }
                
                tbody.appendChild(spacerRow);
            }
        });
        
        updateTableForMobile();
    }

    function updateTableForMobile() {
        const isMobile = window.innerWidth <= 767;
        if (isMobile) {
            const rows = document.querySelectorAll('#calendar-body tr:not(.spacer-row)');
            rows.forEach(row => {
                row.classList.add('mobile-row');
                const roomTypeCell = row.querySelector('.room-type');
                if (roomTypeCell) {
                    roomTypeCell.style.fontWeight = '700';
                }
            });
        }
    }

    function fetchPricingData() {
        var loading = document.getElementById('loading');
        loading.style.display = 'block';
        var xhr = new XMLHttpRequest();
        var hotel = new URLSearchParams(window.location.search).get('name');
        xhr.open('GET', 'fetch_pricing_data.php?hotel=' + encodeURIComponent(hotel), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                loading.style.display = 'none';
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        pricingData = response.data;
                        hotelName = response.hotel_name;
                        currency = response.currency;
                        
                        dateRanges = Object.keys(pricingData).sort(function(a, b) {
                            var partsA = a.split(' - ')[0].trim();
                            var partsB = b.split(' - ')[0].trim();
                            var dateA = new Date(partsA);
                            var dateB = new Date(partsB);
                            return dateA - dateB;
                        });
                        
                        document.getElementById('hotel-name').innerText = hotelName;
                        document.getElementById('currency-description').innerText = 'Prices are in ' + currency;
                        
                        currentIndex = 0;
                        
                        if (dateRanges.length > 0) {
                            console.log('Available date ranges:', dateRanges);
                            updateTable(dateRanges[currentIndex]);
                            updateNavigationButtons();
                        } else {
                            var tbody = document.getElementById('calendar-body');
                            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:2rem;">No pricing data available for this period.</td></tr>';
                            updateNavigationButtons();
                        }
                        
                    } catch (e) {
                        console.error('Error parsing JSON response:', e);
                        alert('Failed to load pricing data. Please try again later.');
                    }
                } else {
                    console.error('Request failed with status:', xhr.status);
                    alert('Failed to load pricing data. Please try again later.');
                }
            }
        };
        xhr.send();
    }

    document.getElementById('nextBtn').addEventListener('click', function() {
        console.log('Next clicked - Current Index:', currentIndex, 'Total Ranges:', dateRanges.length);
        if (currentIndex < dateRanges.length - 1) {
            currentIndex++;
            console.log('Moving to index:', currentIndex, 'Date Range:', dateRanges[currentIndex]);
            updateTable(dateRanges[currentIndex]);
            updateNavigationButtons();
        }
    });

    document.getElementById('prevBtn').addEventListener('click', function() {
        console.log('Previous clicked - Current Index:', currentIndex);
        if (currentIndex > 0) {
            currentIndex--;
            console.log('Moving to index:', currentIndex, 'Date Range:', dateRanges[currentIndex]);
            updateTable(dateRanges[currentIndex]);
            updateNavigationButtons();
        }
    });

    function animateOnScroll() {
        const elements = document.querySelectorAll('.calendar-container');
        elements.forEach(element => {
            const position = element.getBoundingClientRect();
            if(position.top < window.innerHeight && position.bottom >= 0) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    }

    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        
        console.log('Updating buttons - Current Index:', currentIndex, 'Total Ranges:', dateRanges.length);
        
        if (currentIndex === 0 || dateRanges.length === 0) {
            prevBtn.disabled = true;
            prevBtn.classList.add('disabled');
        } else {
            prevBtn.disabled = false;
            prevBtn.classList.remove('disabled');
        }
        
        if (currentIndex === dateRanges.length - 1 || dateRanges.length === 0) {
            nextBtn.disabled = true;
            nextBtn.classList.add('disabled');
        } else {
            nextBtn.disabled = false;
            nextBtn.classList.remove('disabled');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const animatedElements = document.querySelectorAll('.calendar-container');
        animatedElements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        });
        
        setTimeout(animateOnScroll, 300);
        fetchPricingData();
    });

    window.addEventListener('scroll', animateOnScroll);
    window.addEventListener('resize', function() {
        if (dateRanges.length > 0) {
            updateTableForMobile();
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>