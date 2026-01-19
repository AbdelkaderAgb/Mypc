<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Generate a unique transaction ID
    $transaction_id = 'CY-' . substr(uniqid(), -8);
    
    // Safely retrieve and sanitize POST data
    $company_name = isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : '';
    $amount = isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : '';
    $currency = isset($_POST['currency']) ? htmlspecialchars($_POST['currency']) : '';
    $reason = isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '';
    $date = isset($_POST['date']) ? htmlspecialchars($_POST['date']) : '';
    $payment_method = isset($_POST['payment_method']) ? htmlspecialchars($_POST['payment_method']) : '';
    $received_by = isset($_POST['received_by']) ? htmlspecialchars($_POST['received_by']) : '';
    $remaining_amount = isset($_POST['remaining_amount']) ? htmlspecialchars($_POST['remaining_amount']) : '';

    // Currency mapping for display
    $currency_codes = [
        '$' => 'USD',
        '€' => 'EUR',
        '₺' => 'TRY',
        'د.ج' => 'DZD'
    ];
    $currency_display = isset($currency_codes[$currency]) 
                        ? $currency_codes[$currency] . " ($currency)" 
                        : $currency;

    // Validation for required fields
    if (empty($company_name) || empty($amount) || empty($currency) || empty($reason) || empty($date) || empty($payment_method) || empty($received_by)) {
        die("Error: Missing required fields.");
    }
    
    // Numeric validation for amount and remaining_amount
    if (!is_numeric($amount)) {
        die("Error: Amount must be a number.");
    }
    if (!empty($remaining_amount) && !is_numeric($remaining_amount)) {
        die("Error: Remaining Amount must be a number.");
    }
    
    // Date validation and formatting
    $date_formatted = strtotime($date);
    if ($date_formatted === false) {
        die("Error: Invalid date format.");
    }
    $date = date('F j, Y', $date_formatted);
} else {
    die("Error: Invalid request method.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt Voucher</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', sans-serif;
            background: #f8f9fa;
            margin: 0;
        }

        .a4-wrapper {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .container {
            width: 100%;
            padding: 15mm;
            background: white;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .watermark {
            position: absolute;
            opacity: 0.1;
            font-size: 80pt;
            transform: rotate(-45deg);
            top: 40%;
            left: 20%;
            z-index: 0;
            color: #2c3e50;
            font-family: 'Montserrat', sans-serif;
            pointer-events: none;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 8mm;
            margin-bottom: 10mm;
            border-bottom: 3px solid #3498db;
            max-height: 40mm;
        }

        .logo img {
            height: 25mm;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.1));
        }

        .voucher-title {
            text-align: center;
            flex-grow: 1;
        }

        .voucher-title h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 20pt;
            margin: 0;
            color: #2c3e50;
            letter-spacing: 2px;
            font-weight: 700;
            text-transform: uppercase;
            padding-bottom: 5px;
        }

        .voucher-title h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: #3498db;
        }

        .details table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 10mm;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        th, td {
            padding: 12px;
            text-align: left;
            font-size: 11pt;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: #ffffff;
            font-weight: 600;
            width: 35%;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        td {
            background: #ffffff;
            font-weight: 500;
            color: #34495e;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .footer {
            padding-top: 8mm;
            border-top: 3px solid #3498db;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            max-height: 30mm;
        }

        .footer-contact p {
            margin: 5px 0;
            font-size: 9pt;
            color: #7f8c8d;
        }

        .footer-logo img {
            height: 15mm;
            margin-bottom: 3px;
        }

        .footer-logo p {
            font-size: 8pt;
            color: #7f8c8d;
            text-align: right;
        }

        .company-info {
            text-align: right;
            font-size: 9pt;
            color: #7f8c8d;
        }

        #downloadBtn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        @media print {
            body, html {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: #fff;
            }
            .a4-wrapper {
                width: 210mm;
                height: 297mm;
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 15mm;
            }
            #downloadBtn {
                display: none;
            }
            .header, .footer {
                width: 100%;
                overflow: hidden;
            }
        }
    </style>
</head>
<body>
    <button id="downloadBtn" onclick="downloadAsPDF()">Download PDF</button>
    
    <div class="a4-wrapper" id="voucherContent">
        <div class="watermark">CYNTURIZM</div>
        <div class="container">
            <div class="header">
                <div class="logo">
                    <img src="logo.png" alt="Company Logo">
                </div>
                <div class="voucher-title">
                    <h2>Official Receipt</h2>
                    <div class="text-muted text-center mt-2" style="font-size: 10pt;">
                        Transaction ID: <?php echo $transaction_id; ?>
                    </div>
                </div>
                <div class="company-info">
                    <div>34093 Fatih/İstanbul</div>
                    <div>www.cyntourism.info</div>
                </div>
            </div>

            <div class="details">
                <table>
                    <tr>
                        <th>Company Name:</th>
                        <td><?php echo $company_name; ?></td>
                    </tr>
                    <tr>
                        <th>Amount:</th>
                        <td><?php echo $currency_display . ' ' . number_format($amount, 2); ?></td>
                    </tr>
                    <tr>
                        <th>Reason:</th>
                        <td><?php echo $reason; ?></td>
                    </tr>
                    <tr>
                        <th>Date:</th>
                        <td><?php echo $date; ?></td>
                    </tr>
                    <tr>
                        <th>Payment Method:</th>
                        <td><?php echo $payment_method; ?></td>
                    </tr>
                    <tr>
                        <th>Received By:</th>
                        <td><?php echo $received_by; ?></td>
                    </tr>
                    <?php if (!empty($remaining_amount)): ?>
                    <tr>
                        <th>Remaining Amount:</th>
                        <td><?php echo $currency_display . ' ' . number_format($remaining_amount, 2); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>

            <div class="footer">
                <div class="footer-contact">
                    <p><i class="fas fa-map-marker-alt"></i> Molla Gürani, Karakoyunlu Sokağı No:2 D:4, 34093 Fatih/İstanbul</p>
                    <p><i class="fas fa-phone"></i> +90 531 817 6770</p>
                    <p><i class="fas fa-envelope"></i> info@cyntour.com</p>
                </div>
                <div class="footer-logo">
                    <img src="footer-logo.png" alt="Footer Logo">
                    <p><span style="font-weight:600">Tursab Belge No:</span> <span style="color:#e74c3c">11738</span></p>
                    <p>Issued: <?php echo date('M d, Y H:i'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        function downloadAsPDF() {
            const transactionId = '<?php echo $transaction_id; ?>';
            const element = document.getElementById('voucherContent');
            const opt = {
                margin: 0,
                filename: `Receipt_${transactionId}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    width: 210 * 3.78,
                    height: 297 * 3.78
                },
                jsPDF: { 
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait',
                    precision: 16
                }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>