<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Voucher Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Receipt Voucher Form</h2>
        <form action="receipt.php" method="post">
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="form-group">
                <label for="currency">Currency:</label>
                <select class="form-control" id="currency" name="currency" required>
                    <option value="$">USD ($)</option>
                    <option value="€">EUR (€)</option>
                    <option value="₺">TRY (₺)</option>
                    <option value="DZD">DZD (DZD)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="reason">Reason:</label>
                <input type="text" class="form-control" id="reason" name="reason" required>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="payment_method">Payment Method:</label>
                <input type="text" class="form-control" id="payment_method" name="payment_method" required>
            </div>
            <div class="form-group">
                <label for="received_by">Received By:</label>
                <input type="text" class="form-control" id="received_by" name="received_by" required>
            </div>
            <div class="form-group">
                <label for="remaining_amount">Remaining Amount (optional):</label>
                <input type="number" class="form-control" id="remaining_amount" name="remaining_amount">
            </div>
            <button type="submit" class="btn btn-primary">Generate Receipt Voucher</button>
        </form>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>