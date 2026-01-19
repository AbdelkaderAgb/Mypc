<?php
// This block will only run when a POST request is made to this page.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set the content type to application/json for the response
    header('Content-Type: application/json');

    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Database connection details
    $servername = "localhost";
    $username = "cyntzsrb_cyn";
    $password = "Qj!d$}Zh,-~m";
    $dbname = "cyntzsrb_cyn";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        // Exit and send a JSON error response
        die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
    }

    // Get POST data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Log received data (for debugging purposes, can be removed in production)
    error_log('Received data: ' . print_r($data, true));

    // Check if the first customer exists before accessing it
    $customer_name = isset($data['customers'][0]['name']) ? $data['customers'][0]['name'] : '';

    // Check if voucher number already exists
    $check_sql = "SELECT voucher_no FROM h_vouchers WHERE voucher_no = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $data['voucher_no']);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Voucher number already exists. Please use a unique voucher number."]);
        $check_stmt->close();
        $conn->close();
        exit(); // Stop script execution
    }
    $check_stmt->close();

    // Handle room value
    $room = isset($data['room']) ? (string)$data['room'] : 'Unknown';
    error_log('Room value: ' . $room);

    // Prepare SQL query to insert new voucher
    $sql = "INSERT INTO h_vouchers (voucher_no, company_name, hotel, room_count, check_in_date, check_out_date, nights, room, transfer_type, customer_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    error_log('SQL query: ' . $sql);

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die(json_encode(["success" => false, "message" => "Query preparation error: " . $conn->error]));
    }

    // Bind form data to SQL query
    $stmt->bind_param("sssissssss", 
        $data['voucher_no'],
        $data['company_name'],
        $data['hotel'],
        $data['room_count'],
        $data['accommodation_start'],
        $data['accommodation_end'],
        $data['nights'],
        $room,
        $data['transfer_type'],
        $customer_name
    );

    // Execute query and handle response
    if ($stmt->execute()) {
        error_log('Affected rows: ' . $stmt->affected_rows);
        echo json_encode(["success" => true, "message" => "Voucher saved successfully"]);
    } else {
        error_log('SQL error: ' . $stmt->error);
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
    exit(); // IMPORTANT: Stop the script to prevent the HTML below from being sent with the JSON response.
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYN Voucher Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0 auto;
            padding: 20px;
            max-width: 800px;
        }
        .customer-info {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }
        .form-section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1 class="text-center mb-4">CYN Voucher Form</h1>
    <form id="voucherForm">
        <div class="form-section">
            <div class="form-group">
                <label for="voucher_no">Voucher No:</label>
                <input type="text" class="form-control" id="voucher_no" name="voucher_no" required>
            </div>
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" class="form-control" id="address" name="address" value="MOLLA GURANI MAH. OGUSHAN CAD. KARAKOYUNLU SOK. NO: 2 D: 4 FINDIKZADE / FATIH" readonly>
            </div>
            <div class="form-group">
                <label for="telephone">Telephone:</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" value="+90 5318176770" readonly>
            </div>
            <div class="form-group">
                <label for="hotel">Hotel:</label>
                <input type="text" class="form-control" id="hotel" name="hotel" required>
            </div>
            <div class="form-group">
                <label for="room_count">Room Count:</label>
                <input type="number" class="form-control" id="room_count" name="room_count" required>
            </div>
            <div class="form-group">
                <label for="accommodation_start">Check-in Date:</label>
                <input type="text" class="form-control" id="accommodation_start" name="accommodation_start" placeholder="Select check-in date..." required>
            </div>
            <div class="form-group">
                <label for="accommodation_end">Check-out Date:</label>
                <input type="text" class="form-control" id="accommodation_end" name="accommodation_end" placeholder="Select check-out date..." required>
            </div>
            <div class="form-group">
                <label for="nights">Nights:</label>
                <input type="number" class="form-control" id="nights" name="nights" readonly>
            </div>
            <div class="form-group">
                <label for="room">Room:</label>
                <input type="text" class="form-control" id="room" name="room" required>
            </div>
            <div class="form-group">
                <label for="transfer_type">Transfer Type:</label>
                <select class="form-control" id="transfer_type" name="transfer_type">
                    <option value="">Select transfer type</option>
                    <option value="Arrival - Return">Arrival - Return</option>
                    <option value="One Way">One Way</option>
                </select>
            </div>
            <div class="form-group">
                <label for="board">Board:</label>
                <input type="text" class="form-control" id="board" name="board" required>
            </div>
            <div class="form-group">
                <label for="adult">Adult:</label>
                <input type="number" class="form-control" id="adult" name="adult" required>
            </div>
            <div class="form-group">
                <label for="child">Child:</label>
                <input type="number" class="form-control" id="child" name="child" required>
            </div>
            <div class="form-group">
                <label for="infant">Infant:</label>
                <input type="number" class="form-control" id="infant" name="infant" required>
            </div>
        </div>

        <h2>Customers</h2>
        <div id="customers">
            <div class="customer-info">
                <div class="form-group">
                    <label for="title1">Title:</label>
                    <select class="form-control" id="title1" name="title1" required>
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Ms">Ms</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name1">Name:</label>
                    <input type="text" class="form-control" id="name1" name="name1" required>
                </div>
                <div class="form-group">
                    <label for="nationality1">Nationality:</label>
                    <input type="text" class="form-control" id="nationality1" name="nationality1" required>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-3" onclick="addCustomer()">Add Customer</button>
        <button type="submit" class="btn btn-primary">Generate Voucher</button>
    </form>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        let customerCount = 1;
        let fp_start, fp_end; // Declare flatpickr instances in a wider scope

        function addCustomer() {
            customerCount++;
            const customersDiv = document.getElementById('customers');
            const newCustomer = document.createElement('div');
            newCustomer.className = 'customer-info';
            newCustomer.innerHTML = `
                <div class="form-group">
                    <label for="title${customerCount}">Title:</label>
                    <select class="form-control" id="title${customerCount}" name="title${customerCount}" required>
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Ms">Ms</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name${customerCount}">Name:</label>
                    <input type="text" class="form-control" id="name${customerCount}" name="name${customerCount}" required>
                </div>
                <div class="form-group">
                    <label for="nationality${customerCount}">Nationality:</label>
                    <input type="text" class="form-control" id="nationality${customerCount}" name="nationality${customerCount}" required>
                </div>
            `;
            customersDiv.appendChild(newCustomer);
        }

        // A new function to handle the calculation
        function calculateAndSetNights() {
            const startDate = fp_start.selectedDates[0];
            const endDate = fp_end.selectedDates[0];

            if (startDate && endDate && startDate < endDate) {
                const diffTime = endDate.getTime() - startDate.getTime();
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                document.getElementById('nights').value = diffDays;
            } else {
                document.getElementById('nights').value = '';
            }
        }

        // Initialize flatpickr on page load
        document.addEventListener('DOMContentLoaded', (event) => {
            fp_start = flatpickr("#accommodation_start", {
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    // When start date changes, set the minimum date for the end date picker
                    if (selectedDates[0]) {
                        fp_end.set('minDate', new Date(selectedDates[0].getTime() + 86400000)); // next day
                    }
                    calculateAndSetNights();
                }
            });

            fp_end = flatpickr("#accommodation_end", {
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    calculateAndSetNights();
                }
            });
        });

        document.getElementById('voucherForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Collect form data
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            // Ensure 'room' is treated as text
            data.room = data.room.toString();

            // Collect customer data
            data.customers = [];
            const customerDivs = document.querySelectorAll('.customer-info');
            customerDivs.forEach((div, index) => {
                const title = div.querySelector(`select[name="title${index + 1}"]`).value;
                const name = div.querySelector(`input[name="name${index + 1}"]`).value;
                const nationality = div.querySelector(`input[name="nationality${index + 1}"]`).value;
                data.customers.push({ title, name, nationality });
            });

            console.log('Data being sent:', data);

            // Send data to this same page for processing by the PHP block at the top
            fetch(window.location.href, { // Using window.location.href to post to the same page
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Store data in localStorage
                    localStorage.setItem('voucherData', JSON.stringify(data));
                    // Redirect to voucher.php
                    window.location.href = 'voucher.php';
                } else {
                    // Display error message
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the voucher.');
            });
        });
    </script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>