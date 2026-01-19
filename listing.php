<?php
include 'auth.php'; // Include auth.php to restrict access
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotels List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Acme&family=Playfair+Display:ital,wght@0,675;1,675&display=swap" rel="stylesheet">
    <style>
    h2 {
            font-family: 'Acme', sans-serif;
            font-size: 40px;
            color: #b8860b;
            text-align: center;
            margin-bottom: 20px;
        }
    .navbar-nav .nav-link {
        font-weight: bold; /* This makes the font bold for all nav links */
    }

    @media (max-width: 991px) {
        .navbar-nav {
            text-align: center;
        }
        .navbar-nav .nav-item {
            display: inline-block;
        }
        .navbar-nav .nav-link {
            display: inline-block; /* Ensures the links are aligned in line */
        }
    }
    .navbar {
        background-color: #fff !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .navbar-brand img {
        height: 120px;
        width: auto;
    }

    .card-img-top {
        width: 100%;
        height: 50vw;
        object-fit: cover;
    }

    .footer {
        padding: 20px 0;
        font-size: 15px;
        font-weight: bold;
        background-color: #CA8C05;
        color: #000000;
        text-align: center;
        margin-top: 40px; /* Adds some space between the content and the footer */
        border-top: 1px solid #A06000;
    }
    .navbar-light .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='%23b8860b' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }

        /* Table Styles */
        .table {
        background-color: #f8f9fa; /* Light background for the table */
        border: 1px solid #dee2e6; /* Adds a border around the table */
        border-radius: 0.25rem; /* Rounds the corners of the table */
    }
    .table th {
        background-image: linear-gradient(to right, #00aeef, #007dc5);
    color: white; /* Ensures text in headers is white for better readability */
    border: 1px solid #FFD700; /* Optional: adds a golden border */
    }
    .table th, .table td {
        padding: 0.75rem; /* Padding inside each cell */
        border: 1px solid #dee2e6; /* Border for cells */
    }
    .table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    /* Center the total price container */
.total-price-container {
    text-align: center; /* Center-aligns the content */
    margin-top: 20px; /* Adds some space between the table and the total price */
    font-size: 1.25rem; /* Adjusts the font size */
    font-weight: bold; /* Makes the text bold */
}

#totalPrice {
    padding: 10px; /* Adds some padding around the price */
    background-color: #E09900; /* Sets a background color */
    color: white; /* Sets the text color */
    border-radius: 5px; /* Rounds the corners */
}
    /* Container Styling */
    .container {
        padding: 20px;
        background-color: #fff; /* Light background for contrast */
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Soft shadow for depth */
        border-radius: 8px; /* Rounded corners */
        margin-bottom: 30px; /* Space below the container */
    }

    /* Sidebar Card Styling */
    .sidebar-card {
        padding: 20px;
        text-align: center; /* Centers the content */
        background-color: #f8f9fa; /* Slightly different background for the card */
        border-radius: 8px; /* Consistent rounded corners */
        box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Subtle shadow for the card */
    }

    /* Styling for the certification text */
    .sidebar-card p {
        color: #333; /* Darker text for readability */
        margin-bottom: 15px; /* Spacing below the paragraph */
    }

    /* Button Styling */
    .btn-success {
        background-image: linear-gradient(to right, #FFD700, #FDBA21, #FFD700); /* Golden gradient for the button */
        border: none; /* Removes the border */
    }

    /* Icons and contact info styling */
    .sidebar-card .text-center p {
        margin: 5px 0; /* Adds spacing between contact lines */
        color: #555; /* Slightly lighter text for a softer look */
    }

    .sidebar-card .text-center p i {
        color: #FFD700; /* Golden color for icons */
        margin-right: 8px; /* Spacing between icon and text */
    }

    .sidebar-card .text-center a {
        color: #007bff; /* Blue color for links for contrast and visibility */
        text-decoration: underline; /* Underlines the link for clarity */
    }
/* Modal Custom Styling */
.modal-header {
    background-color: #007bff; /* Primary color for header */
    color: #fff; /* White text for contrast */
}

.modal-title {
    font-family: 'Playfair Display', serif; /* Elegant font for the title */
    font-weight: bold;
}

.close {
    color: #fff; /* White color for close button */
    opacity: 1; /* Make sure it's fully visible */
}

.modal-body {
    font-family: 'Nunito', sans-serif; /* Soft, readable font for the body */
    background-color: #f8f9fa; /* Light background for the modal body */
    color: #333; /* Darker text for readability */
}

.modal-content {
    border-radius: 15px; /* Rounded corners for the modal */
}

/* Style adjustments for the modal close button */
.close:focus,
.close:hover {
    opacity: 0.75; /* Slight transparency on hover/focus for a smoother effect */
}

/* Enhancing the button within the modal */
.btn-success {
    background-image: linear-gradient(to right, #28a745, #1e7e34); /* Gradient for the button */
    border: none; /* Remove default border */
    border-radius: 5px; /* Rounded corners for the button */
}

.btn-success:hover {
    background-image: linear-gradient(to right, #1e7e34, #28a745); /* Slightly different gradient on hover */
}

    </style>
</head>
<body>

<!-- Responsive Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">
    <img src="img/logo.png" alt="Logo" heighht="20">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="listing.php">Hotels list<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="import.php">Import hotel</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="importPrices.php">Import prices</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users.php">Users</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <h2>Hotel Listings</h2>
    <div class="row mt-3">
        <div class="col">
            <label for="priceIncrease">Increase Prices By (%)</label>
            <input type="number" id="priceIncrease" class="form-control" value="0" placeholder="Positive percentage">
        </div>
        <div class="col">
            <label for="priceDecrease">Decrease Prices By (%)</label>
            <input type="number" id="priceDecrease" class="form-control" value="0" placeholder="Negative percentage">
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <button onclick="adjustPrices('increase')" class="btn btn-success mt-2">Increase Prices</button>
        </div>
        <div class="col">
            <button onclick="adjustPrices('decrease')" class="btn btn-danger mt-2">Decrease Prices</button>
        </div>
    </div>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Select</th>
                <th>Hotel Name</th>
                <th>City</th>
            </tr>
        </thead>
        <tbody id="hotelTableBody">
            <!-- Hotel data will be inserted here -->
        </tbody>
    </table>
    <div class="row mt-3">
        <div class="col">
            <input type="text" id="searchHotelInput" class="form-control" placeholder="Search by hotel name">
        </div>
    </div>
    <div class="row mt-3">
        <div class="col">
            <button id="prevPageBtn" class="btn btn-primary" disabled>Previous</button>
            <button id="nextPageBtn" class="btn btn-primary">Next</button>
        </div>
    </div>
</div>

<!-- Modal for Displaying Hotel Details -->
<div class="modal fade" id="hotelDetailsModal" tabindex="-1" role="dialog" aria-labelledby="hotelDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hotelDetailsModalLabel">Hotel Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="hotelDetailsContent">
                <!-- Hotel details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<div class="container mt-5 mb-5">
    <div class="row">
        <!-- Sidebar Certification Block on the Left -->
        <div class="col-md-6">
            <div class="sidebar-card d-flex flex-column align-items-center">
                <img class="sidebar-card-illustration mb-2" src="img/tursab-seeklogo-removebg.png" alt="Icon" style="width: 230px; height: 150px;">
                <p class="text-center mb-2"><strong>CYN TURIZM </strong> company is certified by Tursab under address BELGE NO: 11738</p>
                <a class="btn btn-success btn-sm" href="https://www.tursab.org.tr/acenta-arama">Check here!</a>
            </div>
        </div>

        <!-- Contact Information on the Right -->
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center">
            <div class="text-center">
                <p><i class=""></i></p>
                <p><i class="fas fa-phone-alt"></i> +90531 817 67 70</p>
                <p><i class="fas fa-envelope"></i> info@cyntour.com</p>
                <p><i class="fas fa-envelope"></i> sales@cyntourim.com</p>
                <p><i class="fas fa-map-marker-alt"></i> Address: Molla Gürani, Karakoyunlu Sokağı No:2 D:4, 34093 Fatih/İstanbul</p>
                <p><i class="fab fa-instagram"></i> <a href="https://www.instagram.com/cyn__turizm/">cyn__turizm</a></p>
            </div>
        </div>
    </div>
</div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <span>© 2006-2024 CYN TURIZM All Rights Reserved</span>
        </div>
    </footer>
    <!-- End of Footer -->

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <!-- Mobile Menu Script -->
    <script>
        $(document).ready(function () {
            $('.navbar-toggler').click(function () {
                $('.mobile-menu').toggleClass('active');
            });
        });
    </script>
<script>
let currentPage = 1;
const hotelsPerPage = 20;

function populateTable(hotels) {
    const tableBody = document.getElementById('hotelTableBody');
    tableBody.innerHTML = ''; // Clear existing data
    const startIndex = (currentPage - 1) * hotelsPerPage;
    const endIndex = startIndex + hotelsPerPage;
    hotels.slice(startIndex, endIndex).forEach(hotel => {
        const row = `<tr>
                        <td><input type="checkbox" name="hotelSelect" value="${hotel.id}"></td>
                        <td><a href="#" onclick="showHotelDetails(${hotel.id})">${hotel.name}</a></td>
                        <td>${hotel.city}</td>
                         <td>${hotel.district}</td>
                         <td><a href="https://cyntourism.com/importPrices.php?id=${hotel.id}">import prices</a></td>
                    </tr>`;
        tableBody.innerHTML += row;
    });
    
    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');
    if (currentPage === 1) {
        prevPageBtn.disabled = true;
    } else {
        prevPageBtn.disabled = false;
    }
    if (endIndex >= hotels.length) {
        nextPageBtn.disabled = true;
    } else {
        nextPageBtn.disabled = false;
    }
}

function showHotelDetails(hotelId) {
    fetch(`getHotelDetails.php?hotel_id=${hotelId}`)
        .then(response => response.json())
        .then(details => {
            const modalBody = document.getElementById('hotelDetailsContent');
            modalBody.innerHTML = ''; // Clear existing content
            if(details.error) {
                modalBody.innerHTML = '<p>' + details.error + '</p>';
            } else {
                details.forEach(detail => {
                    modalBody.innerHTML += `
                        <div>
                            <strong>Room Type:</strong> ${detail.room_type}<br>
                            <strong>Adult Price:</strong> ${detail.adult_price}<br>
                            <strong>Child Price:</strong> ${detail.child_price}<br>
                            <p><strong>Description:</strong> ${detail.description}</p>
                            <p><strong>From:</strong> ${detail.start_date}</p> 
                            <p><strong>To:</strong>${detail.end_date}</p>
                        </div>
                        <hr>`;
                });
            }
            $('#hotelDetailsModal').modal('show');
        })
        .catch(error => {
            console.error('Error fetching hotel details:', error);
        });
}

function adjustPrices(action) {
    const selectedCheckboxes = document.querySelectorAll('input[name="hotelSelect"]:checked');
    const hotelIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    let percentage;
    
    if (action === 'increase') {
        percentage = document.getElementById('priceIncrease').value;
    } else if (action === 'decrease') {
        percentage = document.getElementById('priceDecrease').value;
    } else {
        return;
    }

    fetch('updateHotelPrices.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `hotelIds=${hotelIds.join(",")}&percentage=${percentage}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Prices updated successfully!");
            // Optionally, refresh the hotel list or details here if needed
        } else {
            alert("There was a problem updating the prices.");
        }
    })
    .catch(error => {
        console.error('Error updating hotel prices:', error);
        alert("An error occurred while trying to update the prices.");
    });
}

function fetchHotels() {
    fetch('list.php') // Adjust the path if necessary
        .then(response => response.json())
        .then(hotels => {
            populateTable(hotels);
        })
        .catch(error => {
            console.error('Error fetching hotels:', error);
        });
}

document.getElementById('prevPageBtn').addEventListener('click', () => {
    currentPage--;
    fetchHotels();
});

document.getElementById('nextPageBtn').addEventListener('click', () => {
    currentPage++;
    fetchHotels();
});

document.addEventListener('DOMContentLoaded', function() {
    fetchHotels();
    
    // Add event listener for filtering hotels by name
    document.getElementById('searchHotelInput').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const hotelRows = document.querySelectorAll('#hotelTableBody tr');

        hotelRows.forEach(row => {
            const hotelName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            if (hotelName.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>

</body>
</html>