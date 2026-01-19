<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYN Voucher</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        /* --- SCREEN LAYOUT FIXES START HERE --- */
        body {
            font-family: 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 20px; /* Add padding around the container */
            box-sizing: border-box;
            background-color: #e9ecef; /* A neutral background to make the page stand out */
            
            /* Use flexbox to center the voucher on screen */
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            width: 210mm;
            /* For SCREEN: height is auto so it fits the content perfectly */
            height: auto; 
            /* For SCREEN: use min-height to resemble the page, but prevent excessive empty space */
            min-height: 297mm; 
            margin: 0; /* Margin is handled by body padding */
            padding: 15mm;
            background-color: white;
            box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* A more pronounced shadow for depth */

            display: flex;
            flex-direction: column;
        }
        /* --- SCREEN LAYOUT FIXES END HERE --- */

        .main-content {
            flex-grow: 1; /* Pushes footer down, essential for both screen and print */
        }
        
        .footer {
            border-top: 2px solid #ddd;
            padding-top: 5mm;
            font-size: 10pt;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-shrink: 0; 
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
            font-weight: bold;
        }

        .voucher-no span {
            color: red;
        }

        .details { margin-bottom: 10mm; }
        .details p { margin: 0; padding: 2mm 0; font-size: 12pt; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10mm; }
        th, td { border: 1px solid #ddd; padding: 3mm; text-align: left; font-size: 11pt; vertical-align: top; }
        th { background-color: #f2f2f2; width: 25%; }
        h3 { font-size: 16pt; margin: 10mm 0 5mm; color: #333; }

        .note { color: red; margin-top: 10mm; font-size: 10pt; border: 1px solid #ffdddd; background-color: #fff5f5; padding: 3mm; border-radius: 4px; }
        .note p { margin: 0; }

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
            z-index: 1000;
        }

        .footer p { margin: 1mm 0; }
        .footer .fas { width: 15px; text-align: center; margin-right: 5px; }
        .footer-logo { text-align: right; }
        .footer-logo img { height: 50px; }
        .footer-logo p { margin-top: 2mm; font-size: 10pt; }
        .footer-logo .belge { font-weight: bold; color: black; }
        .footer-logo .number { font-weight: bold; color: red; }

        /* --- DEDICATED PRINT STYLES START HERE --- */
        @media print {
            body {
                /* Reset screen styles for printing */
                background-color: white;
                display: block;
                padding: 0;
            }

            .container {
                /* Restore the RIGID A4 height ONLY for printing/PDF generation */
                height: 297mm;
                /* Remove screen-only styles */
                margin: 0;
                box-shadow: none;
                min-height: 0; /* Reset min-height */
                page-break-after: always;
            }

            #downloadBtn {
                display: none;
            }
        }
        /* --- DEDICATED PRINT STYLES END HERE --- */
    </style>
</head>
<body>
    <button id="downloadBtn" onclick="downloadAsPDF()">Download as PDF</button>

    <div class="container" id="voucherContent">
        <!-- Main content wrapper -->
        <div class="main-content">
            <header class="header">
                <div class="logo">
                    <img src="logo.png" alt="CYN TURIZM">
                </div>
                <div class="voucher-title">
                    <h2>Hotel Voucher</h2>
                </div>
                <div class="voucher-no">
                    <p>Voucher No: <span id="voucher_no"></span></p>
                </div>
            </header>
            <div class="details">
                <p><strong>Company Name:</strong> <span id="company_name"></span></p>
            </div>
            <table class="table table-bordered">
                <tr>
                    <th>Hotel:</th>
                    <td><span id="hotel"></span></td>
                    <th>Room Count:</th>
                    <td><span id="room_count"></span></td>
                </tr>
                <tr>
                    <th>Check-in/Check-out:</th>
                    <td><span id="checkin_checkout"></span></td>
                    <th>Adult:</th>
                    <td><span id="adult"></span></td>
                </tr>
                <tr>
                    <th>Room:</th>
                    <td><span id="room"></span></td>
                    <th>Child:</th>
                    <td><span id="child"></span></td>
                </tr>
                <tr>
                    <th>Transfer Type:</th>
                    <td><span id="transfer_type"></span></td>
                    <th>Infant:</th>
                    <td><span id="infant"></span></td>
                </tr>
                <tr>
                    <th>Board:</th>
                    <td><span id="board"></span></td>
                    <th>Total Pax:</th>
                    <td><span id="total_pax"></span></td>
                </tr>
            </table>

            <h3>Customers</h3>
            <table class="table table-bordered" id="customers_table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Title</th>
                        <th style="width: 40%;">Name</th>
                        <th style="width: 40%;">Nationality</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Customer rows will be dynamically added here -->
                </tbody>
            </table>

            <div class="note">
                <p style="font-weight: bold; margin-bottom: 5px;">Attention</p>
                <p>We kindly remind you that the payment must be completed no later than 3 days prior to the reservation check-in date. Once the payment has been made, we would appreciate it if you could send us the payment receipt.</p>
                <p style="margin-top: 1em;">Should you require any further assistance, please do not hesitate to contact us.</p>
            </div>
        </div>
        <!-- End of main content wrapper -->

        <footer class="footer">
            <div class="footer-contact">
                <p><i class="fas fa-map-marker-alt"></i> Molla Gürani, Karakoyunlu Sokağı No:2 D:4, 34093 Fatih/İstanbul</p>
                <p><i class="fas fa-phone"></i> +90 5318176770</p>
                <p><i class="fas fa-envelope"></i> info@cyntour.com</p>
            </div>
            <div class="footer-logo">
                <img src="footer-logo.png" alt="Footer Logo">
                <p><span class="belge">BELGE</span> <span class="number">11738</span></p>
            </div>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // Helper function to format date from YYYY-MM-DD to DD-MM-YYYY
        function formatDate(isoDate) {
            if (!isoDate || isoDate.split('-').length !== 3) return isoDate;
            const [year, month, day] = isoDate.split('-');
            return `${day}-${month}-${year}`;
        }

        function fillVoucher(data) {
            document.getElementById('voucher_no').textContent = data.voucher_no || 'N/A';
            document.getElementById('company_name').textContent = data.company_name || 'N/A';
            document.getElementById('hotel').textContent = data.hotel || 'N/A';
            document.getElementById('room_count').textContent = data.room_count || 'N/A';
            const checkinDate = formatDate(data.accommodation_start);
            const checkoutDate = formatDate(data.accommodation_end);
            document.getElementById('checkin_checkout').textContent = `${checkinDate} - ${checkoutDate} (${data.nights || 'N/A'} Nights)`;
            document.getElementById('room').textContent = data.room || 'N/A';
            document.getElementById('transfer_type').textContent = data.transfer_type || 'N/A';
            document.getElementById('board').textContent = data.board || 'N/A';
            const adultCount = parseInt(data.adult, 10) || 0;
            const childCount = parseInt(data.child, 10) || 0;
            const infantCount = parseInt(data.infant, 10) || 0;
            document.getElementById('adult').textContent = adultCount;
            document.getElementById('child').textContent = childCount;
            document.getElementById('infant').textContent = infantCount;
            document.getElementById('total_pax').textContent = adultCount + childCount + infantCount;
            const customersTableBody = document.querySelector('#customers_table tbody');
            customersTableBody.innerHTML = '';
            if (data.customers && Array.isArray(data.customers) && data.customers.length > 0) {
                data.customers.forEach(customer => {
                    const row = customersTableBody.insertRow(-1);
                    row.insertCell(0).textContent = customer.title || '';
                    row.insertCell(1).textContent = customer.name || '';
                    row.insertCell(2).textContent = customer.nationality || '';
                });
            } else {
                customersTableBody.innerHTML = '<tr><td colspan="3">No customer details provided.</td></tr>';
            }
        }

        function downloadAsPDF() {
            const downloadButton = document.getElementById('downloadBtn');
            downloadButton.textContent = 'Generating PDF...';
            downloadButton.disabled = true;

            const element = document.getElementById('voucherContent');
            const companyName = document.getElementById('company_name').textContent || 'Voucher';
            const voucherNo = document.getElementById('voucher_no').textContent || 'No';
            const filename = `${companyName.trim()}_${voucherNo.trim()}.pdf`;

            const opt = {
                margin: 0,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().from(element).set(opt).save().then(() => {
                downloadButton.textContent = 'Download as PDF';
                downloadButton.disabled = false;
            }).catch(err => {
                console.error("PDF generation failed:", err);
                downloadButton.textContent = 'Download Failed. Try Again.';
                downloadButton.disabled = false;
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const sampleData = {
                voucher_no: "CYN-2024-101", company_name: "Global Travel Inc.", hotel: "Grand Seaside Resort", accommodation_start: "2024-08-15", accommodation_end: "2024-08-22", nights: 7, room_count: "1", room: "Double Room, Sea View", transfer_type: "Private Airport Transfer", board: "All-Inclusive", adult: "2", child: "0", infant: "0", customers: [{ title: "Mr", name: "John Doe", nationality: "American" }, { title: "Mrs", name: "Jane Doe", nationality: "American" }]
            };
            const voucherDataString = localStorage.getItem('voucherData');
            if (voucherDataString) {
                try {
                    const voucherData = JSON.parse(voucherDataString);
                    fillVoucher(voucherData);
                } catch (e) {
                    fillVoucher(sampleData);
                }
            } else {
                fillVoucher(sampleData);
            }
        });
    </script>
</body>
</html>