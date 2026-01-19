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

// Get POST data
$hotel = $_POST['hotel'];
$percentage = floatval($_POST['percentage']);
$action = $_POST['action'];

// Determine the SQL operator
$operator = $action === 'increase' ? '+' : '-';

// Query to update prices
$sql = "UPDATE pricing_data SET price = price * (1 $operator ? / 100) WHERE hotel_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ds", $percentage, $hotel);
$stmt->execute();

// Check for errors
if ($stmt->error) {
    $response = array('success' => false, 'message' => $stmt->error);
} else {
    $response = array('success' => true, 'message' => 'Prices adjusted successfully.');
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>
