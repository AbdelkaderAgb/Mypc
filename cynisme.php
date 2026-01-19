<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 210mm;
            height: 297mm;
            background-color: white;
            margin: 0 auto;
            padding: 0;
            box-sizing: border-box;
            position: relative;
        }

        .content {
            padding: 20mm;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10mm 20mm;
            border-bottom: 2px solid #000080;
        }

        .header .logo img {
            width: 150px; /* Increased from 80px */
            height: auto;
        }

        .header h1 {
            margin: 0;
            color: #000080;
            font-size: 36px;
        }

        .header .invoice-number {
            font-size: 18px;
            color: #333;
        }

        .address-section {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .address-section div {
            width: 48%;
        }

        .address-section div p {
            margin: 4px 0;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }

        .invoice-info {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        .invoice-info div {
            width: auto;
        }

        .invoice-info div p {
            margin: 4px 0;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: none;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        table th {
            background-color: #f0f0f0;
            color: #000080;
        }

        .total-section {
            margin-top: 20px;
            text-align: right;
            font-size: 18px;
        }

        .total-section span {
            font-weight: bold;
            color: #000080;
        }

        .footer {
            padding: 10mm 20mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #333;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 2px solid #000080;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer .address {
            text-align: left;
            width: 50%;
        }

        .footer .logo {
            text-align: right;
            width: 50%;
        }

        .footer .logo img {
            width: 80px;
            height: auto;
            margin-bottom: 5px;
        }

        .footer .logo p {
            margin-top: 5px;
            font-size: 12px;
            color: #333;
        }

        .thank-you {
            font-size: 24px;
            color: #000080;
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 40px;
            text-align: left;
            padding-left: 20mm;
            position: relative;
        }

        .signature-section p {
            font-size: 16px;
            margin: 20px 0;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin-top: 20px;
            text-align: right;
        }

        .signature-line span {
            font-size: 14px;
            color: #333;
        }

        .stamp {
            position: absolute;
            left: 0;
            top: -10px;
        }

        .stamp img {
            width: 180px; /* Adjust the size based on your preference */
            height: auto;
        }

        .terms {
            margin-top: 20px;
            font-size: 14px;
            text-align: left;
        }

        @media print {
            body {
                background-color: white;
            }
            .container {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="logo.png" alt="Logo">
            </div>
            <h1>INVOICE</h1>
            <div class="invoice-number">Invoice # PT-001</div>
        </div>

        <div class="content">
            <div class="address-section">
                <div>
                    <p><strong>BILL TO:</strong></p>
                    <p>Rhonda Poulos</p>
                    <p>Rua Antunes 263</p>
                    <p>3730-221 Macieira</p>
                </div>
            </div>

            <div class="invoice-info">
                <div>
                    <p><strong>INVOICE DATE:</strong> 12/02/2019</p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>QTY</th>
                        <th>DESCRIPTION</th>
                        <th>AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Carambola</td>
                        <td>100.00</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Ham and Cheese Sandwich</td>
                        <td>50.00</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Rain Doughnut</td>
                        <td>36.00</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;">Subtotal</td>
                        <td>186.00</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;">VAT 23.0%</td>
                        <td>42.78</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;"><strong>Total</strong></td>
                        <td><strong>228.78 €</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class="total-section">
                <p><span>Total:</span> 228.78 €</p>
            </div>

            <div class="signature-section">
                <p>Authorized Signature:</p>
                <div class="signature-line"><span> Cünyet Yedikardeş-manger</span></div>
                <div class="stamp">
                    <img src="stamp.png" alt="Stamp">
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="address">
                <p><strong>ADDRESS & CONTACT INFO</strong></p>
                <p>Molla Gürani, Karakoyunlu Sokağı No:2 D:4, 34093 Fatih/İstanbul</p>
                <p>Telephone: +90 5318176770</p>
                <p>Email: info@cyntour.com</p>
            </div>
            <div class="logo">
                <img src="footer-logo.png" alt="Logo">
                <p>BELGE: 11738</p>
            </div>
        </div>
    </div>
</body>

</html>
