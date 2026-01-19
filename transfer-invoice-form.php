<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Invoice Form</title>
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
            <h1>Transfer Invoice Form</h1>
        </div>
        <form action="generate-transfer-invoice.php" method="post">
            <div class="form-group">
                <label for="invoice_no">Invoice No:</label>
                <input type="text" class="form-control" id="invoice_no" name="invoice_no" required>
            </div>
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>
            <div class="form-group">
                <label for="starting_point">Starting Point:</label>
                <input type="text" class="form-control" id="starting_point" name="starting_point" required>
            </div>
            <div class="form-group">
                <label for="return_point">Destination:</label>
                <input type="text" class="form-control" id="return_point" name="return_point" required>
            </div>
            <div class="form-group">
                <label for="hotel">Hotel:</label>
                <input type="text" class="form-control" id="hotel" name="hotel" required>
            </div>
            <div class="form-group">
                <label for="pickup_date">Pickup Date:</label>
                <input type="date" class="form-control" id="pickup_date" name="pickup_date" required>
            </div>
            <div class="form-group">
                <label for="transfer_type">Transfer Type:</label>
                <select class="form-control" id="transfer_type" name="transfer_type" required onchange="toggleReturnDate(this.value)">
                    <option value="One Way">One Way</option>
                    <option value="Arrival-Return">Arrival-Return</option>
                </select>
            </div>
            <div class="form-group" id="return_date_group" style="display: none;">
                <label for="return_date">Return Date:</label>
                <input type="date" class="form-control" id="return_date" name="return_date">
            </div>
            <div class="form-group">
                <label for="total_pax">Total Pax:</label>
                <input type="number" class="form-control" id="total_pax" name="total_pax" required>
            </div>
            <div class="form-group">
                <label for="total_price">Total Price:</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="total_price" name="total_price" step="0.01" required>
                    <div class="input-group-append">
                        <select class="form-control" id="currency" name="currency" required>
                            <option value="$">$</option>
                            <option value="€">€</option>
                            <option value="₺">₺</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="passengers">Passengers:</label>
                <textarea class="form-control" id="passengers" name="passengers" rows="5" placeholder="Enter passenger names separated by new lines" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-generate">Generate Invoice</button>
        </form>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function toggleReturnDate(value) {
            const returnDateGroup = document.getElementById('return_date_group');
            if (value === 'Arrival-Return') {
                returnDateGroup.style.display = 'block';
            } else {
                returnDateGroup.style.display = 'none';
            }
        }
    </script>
</body>

</html>