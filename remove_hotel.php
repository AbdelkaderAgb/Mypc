<?php
include 'auth.php'; // Include auth.php to restrict access

// Database connection
$host = 'localhost';
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$database = 'cyntzsrb_cyn';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hotel_name'])) {
    $hotelName = $conn->real_escape_string($_POST['hotel_name']);
    
    $deleteQuery = "DELETE FROM pricing_data WHERE hotel_name = '$hotelName'";
    
    if ($conn->query($deleteQuery) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }
}

$conn->close();
?>
