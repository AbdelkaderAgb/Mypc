<?php
$host = 'localhost';
$dbName = 'cyntzsrb_cyn';
$username = 'cyntzsrb_cyn';
$password = 'Qj!d$}Zh,-~m';

$conn = new mysqli($host, $username, $password, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT DISTINCT city FROM hotels"; // Select distinct city names from hotels table
$result = $conn->query($sql);

$cities = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cities[] = $row['city'];
    }
    echo json_encode($cities);
} else {
    echo json_encode([]);
}

$conn->close();
?>
