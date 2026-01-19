<?php
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

// Query to fetch hotel names
$sql = "SELECT DISTINCT hotel_name FROM pricing_data";
$result = $conn->query($sql);

// Build the response array
$response = array('hotels' => array());

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response['hotels'][] = $row['hotel_name'];
    }
} else {
    $response['message'] = 'No hotels found.';
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>
