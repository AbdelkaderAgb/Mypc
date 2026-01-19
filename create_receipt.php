<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'database.php'; // or 'db.php', ensure this filename is correct
require_once 'helpers.php';

// If we are rendering a saved receipt, populate variables from the passed data
if (isset($receipt_data)) {
    // This flag tells the page to render the letter instead of the form
    $render_letter = true; 

    // --- FIX STARTS HERE: The logic in this block has been corrected ---

    // Populate variables from the data provided by the viewer page
    $letter_date          = $receipt_data['receipt_date'];
    $subject              = $receipt_data['subject'];
    $letter_content       = $receipt_data['content'];
    $total                = $receipt_data['total'];
    $received_by          = $receipt_data['received_by'];
    $recipient_name       = $received_by;
    $partner_company_name = $receipt_data['partner_company'] ?? '';
    $recipient_company    = $partner_company_name;
    $recipient_address    = $receipt_data['partner_address'] ?? '';
    $signatory_name       = "Cüneyt Yedikardeş";
    $signatory_title      = "General Manager";

    // **CORRECTED LOGIC**: Get payments directly from the data that was passed in.
    // We REMOVED the database query from this file because the viewer page already fetched the data.
    $payments_for_render = $receipt_data['payments'] ?? [];

    // When rendering, get the money provider from the first payment record (if it exists)
    $money_provider = ''; // Default to empty
    if (!empty($payments_for_render)) {
        $money_provider = $payments_for_render[0]['money_provider'] ?? '';
    }
    
    // --- FIX ENDS HERE ---

} else {
    // This is for the 'create' page context, initialize variables for the form
    $render_letter     = false;
    $letter_date       = date('Y-m-d');
    $subject           = '';
    $letter_content    = '';
    $recipient_address = '';
    $total             = 0;
    $currency          = 'USD'; // Default currency
    $received_by       = '';
    $money_provider    = '';
    $partner_company_name = '';
    $signatory_name    = "Cüneyt Yedikardeş";
    $signatory_title   = "General Manager";
}

// Fetch all partners for the dropdown (needed for the 'create' form)
$partners_stmt = $pdo->query("SELECT id, company FROM partners ORDER BY company");
$partners = $partners_stmt->fetchAll();

// Company branding details
$company_name = 'Cyntour';
$company_address = 'Istanbul, Turkey';
$company_logo = 'logo.png'; 

// Logic for handling the form submission to CREATE a new receipt
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $partner_id = $_POST['partner_id'];
    $new_company_name = trim($_POST['new_company_name']);

    if (!empty($new_company_name)) {
        $sql = "INSERT INTO partners (company) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_company_name]);
        $partner_id = $pdo->lastInsertId();
    } elseif (empty($partner_id)) {
        die('Please select a company or enter a new company name.');
    }

    $letter_date_str   = $_POST['letter_date'] ?? date('d/m/y');
    $letter_date       = DateTime::createFromFormat('d/m/y', $letter_date_str)->format('Y-m-d');
    $subject           = trim($_POST['subject'] ?? '');
    $letter_content    = trim($_POST['letter_content'] ?? '');
    $received_by       = trim($_POST['received_by'] ?? '');
    $recipient_name    = $received_by;

    if (!empty($new_company_name)) {
        $partner_company_name = $new_company_name;
    } else {
        $stmt = $pdo->prepare("SELECT company FROM partners WHERE id = ?");
        $stmt->execute([$partner_id]);
        $partner_company_name = $stmt->fetchColumn();
    }

    $payments = $_POST['payments'] ?? [];
    $total_amount = 0;
    foreach ($payments as $payment) {
        $total_amount += floatval($payment['amount']);
    }

    try {
        $pdo->beginTransaction();

        $insRec = $pdo->prepare("INSERT INTO receipts (partner_id, receipt_date, subject, content, total, received_by) VALUES (?,?,?,?,?,?)");
        $insRec->execute([$partner_id, $letter_date, $subject, $letter_content, $total_amount, $received_by]);
        $receipt_id = $pdo->lastInsertId();

        $money_provider = trim($_POST['money_provider'] ?? '');

        $insPayment = $pdo->prepare("INSERT INTO receipt_payments (receipt_id, amount, currency, money_provider) VALUES (?, ?, ?, ?)");
        foreach ($payments as $payment) {
            if (!empty($payment['amount'])) {
                $insPayment->execute([
                    $receipt_id,
                    floatval($payment['amount']),
                    $payment['currency'],
                    $money_provider
                ]);
            }
        }

        $pdo->commit();
        $render_letter = true; 

        // For rendering the newly created receipt, prepare the data
        $payments_for_render = $payments;
        $total = $total_amount;

    } catch (Exception $e) {
        $pdo->rollBack();
        die('Error saving data: ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyntour Letterhead</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* All your CSS styles from the original file go here... */
        /* I am omitting the large CSS block for brevity, but you should keep it. */
        :root {
            --gold-light: rgba(212, 175, 55, 0.3);
            --gold-medium: rgba(212, 175, 55, 0.7);
            --gold-dark: rgb(184, 151, 46);
        }

        @page {
            size: A4;
            margin: 0;
        }

        /* Basic Page Setup */
        body {
            font-family: 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 0;
            width: 210mm;
            height: 297mm;
            background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%, transparent 75%, #f8f9fa 75%),
                linear-gradient(45deg, #f8f9fa 25%, transparent 25%, transparent 75%, #f8f9fa 75%);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            position: relative;
        }

        .container {
            width: 210mm; /* A4 width */
            height: 297mm; /* A4 height */
            padding: 15mm;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
            box-sizing: border-box;
            overflow: hidden; /* Prevent content from spilling over */
        }

        .letter-container {
            padding: 10mm 25mm 15mm 25mm; /* Compacted vertical padding */
            position: relative;
            min-height: 297mm; /* A4 height */
            display: flex;
            flex-direction: column;
        }

        /* Watermark */
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

        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 2mm;
            margin-bottom: 4mm; /* Compacted */
            border-bottom: none;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(to right, 
                rgba(212, 175, 55, 0.1), 
                rgba(212, 175, 55, 0.7) 50%,
                rgba(212, 175, 55, 0.1));
        }

        /* Logo Styles */
        .logo {
            position: relative;
            padding: 10px;
        }

        .logo::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: radial-gradient(circle at center, rgba(212, 175, 55, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
        }

        .logo img {
            height: 120px;
            filter: drop-shadow(2px 2px 6px rgba(0,0,0,0.15));
            transition: all 0.3s ease;
        }

        /* Company Info */
        .company-info {
            text-align: right;
            color: #2c3e50;
            position: relative;
            padding-right: 15px;
        }

        .company-info::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            width: 3px;
            background: linear-gradient(to bottom, 
                transparent, 
                rgba(212, 175, 55, 0.5) 50%,
                transparent);
        }

        .company-info-item {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-bottom: 8px;
            font-size: 11pt;
            letter-spacing: 0.5px;
            font-family: 'Montserrat', sans-serif;
        }

        .company-info-icon {
            color: var(--gold-dark);
            margin-left: 10px;
            font-size: 14px;
        }

        /* Letter Content */
        .letter-content {
            margin-bottom: 40px;
            line-height: 1.6;
            color: #34495e;
            position: relative;
            z-index: 1;
        }

        .letter-heading {
            margin-bottom: 15px; /* Compacted */
            position: relative;
        }

        .letter-date {
            font-family: 'Montserrat', sans-serif;
            color: #555;
            display: inline-block;
            padding: 8px 15px;
            border-left: 3px solid var(--gold-light);
            background: rgba(212, 175, 55, 0.05);
            float: right;
            letter-spacing: 0.5px;
        }

        /* Recipient Info */
        .recipient-info {
            margin-bottom: 10px; /* Compacted */
            padding-bottom: 5px; /* Compacted */
            border-bottom: 1px solid #eee;
            font-size: 11pt;
        }

        .recipient-info p {
            margin: 2px 0; /* Reduced */
            line-height: 1.6;
        }
        .recipient-info p strong {
            display: inline-block;
            width: 120px;
            color: #555;
        }

        /* Letter Subject */
        .letter-subject {
            margin-top: 8mm; /* Compacted */
            margin-bottom: 4mm; /* Compacted */
            font-size: 12pt;
            font-weight: bold;
            position: relative;
            padding: 8px 25px; /* Compacted */
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(to right, rgba(212, 175, 55, 0.05), transparent);
            border-radius: 4px;
        }

        .letter-subject::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 3px;
            background: var(--gold-dark);
        }

        /* Letter Body */
        .letter-body {
            text-align: justify;
            position: relative;
            font-size: 11pt;
            line-height: 1.7;
        }

        .letter-body p:first-of-type {
            margin-bottom: 15px; /* Compacted */
        }

        .payments-heading {
            font-family: 'Playfair Display', serif;
            font-size: 14pt;
            margin-top: 20px; /* Compacted */
            margin-bottom: 10px; /* Compacted */
            border-bottom: 2px solid var(--gold-light);
            padding-bottom: 8px; /* Compacted */
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px; /* Compacted */
            font-size: 10pt;
        }

        .payments-table th, .payments-table td {
            border: 1px solid #e0e0e0;
            padding: 6px 8px; /* Compacted */
            text-align: left;
        }

        .payments-table th {
            background-color: #f9f9f9;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }

        .grand-total-container {
            text-align: right;
            margin-top: 15px; /* Compacted */
            font-size: 12pt;
            font-family: 'Playfair Display', serif;
        }

        .letter-body p:first-of-type::first-letter {
            font-size: 200%;
            font-weight: bold;
            color: var(--gold-dark);
            float: left;
            line-height: 1;
            margin-right: 8px;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 8mm; /* Compacted */
            text-align: right;
            flex-shrink: 0;
            margin-bottom: 10mm; /* Compacted */
        }

        .signature-section::before {
            content: '';
            position: absolute;
            top: -20px;
            right: 0;
            width: 30%;
            height: 1px;
            background: linear-gradient(to left, var(--gold-medium), transparent);
        }

        .signature img {
            height: 170px;
            margin-bottom: 10px;
            filter: drop-shadow(1px 1px 2px rgba(0,0,0,0.1));
        }

        .signatory-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 12pt;
            margin-bottom: 5px;
            font-family: 'Montserrat', sans-serif;
        }

        .signatory-title {
            color: #7f8c8d;
            font-size: 10pt;
            font-style: italic;
        }

        /* Footer */
        .document-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px 25mm 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, 
                rgba(212, 175, 55, 0.08), 
                rgba(212, 175, 55, 0.02));
            border-top: 1px solid rgba(212, 175, 55, 0.3);
            z-index: 2;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .footer-logo-container {
            border-right: 1px solid rgba(212, 175, 55, 0.3);
            padding-right: 25px;
            margin-right: 15px;
            position: relative;
            overflow: hidden;
        }

        .footer-logo {
            height: 40px;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
        }

        .footer-contact-grid {
            display: grid;
            grid-template-columns: repeat(1, auto);
            gap: 12px 30px;
        }

        .footer-contact-item {
            font-size: 10px;
            color: #555;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .footer-contact-icon {
            color: var(--gold-medium);
            font-size: 11px;
        }

        /* Certificate Section */
        .certificate-container {
            background: rgba(212, 175, 55, 0.07);
            border-left: 3px solid var(--gold-light);
            border-radius: 6px;
            padding: 8px 15px;
            transition: all 0.3s ease;
        }

        .certificate-container:hover {
            background: rgba(212, 175, 55, 0.12);
            transform: translateY(-2px);
        }

        .certificate-mini {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .certificate-number {
            color: var(--gold-dark);
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 1px;
        }

        .generation-info {
            font-size: 9px;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }

        /* Form Styles */
        .form-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-container-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 15px;
            background: #fdfaf6; /* Warmer off-white from dashboard */
            font-family: 'Inter', sans-serif;
        }

        .form-container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            max-width: 900px;
            width: 100%;
            border: 1px solid #eee;
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            color: #c5a47e; /* --primary-gold */
            margin-bottom: 35px;
            text-align: center;
            font-weight: 700;
            font-size: 2.25rem;
        }

        .form-group label {
            font-weight: 600;
            color: #555;
            margin-bottom: .5rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: #c5a47e;
            box-shadow: 0 0 0 0.2rem rgba(197, 164, 126, 0.25);
        }

        hr {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        /* Button Styles */
        .btn-gradient {
            background-image: linear-gradient(to right, #e6c9a7, #c5a47e);
            border: none;
            color: white;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 8px;
            transition: opacity 0.3s ease;
            width: 100%;
            font-size: 1.1rem;
        }

        .btn-gradient:hover {
            opacity: 0.9;
        }

        .btn-primary:hover {
            background: rgb(155, 126, 36);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
        }

        /* Download Button */
        #downloadBtn {
            background: var(--gold-dark);
            padding: 12px 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            position: sticky;
            top: 20px;
            margin: 20px auto;
            display: block;
            z-index: 1000;
        }

        /* Media Queries */
        @media screen and (max-width: 768px) {
            #downloadBtn {
                width: calc(100% - 40px);
                margin: 20px;
                z-index: 1000;
                margin-bottom: 30px;
            }

            .container {
                padding: 10mm;
            }
        }

        /* Print Styles */
        @media print {
            #downloadBtn {
                display: none;
            }
            
            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: none;
            }
            
            .container {
                width: 210mm;
                min-height: 297mm;
                padding: 15mm;
                margin: 0;
                box-shadow: none;
                background: white;
            }
        }
    </style>
</head>

<body>
    <?php if (!$render_letter): ?>
    <!-- The HTML form for creating a new receipt -->
    <div class="form-container-wrapper">
        <div class="form-container">
            <h2 class="form-title">Create Receipt</h2>
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="partner_id">Select Existing Company</label>
                        <select class="form-control" id="partner_id" name="partner_id">
                            <option value="">-- Or Add a New Company Below --</option>
                            <?php foreach ($partners as $partner): ?>
                                <option value="<?= $partner['id'] ?>"><?= htmlspecialchars($partner['company']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="new_company_name">Or Add New Company</label>
                        <input type="text" class="form-control" id="new_company_name" name="new_company_name" placeholder="e.g., New Tech Inc.">
                    </div>
                </div>
                <hr>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="letter_date">Receipt Date</label>
                        <input type="text" class="form-control" id="letter_date" name="letter_date" value="<?php echo date('d/m/y'); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="received_by">Received By</label>
                        <input type="text" class="form-control" id="received_by" name="received_by" value="<?php echo htmlspecialchars($received_by); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="money_provider">Money Provider/Payer</label>
                    <input type="text" class="form-control" id="money_provider" name="money_provider" value="<?php echo htmlspecialchars($money_provider); ?>" placeholder="e.g., John Doe, XYZ Corp">
                </div>
                <hr>
                <div class="form-group">
                    <label>Payments</label>
                    <div id="payments-container">
                        <div class="payment-entry">
                            <div class="form-row">
                                <div class="form-group col-md-7">
                                    <input type="number" step="0.01" class="form-control" name="payments[0][amount]" placeholder="Amount" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <select class="form-control" name="payments[0][currency]">
                                        <option value="USD">$</option>
                                        <option value="EUR">€</option>
                                        <option value="TRY">₺</option>
                                        <option value="GBP">£</option>
                                        <option value="DZD">DA</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger w-100 remove-payment" style="display:none;">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-payment" class="btn btn-secondary btn-sm mt-2">Add Another Payment</button>
                </div>
                <hr>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required placeholder="e.g., Q3 Services Rendered">
                </div>
                <div class="form-group">
                    <label for="letter_content">Content / Description</label>
                    <textarea class="form-control" id="letter_content" name="letter_content" rows="5" required placeholder="Describe the transaction or service details here..."></textarea>
                </div>
                <button type="submit" class="btn btn-gradient mt-4">Generate Receipt</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <!-- The HTML for rendering the receipt -->
    <div class="watermark">CYNTOUR</div>
    <div class="container" id="letterContent">
        <div class="header">
            <div class="logo">
                <img src="logo.png" alt="Cyntour Logo">
            </div>
            <div class="company-info">
                <div class="company-info-item">
                    <span>info@cyntour.com</span>
                    <i class="fas fa-envelope company-info-icon"></i>
                </div>
                <div class="company-info-item">
                    <span>+90 531 817 6770</span>
                    <i class="fas fa-phone company-info-icon"></i>
                </div>
                <div class="company-info-item">
                    <span>www.cyntourism.info</span>
                    <i class="fas fa-globe company-info-icon"></i>
                </div>
            </div>
        </div>

        <div class="letter-content">
            <div class="letter-heading">
                <div class="letter-date"><strong>Date:</strong> <?= date('d/m/y', strtotime($letter_date)) ?></div>
            </div>
            <div class="recipient-info">
                <p><strong>Company Name:</strong><span><?php echo htmlspecialchars($partner_company_name); ?></span></p>
                <p><strong>Received By:</strong><span><?php echo htmlspecialchars($received_by); ?></span></p>
                <p><strong>Money Provider:</strong><span><?php echo htmlspecialchars($money_provider); ?></span></p>
            </div>
            <div class="letter-subject">
                <strong>Subject:</strong> <?php echo htmlspecialchars($subject); ?>
            </div>
            <div class="letter-body">
                <p><?php echo nl2br(htmlspecialchars($letter_content)); ?></p>
                <?php if (!empty($payments_for_render)): ?>
                <h5 class="payments-heading">Payment Transaction Details</h5>
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th style="text-align: right;">Amount</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grand_total_by_currency = [];
                        foreach ($payments_for_render as $p):
                            $amount = $p['amount'] ?? 0;
                            $currency = $p['currency'] ?? 'USD';
                            if (!isset($grand_total_by_currency[$currency])) {
                                $grand_total_by_currency[$currency] = 0;
                            }
                            $grand_total_by_currency[$currency] += $amount;
                        ?>
                        <tr>
                            <td style="text-align: right;"><?php echo number_format($amount, 2); ?></td>
                            <td><?php echo htmlspecialchars($currency); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="grand-total-container">
                    <strong>Total Received:</strong>
                    <?php 
                    $total_strings = [];
                    foreach ($grand_total_by_currency as $currency => $total) {
                        $total_strings[] = '<strong>' . number_format($total, 2) . ' ' . htmlspecialchars($currency) . '</strong>';
                    }
                    echo implode(' + ', $total_strings);
                    ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="signature-section">
                <div class="signature">
                    <img src="singateur.png" alt="Signature">
                </div>
                <div class="signatory-name"><?php echo htmlspecialchars($signatory_name); ?></div>
                <div class="signatory-title"><?php echo htmlspecialchars($signatory_title); ?></div>
            </div>
        </div>
        <div class="document-footer">
            <div class="footer-left">
                <div class="footer-logo-container">
                    <img src="footer-logo.png" alt="Cyntour" class="footer-logo">
                </div>
                <div class="footer-contact-grid">
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt footer-contact-icon"></i>
                        <span>Molla Gürani, Karakoyunlu Sokağı No:2 D:4</span>
                    </div>
                </div>
            </div>
            <div class="footer-right">
                <div class="certificate-container">
                    <div class="certificate-mini">TURSAB BELGE NO</div>
                    <div class="certificate-number">11738</div>
                    <div class="generation-info">Generated: <?php echo date('M d, Y H:i'); ?></div>
                </div>
            </div>
        </div>
    </div>
    <button id="downloadBtn" onclick="downloadAsPDF()">Download PDF</button>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>
    <script>
        // All your JavaScript from the original file goes here...
        // I am omitting the large JS block for brevity, but you should keep it.
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#letter_date", {
                dateFormat: "d/m/y",
            });

            let paymentIndex = 1;
            const container = document.getElementById('payments-container');

            document.getElementById('add-payment').addEventListener('click', function() {
                const newPayment = document.createElement('div');
                newPayment.className = 'payment-entry';
                newPayment.innerHTML = `
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <input type="number" step="0.01" class="form-control" name="payments[${paymentIndex}][amount]" placeholder="Amount" required>
                        </div>
                        <div class="form-group col-md-3">
                            <select class="form-control" name="payments[${paymentIndex}][currency]">
                                <option value="USD">$</option>
                                <option value="EUR">€</option>
                                <option value="TRY">₺</option>
                                <option value="GBP">£</option>
                                <option value="DZD">DA</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger w-100 remove-payment">Remove</button>
                        </div>
                    </div>
                `;
                container.appendChild(newPayment);
                paymentIndex++;
                updateRemoveButtons();
            });

            container.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-payment')) {
                    e.target.closest('.payment-entry').remove();
                    updateRemoveButtons();
                }
            });

            function updateRemoveButtons() {
                const removeButtons = container.querySelectorAll('.remove-payment');
                if (removeButtons.length > 1) {
                    removeButtons.forEach(btn => btn.style.display = 'inline-block');
                } else {
                    removeButtons.forEach(btn => btn.style.display = 'none');
                }
            }
            updateRemoveButtons();

            // Logic to toggle the 'Add New Company' field
            const partnerSelect = document.getElementById('partner_id');
            const newCompanyGroup = document.getElementById('new_company_name').closest('.form-group');
            function toggleNewCompanyField() {
                if(newCompanyGroup) { // Check if the element exists (it won't in render mode)
                    if (partnerSelect.value) {
                        newCompanyGroup.style.display = 'none';
                        document.getElementById('new_company_name').value = '';
                    } else {
                        newCompanyGroup.style.display = 'block';
                    }
                }
            }
            if(partnerSelect) {
                partnerSelect.addEventListener('change', toggleNewCompanyField);
                toggleNewCompanyField();
            }
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        function downloadAsPDF() {
            const element = document.getElementById('letterContent');
            const opt = {
                margin: 0,
                filename: (() => {
                    let companyName = "<?php echo addslashes($partner_company_name); ?>";
                    let moneyProvider = "<?php echo addslashes($money_provider); ?>";
                    let baseName = 'receipt';

                    if (companyName.trim()) {
                        baseName = companyName.trim();
                    } else if (moneyProvider.trim()) {
                        baseName = moneyProvider.trim();
                    }
                    
                    return baseName.replace(/[^a-z0-9_\-]/gi, '_').toLowerCase() + '.pdf';
                })(),
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    letterRendering: true,
                    scrollY: 0,
                    windowWidth: element.offsetWidth,
                    windowHeight: element.offsetHeight
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait',
                    compress: true,
                    precision: 16
                }
            };

            document.getElementById('downloadBtn').style.display = 'none';
            const originalBackground = document.body.style.background;
            document.body.style.background = 'none';

            html2pdf().set(opt).from(element).save().then(() => {
                document.body.style.background = originalBackground;
                document.getElementById('downloadBtn').style.display = 'block';
            });
        }
    </script>
</body>
</html>