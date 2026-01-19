<?php
include 'db.php';

$stmt = $conn->prepare("SELECT DISTINCT city FROM hotels ORDER BY city ASC");
$stmt->execute();

$cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($cities);
?>
