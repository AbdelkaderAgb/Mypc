<?php
include 'db.php';

$hotelId = $_GET['hotelId'];

$stmt = $conn->prepare("
    SELECT hp.id ,hp.room_type, hp.adult_price, hp.child_price, hp.description ,hp.start_date , hp.end_date ,hp.currency
    FROM hotel_prices hp
    WHERE hp.hotel_id = :hotelId
    ORDER BY hp.room_type ASC
");
$stmt->bindParam(':hotelId', $hotelId, PDO::PARAM_INT);
$stmt->execute();

$prices = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($prices);
?>
