<?php 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $letter_date = htmlspecialchars($_POST['letter_date']);
    $subject = htmlspecialchars($_POST['subject']);
    $letter_content = htmlspecialchars($_POST['letter_content']);
    
    // User-editable recipient information
    $recipient_name = htmlspecialchars($_POST['recipient_name']);
    $recipient_company = htmlspecialchars($_POST['recipient_company']);
    $recipient_address = htmlspecialchars($_POST['recipient_address']);
    
    // Fixed values for signatory
    $signatory_name = "Cüneyt Yedikardeş";
    $signatory_title = "General Manager";
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
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
    width: 100%;
    padding: 15mm;
    background: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1;
    min-height: 297mm;
    box-sizing: border-box;
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
    padding-bottom: 15mm;
    margin-bottom: 20mm;
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
    margin-bottom: 30px;
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
    margin-bottom: 30px;
    padding-left: 15px;
    border-left: 3px solid var(--gold-light);
    font-family: 'Montserrat', sans-serif;
}

.recipient-info strong {
    color: #2c3e50;
    font-weight: 600;
    font-size: 14pt;
}

.recipient-info div {
    margin-bottom: 5px;
}

/* Letter Subject */
.letter-subject {
    margin: 30px 0;
    font-weight: 600;
    font-size: 14pt;
    color: #2c3e50;
    position: relative;
    padding: 10px 25px;
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
    margin-top: 50px;
    text-align: right;
    position: relative;
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

.form-title {
    color: var(--gold-dark);
    margin-bottom: 30px;
    text-align: center;
    font-weight: bold;
}

/* Button Styles */
.btn-primary {
    background: var(--gold-dark);
    border-color: var(--gold-dark);
    padding: 10px 30px;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
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
    <?php if ($_SERVER["REQUEST_METHOD"] != "POST"): ?>
    <div class="form-container">
        <h2 class="form-title">Create Cyntour Letterhead</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="recipient_name">Recipient Name:</label>
                <input type="text" class="form-control" id="recipient_name" name="recipient_name" required value="Dear Client">
            </div>
            <div class="form-group">
                <label for="recipient_company">Recipient Company:</label>
                <input type="text" class="form-control" id="recipient_company" name="recipient_company" required value="Cyntour Customer">
            </div>
            <div class="form-group">
                <label for="recipient_address">Recipient Address:</label>
                <textarea class="form-control" id="recipient_address" name="recipient_address" required>Istanbul, Turkey</textarea>
            </div>
            <div class="form-group">
                <label for="letter_date">Letter Date:</label>
                <input type="date" class="form-control" id="letter_date" name="letter_date" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label for="subject">Subject/Title:</label>
                <input type="text" class="form-control" id="subject" name="subject" required placeholder="Enter letter subject">
            </div>
            <div class="form-group">
                <label for="letter_content">Letter Content:</label>
                <textarea class="form-control" id="letter_content" name="letter_content" rows="10" required placeholder="Enter your letter content here..."></textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Generate Letterhead</button>
            </div>
        </form>
    </div>
    <?php else: ?>
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
                <div class="letter-date"><?php echo date('F j, Y', strtotime($letter_date)); ?></div>
            </div>

            <div class="recipient-info">
                <div><strong><?php echo $recipient_name; ?></strong></div>
                <div><?php echo $recipient_company; ?></div>
                <div><?php echo nl2br($recipient_address); ?></div>
            </div>

            <div class="letter-subject">
                <?php echo $subject; ?>
            </div>

            <div class="letter-body">
                <?php echo nl2br($letter_content); ?>
            </div>

            <div class="signature-section">
                <div class="signature">
                    <img src="singateur.png" alt="Signature">
                </div>
                <div class="signatory-name"><?php echo $signatory_name; ?></div>
                <div class="signatory-title"><?php echo $signatory_title; ?></div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        function downloadAsPDF() {
            const element = document.getElementById('letterContent');
            const opt = {
                margin: 0,
                filename: `Cyntour_Letter_${Date.now()}.pdf`,
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

            // Remove background pattern temporarily
            const originalBackground = document.body.style.background;
            document.body.style.background = 'none';

            // Generate PDF
            html2pdf().set(opt).from(element).save().then(() => {
                // Restore background pattern
                document.body.style.background = originalBackground;
            });
        }
    </script>
</body>
</html>