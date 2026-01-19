<?php
$host = 'localhost';
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$database = 'cyntzsrb_cyn';

// Bağlantı oluştur
$conn = new mysqli($host, $username, $password, $database);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$success_message = '';

// Silme isteğini işle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_voucher'])) {
    $id = $_POST['delete_voucher'];

    // Voucheri veritabanından sil
    $sql_delete = "DELETE FROM vouchers WHERE id = $id";
    if ($conn->query($sql_delete) === TRUE) {
        // Silme başarılı
        $success_message = "Voucher başarıyla silindi";
    } else {
        echo "Kayıt silme hatası: " . $conn->error;
    }
}

// Voucherleri çek ve pickup_date'e göre sırala
$sql = "SELECT * FROM vouchers ORDER BY pickup_date DESC";
$result = $conn->query($sql);

$vouchers = array();
while ($row = $result->fetch_assoc()) {
    $vouchers[] = array(
        'id' => $row['id'],
        'voucher_no' => $row['voucher_no'],
        'company_name' => $row['company_name'],
        'pickup_location' => $row['pickup_location'],
        'dropoff_location' => $row['dropoff_location'],
        'pickup_date' => $row['pickup_date'],
        'pickup_time' => $row['pickup_time'],
        'transfer_type' => $row['transfer_type'],
        'return_date' => $row['return_date'],
        'return_time' => $row['return_time'],
        'total_pax' => $row['total_pax'],
        'passengers' => nl2br($row['passengers'])
    );
}

// Voucherleri pickup_date'e göre sırala, en yeni tarih ilk sırada
usort($vouchers, function($a, $b) {
    return strtotime($b['pickup_date']) - strtotime($a['pickup_date']);
});
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Listesi</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Helvetica Neue', sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }

        .voucher-list {
            list-style-type: none;
            padding: 0;
        }

        .voucher-list li {
            background-color: #fff;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .voucher-list li:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .voucher-info {
            margin-bottom: 10px;
        }

        .voucher-info strong {
            margin-right: 5px;
        }

        .voucher-details {
            margin-top: 10px;
        
        .logo {
    max-width: 200px;
    margin-bottom: 20px;
}

        .delete-button {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center mb-4">Voucher Listesi</h1>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <ul class="voucher-list">
            <?php foreach ($vouchers as $voucher): ?>
            <li>
                <div class="voucher-info">
                    <strong>Voucher No:</strong> <?php echo $voucher['voucher_no']; ?><br>
                    <strong>Şirket Adı:</strong> <?php echo $voucher['company_name']; ?><br>
                    <strong>Alış Yeri:</strong> <?php echo $voucher['pickup_location']; ?><br>
                    <strong>Bırakış Yeri:</strong> <?php echo $voucher['dropoff_location']; ?><br>
                    <strong>Alış Tarihi:</strong> <?php echo $voucher['pickup_date']; ?><br>
                    <strong>Alış Saati:</strong> <?php echo $voucher['pickup_time']; ?><br>
                    <strong>Transfer Türü:</strong> <?php echo $voucher['transfer_type']; ?><br>
                    <strong>Toplam Yolcu:</strong> <?php echo $voucher['total_pax']; ?><br>
                </div>
                <div class="voucher-details">
                    <strong>Yolcular:</strong><br>
                    <?php echo $voucher['passengers']; ?>
                </div>
                <!-- Silme butonu -->
                <form method="post">
                    <input type="hidden" name="delete_voucher" value="<?php echo $voucher['id']; ?>">
                    <button type="submit" class="btn btn-danger delete-button">Sil</button>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Bootstrap JS ve bağımlılıkları -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>