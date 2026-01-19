<?php
$host = 'localhost';
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$database = 'cyntzsrb_cyn';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voucher_no = htmlspecialchars($_POST['voucher_no']);
    $company_name = htmlspecialchars($_POST['company_name']);
    $hotel_name = htmlspecialchars($_POST['hotel_name']);
    $flight_number = htmlspecialchars($_POST['flight_number']);
    $pickup_location = htmlspecialchars($_POST['pickup_location']);
    $dropoff_location = htmlspecialchars($_POST['dropoff_location']);
    $pickup_date = htmlspecialchars($_POST['pickup_date']);
    $pickup_time = htmlspecialchars($_POST['pickup_time']);
    $transfer_type = htmlspecialchars($_POST['transfer_type']);
    $return_date = htmlspecialchars($_POST['return_date'] ?? '');
    $return_time = htmlspecialchars($_POST['return_time'] ?? '');
    $total_pax = htmlspecialchars($_POST['total_pax']);
    $passengers = htmlspecialchars($_POST['passengers']);

    // Check if voucher_no already exists
    $check_sql = "SELECT id FROM vouchers WHERE voucher_no = '$voucher_no'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $error_message = "Voucher No already exists. Please use a different Voucher No.";
    } else {
        $sql = "INSERT INTO vouchers (voucher_no, company_name, hotel_name, flight_number, pickup_location, dropoff_location, pickup_date, pickup_time, transfer_type, return_date, return_time, total_pax, passengers)
                VALUES ('$voucher_no', '$company_name', '$hotel_name', '$flight_number', '$pickup_location', '$dropoff_location', '$pickup_date', '$pickup_time', '$transfer_type', '$return_date', '$return_time', '$total_pax', '$passengers')";

        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            header("Location: transfer-voucher.php?id=$last_id");
            exit();
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM vouchers WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No voucher found with ID: " . $id;
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYN Transfer Voucher</title>
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
            height: 297mm;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }

        .container {
            width: 100%;
            padding: 10mm;
            background-color: white;
            border-radius: 5mm;
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

        .logo img {
            height: 120px;
        }

        .voucher-title {
            text-align: center;
            flex-grow: 1;
        }

        .voucher-title h2 {
            font-size: 20pt;
            margin: 0;
            color: #333;
            letter-spacing: 1mm;
            font-weight: bold;
        }

        .voucher-no {
            text-align: right;
            font-size: 14pt;
            color: black;
            font-weight: bold;
        }

        .voucher-no span {
            color: red;
        }

        .details {
            margin-bottom: 10mm;
        }

        .details p {
            margin: 0;
            padding: 2mm 0;
            font-size: 12pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10mm;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5mm;
            text-align: left;
            font-size: 12pt;
            white-space: nowrap;
        }

        th {
            background-color: #f2f2f2;
            width: 20%;
        }

        td {
            background-color: #fff;
        }

        h3 {
            font-size: 18pt;
            margin: 10mm 0 5mm;
            color: #333;
        }

        #downloadBtn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .footer {
            margin-top: 20mm;
            border-top: 2px solid #ddd;
            padding-top: 5mm;
            font-size: 12pt;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .footer p {
            margin: 0.5mm 0;
        }

        .footer .fa {
            margin-right: 5px;
        }

        .footer-logo {
            text-align: right;
        }

        .footer-logo img {
            height: 50px;
        }

        .footer-logo p {
            margin-top: 2mm;
            font-size: 10pt;
            color: #333;
        }

        .footer-logo p .belge {
            font-weight: bold;
            color: black;
        }

        .footer-logo p .number {
            font-weight: bold;
            color: red;
        }

        .footer-contact {
            text-align: left;
        }

        @media print {
            body {
                width: 210mm;
                height: 297mm;
            }

            .container {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }

            #downloadBtn {
                display: none;
            }
        }
    </style>
</head>

<body>
    <?php if (isset($row)) : ?>
        <div class="container" id="voucherContent">
            <div class="header">
                <div class="logo">
                    <img src="logo.png" alt="CYN TURIZM">
                </div>
                <div class="voucher-title">
                    <h2>TRANSFER VOUCHER</h2>
                </div>
                <div class="voucher-no">
                    <p>Voucher No: <span id="voucher_no_display"><?php echo $row['voucher_no']; ?></span></p>
                </div>
            </div>

            <div class="details">
                <p><strong>Company Name:</strong> <span id="company_name_display"><?php echo $row['company_name']; ?></span></p>
                <p><strong>Hotel Name:</strong> <span id="hotel_name_display"><?php echo $row['hotel_name']; ?></span></p>
                <p><strong>Flight Number:</strong> <span id="flight_number_display"><?php echo $row['flight_number']; ?></span></p>
            </div>

            <table class="table table-bordered">
                <tr>
                    <th>Starting Point:</th>
                    <td><span id="pickup_location_display"><?php echo $row['pickup_location']; ?></span></td>
                    <th>Destination:</th>
                    <td><span id="dropoff_location_display"><?php echo $row['dropoff_location']; ?></span></td>
                </tr>
                <tr>
                    <th> Date:</th>
                    <td><span id="pickup_date_display"><?php if (!empty($row['pickup_date'])) { echo date('d/m/Y', strtotime($row['pickup_date'])); } ?></span></td>
                    <th>Total Pax:</th>
                    <td><span id="total_pax_display"><?php echo $row['total_pax']; ?></span></td>
                </tr>
                <tr>
                    <th>Time:</th>
                    <td><span id="pickup_time_display"><?php echo $row['pickup_time']; ?></span></td>
                    <?php if ($row['transfer_type'] == "Arrival-Return") : ?>
                        <th>Return Date:</th>
                        <td><span id="return_date_display"><?php if (!empty($row['return_date'])) { echo date('d/m/Y', strtotime($row['return_date'])); } ?></span></td>
                    <?php else: ?>
                        <th></th>
                        <td></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th>Transfer Type:</th>
                    <td><span id="transfer_type_display"><?php echo $row['transfer_type']; ?></span></td>
                    <?php if ($row['transfer_type'] == "Arrival-Return") : ?>
                        <th>Return Time:</th>
                        <td><span id="return_time_display"><?php echo $row['return_time']; ?></span></td>
                    <?php else: ?>
                        <th></th>
                        <td></td>
                    <?php endif; ?>
                </tr>
            </table>

            <h3>Passengers</h3>
            <table class="table table-bordered" id="passengers_table">
                <tr>
                    <th>Name</th>
                </tr>
                <?php
                $passenger_data = explode("\n", $row['passengers']);
                foreach ($passenger_data as $passenger) :
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($passenger); ?></td>
                    </tr>
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
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <button id="downloadBtn" onclick="downloadAsPDF()">Download as PDF</button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        function downloadAsPDF() {
            const element = document.getElementById('voucherContent');
            const companyName = document.getElementById('company_name_display').textContent;
            html2pdf()
                .from(element)
                .set({
                    margin: [0, 0, 0, 0],
                    filename: `${companyName}_transfer_voucher.pdf`,
                    image: { type: 'jpeg', quality: 1 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                })
                .save();
        }
    </script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>