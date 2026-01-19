<?php
// Initialize error message variable
$error_message = "";
$success_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection parameters
    $host = 'localhost';
    $username = "cyntzsrb_cyn";
    $password = "Qj!d$}Zh,-~m";
    $database = 'cyntzsrb_cyn';
    
    try {
        // Create connection
        $conn = new mysqli($host, $username, $password, $database);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }
        
        // Get form data
        $voucher_no = $_POST['voucher_no'];
        $company_name = $_POST['company_name'];
        $hotel_name = isset($_POST['hotel_name']) ? $_POST['hotel_name'] : '';
        $flight_number = isset($_POST['flight_number']) ? $_POST['flight_number'] : '';
        $pickup_location = $_POST['pickup_location'];
        $dropoff_location = $_POST['dropoff_location'];
        $pickup_date = $_POST['pickup_date'];
        $pickup_time = $_POST['pickup_time'];
        $transfer_type = $_POST['transfer_type'];
        $return_date = isset($_POST['return_date']) && !empty($_POST['return_date']) ? $_POST['return_date'] : null;
        $return_time = isset($_POST['return_time']) && !empty($_POST['return_time']) ? $_POST['return_time'] : null;
        $total_pax = $_POST['total_pax'];
        $passengers = $_POST['passengers'];
        
        // Prepare and execute SQL statement
        $sql = "INSERT INTO transfer_vouchers (voucher_no, company_name, hotel_name, flight_number, pickup_location, dropoff_location, 
                pickup_date, pickup_time, transfer_type, return_date, return_time, total_pax, passengers, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssss", $voucher_no, $company_name, $hotel_name, $flight_number, $pickup_location, 
                        $dropoff_location, $pickup_date, $pickup_time, $transfer_type, $return_date, $return_time, 
                        $total_pax, $passengers);
        
        if ($stmt->execute()) {
            $success_message = "Voucher data saved to database successfully";
            $stmt->close();
            $conn->close();
            
            // Forward the form data to transfer-voucher.php
            // The form will be submitted to transfer-voucher.php as if it came directly from the form
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Forwarding to Transfer Voucher</title>
            </head>
            <body>
                <p>Saving to database and forwarding to transfer voucher...</p>
                
                <form id="forwardForm" action="transfer-voucher.php" method="post">
                    <?php foreach($_POST as $key => $value): ?>
                        <?php if(is_array($value)): ?>
                            <?php foreach($value as $item): ?>
                                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>[]" value="<?php echo htmlspecialchars($item); ?>">
                            <?php endforeach; ?>
                        <?php else: ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </form>
                
                <script>
                    // Automatically submit the form
                    document.getElementById('forwardForm').submit();
                </script>
            </body>
            </html>
            <?php
            exit;
        } else {
            throw new Exception("Error inserting data: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Voucher Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Helvetica Neue', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }

        .container {
            width: 100%;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-generate {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Transfer Voucher Form</h1>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success mt-3 mb-3" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger mt-3 mb-3" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="voucher_no">Voucher No:</label>
                <input type="text" class="form-control" id="voucher_no" name="voucher_no" required>
            </div>
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>
            <div class="form-group">
                <label for="hotel_name">Hotel Name:</label>
                <input type="text" class="form-control" id="hotel_name" name="hotel_name">
            </div>
            <div class="form-group">
                <label for="flight_number">Flight Number:</label>
                <input type="text" class="form-control" id="flight_number" name="flight_number">
            </div>
            <div class="form-group">
                <label for="pickup_location">Starting Point:</label>
                <input type="text" class="form-control" id="pickup_location" name="pickup_location" required>
            </div>
            <div class="form-group">
                <label for="dropoff_location">Destination:</label>
                <input type="text" class="form-control" id="dropoff_location" name="dropoff_location" required>
            </div>
            <div class="form-group">
                <label for="pickup_date">Date:</label>
                <input type="date" class="form-control" id="pickup_date" name="pickup_date" required>
            </div>
            <div class="form-group">
                <label for="pickup_time">Time:</label>
                <input type="time" class="form-control" id="pickup_time" name="pickup_time" required>
            </div>
            <div class="form-group">
                <label for="transfer_type">Transfer Type:</label>
                <select class="form-control" id="transfer_type" name="transfer_type" required onchange="toggleReturnDateTime(this.value)">
                    <option value="One Way">One Way</option>
                    <option value="Arrival-Return">Arrival-Return</option>
                </select>
            </div>
            <div class="form-group" id="return_date_group" style="display: none;">
                <label for="return_date">Return Date:</label>
                <input type="date" class="form-control" id="return_date" name="return_date">
            </div>
            <div class="form-group" id="return_time_group" style="display: none;">
                <label for="return_time">Return Time:</label>
                <input type="time" class="form-control" id="return_time" name="return_time">
            </div>
            <div class="form-group">
                <label for="total_pax">Total Pax:</label>
                <input type="number" class="form-control" id="total_pax" name="total_pax" required>
            </div>
            <div class="form-group">
                <label for="passengers">Passengers:</label>
                <textarea class="form-control" id="passengers" name="passengers" rows="5" placeholder="Enter passenger names separated by new lines" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-generate">Generate Voucher</button>
        </form>
    </div>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function toggleReturnDateTime(value) {
            const returnDateGroup = document.getElementById('return_date_group');
            const returnTimeGroup = document.getElementById('return_time_group');
            if (value === 'Arrival-Return') {
                returnDateGroup.style.display = 'block';
                returnTimeGroup.style.display = 'block';
            } else {
                returnDateGroup.style.display = 'none';
                returnTimeGroup.style.display = 'none';
            }
        }
    </script>
</body>

</html>