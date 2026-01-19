<?php
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

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'getHotels':
        getHotels($conn);
        break;
    case 'getHotelPrices':
        getHotelPrices($conn);
        break;
    default:
        echo json_encode(["error" => "Invalid action"]);
}

function getHotels($conn) {
    $city = isset($_GET['city']) ? $_GET['city'] : '';
    $sql = "SELECT name FROM hotels WHERE city = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $city);
    $stmt->execute();
    $result = $stmt->get_result();
    $hotels = [];
    while($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
    echo json_encode($hotels);
    $stmt->close();
}

function getHotelPrices($conn) {
    $hotelName = isset($_GET['hotelName']) ? $_GET['hotelName'] : '';
    $sql = "SELECT hp.* FROM hotel_prices hp JOIN hotels h ON hp.hotel_id = h.id WHERE h.name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $hotelName);
    $stmt->execute();
    $result = $stmt->get_result();
    $prices = [];
    while($row = $result->fetch_assoc()) {
        $prices[] = $row;
    }
    echo json_encode($prices);
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Finder</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
/* Custom CSS styles */
.hotel-list {
    cursor: pointer;
    transition: background-color 0.2s ease-in-out;
}

.hotel-list:hover {
    background-color: #f0f0f0;
}

/* Enhance the select dropdown */
#citySelector {
    max-width: 400px; /* Adjust based on preference */
    margin: 0 auto; /* Center align the dropdown */
}

/* Styling for the hotel list */
#hotelList {
    margin-top: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.list-group-item {
    border: none; /* Remove borders within the list */
    border-bottom: 1px solid #eee; /* Add a light border to separate items */
}

.list-group-item:last-child {
    border-bottom: none; /* Remove bottom border for the last item */
}

/* Modal customizations */
#roomDetailsModal .modal-content {
    border-radius: 10px; /* Rounded corners for the modal */
}

#roomDetailsModal .modal-header {
    border-bottom: 1px solid #dee2e6; /* Consistent with Bootstrap's styling */
    background-color: #f8f9fa; /* Slightly different header bg for emphasis */
}

#roomDetailsModalLabel {
    font-size: 1.25rem; /* Larger modal title */
}

/* Responsiveness: Adjust modal width on smaller screens */
@media (max-width: 768px) {
    #roomDetailsModal .modal-dialog {
        max-width: 90%; /* Make the modal wider on small screens */
    }
}

    </style>
</head>
<body>
    <div class="container mt-5">
        <select id="citySelector" class="form-control mb-3">
            <option value="">Select a City</option>
            <option value="City1">City1</option>
            <option value="City2">City2</option>
            <!-- Add more cities as needed -->
        </select>
        <ul id="hotelList" class="list-group"></ul>
    </div>

    <!-- Hotel Room Details Modal -->
    <div class="modal fade" id="roomDetailsModal" tabindex="-1" role="dialog" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roomDetailsModalLabel">Room Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalBodyContent">
                    <!-- Room details will be injected here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#citySelector').on('change', function() {
                const city = $(this).val();
                $.ajax({
                    url: '?action=getHotels',
                    type: 'GET',
                    data: { city: city },
                    success: function(response) {
                        const hotels = JSON.parse(response);
                        const hotelList = $('#hotelList');
                        hotelList.empty(); // Clear existing list

                        hotels.forEach(hotel => {
                            const listItem = $(`<li class="list-group-item hotel-list">${hotel.name}</li>`);
                            listItem.on('click', function() {
                                // Fetch and display room details here using another AJAX call
                                // Example: '?action=getHotelPrices&hotelName=' + hotel.name
                                $.ajax({
                                    url: '?action=getHotelPrices',
                                    type: 'GET',
                                    data: { hotelName: hotel.name },
                                    success: function(detailsResponse) {
                                        const details = JSON.parse(detailsResponse);
                                        // Process and display room details in the modal
                                        // This is a placeholder; you'll need to format the details as needed.
                                        $('#modalBodyContent').html(details.map(detail => `<p>${detail.room_type}: Adult Price - ${detail.adult_price}, Child Price - ${detail.child_price}</p>`).join(''));
                                        $('#roomDetailsModalLabel').text(hotel.name); // Set hotel name as modal title
                                        $('#roomDetailsModal').modal('show'); // Show the modal
                                    }
                                });
                            });
                            hotelList.append(listItem);
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>