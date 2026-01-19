<?php
$host = 'localhost';
$dbName = 'cyntzsrb_cyn';
$username = 'cyntzsrb_cyn';
$password = 'Qj!d$}Zh,-~m';

$conn = new mysqli($host, $username, $password, $dbName);
$conn -> set_charset("utf8");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$hotel_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT room_type, adult_price, child_price, description, currency ,start_date , end_date FROM hotel_prices WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
//print_r($result->fetch_assoc()) ;
$details = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $details[] = $row;
    }
    echo json_encode($details);
} else {
    echo json_encode(["error" => "No details found for hotel_id: $hotel_id"]);
}

$stmt->close();
$conn->close();
?>