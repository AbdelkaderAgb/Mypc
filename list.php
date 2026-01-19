<?php
$host = 'localhost';
$dbName = 'cyntzsrb_cyn';
$username = 'cyntzsrb_cyn';
$password = 'Qj!d$}Zh,-~m';

$conn = new mysqli($host, $username, $password, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, city ,district FROM hotels";
$result = $conn->query($sql);

$hotels = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
    echo json_encode($hotels);
} else {
    echo json_encode([]);
}

$conn->close();
?>