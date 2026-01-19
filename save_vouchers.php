<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost";
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$dbname = "cyntzsrb_cyn";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Log received data
error_log('Received data: ' . print_r($data, true));

// Check if the first customer exists before accessing it
$customer_name = isset($data['customers'][0]['name']) ? $data['customers'][0]['name'] : '';

// Check if voucher number already exists
$check_sql = "SELECT voucher_no FROM h_vouchers WHERE voucher_no = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $data['voucher_no']);
$check_stmt->execute();
$check_stmt->store_result();
if ($check_stmt->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Voucher number already exists. Please use a unique voucher number."]);
    $check_stmt->close();
    $conn->close();
    exit();
}
$check_stmt->close();

// Handle room value
$room = isset($data['room']) ? (string)$data['room'] : 'Unknown';
error_log('Room value: ' . $room);

// Prepare SQL query to insert new voucher
$sql = "INSERT INTO h_vouchers (voucher_no, company_name, hotel, room_count, check_in_date, check_out_date, nights, room, transfer_type, customer_name)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
error_log('SQL query: ' . $sql);

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die(json_encode(["success" => false, "message" => "Query preparation error: " . $conn->error]));
}

// Bind form data to SQL query
$stmt->bind_param("sssissssss", 
    $data['voucher_no'],
    $data['company_name'],
    $data['hotel'],
    $data['room_count'],
    $data['accommodation_start'],
    $data['accommodation_end'],
    $data['nights'],
    $room,
    $data['transfer_type'],
    $customer_name
);

// Execute query and handle response
if ($stmt->execute()) {
    error_log('Affected rows: ' . $stmt->affected_rows);
    echo json_encode(["success" => true, "message" => "Voucher saved successfully"]);
} else {
    error_log('SQL error: ' . $stmt->error);
    echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
}

// Close statement and connection
$stmt->close();
$conn->close();
?>