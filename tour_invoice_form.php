<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akteon Travel Invoice</title>
    
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            h1 {
                font-size: 24px;
            }
            .form-group {
                margin-bottom: 10px;
            }
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
            .form-row {
                flex-direction: column;
            }
            .form-row > .form-group {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Invoice Form</h1>
        <form id="invoiceDataForm" action="tour_invoice_voucher.php" method="post">
            <div class="form-group">
                <label for="invoice_no">Invoice No:</label>
                <input type="text" class="form-control" id="invoice_no" name="invoice_no" required>
            </div>
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>
            <div class="form-group">
                <label for="client_name">Client Name:</label>
                <input type="text" class="form-control" id="client_name" name="client_name" required>
            </div>
            <div id="toursContainer">
                <div class="tour-group">
                    <h3>Tour 1</h3>
                    <div class="form-group">
                        <label for="tour_name_1">Tour Name:</label>
                        <input type="text" class="form-control" id="tour_name_1" name="tour_name_1" required>
                    </div>
                    <div class="form-group">
                        <label for="tour_date_1">Tour Date:</label>
                        <input type="text" class="form-control" id="tour_date_1" name="tour_date_1" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="pax_count_1">Pax Count:</label>
                            <input type="number" class="form-control" id="pax_count_1" name="pax_count_1" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="pax_price_1">Pax Price:</label>
                            <input type="number" class="form-control" id="pax_price_1" name="pax_price_1" step="0.01" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" id="use_special_price_1" name="use_special_price_1">
                        <label for="use_special_price_1">Use Special Price</label>
                    </div>
                    <div id="special_prices_1" style="display:none;">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="adult_count_1">Adult Count:</label>
                                <input type="number" class="form-control" id="adult_count_1" name="adult_count_1">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="adult_price_1">Adult Price:</label>
                                <input type="number" class="form-control" id="adult_price_1" name="adult_price_1" step="0.01">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="child_count_1">Child Count:</label>
                                <input type="number" class="form-control" id="child_count_1" name="child_count_1">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="child_price_1">Child Price:</label>
                                <input type="number" class="form-control" id="child_price_1" name="child_price_1" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-add-tour">Add Tour</button>
            <div class="form-group mt-4">
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
            <div class="form-group">
                <label for="total_invoice">Total Invoice:</label>
                <input type="text" class="form-control" id="total_invoice" name="total_invoice" readonly>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Generate Invoice</button>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    
    <script>
        let tourCount = 1;

        // Function to calculate total invoice
        function calculateTotalInvoice() {
            let total = 0;

            for (let i = 1; i <= tourCount; i++) {
                const useSpecialPrice = document.getElementById(`use_special_price_${i}`).checked;
                if (useSpecialPrice) {
                    const adultCount = parseFloat(document.getElementById(`adult_count_${i}`).value) || 0;
                    const childCount = parseFloat(document.getElementById(`child_count_${i}`).value) || 0;
                    const adultPrice = parseFloat(document.getElementById(`adult_price_${i}`).value) || 0;
                    const childPrice = parseFloat(document.getElementById(`child_price_${i}`).value) || 0;
                    total += (adultCount * adultPrice) + (childCount * childPrice);
                } else {
                    const paxCount = parseFloat(document.getElementById(`pax_count_${i}`).value) || 0;
                    const paxPrice = parseFloat(document.getElementById(`pax_price_${i}`).value) || 0;
                    total += paxCount * paxPrice;
                }
            }

            document.getElementById('total_invoice').value = total.toFixed(2);
        }

        // Function to set up event listeners for a tour group
        function setupTourGroupEventListeners(tourGroup, tourIndex) {
            // Attach event listeners to inputs in the tour group
            tourGroup.querySelectorAll('input[type="number"], input[type="text"]').forEach(input => {
                input.addEventListener('input', calculateTotalInvoice);
            });

            // Get the special price checkbox and corresponding elements
            const specialPriceCheckbox = document.getElementById(`use_special_price_${tourIndex}`);
            const specialPricesDiv = document.getElementById(`special_prices_${tourIndex}`);
            const paxCountInput = document.getElementById(`pax_count_${tourIndex}`);
            const paxPriceInput = document.getElementById(`pax_price_${tourIndex}`);

            specialPriceCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    specialPricesDiv.style.display = 'block';
                    paxCountInput.disabled = true;
                    paxPriceInput.disabled = true;
                    paxCountInput.value = '';
                    paxPriceInput.value = '';
                } else {
                    specialPricesDiv.style.display = 'none';
                    paxCountInput.disabled = false;
                    paxPriceInput.disabled = false;
                }
                calculateTotalInvoice();
            });
        }

        // Set up event listeners for the initial tour group
        const tourGroup1 = document.querySelector('.tour-group');
        setupTourGroupEventListeners(tourGroup1, 1);

        // Add event listener to the 'Add Tour' button
        document.querySelector('.btn-add-tour').addEventListener('click', function () {
            tourCount++;
            const tourGroup = document.createElement('div');
            tourGroup.className = 'tour-group';
            tourGroup.innerHTML = `
                <h3>Tour ${tourCount}</h3>
                <div class="form-group">
                    <label for="tour_name_${tourCount}">Tour Name:</label>
                    <input type="text" class="form-control" id="tour_name_${tourCount}" name="tour_name_${tourCount}" required>
                </div>
                <div class="form-group">
                    <label for="tour_date_${tourCount}">Tour Date:</label>
                    <input type="text" class="form-control" id="tour_date_${tourCount}" name="tour_date_${tourCount}" required>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="pax_count_${tourCount}">Pax Count:</label>
                        <input type="number" class="form-control" id="pax_count_${tourCount}" name="pax_count_${tourCount}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pax_price_${tourCount}">Pax Price:</label>
                        <input type="number" class="form-control" id="pax_price_${tourCount}" name="pax_price_${tourCount}" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <input type="checkbox" id="use_special_price_${tourCount}" name="use_special_price_${tourCount}">
                    <label for="use_special_price_${tourCount}">Use Special Price</label>
                </div>
                <div id="special_prices_${tourCount}" style="display:none;">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="adult_count_${tourCount}">Adult Count:</label>
                            <input type="number" class="form-control" id="adult_count_${tourCount}" name="adult_count_${tourCount}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="adult_price_${tourCount}">Adult Price:</label>
                            <input type="number" class="form-control" id="adult_price_${tourCount}" name="adult_price_${tourCount}" step="0.01">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="child_count_${tourCount}">Child Count:</label>
                            <input type="number" class="form-control" id="child_count_${tourCount}" name="child_count_${tourCount}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="child_price_${tourCount}">Child Price:</label>
                            <input type="number" class="form-control" id="child_price_${tourCount}" name="child_price_${tourCount}" step="0.01">
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('toursContainer').appendChild(tourGroup);

            // Set up event listeners for the new tour group
            setupTourGroupEventListeners(tourGroup, tourCount);

            // Recalculate total invoice
            calculateTotalInvoice();
        });

        // Initial calculation
        calculateTotalInvoice();
    </script>
</body>
</html>