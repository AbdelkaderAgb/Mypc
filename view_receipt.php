<?php
/**
 * view_receipt.php
 * 
 * This page is responsible for viewing a single, existing receipt.
 * It acts as a "controller" to fetch all necessary data from the database.
 * 1. Gets the receipt ID from the URL.
 * 2. Fetches the main receipt details from the `receipts` table.
 * 3. Fetches the partner's company name from the `partners` table.
 * 4. Fetches all associated payments from the `receipt_payments` table.
 * 5. Bundles all this data into a single array (`$receipt_data`).
 * 6. Includes the `create_receipt.php` template to render the final HTML.
 */

// Show errors for easier debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Make sure the filename matches your database connection file (e.g., db.php, database.php)
require_once 'database.php';

// --- Step 1: Get and Validate the Receipt ID ---
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If the ID is missing or invalid, stop execution.
if (!$id) {
    die('ERROR: Invalid or missing receipt ID.');
}

// --- Step 2: Fetch the Main Receipt and Partner Data ---
$sql_receipt = "SELECT r.*, p.company AS partner_company, p.address AS partner_address
                FROM receipts r
                LEFT JOIN partners p ON p.id = r.partner_id
                WHERE r.id = ? 
                LIMIT 1";

$stmt_receipt = $pdo->prepare($sql_receipt);
$stmt_receipt->execute([$id]);
$receipt_data = $stmt_receipt->fetch(PDO::FETCH_ASSOC);

// If no receipt was found for the given ID, stop execution.
if (!$receipt_data) {
    die('ERROR: Receipt not found.');
}

// --- Step 3: Fetch the Associated Payment Data ---
$sql_payments = "SELECT amount, currency, money_provider 
                 FROM receipt_payments 
                 WHERE receipt_id = ?";
                 
$stmt_payments = $pdo->prepare($sql_payments);
$stmt_payments->execute([$id]);
// A receipt can have multiple payments, so use fetchAll()
$payments = $stmt_payments->fetchAll(PDO::FETCH_ASSOC);


// --- Step 4: BUNDLE ALL DATA TOGETHER ---
// This is the most important step. We add the fetched payments array
// into our main `$receipt_data` array. The template will use this.
$receipt_data['payments'] = $payments;


// --- Step 5: Render the Template ---
// Now that `$receipt_data` is fully populated with all the information,
// we simply include the template file. It will handle the entire display.
include 'create_receipt.php';

?>