<?php
$host = 'localhost';
$dbName = 'cyntzsrb_cyn';
$username = 'cyntzsrb_cyn';
$password = 'Qj!d$}Zh,-~m';
$conn = new mysqli($host, $username, $password, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$hotelIds = isset($_POST['hotelIds']) ? explode(',', $_POST['hotelIds']) : [];
$percentage = isset($_POST['percentage']) ? (float)$_POST['percentage'] : 0;

// Validate percentage
if ($percentage == 0) {
    echo json_encode(['success' => false, 'message' => 'Percentage cannot be zero.']);
    exit;
}

// Check if hotelIds are not empty and are numeric
foreach ($hotelIds as $id) {
    if (!is_numeric($id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid hotel ID.']);
        exit;
    }
}

if (count($hotelIds) > 0) {
    // Create placeholders for the query
    $placeholders = implode(',', array_fill(0, count($hotelIds), '?'));

    // Calculate new prices
    $sql = "UPDATE hotel_prices SET adult_price = adult_price * (1 + (? / 100)), child_price = child_price * (1 + (? / 100)) WHERE hotel_id IN ($placeholders)";

    // Prepare statement
    $stmt = $conn->prepare($sql);

    // Dynamically bind parameters
    $types = str_repeat('d', 2) . str_repeat('i', count($hotelIds));
    $params = array_merge([$percentage, $percentage], $hotelIds);
    $stmt->bind_param($types, ...$params);

    // Execute query
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Prices updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update prices.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No hotel IDs provided.']);
}

$conn->close();
?>
