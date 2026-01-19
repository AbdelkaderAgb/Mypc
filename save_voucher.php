<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Database connection settings
$servername = "cyntzsrb_cyn";
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$dbname = "tour_voucher";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert voucher data
    $stmt = $conn->prepare("INSERT INTO tour_voucher (voucher_no, company_name, adult_count, child_count, infant_count, tours, customers) VALUES (:voucher_no, :company_name, :adult_count, :child_count, :infant_count, :tours, :customers)");
    $stmt->bindParam(':voucher_no', $data['voucher_no']);
    $stmt->bindParam(':company_name', $data['company_name']);
    $stmt->bindParam(':adult_count', $data['adult']);
    $stmt->bindParam(':child_count', $data['child']);
    $stmt->bindParam(':infant_count', $data['infant']);
    $stmt->bindParam(':tours', json_encode($data['tours']));
    $stmt->bindParam(':customers', json_encode($data['customers']));
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn = null;
?>