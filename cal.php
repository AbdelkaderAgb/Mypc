<?php
// Database configuration
$host = 'localhost';
$username = "cyntzsrb_cyn";  // Replace with actual username
$password = "Qj!d$}Zh,-~m";  // Replace with actual password
$database = 'cyntzsrb_cyn';  // Replace with actual database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection error: " . $conn->connect_error);
}

$success_message = '';

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_voucher'])) {
    $id = $conn->real_escape_string($_POST['delete_voucher']);
    $sql_delete = "DELETE FROM h_vouchers WHERE id = '$id'";
    if ($conn->query($sql_delete) === TRUE) {
        $success_message = "Voucher successfully deleted";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Handle date request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['date'])) {
    $date = $conn->real_escape_string($_POST['date']);
    $sql = "SELECT * FROM h_vouchers WHERE check_in_date = '$date' OR check_out_date = '$date'";
    $result = $conn->query($sql);

    $vouchers = array();
    while ($row = $result->fetch_assoc()) {
        $vouchers[] = $row;
    }

    echo json_encode($vouchers);
    $conn->close();
    exit();
}

// Fetch vouchers
$sql = "SELECT * FROM h_vouchers ORDER BY check_in_date DESC";
$result = $conn->query($sql);

$vouchers = array();
while ($row = $result->fetch_assoc()) {
    $vouchers[] = $row;
}

// Sort vouchers by check-in date, most recent first
usort($vouchers, function($a, $b) {
    return strtotime($b['check_in_date']) - strtotime($a['check_in_date']);
});

$vouchers_calendar = array_map(function($voucher) {
    return [
        'id' => $voucher['id'],
        'title' => $voucher['company_name'] . ' - ' . $voucher['voucher_no'],
        'check_in_date' => $voucher['check_in_date'],
        'check_out_date' => $voucher['check_out_date']
    ];
}, $vouchers);

$conn->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Voucher Listesi ve Takvim</title>
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    :root {
      --primary-color: #2563eb;
      --secondary-color: #1e40af;
      --success-color: #059669;  /* Updated to a more professional green */
      --warning-color: #d97706;  /* Updated to a more professional amber */
      --danger-color: #dc2626;   /* Updated to a more professional red */
      --light-bg: #f3f4f6;
      --dark-bg: #1f2937;
      --calendar-bg: #ffffff;
      --day-hover: #f8fafc;
      --border-color: #e5e7eb;
    }

    /* Global Styles */
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: var(--light-bg);
      color: #333;
      line-height: 1.5;
    }

    .container {
      max-width: 1000px;
      padding: 15px;
    }

    /* Header Styles */
    .header-logo {
      margin: 15px 0;
      text-align: center;
      transition: transform 0.3s ease;
    }

    .header-logo img {
      max-width: 150px;
      height: auto;
      filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
    }

    h1 {
      font-size: 1.8rem;
      font-weight: 600;
      color: var(--dark-bg);
      margin-bottom: 1.5rem;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* Navigation Buttons */
    .navigation-buttons {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      margin-bottom: 20px;
      padding: 0 10px;
    }

    .navigation-buttons button {
      background: var(--primary-color);
      border: none;
      padding: 8px 12px;
      border-radius: 6px;
      color: white;
      font-weight: 500;
      transition: all 0.3s ease;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      font-size: 0.85rem;
    }

    .navigation-buttons button:hover {
      background: var(--secondary-color);
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    /* Calendar Styles */
    .calendar {
      background: var(--calendar-bg);
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      padding: 10px;
      margin: 15px 0;
      overflow-x: auto;
      border: 1px solid var(--border-color);
    }

    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 3px;
    }

    .day-name {
      font-weight: 600;
      color: var(--primary-color);
      padding: 5px 3px;
      text-align: center;
      background: var(--light-bg);
      border-radius: 5px;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .day {
      aspect-ratio: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3px;
      border-radius: 5px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 500;
      font-size: 0.8rem;
      position: relative;
      background: var(--calendar-bg);
      border: 1px solid var(--border-color);
      min-width: 28px;
      min-height: 28px;
    }

    .day:hover {
      background: var(--day-hover);
      transform: scale(1.05);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .day.has-transfer {
      border: 2px solid var(--primary-color);
    }

    /* Calendar day status colors */
    .check-in-upcoming {
      background-color: var(--success-color) !important;
      color: white;
      border: none !important;
    }

    .check-out-upcoming {
      background-color: var(--warning-color) !important;
      color: white;
      border: none !important;
    }

    .check-in-passed, .check-out-passed {
      background-color: var(--danger-color) !important;
      color: white;
      border: none !important;
    }

    /* Color Legend */
    .color-legend {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
      margin: 20px 0;
      padding: 12px;
      background: var(--calendar-bg);
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      border: 1px solid var(--border-color);
    }

    .legend-item {
      display: flex;
      align-items: center;
      padding: 6px 12px;
      background: var(--light-bg);
      border-radius: 6px;
      font-size: 0.85rem;
      font-weight: 500;
      transition: transform 0.3s ease;
    }

    .legend-item:hover {
      transform: translateY(-2px);
    }

    .legend-color {
      width: 20px;
      height: 20px;
      margin-right: 10px;
      border-radius: 4px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .legend-checkin {
      background-color: var(--success-color);
    }

    .legend-checkout {
      background-color: var(--warning-color);
    }

    .legend-past {
      background-color: var(--danger-color);
    }

    /* Modal Styles */
    .modal-content {
      border-radius: 15px;
      border: none;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .modal-header {
      background: var(--primary-color);
      color: white;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
      padding: 0.8rem;
    }

    .modal-header h5 {
      font-size: 1.3rem;
      font-weight: 600;
      margin: 0;
      letter-spacing: 0.5px;
    }

    .modal-body {
      padding: 1rem;
    }

    .voucher-info {
      background: var(--light-bg);
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 12px;
      border: 1px solid var(--border-color);
    }

    .voucher-info div {
      margin-bottom: 6px;
      padding: 4px 0;
    }

    .voucher-info strong {
      color: var(--dark-bg);
      font-size: 0.9rem;
    }

    .delete-voucher {
      background: var(--danger-color);
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      transition: all 0.3s ease;
      font-weight: 500;
      font-size: 0.85rem;
    }

    .delete-voucher:hover {
      background: #b91c1c;
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    /* Footer */
    .footer {
      background: var(--dark-bg);
      color: #9ca3af;
      padding: 25px;
      text-align: center;
      margin-top: 50px;
      border-top: 3px solid var(--primary-color);
    }

    .footer a {
      color: var(--warning-color);
      text-decoration: none;
      transition: color 0.3s ease;
      font-weight: 500;
    }

    .footer a:hover {
      color: #fbbf24;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .container {
        padding: 10px;
      }

      h1 {
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
      }

      .navigation-buttons {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 20px;
      }

      .navigation-buttons button {
        padding: 10px 15px;
        font-size: 0.85rem;
      }

      .calendar {
        padding: 10px;
        margin: 15px 0;
      }

      .day-name {
        padding: 8px 4px;
        font-size: 0.85rem;
      }

      .day {
        padding: 4px;
        font-size: 0.85rem;
        min-width: 30px;
        min-height: 30px;
      }

      .color-legend {
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 12px;
        margin: 20px 0;
      }

      .legend-item {
        padding: 8px 12px;
        font-size: 0.85rem;
      }

      .legend-color {
        width: 24px;
        height: 24px;
        margin-right: 10px;
      }

      .modal-dialog {
        margin: 0.5rem;
      }

      .modal-body {
        padding: 1rem;
      }

      .voucher-info {
        padding: 12px;
      }

      .voucher-info div {
        padding: 6px 0;
        font-size: 0.9rem;
      }

      .voucher-info strong {
        font-size: 0.9rem;
      }
    }

    @media (max-width: 576px) {
      .container {
        padding: 8px;
      }

      h1 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
      }

      .navigation-buttons {
        grid-template-columns: 1fr;
        gap: 6px;
      }

      .navigation-buttons button {
        padding: 8px 12px;
        font-size: 0.8rem;
      }

      .calendar {
        padding: 8px;
        margin: 10px 0;
      }

      .day-name {
        padding: 6px 3px;
        font-size: 0.75rem;
      }

      .day {
        padding: 3px;
        font-size: 0.75rem;
        min-width: 25px;
        min-height: 25px;
      }

      .color-legend {
        padding: 10px;
        margin: 15px 0;
      }

      .legend-item {
        padding: 6px 10px;
        font-size: 0.75rem;
      }

      .legend-color {
        width: 20px;
        height: 20px;
        margin-right: 8px;
      }

      .modal-header h5 {
        font-size: 1.2rem;
        padding: 0.8rem;
      }

      .modal-body {
        padding: 0.8rem;
      }

      .voucher-info {
        padding: 10px;
      }

      .voucher-info div {
        padding: 4px 0;
        font-size: 0.8rem;
      }

      .voucher-info strong {
        font-size: 0.8rem;
      }

      .delete-voucher {
        padding: 8px 16px;
        font-size: 0.8rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header -->
    <div class="header-logo">
      <img src="logo.png" alt="Logo" class="img-fluid">
    </div>
    <h1>Voucher Takvimi</h1>

    <!-- Navigation Buttons -->
    <div class="navigation-buttons">
      <button class="btn" id="prevYear"><i class="fas fa-chevron-left"></i> Önceki Yıl</button>
      <button class="btn" id="prevMonth"><i class="fas fa-chevron-left"></i> Önceki Ay</button>
      <button class="btn" id="nextMonth">Sonraki Ay <i class="fas fa-chevron-right"></i></button>
      <button class="btn" id="nextYear">Sonraki Yıl <i class="fas fa-chevron-right"></i></button>
    </div>

    <!-- Month & Year Display -->
    <div class="month-year text-center mb-4" id="monthYear"></div>

    <!-- Calendar -->
    <div class="calendar">
      <div class="calendar-grid" id="calendar"></div>
    </div>

    <!-- Color Legend -->
    <div class="color-legend">
      <div class="legend-item">
        <div class="legend-color legend-checkin"></div>
        <span>Giriş</span>
      </div>
      <div class="legend-item">
        <div class="legend-color legend-checkout"></div>
        <span>Çıkış</span>
      </div>
      <div class="legend-item">
        <div class="legend-color legend-past"></div>
        <span>Geçmiş Rezervasyon</span>
      </div>
    </div>
    
    <!-- Voucher Details Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="transferModalLabel">Voucher Detayları</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <ul id="transfer-list" class="list-unstyled"></ul>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p class="mb-0">&copy; <?php echo date("Y"); ?> Tüm Hakları Saklıdır. | <a href="#">Gizlilik Politikası</a> | <a href="#">Kullanım Koşulları</a></p>
    </div>
  </div>

  <!-- jQuery and Bootstrap Bundle (includes Popper) -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function () {
      var vouchers = <?php echo json_encode($vouchers_calendar); ?>;
      var currentDate = new Date();
      var currentYear = currentDate.getFullYear();
      var currentMonth = currentDate.getMonth();
      var monthNames = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];

      function getDaysInMonth(year, month) {
        return new Date(year, month + 1, 0).getDate();
      }

      function renderCalendar(year, month) {
        $('#monthYear').text(monthNames[month] + ' ' + year);
        var firstDay = new Date(year, month, 1).getDay();
        var daysInMonth = getDaysInMonth(year, month);
        var calendar = $('#calendar');
        calendar.empty();

        // Render day names
        var dayNames = ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt'];
        for (var i = 0; i < dayNames.length; i++) {
          calendar.append('<div class="day-name">' + dayNames[i] + '</div>');
        }

        // Blank spaces for days before the first day of month
        for (var i = 0; i < firstDay; i++) {
          calendar.append('<div class="day"></div>');
        }

        // Render each day
        for (var i = 1; i <= daysInMonth; i++) {
          var day = $('<div class="day">' + i + '</div>');
          var fullDate = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(i).padStart(2, '0');

          var hasCheckIn = vouchers.some(function (voucher) {
            return voucher.check_in_date === fullDate;
          });
          var hasCheckOut = vouchers.some(function (voucher) {
            return voucher.check_out_date === fullDate;
          });

          if (hasCheckIn || hasCheckOut) {
            day.addClass('has-transfer');
            if (new Date(fullDate) < new Date()) {
              day.addClass('check-in-passed');
            } else {
              if (hasCheckIn) {
                day.addClass('check-in-upcoming');
              }
              if (hasCheckOut) {
                day.addClass('check-out-upcoming');
              }
            }
            day.data('date', fullDate);
          }
          calendar.append(day);
        }
      }

      $('#calendar').on('click', '.day.has-transfer', function () {
        var date = $(this).data('date');
        $.ajax({
          type: "POST",
          url: "",
          data: { date: date },
          success: function (response) {
            var vouchers = JSON.parse(response);
            var voucherList = $('#transfer-list');
            voucherList.empty();

            vouchers.forEach(function (voucher) {
              var listItem = '<li class="p-3 mb-2 bg-white rounded shadow-sm">' +
                '<div class="voucher-info">' +
                  '<div><strong>Voucher No:</strong></div>' +
                  '<div>' + voucher.voucher_no + '</div>' +
                  '<div><strong>Şirket Adı:</strong></div>' +
                  '<div>' + voucher.company_name + '</div>' +
                  '<div><strong>Otel:</strong></div>' +
                  '<div>' + voucher.hotel + '</div>' +
                  '<div><strong>Oda Sayısı:</strong></div>' +
                  '<div>' + voucher.room_count + '</div>' +
                  '<div><strong>Giriş Tarihi:</strong></div>' +
                  '<div>' + voucher.check_in_date + '</div>' +
                  '<div><strong>Çıkış Tarihi:</strong></div>' +
                  '<div>' + voucher.check_out_date + '</div>' +
                  '<div><strong>Gece:</strong></div>' +
                  '<div>' + voucher.nights + '</div>' +
                  '<div><strong>Oda:</strong></div>' +
                  '<div>' + voucher.room + '</div>' +
                  '<div><strong>Transfer Tipi:</strong></div>' +
                  '<div>' + voucher.transfer_type + '</div>' +
                  '<div><strong>Müşteri Adı:</strong></div>' +
                  '<div>' + voucher.customer_name + '</div>' +
                '</div>' +
                '<button class="btn btn-danger delete-voucher mt-3" data-id="' + voucher.id + '">Sil</button>' +
              '</li>';
              voucherList.append(listItem);
            });
            $('#transferModal').modal('show');
          }
        });
      });

      $(document).on('click', '.delete-voucher', function () {
        var voucherId = $(this).data('id');
        $.ajax({
          type: "POST",
          url: "",
          data: { delete_voucher: voucherId },
          success: function () {
            location.reload();
          }
        });
      });

      $('#prevMonth').click(function () {
        if (currentMonth === 0) {
          currentMonth = 11;
          currentYear--;
        } else {
          currentMonth--;
        }
        renderCalendar(currentYear, currentMonth);
      });

      $('#nextMonth').click(function () {
        if (currentMonth === 11) {
          currentMonth = 0;
          currentYear++;
        } else {
          currentMonth++;
        }
        renderCalendar(currentYear, currentMonth);
      });

      $('#prevYear').click(function () {
        currentYear--;
        renderCalendar(currentYear, currentMonth);
      });

      $('#nextYear').click(function () {
        currentYear++;
        renderCalendar(currentYear, currentMonth);
      });

      renderCalendar(currentYear, currentMonth);
    });
  </script>
</body>
</html>
