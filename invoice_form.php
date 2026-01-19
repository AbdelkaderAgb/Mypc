<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akteon Travel Invoice</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            font-family: 'Helvetica Neue', sans-serif;
            background-color: #f9f9f9;
        }

        .container {
            margin-top: 50px;
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn-add-room {
            margin-top: 20px;
        }
I just asking was
      
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -5px;
            margin-left: -5px;
        }

        .form-row .form-group {
            padding-right: 5px;
            padding-left: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center mb-4">Invoice Form</h1>
        <form id="invoiceDataForm" action="invoice.php" method="post">
            <div class="form-group">
                <label for="invoice_no">Invoice No:</label>
                <input type="text" class="form-control" id="invoice_no" name="invoice_no" required>
            </div>
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>
            <div class="form-group">
                <label for="hotel_name">Hotel Name:</label>
                <input type="text" class="form-control" id="hotel_name" name="hotel_name" required>
            </div>
            <div id="roomsContainer">
                <div class="room-group">
                    <h3>Room 1</h3>
                    <div class="form-group">
                        <label for="room_1">Room Type:</label>
                        <input type="text" class="form-control" id="room_1" name="room_1" required>
                    </div>
                    <div class="form-group">
                        <label for="price_per_night_1">Price per Night:</label>
                        <input type="number" class="form-control" id="price_per_night_1" name="price_per_night_1" step="0.01" required>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-add-room">Add Room</button>

            <!-- Date pickers as hidden inputs; Flatpickr will create visible inputs -->
            <div class="form-group">
                <label for="accommodation_start">Check-in Date (dd/mm/yyyy):</label>
                <input type="hidden" id="accommodation_start" name="accommodation_start" required>
            </div>
            <div class="form-group">
                <label for="accommodation_end">Check-out Date (dd/mm/yyyy):</label>
                <input type="hidden" id="accommodation_end" name="accommodation_end" required>
            </div>

            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="transfer_price">Transfer Price:</label>
                    <input type="number" class="form-control" id="transfer_price" name="transfer_price" step="0.01">
                </div>
                <div class="form-group col-md-4">
                    <label for="notes">Notes:</label>
                    <input type="text" class="form-control" id="notes" name="notes" placeholder="Notes">
                </div>
            </div>

            <div class="form-group">
                <label for="currency">Currency:</label>
                <select class="form-control" id="currency" name="currency" required>
                    <option value="$">$</option>
                    <option value="€">€</option>
                    <option value="₺">₺</option>
                </select>
            </div>
            <div class="form-group">
                <label for="customers">Customers:</label>
                <textarea class="form-control" id="customers" name="customers" rows="3" placeholder="Enter customer names separated by commas" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Generate Invoice</button>
        </form>
    </div>

    <!-- Essential JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2h5tZ0n1Ao5r14BLnaJgyRPPnNQZ4YkE6ITC3p9SJOKp3XAAZIv57jLg9xM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // Initialise date pickers to use dd/mm/yyyy for the user, while preserving ISO format for form submission
        flatpickr("#accommodation_start", {
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d", // value that will be submitted to PHP
            altInputClass: "form-control"
        });

        flatpickr("#accommodation_end", {
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            altInputClass: "form-control"
        });

        // Dynamic room sections
        let roomCount = 1;
        document.querySelector('.btn-add-room').addEventListener('click', function () {
            roomCount++;
            const roomGroup = document.createElement('div');
            roomGroup.className = 'room-group';
            roomGroup.innerHTML = `
                <h3>Room ${roomCount}</h3>
                <div class="form-group">
                    <label for="room_${roomCount}">Room Type:</label>
                    <input type="text" class="form-control" id="room_${roomCount}" name="room_${roomCount}" required>
                </div>
                <div class="form-group">
                    <label for="price_per_night_${roomCount}">Price per Night:</label>
                    <input type="number" class="form-control" id="price_per_night_${roomCount}" name="price_per_night_${roomCount}" step="0.01" required>
                </div>
            `;
            document.getElementById('roomsContainer').appendChild(roomGroup);
        });
    </script>
</body>

</html>
