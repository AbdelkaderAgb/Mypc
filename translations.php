<?php
// Database settings
$host = 'localhost';
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$database = 'cyntzsrb_cyn';

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$results = [];
$searched = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_date = trim(htmlspecialchars($_POST['search_date']));

    if (!empty($search_date)) {
        $searched = true;

        $stmt = $conn->prepare("SELECT * FROM vouchers WHERE pickup_date = ? OR return_date = ?");
        $stmt->bind_param("ss", $search_date, $search_date);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }

        $stmt->close();

        if (empty($results)) {
            $error_message = "No transfers found for the selected date.";
        }
    } else {
        $error_message = "Please select a date!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Lists</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 1200px;
            margin-top: 50px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .logo {
            max-width: 150px;
            margin-bottom: 30px;
        }
        .search-form {
            background-color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }
        .modal-content {
            border-radius: 15px;
        }
        .footer {
            margin-top: 50px;
            padding: 20px 0;
            background-color: #4e73df;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center">
            <img src="logo.png" alt="Logo" class="logo">
            <h2 class="mb-4">Transfer Lists</h2>
        </div>
        
        <!-- Search Form -->
        <form method="POST" action="" class="search-form">
            <div class="row g-3 align-items-center justify-content-center">
                <div class="col-auto">
                    <label for="search_date" class="col-form-label">Search Date:</label>
                </div>
                <div class="col-auto">
                    <input type="date" id="search_date" name="search_date" class="form-control" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <?php echo $error_message ? '<div class="alert alert-warning mt-3">' . $error_message . '</div>' : ''; ?>

        <!-- Search Results -->
        <?php if ($searched && !empty($results)) { ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($results as $row) { ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['company_name']); ?></h5>
                                <p class="card-text"><strong>Voucher:</strong> <?php echo htmlspecialchars($row['voucher_no']); ?></p>
                                <p class="card-text"><strong>Hotel:</strong> <?php echo htmlspecialchars($row['hotel_name']); ?></p>
                                <p class="card-text"><strong>Flight:</strong> <?php echo htmlspecialchars($row['flight_number']); ?></p>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $row['voucher_no']; ?>">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="detailsModal<?php echo $row['voucher_no']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Transfer Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Pickup:</strong> <?php echo htmlspecialchars($row['pickup_location']); ?></p>
                                    <p><strong>Dropoff:</strong> <?php echo htmlspecialchars($row['dropoff_location']); ?></p>
                                    <p><strong>Pickup Date:</strong> <?php echo htmlspecialchars($row['pickup_date']); ?></p>
                                    <p><strong>Pickup Time:</strong> <?php echo htmlspecialchars($row['pickup_time']); ?></p>
                                    <?php if (!empty($row['transfer_type'])) { ?>
                                    <p><strong>Type:</strong> <?php echo htmlspecialchars($row['transfer_type']); ?></p>
                                    <?php } ?>
                                    <?php if ($row['return_date'] !== '0000-00-00') { ?>
                                    <p><strong>Return Date:</strong> <?php echo htmlspecialchars($row['return_date']); ?></p>
                                    <?php } ?>
                                    <?php if ($row['return_time'] !== '00:00:00') { ?>
                                    <p><strong>Return Time:</strong> <?php echo htmlspecialchars($row['return_time']); ?></p>
                                    <?php } ?>
                                    <?php if (!empty($row['total_pax'])) { ?>
                                    <p><strong>Passengers:</strong> <?php echo htmlspecialchars($row['total_pax']); ?></p>
                                    <?php } ?>
                                    <?php if (!empty($row['passengers'])) { ?>
                                    <p><strong>Names:</strong> <?php echo htmlspecialchars($row['passengers']); ?></p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <div class="footer">
        &copy; Cyn Tourism 2024 All Rights Reserved
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>