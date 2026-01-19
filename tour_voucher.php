<?php
/* ────────────────────────────────────────────────
   1. DB connection + sanitised voucher_id
   ──────────────────────────────────────────────── */
$host='localhost';  $user='cyntzsrb_cyn';  $pass='Qj!d$}Zh,-~m';  $db='cyntzsrb_cyn';
$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error){ die('DB error: '.$conn->connect_error); }

$voucher_id = isset($_GET['voucher_id']) ? intval($_GET['voucher_id']) : 0;
if(!$voucher_id){ die('No voucher_id supplied.'); }

/* ────────────────────────────────────────────────
   2. Fetch header + tours + customers
   ──────────────────────────────────────────────── */
$voucher = $conn->query(
  "SELECT * FROM city_tour_vouchers WHERE id=$voucher_id LIMIT 1"
)->fetch_assoc();
if(!$voucher){ die('Voucher not found.'); }

$tours = $conn->query(
  "SELECT tour_name, tour_date, duration
     FROM city_tour_voucher_tours
    WHERE voucher_id=$voucher_id
 ORDER BY tour_date"
)->fetch_all(MYSQLI_ASSOC);

$customers = $conn->query(
  "SELECT customer_name
     FROM city_tour_voucher_customers
    WHERE voucher_id=$voucher_id"
)->fetch_all(MYSQLI_ASSOC);

$conn->close();

/* Decide if we need compact mode */
$needCompact = count($tours) > 8 || count($customers) > 14;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>CYN Voucher #<?=htmlspecialchars($voucher['voucher_no'])?></title>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

<style>
/* ─── Base (normal) style ─────────────────────────────── */
@page{size:A4;margin:0}
body{font-family:"Helvetica Neue",Arial,sans-serif;margin:0;width:210mm;height:297mm;background:#f9f9f9}
.container{width:100%;padding:9mm;background:#fff;border-radius:5mm;box-shadow:0 0 8px rgba(0,0,0,.1)}
.header{display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid #ddd;padding-bottom:4mm;margin-bottom:4mm}
.logo img{height:105px}
.voucher-title{flex-grow:1;text-align:center}
.voucher-title h2{font-size:18pt;margin:0 0 -2mm;letter-spacing:.9mm;font-weight:700}
.voucher-no{font-size:12pt;font-weight:700}
.voucher-no span{color:#e11d48}
.details p{margin:0;padding:1.6mm 0;font-size:11pt}
h3{font-size:16pt;margin:8mm 0 4mm}
table{width:100%;border-collapse:collapse;margin-bottom:8mm}
th,td{border:1px solid #ddd;padding:4mm 3mm;font-size:11pt;page-break-inside:avoid}
th{background:#f2f2f2;width:22%}
.footer{margin-top:10mm;border-top:2px solid #ddd;padding-top:4mm;font-size:10.5pt;color:#333;display:flex;justify-content:space-between}
#downloadBtn{position:fixed;top:20px;right:20px;padding:9px 18px;background:#16a34a;color:#fff;border:none;border-radius:5px;box-shadow:0 2px 4px rgba(0,0,0,.2);cursor:pointer}

/* ─── COMPACT MODE ─────────────────────────────────────── */
body.compact .container      {padding:5.5mm}
body.compact .logo img       {height:85px}
body.compact .voucher-title h2{font-size:15pt}
body.compact .voucher-no     {font-size:10.5pt}
body.compact .details p,
body.compact th,
body.compact td              {font-size:9.5pt}
body.compact th,body.compact td{padding:2mm 1.5mm}
body.compact h3              {font-size:13.5pt;margin:6mm 0 3mm}

/* two-column tour list in compact mode */
body.compact .tours-wrapper  {column-count:2;column-gap:4mm}
body.compact .tours-wrapper table{break-inside:avoid;margin-bottom:0;border:none}
body.compact .tours-wrapper tr{border:1px solid #ddd}
body.compact .tours-wrapper table th{display:none}

/* print tweaks */
@media print{#downloadBtn{display:none}}
</style>
</head>

<body<?= $needCompact ? ' class="compact"' : '' ?>>

<div class="container" id="voucherContent">
  <!-- ── Header ───────────────────────── -->
  <div class="header">
    <div class="logo"><img src="logo.png" alt="CYN TURIZM"></div>
    <div class="voucher-title"><h2>VOUCHER</h2></div>
    <div class="voucher-no">
      Voucher&nbsp;No:&nbsp;<span><?=htmlspecialchars($voucher['voucher_no'])?></span>
    </div>
  </div>

  <!-- ── Details block ────────────────── -->
  <div class="details">
    <p><strong>Company Name:</strong> <?=htmlspecialchars($voucher['company_name'])?></p>
    <p><strong>Customer Phone:</strong> <?=htmlspecialchars($voucher['customer_phone'])?></p>
    <p><strong>Hotel Name:</strong> <?=htmlspecialchars($voucher['hotel_name'])?></p>
    <p><strong>Adult:</strong> <?=$voucher['adult']?></p>
    <p><strong>Child:</strong> <?=$voucher['child']?></p>
    <p><strong>Infant:</strong> <?=$voucher['infant']?></p>
  </div>

  <!-- ── Tours (auto two-column if compact) ─────────────── -->
  <h3>Tours</h3>
  <div class="tours-wrapper">
    <table>
      <tr><th>Tour Name</th><th>Date</th><th>Dur.</th></tr>
      <?php foreach($tours as $t): ?>
        <tr>
          <td><?=htmlspecialchars($t['tour_name'])?></td>
          <td><?=htmlspecialchars($t['tour_date'])?></td>
          <td><?=htmlspecialchars($t['duration'])?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <!-- ── Customers table ──────────────── -->
  <h3>Customers</h3>
  <table>
    <tr><th style="width:35%">Name</th></tr>
    <?php foreach($customers as $c): ?>
      <tr><td><?=htmlspecialchars($c['customer_name'])?></td></tr>
    <?php endforeach; ?>
  </table>

  <!-- ── Footer ───────────────────────── -->
  <div class="footer">
    <div>
      <p><i class="fas fa-map-marker-alt"></i> Molla Gürani, Karakoyunlu Sk. No:2 D:4, 34093 Fatih/İstanbul</p>
      <p><i class="fas fa-phone"></i> +90 531 817 6770</p>
      <p><i class="fas fa-envelope"></i> info@cyntour.com</p>
    </div>
    <div style="text-align:right">
      <img src="footer-logo.png" alt="Footer Logo" style="height:50px"><br>
      <span style="font-weight:bold">BELGE </span><span style="font-weight:bold;color:#e11d48">11738</span>
    </div>
  </div>
</div><!-- /container -->

<button id="downloadBtn" onclick="downloadAsPDF()">Download as PDF</button>

<!-- html2pdf.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
function downloadAsPDF(){
  const el=document.getElementById('voucherContent');
  const fname='Voucher_<?=preg_replace("/[^A-Za-z0-9_-]/","_", $voucher['voucher_no'])?>.pdf';
  html2pdf().from(el).set({
    margin:0,
    filename:fname,
    image:{type:'jpeg',quality:1},
    html2canvas:{scale:2},
    jsPDF:{unit:'mm',format:'a4',orientation:'portrait'}
  }).save();
}
</script>
</body>
</html>
