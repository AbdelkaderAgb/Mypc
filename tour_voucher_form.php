<?php
/* ───── 1.  DB connection ───────────────────────────────────── */
$host='localhost'; $user='cyntzsrb_cyn'; $pass='Qj!d$}Zh,-~m'; $db='cyntzsrb_cyn';
$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error){ die('DB error: '.$conn->connect_error); }

/* ───── 2.  Handle POST save then redirect ─────────────────── */
if($_SERVER['REQUEST_METHOD']==='POST'){
    /* 2-a: insert voucher header and grab its id */
    $stmt = $conn->prepare(
        "INSERT INTO city_tour_vouchers
         (voucher_no,company_name,customer_phone,hotel_name,adult,child,infant)
         VALUES (?,?,?,?,?,?,?)"
    );
    $stmt->bind_param(
        'ssssiii',
        $_POST['voucher_no'],
        $_POST['company_name'],
        $_POST['customer_phone'],
        $_POST['hotel_name'],
        $_POST['adult'],
        $_POST['child'],
        $_POST['infant']
    );
    $stmt->execute();
    $voucher_id = $stmt->insert_id;
    $stmt->close();

    /* 2-b: insert each tour row */
    if(!empty($_POST['tour_name'])){
        $stmt=$conn->prepare(
            "INSERT INTO city_tour_voucher_tours
             (voucher_id,tour_name,tour_date,duration)
             VALUES (?,?,?,?)"
        );
        for($i=0;$i<count($_POST['tour_name']);$i++){
            $stmt->bind_param(
                'isss',
                $voucher_id,
                $_POST['tour_name'][$i],
                $_POST['tour_date'][$i],
                $_POST['tour_duration'][$i]
            );
            $stmt->execute();
        }
        $stmt->close();
    }

    /* 2-c: insert each passenger name */
    if(!empty($_POST['customer_name'])){
        $stmt=$conn->prepare(
            "INSERT INTO city_tour_voucher_customers (voucher_id,customer_name)
             VALUES (?,?)"
        );
        foreach($_POST['customer_name'] as $name){
            $stmt->bind_param('is',$voucher_id,$name);
            $stmt->execute();
        }
        $stmt->close();
    }

    /* 2-d: all done – jump to the PDF page */
    header("Location: tour_voucher.php?voucher_id=$voucher_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>CYN City Tour Voucher Form</title>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
body{font-family:Arial,Helvetica,sans-serif;max-width:840px;margin:auto;padding:20px}
.customer-info,.tour-info{border:1px solid #ddd;padding:10px;margin-bottom:10px}
</style>
</head>
<body>
<h1 class="text-center mb-4">CYN City Tour Voucher Form</h1>

<!-- ───── 3.  Voucher form (POSTs back to same file) ────────── -->
<form method="POST" id="tourVoucherForm">

  <!-- Header -->
  <div class="form-row">
    <div class="form-group col-md-6">
      <label>Voucher No</label>
      <input type="text" class="form-control" name="voucher_no" required>
    </div>
    <div class="form-group col-md-6">
      <label>Company Name</label>
      <input type="text" class="form-control" name="company_name" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label>Customer Phone</label>
      <input type="tel" class="form-control" name="customer_phone" required>
    </div>
    <div class="form-group col-md-6">
      <label>Hotel Name</label>
      <input type="text" class="form-control" name="hotel_name" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-4">
      <label>Adult</label>
      <input type="number" class="form-control" name="adult" min="0" value="0" required>
    </div>
    <div class="form-group col-md-4">
      <label>Child</label>
      <input type="number" class="form-control" name="child" min="0" value="0" required>
    </div>
    <div class="form-group col-md-4">
      <label>Infant</label>
      <input type="number" class="form-control" name="infant" min="0" value="0" required>
    </div>
  </div>

  <!-- Tours -->
  <h4 class="mt-4">Tours</h4>
  <div id="tours">
    <div class="tour-info">
      <div class="form-row">
        <div class="form-group col-md-6">
          <label>Tour Name</label>
          <input type="text" class="form-control" name="tour_name[]" required>
        </div>
        <div class="form-group col-md-3">
          <label>Tour Date</label>
          <input type="date" class="form-control" name="tour_date[]" required>
        </div>
        <div class="form-group col-md-3">
          <label>Duration</label>
          <input type="text" class="form-control" name="tour_duration[]" required>
        </div>
      </div>
    </div>
  </div>
  <button type="button" class="btn btn-secondary mb-3" onclick="addTour()">Add Tour</button>

  <!-- Customers -->
  <h4>Customers</h4>
  <div id="customers">
    <div class="customer-info">
      <div class="form-group">
        <label>Name</label>
        <input type="text" class="form-control" name="customer_name[]" required>
      </div>
    </div>
  </div>
  <button type="button" class="btn btn-secondary mb-3" onclick="addCustomer()">Add Customer</button>

  <button type="submit" class="btn btn-primary">Save &amp; Generate PDF</button>
</form>

<!-- ───── 4.  JS helpers to clone blocks ───────────────────── -->
<script>
function addTour(){
  const div=document.createElement('div');
  div.className='tour-info';
  div.innerHTML=document.querySelector('.tour-info').innerHTML+
       '<button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.parentNode.remove()">Remove Tour</button>';
  document.getElementById('tours').appendChild(div);
}
function addCustomer(){
  const div=document.createElement('div');
  div.className='customer-info';
  div.innerHTML=document.querySelector('.customer-info').innerHTML+
       '<button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.parentNode.remove()">Remove Customer</button>';
  document.getElementById('customers').appendChild(div);
}
</script>
</body>
</html>
