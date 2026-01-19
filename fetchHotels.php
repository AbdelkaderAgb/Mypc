<?php
include 'db.php';

$city = $_GET['city'];

$stmt = $conn->prepare("SELECT id, name FROM hotels WHERE city = :city ORDER BY name ASC");
$stmt->bindParam(':city', $city, PDO::PARAM_STR);
$stmt->execute();

$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($hotels);
?>
