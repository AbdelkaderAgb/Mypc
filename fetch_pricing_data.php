<?php
// Database connection details
$servername = "localhost";
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$dbname = "cyntzsrb_cyn";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the hotel name from the query parameter
$hotel = isset($_GET['hotel']) ? $_GET['hotel'] : '';

// Query to fetch pricing data for the specific hotel
$sql = "SELECT room_type, accommodation, hotel_name, start_date, end_date, price, currency FROM pricing_data WHERE hotel_name = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $hotel);
$stmt->execute();
$result = $stmt->get_result();

// Build the response array
$response = array(
    'hotel_name' => '',
    'currency' => '',
    'data' => array()
);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dateRange = date('Y.m.d', strtotime($row['start_date'])) . ' - ' . date('Y.m.d', strtotime($row['end_date']));
        $roomType = $row['room_type'];
        $accommodation = $row['accommodation'];
        $currency = $row['currency'];
        
        // Set the hotel name and currency if not already set
        if (empty($response['hotel_name'])) {
            $response['hotel_name'] = $row['hotel_name'];
            $response['currency'] = $currency;
        }
        
        // Initialize date range array if not already set
        if (!isset($response['data'][$dateRange])) {
            $response['data'][$dateRange] = array();
        }

        // Add the room type and accommodation data
        $response['data'][$dateRange][] = array(
            'room_type' => $roomType,
            'accommodation' => $accommodation,
            'price' => htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8')
        );
    }
} else {
    // Handle case where no data is found
    $response['message'] = 'No pricing data found.';
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>
