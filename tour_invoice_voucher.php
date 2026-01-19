<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $invoice_no = $_POST['invoice_no'];
    $company_name = $_POST['company_name'];
    $client_name = $_POST['client_name'];
    $currency = $_POST['currency'];
    $customers = explode(',', $_POST['customers']);

    $tours = [];
    $tourCount = 1;
    while (isset($_POST["tour_name_$tourCount"])) {
        $use_special_price = isset($_POST["use_special_price_$tourCount"]) && $_POST["use_special_price_$tourCount"] == 'on';
        $tours[] = [
            'name' => $_POST["tour_name_$tourCount"],
            'date' => $_POST["tour_date_$tourCount"],
            'pax_count' => $use_special_price ? null : $_POST["pax_count_$tourCount"],
            'pax_price' => $use_special_price ? null : $_POST["pax_price_$tourCount"],
            'adult_count' => $use_special_price ? $_POST["adult_count_$tourCount"] : null,
            'child_count' => $use_special_price ? $_POST["child_count_$tourCount"] : null,
            'adult_price' => $use_special_price ? $_POST["adult_price_$tourCount"] : null,
            'child_price' => $use_special_price ? $_POST["child_price_$tourCount"] : null,
        ];
        $tourCount++;
    }

    $totalInvoice = 0;
    $hasSpecialPrice = false;
    $hasPaxPrice = false;

    foreach ($tours as $tour) {
        if ($tour['pax_count'] !== null) {
            $totalInvoice += $tour['pax_count'] * $tour['pax_price'];
            $hasPaxPrice = true;
        } else {
            $totalInvoice += ($tour['adult_count'] * $tour['adult_price']) + ($tour['child_count'] * $tour['child_price']);
            $hasSpecialPrice = true;
        }
    }
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

        .invoice-title {
            text-align: center;
            flex-grow: 1;
        }

        .invoice-title h2 {
            font-size: 20pt;
            margin: 0;
            color: #333;
            letter-spacing: 1mm;
            font-weight: bold;
        }

        .invoice-no {
            text-align: right;
            font-size: 14pt;
            color: black;
            font-weight: bold;
        }

        .invoice-no span {
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
    <div class="container" id="invoiceContent">
        <div class="header">
            <div class="logo">
                <img src="logo.png" alt="AKTEON TRAVEL">
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
            </div>
            <div class="invoice-no">
                <p>Invoice No: <span><?php echo htmlspecialchars($invoice_no); ?></span></p>
            </div>
        </div>

        <div class="details">
            <p><strong>Company Name:</strong> <span><?php echo htmlspecialchars($company_name); ?></span></p>
            <p><strong>Client Name:</strong> <span><?php echo htmlspecialchars($client_name); ?></span></p>
        </div>

        <h3>Tour Details</h3>
        <table class="table table-bordered">
            <tr>
                <th>Tour Name</th>
                <th>Date</th>
                <?php if ($hasPaxPrice): ?>
                    <th>Pax Count</th>
                    <th>Pax Price</th>
                <?php endif; ?>
                <?php if ($hasSpecialPrice): ?>
                    <th>Special Price</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($tours as $tour) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($tour['name']); ?></td>
                    <td><?php echo htmlspecialchars($tour['date']); ?></td>
                    <?php if ($hasPaxPrice): ?>
                        <td><?php echo $tour['pax_count'] !== null ? htmlspecialchars($tour['pax_count']) : 'N/A'; ?></td>
                        <td><?php echo $tour['pax_price'] !== null ? htmlspecialchars($currency . $tour['pax_price']) : 'N/A'; ?></td>
                    <?php endif; ?>
                    <?php if ($hasSpecialPrice): ?>
                        <td>
                            <?php if ($tour['adult_count'] !== null && $tour['child_count'] !== null) : ?>
                                <?php echo htmlspecialchars($tour['adult_count'] . ' adult×' . $tour['adult_price'] . $currency . ', ' . $tour['child_count'] . ' child×' . $tour['child_price'] . $currency); ?>
                            <?php else : ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Total Invoice</h3>
        <table class="table table-bordered">
            <tr>
                <th>Total</th>
                <td><?php echo htmlspecialchars($currency . $totalInvoice); ?></td>
            </tr>
        </table>

        <h3>Customers</h3>
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
            </tr>
            <?php foreach ($customers as $customer) : ?>
                <tr>
                    <td><?php echo htmlspecialchars(trim($customer)); ?></td>
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
    <button id="downloadBtn" onclick="downloadAsPDF()">Download as PDF</button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        function downloadAsPDF() {
            const element = document.getElementById('invoiceContent');
            const companyName = "<?php echo htmlspecialchars($company_name); ?>";
            html2pdf()
                .from(element)
                .set({
                    margin: [0, 0, 0, 0],
                    filename: `${companyName}_invoice.pdf`,
                    image: { type: 'jpeg', quality: 1 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                })
                .save();
        }
    </script>
</body>

</html>