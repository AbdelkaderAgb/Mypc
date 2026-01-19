<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $invoice_no = htmlspecialchars($_POST['invoice_no']);
    $company_name = htmlspecialchars($_POST['company_name']);
    $starting_point = htmlspecialchars($_POST['starting_point']);
    $return_point = htmlspecialchars($_POST['return_point']);
    $hotel = htmlspecialchars($_POST['hotel']);
    $pickup_date = htmlspecialchars($_POST['pickup_date']);
    $transfer_type = htmlspecialchars($_POST['transfer_type']);
    $return_date = htmlspecialchars($_POST['return_date'] ?? '');
    $total_pax = htmlspecialchars($_POST['total_pax']);
    $total_price = htmlspecialchars($_POST['total_price']);
    $currency = htmlspecialchars($_POST['currency']);
    $passengers = htmlspecialchars($_POST['passengers']);

    $passenger_lines = explode("\n", $passengers);
    $passenger_data = [];
    foreach ($passenger_lines as $line) {
        $passenger_data[] = trim($line);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyntourism Travel-Transfer Invoice</title>
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
            width: 190mm;
            padding: 10mm;
            min-height: 277mm;
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
            margin-bottom: 10mm;
        }

        .logo img {
            height: 50mm; /* Increased size for the logo */
        }

        .invoice-title {
            text-align: center;
            flex-grow: 1;
        }

        .invoice-title h2 {
            font-size: 18pt;
            margin: 0;
            color: #333;
            letter-spacing: 1mm;
            font-weight: bold;
        }

        .invoice-no {
            text-align: right;
            font-size: 12pt;
            color: black;
            font-weight: bold;
        }

        .invoice-no span {
            color: red;
        }

        .details {
            margin-bottom: 5mm;
        }

        .details p {
            margin: 0;
            padding: 1mm 0;
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 2mm;
            text-align: left;
            font-size: 10pt;
            white-space: nowrap;
        }

        th {
            background-color: #f2f2f2;
            width: 20%;
        }

        td {
            background-color: #fff;
        }

        .left-column, .right-column {
            width: 50%;
            float: left;
        }

        h3 {
            font-size: 14pt;
            margin: 5mm 0 3mm;
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
            margin-top: 10mm;
            border-top: 2px solid #ddd;
            padding-top: 5mm;
            font-size: 9pt;
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
            height: 10mm; /* Decreased size for the footer logo */
        }

        .footer-logo p {
            margin-top: 2mm;
            font-size: 8pt;
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
    <div class="container" id="invoiceContent">
        <div class="header">
            <div class="logo">
                <img src="logo.png" alt="CYN TURİZM">
            </div>
            <div class="invoice-title">
                <h2>TRANSFER INVOICE</h2>
            </div>
            <div class="invoice-no">
                <p>Invoice No: <span id="invoice_no_display"><?php echo $invoice_no; ?></span></p>
            </div>
        </div>

        <div class="details">
            <p><strong>Company Name:</strong> <span id="company_name_display"><?php echo $company_name; ?></span></p>
            <p><strong>Hotel:</strong> <span id="hotel_display"><?php echo $hotel; ?></span></p>
        </div>

        <div class="left-column">
            <table class="table table-bordered">
                <tr>
                    <th>Starting Point:</th>
                    <td><span id="starting_point_display"><?php echo $starting_point; ?></span></td>
                </tr>
                <tr>
                    <th>Pickup Date:</th>
                    <td><span id="pickup_date_display"><?php echo $pickup_date; ?></span></td>
                </tr>
                <tr>
                    <th>Transfer Type:</th>
                    <td><span id="transfer_type_display"><?php echo $transfer_type; ?></span></td>
                </tr>
                <tr>
                    <th>Total Pax:</th>
                    <td><span id="total_pax_display"><?php echo $total_pax; ?></span></td>
                </tr>
            </table>
        </div>

        <div class="right-column">
            <table class="table table-bordered">
                <tr>
                    <th> Destination:</th>
                    <td><span id="return_point_display"><?php echo $return_point; ?></span></td>
                </tr>
                <?php if ($transfer_type == "Arrival-Return") : ?>
                <tr>
                    <th>Return Date:</th>
                    <td><span id="return_date_display"><?php echo $return_date; ?></span></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>Total Price:</th>
                    <td><span id="total_price_display"><?php echo $currency . ' ' . $total_price; ?></span></td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        <h3>Passengers</h3>
        <table class="table table-bordered" id="passengers_table">
            <tr>
                <th>Name</th>
            </tr>
            <?php foreach ($passenger_data as $passenger) : ?>
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
                <p><span class="belge">Belge No:</span> <span class="number">1079553</span></p>
            </div>
        </div>
    </div>

    <button id="downloadBtn">Download as PDF</button>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <script>
        function downloadAsPDF() {
            const element = document.getElementById('invoiceContent');
            const companyName = document.getElementById('company_name_display').textContent;
            const fileName = `Transfer Invoice - ${companyName}.pdf`;

            const opt = {
                margin: 0,
                filename: fileName,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, logging: true, dpi: 192, letterRendering: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            const images = document.images;
            const totalImages = images.length;
            let loadedImages = 0;

            function checkImagesLoaded() {
                loadedImages++;
                if (loadedImages === totalImages) {
                    html2pdf().set(opt).from(element).save().catch(error => {
                        console.error('Error generating PDF:', error);
                    });
                }
            }

            if (totalImages > 0) {
                for (let i = 0; i < totalImages; i++) {
                    if (images[i].complete) {
                        checkImagesLoaded();
                    } else {
                        images[i].addEventListener('load', checkImagesLoaded);
                        images[i].addEventListener('error', checkImagesLoaded);
                    }
                }
            } else {
                html2pdf().set(opt).from(element).save().catch(error => {
                    console.error('Error generating PDF:', error);
                });
            }
        }

        document.getElementById('downloadBtn').addEventListener('click', downloadAsPDF);
    </script>
</body>

</html>