<?php
// --- FIX: Initialize all variables to safe defaults ---
$invoice_no = '';
$company_name = '';
$hotel_name = '';
$accommodation_start = '';
$accommodation_end = '';
$transfer_price = 0;
$notes = '';
$currency = '$'; // Default currency
$customers = [];
$rooms = [];
$nights = 0;
$roomCharge = 0;
$transferCharge = 0;
$totalCharge = 0;
$formatted_start = 'N/A'; // Default display text
$formatted_end = 'N/A';   // Default display text

// --- Process form data only if it has been submitted ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- Sanitize and retrieve POST data ---
    $invoice_no = htmlspecialchars($_POST['invoice_no']);
    $company_name = htmlspecialchars($_POST['company_name']);
    $hotel_name = htmlspecialchars($_POST['hotel_name']);
    $accommodation_start = $_POST['accommodation_start'];
    $accommodation_end = $_POST['accommodation_end'];
    // --- FIX: Ensure transfer price is a number (float) and handle empty input ---
    $transfer_price = isset($_POST['transfer_price']) && !empty($_POST['transfer_price']) ? (float)$_POST['transfer_price'] : 0;
    $notes = isset($_POST['notes']) ? trim(htmlspecialchars($_POST['notes'])) : '';
    $currency = htmlspecialchars($_POST['currency']);
    // --- Trim whitespace from each customer name ---
    $customers = array_map('trim', explode(',', $_POST['customers']));
    $customers = array_map('htmlspecialchars', $customers); // Sanitize each customer name

    // --- Process rooms and their prices ---
    $roomCount = 1;
    $rooms = [];
    while (isset($_POST["room_$roomCount"])) {
        $rooms[] = [
            'room' => htmlspecialchars($_POST["room_$roomCount"]),
            // --- FIX: Ensure price per night is a number (float) ---
            'price_per_night' => (float)$_POST["price_per_night_$roomCount"],
        ];
        $roomCount++;
    }

    // --- FIX 1: Use DateTime objects for reliable date calculation ---
    if (!empty($accommodation_start) && !empty($accommodation_end)) {
        try {
            $startDateObj = new DateTime($accommodation_start);
            $endDateObj = new DateTime($accommodation_end);
            
            // Calculate the difference in days
            $interval = $startDateObj->diff($endDateObj);
            $nights = $interval->days;

            // --- FIX 2: Format dates to d-m-Y for display ---
            $formatted_start = $startDateObj->format('d-m-Y');
            $formatted_end = $endDateObj->format('d-m-Y');
        } catch (Exception $e) {
            // Handle invalid date formats gracefully
            $nights = 0;
            $formatted_start = 'Invalid Date';
            $formatted_end = 'Invalid Date';
        }
    }

    // --- FIX 3: Calculate total room charges with numeric values ---
    $roomCharge = array_reduce($rooms, function ($sum, $room) use ($nights) {
        // Calculation is now safe as 'price_per_night' is a float
        return $sum + ($room['price_per_night'] * $nights);
    }, 0);

    // --- Final Total Calculation ---
    $transferCharge = $transfer_price; // Already a float from above
    $totalCharge = $roomCharge + $transferCharge;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akteon Travel Invoice</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 0;
            width: 210mm;
            min-height: 297mm;
            box-sizing: border-box;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .container {
            width: 100%;
            padding: 10mm;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
        }
        .logo img { height: 120px; }
        .invoice-title { text-align: center; flex-grow: 1; }
        .invoice-title h2 { font-size: 20pt; margin: 0; color: #333; letter-spacing: 1mm; font-weight: bold; }
        .invoice-no { text-align: right; font-size: 14pt; color: black; font-weight: bold; }
        .invoice-no span { color: red; }
        .details { margin-bottom: 10mm; }
        .details p { margin: 0; padding: 2mm 0; font-size: 12pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10mm; }
        th, td { border: 1px solid #ddd; padding: 5mm; text-align: left; font-size: 12pt; }
        th { background-color: #f2f2f2; width: 20%; }
        h3 { font-size: 18pt; margin: 10mm 0 5mm; color: #333; }
        .footer { margin-top: 20mm; border-top: 2px solid #ddd; padding-top: 5mm; font-size: 12pt; color: #333; display: flex; justify-content: space-between; align-items: flex-start; }
        .footer-contact p, .footer-logo p { margin: 0.5mm 0; }
        .fa { margin-right: 5px; }
        .footer-logo img { height: 50px; }
        .footer-logo p .belge { font-weight: bold; color: black; }
        .footer-logo p .number { font-weight: bold; color: red; }
        #downloadBtn { position: fixed; top: 20px; right: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); }
        @media print {
            body { background-color: white; }
            .container { margin: 0; border: initial; border-radius: initial; width: initial; min-height: initial; box-shadow: initial; background: initial; page-break-after: always; }
            #downloadBtn { display: none; }
        }
    </style>
</head>

<body>
    <div class="container" id="invoiceContent">
        <div class="header">
            <div class="logo"><img src="logo.png" alt="AKTEON TRAVEL"></div>
            <div class="invoice-title"><h2>INVOICE</h2></div>
            <div class="invoice-no"><p>Invoice No: <span><?php echo $invoice_no; ?></span></p></div>
        </div>

        <div class="details">
            <p><strong>Company Name:</strong> <span><?php echo $company_name; ?></span></p>
            <p><strong>Hotel Name:</strong> <span><?php echo $hotel_name; ?></span></p>
        </div>

        <h3>Room Details</h3>
        <table class="table table-bordered">
            <tr><th>Room Type</th><th>Price per Night</th></tr>
            <?php foreach ($rooms as $room) : ?>
                <tr>
                    <td><?php echo $room['room']; // Already escaped ?></td>
                    <!-- FIX: Format price to 2 decimal places -->
                    <td><?php echo htmlspecialchars($currency) . number_format($room['price_per_night'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Accommodation Details</h3>
        <table class="table table-bordered">
            <!-- FIX: Use formatted dates and nights variable -->
            <tr><th>Check-in/out</th><td><?php echo "$formatted_start - $formatted_end ($nights Nights)"; ?></td></tr>
            
            <!-- FIX: Check if transfer price is greater than 0 -->
            <?php if ($transfer_price > 0) : ?>
                <tr>
                    <th>Transfer Price & Notes</th>
                    <td>
                        <!-- FIX: Format price to 2 decimal places -->
                        <?php echo htmlspecialchars($currency) . number_format($transfer_price, 2); ?>
                        <?php if (!empty($notes)) : ?>
                            <br><small><em>Notes: <?php echo $notes; // Already escaped ?></em></small>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
            
            <tr>
                <th>Total Price</th>
                <!-- FIX: Format total price to 2 decimal places -->
                <td><strong><?php echo htmlspecialchars($currency) . number_format($totalCharge, 2); ?></strong></td>
            </tr>
        </table>

        <h3>Customers</h3>
        <table class="table table-bordered">
            <tr><th>Name</th></tr>
            <?php foreach ($customers as $customer) : ?>
                <tr><td><?php echo $customer; // Already escaped ?></td></tr>
            <?php endforeach; ?>
        </table>

        <div class="footer">
            <div class="footer-contact">
                <p><i class="fas fa-map-marker-alt"></i> Molla Gürani, Karakoyunlu Sokağı No:2 D:4, 34093 Fatih/İstanbul</p>
                <p><i class="fas fa-phone"></i> +90 5318176770</p>
                <p><i class="fas fa-envelope"></i> info@cyntour.com</p>
            </div>
            <div class="footer-logo">
                <img src="footer-logo.png" alt="Footer Logo">
                <p><span class="belge">BELGE</span><span class="number">11738</span></p>
            </div>
        </div>
    </div>
    
    <!-- Show button only if form was submitted -->
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <button id="downloadBtn" onclick="downloadAsPDF()">Download as PDF</button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        function downloadAsPDF() {
            const element = document.getElementById('invoiceContent');
            // Use the PHP variable for the filename
            const companyName = "<?php echo addslashes($company_name); ?>";
            const invoiceNo = "<?php echo addslashes($invoice_no); ?>";
            const fileName = `${companyName}_${invoiceNo}_invoice.pdf`;

            const opt = {
                margin: 0,
                filename: fileName,
                image: { type: 'jpeg', quality: 1.0 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().from(element).set(opt).save();
        }
    </script>
    <?php endif; ?>
</body>

</html>