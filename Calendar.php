<?php
$host = 'localhost';
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$database = 'cyntzsrb_cyn';

// Bağlantıyı oluştur
$conn = new mysqli($host, $username, $password, $database);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$success_message = '';

// Silme isteğini işle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_voucher'])) {
    $id = $_POST['delete_voucher'];

    // Veritabanından kuponu sil
    $sql_delete = "DELETE FROM vouchers WHERE id = $id";
    if ($conn->query($sql_delete) === TRUE) {
        $success_message = "Voucher başarıyla silindi";
    } else {
        echo "Kayıt silinirken hata oluştu: " . $conn->error;
    }
}

// Kuponları getir ve alış tarihine göre sırala
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
        'passengers' => nl2br($row['passengers']),
        'hotel_name' => $row['hotel_name'],
        'flight_number' => $row['flight_number']
    );
}

// Kuponları alış tarihine göre sırala, en son tarih önce
usort($vouchers, function($a, $b) {
    return strtotime($b['pickup_date']) - strtotime($a['pickup_date']);
});

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['date'])) {
    $date = $_POST['date'];

    $sql = "SELECT * FROM vouchers WHERE pickup_date = '$date' OR return_date = '$date'";
    $result = $conn->query($sql);

    $transfers = array();
    while ($row = $result->fetch_assoc()) {
        $transfers[] = array(
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
            'passengers' => nl2br($row['passengers']),
            'hotel_name' => $row['hotel_name'],
            'flight_number' => $row['flight_number']
        );
    }

    echo json_encode($transfers);
    $conn->close();
    exit();
}

$sql = "SELECT id, voucher_no, company_name, pickup_date, pickup_time, return_date, return_time FROM vouchers";
$result = $conn->query($sql);

$vouchers_calendar = array();
while ($row = $result->fetch_assoc()) {
    $vouchers_calendar[] = array(
        'id' => $row['id'],
        'title' => $row['company_name'] . ' - ' . $row['voucher_no'],
        'pickup_date' => $row['pickup_date'],
        'pickup_time' => $row['pickup_time'],
        'return_date' => $row['return_date'],
        'return_time' => $row['return_time']
    );
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3b5998;
            --primary-light: #4c70ba;
            --primary-dark: #2f477a;
            --secondary: #5cb85c;
            --warning: #f0ad4e;
            --danger: #d9534f;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-800: #343a40;
            --body-bg: #f5f7fb;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --border-radius: 10px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--body-bg);
            color: var(--dark);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .app-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-300);
        }

        .dashboard-title {
            margin: 0;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.8rem;
        }

        .company-logo {
            height: 60px;
            width: auto;
        }

        .app-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: var(--transition);
        }

        .app-card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .card-header {
            background-color: white;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
        }

        .calendar-controls {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
            padding: 1rem;
            background-color: var(--gray-100);
            border-radius: var(--border-radius);
        }

        .control-btn {
            background-color: white;
            color: var(--primary);
            border: 1px solid var(--primary);
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .control-btn:hover {
            background-color: var(--primary);
            color: white;
        }

        .month-display {
            font-size: 1.4rem;
            font-weight: 600;
            text-align: center;
            margin: 1rem 0;
            color: var(--primary-dark);
            padding: 0.5rem;
            background-color: var(--gray-100);
            border-radius: var(--border-radius);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            padding: 1rem;
        }

        .weekday-header {
            text-align: center;
            font-weight: 600;
            color: var(--primary);
            padding: 0.75rem 0.5rem;
            background-color: var(--gray-100);
            border-radius: 6px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .calendar-day {
            min-height: 90px;
            border-radius: 8px;
            background-color: white;
            border: 1px solid var(--gray-300);
            padding: 0.5rem;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .calendar-day:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .day-number {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .events-indicator {
            display: flex;
            gap: 3px;
            justify-content: center;
            margin-top: auto;
        }

        .event-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--primary);
        }

        .pickup-day {
            background-color: rgba(92, 184, 92, 0.1);
            border-left: 3px solid var(--secondary);
        }

        .return-day {
            background-color: rgba(240, 173, 78, 0.1);
            border-left: 3px solid var(--warning);
        }

        .past-day {
            background-color: rgba(217, 83, 79, 0.05);
            border-left: 3px solid var(--danger);
            opacity: 0.8;
        }

        .today-marker {
            background-color: var(--primary-light);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
        }

        .legend-container {
            display: flex;
            gap: 1.5rem;
            padding: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        /* Modal Styles */
        .custom-modal-header {
            background-color: var(--primary);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .transfer-item {
            background-color: var(--gray-100);
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .transfer-item:hover {
            transform: translateX(3px);
            box-shadow: var(--card-shadow);
        }

        .transfer-detail {
            margin-bottom: 0.5rem;
            display: flex;
        }

        .detail-label {
            font-weight: 600;
            min-width: 140px;
            color: var(--gray-800);
        }

        .detail-value {
            flex: 1;
        }

        .action-btn {
            margin-top: 1rem;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            transition: var(--transition);
        }

        .action-btn.delete {
            background-color: var(--danger);
            border-color: var(--danger);
        }

        .action-btn.delete:hover {
            background-color: #c9302c;
            border-color: #c9302c;
        }

        .app-footer {
            text-align: center;
            padding: 1.5rem 0;
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-top: 2rem;
            border-top: 1px solid var(--gray-300);
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .footer-link {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        @media (max-width: 991px) {
            .calendar-grid {
                gap: 5px;
            }

            .calendar-day {
                min-height: 70px;
                padding: 0.35rem;
            }

            .weekday-header {
                padding: 0.5rem 0.25rem;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 767px) {
            .app-container {
                margin: 1rem auto;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .calendar-controls {
                flex-direction: column;
                gap: 0.5rem;
            }

            .calendar-day {
                min-height: 60px;
                padding: 0.25rem;
            }

            .day-number {
                font-size: 0.85rem;
                margin-bottom: 0.25rem;
            }

            .transfer-detail {
                flex-direction: column;
            }

            .detail-label {
                min-width: auto;
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <header class="dashboard-header">
            <h1 class="dashboard-title">Rezervasyon Yönetim Sistemi</h1>
            <img src="logo.png" alt="Şirket Logosu" class="company-logo">
        </header>

        <div class="app-card">
            <div class="card-header">
                <h3>Transfer Takvimi</h3>
            </div>

            <div class="calendar-controls">
                <button class="control-btn" id="prevYear"><i class="fas fa-angle-double-left"></i> Önceki Yıl</button>
                <button class="control-btn" id="prevMonth"><i class="fas fa-angle-left"></i> Önceki Ay</button>
                <button class="control-btn" id="currentMonth">Bu Ay</button>
                <button class="control-btn" id="nextMonth">Sonraki Ay <i class="fas fa-angle-right"></i></button>
                <button class="control-btn" id="nextYear">Sonraki Yıl <i class="fas fa-angle-double-right"></i></button>
            </div>

            <div class="month-display" id="monthYearDisplay"></div>

            <div class="calendar-grid" id="calendarGrid"></div>

            <div class="legend-container">
                <div class="legend-item">
                    <div class="legend-color" style="background-color: rgba(92, 184, 92, 0.3); border: 1px solid var(--secondary);"></div>
                    <span>Alış Transferi</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: rgba(240, 173, 78, 0.3); border: 1px solid var(--warning);"></div>
                    <span>Dönüş Transferi</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: rgba(217, 83, 79, 0.2); border: 1px solid var(--danger);"></div>
                    <span>Geçmiş Transfer</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: var(--primary-light);"></div>
                    <span>Bugün</span>
                </div>
            </div>
        </div>

        <!-- Transfer Modal -->
        <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header custom-modal-header">
                        <h5 class="modal-title" id="transferModalLabel">Transfer Detayları</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div id="transfersList"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>

        <footer class="app-footer">
            <p>&copy; <?php echo date("Y"); ?> Tüm Hakları Saklıdır.</p>
            <div class="footer-links">
                <a href="#" class="footer-link">Gizlilik Politikası</a>
                <a href="#" class="footer-link">Şartlar ve Koşullar</a>
                <a href="#" class="footer-link">Bize Ulaşın</a>
            </div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // PHP'den gelen veriler
            var vouchers = <?php echo json_encode($vouchers); ?>;
            
            // Takvim değişkenleri
            var currentDate = new Date();
            var currentYear = currentDate.getFullYear();
            var currentMonth = currentDate.getMonth();
            var today = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());
            
            var monthNames = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
            var dayNames = ["Paz", "Pzt", "Sal", "Çar", "Per", "Cum", "Cmt"];
            
            // Takvimi başlat
            renderCalendar(currentYear, currentMonth);
            
            // Navigasyon butonları
            $('#prevMonth').click(function() {
                navigateMonth(-1);
            });
            
            $('#nextMonth').click(function() {
                navigateMonth(1);
            });
            
            $('#prevYear').click(function() {
                navigateYear(-1);
            });
            
            $('#nextYear').click(function() {
                navigateYear(1);
            });
            
            $('#currentMonth').click(function() {
                currentMonth = new Date().getMonth();
                currentYear = new Date().getFullYear();
                renderCalendar(currentYear, currentMonth);
            });
            
            function navigateMonth(change) {
                currentMonth += change;
                
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                } else if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                
                renderCalendar(currentYear, currentMonth);
            }
            
            function navigateYear(change) {
                currentYear += change;
                renderCalendar(currentYear, currentMonth);
            }
            
            function getDaysInMonth(year, month) {
                return new Date(year, month + 1, 0).getDate();
            }
            
            function renderCalendar(year, month) {
                // Ay ve yıl göstergesini güncelle
                $('#monthYearDisplay').text(monthNames[month] + ' ' + year);
                
                // Ayın ilk gününü ve aydaki günleri al
                var firstDay = new Date(year, month, 1).getDay();
                var daysInMonth = getDaysInMonth(year, month);
                
                // Takvimi temizle
                var calendarGrid = $('#calendarGrid');
                calendarGrid.empty();
                
                // Gün isimlerini ekle
                for (var i = 0; i < dayNames.length; i++) {
                    calendarGrid.append('<div class="weekday-header">' + dayNames[i] + '</div>');
                }
                
                // Ayın ilk gününden önceki boş hücreleri ekle
                for (var i = 0; i < firstDay; i++) {
                    calendarGrid.append('<div></div>');
                }
                
                // Günleri ekle
                for (var day = 1; day <= daysInMonth; day++) {
                    var dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                    var cellDate = new Date(year, month, day);
                    
                    // Bu gün için transfer olup olmadığını kontrol et
                    var hasPickup = vouchers.some(function(voucher) {
                        return voucher.pickup_date === dateStr;
                    });
                    
                    var hasReturn = vouchers.some(function(voucher) {
                        return voucher.return_date === dateStr;
                    });
                    
                    // Gün hücresini oluştur
                    var dayCell = $('<div class="calendar-day"></div>');
                    
                    // Bugün olup olmadığını kontrol et
                    if (cellDate.getTime() === today.getTime()) {
                        dayCell.append('<div class="today-marker">' + day + '</div>');
                    } else {
                        dayCell.append('<div class="day-number">' + day + '</div>');
                    }
                    
                    // Transfer göstergelerini ekle
                    if (hasPickup || hasReturn) {
                        var indicators = $('<div class="events-indicator"></div>');
                        
                        if (hasPickup) {
                            indicators.append('<div class="event-dot" style="background-color: var(--secondary);"></div>');
                        }
                        
                        if (hasReturn) {
                            indicators.append('<div class="event-dot" style="background-color: var(--warning);"></div>');
                        }
                        
                        dayCell.append(indicators);
                        
                        // Transfer tipine ve tarihe göre stil ekle
                        if (cellDate < today) {
                            dayCell.addClass('past-day');
                        } else {
                            if (hasPickup) {
                                dayCell.addClass('pickup-day');
                            }
                            if (hasReturn) {
                                dayCell.addClass('return-day');
                            }
                        }
                        
                        // Tıklama işlevini ekle
                        dayCell.data('date', dateStr);
                        dayCell.css('cursor', 'pointer');
                        dayCell.click(function() {
                            showTransfers($(this).data('date'));
                        });
                    }
                    
                    calendarGrid.append(dayCell);
                }
            }
            
            function showTransfers(date) {
                $.ajax({
                    type: 'POST',
                    url: '',
                    data: { date: date },
                    success: function(response) {
                        var transfers = JSON.parse(response);
                        var transfersList = $('#transfersList');
                        transfersList.empty();
                        
                        if (transfers.length === 0) {
                            transfersList.html('<div class="alert alert-info">Bu tarih için transfer bulunamadı.</div>');
                        } else {
                            $('#transferModalLabel').text(formatDate(date) + ' için Transferler');
                            
                            transfers.forEach(function(transfer) {
                                var transferItem = $('<div class="transfer-item"></div>');
                                
                                var details = [
                                    { label: 'Voucher No', value: transfer.voucher_no },
                                    { label: 'Şirket', value: transfer.company_name },
                                    { label: 'Otel', value: transfer.hotel_name },
                                    { label: 'Uçuş No', value: transfer.flight_number },
                                    { label: 'Alış Yeri', value: transfer.pickup_location },
                                    { label: 'Bırakış Yeri', value: transfer.dropoff_location },
                                    { label: 'Alış Tarihi', value: transfer.pickup_date },
                                    { label: 'Alış Saati', value: transfer.pickup_time },
                                    { label: 'Transfer Tipi', value: transfer.transfer_type },
                                    { label: 'Dönüş Tarihi', value: transfer.return_date },
                                    { label: 'Dönüş Saati', value: transfer.return_time },
                                    { label: 'Toplam Yolcu', value: transfer.total_pax }
                                ];
                                
                                details.forEach(function(detail) {
                                    if (detail.value) {
                                        transferItem.append(
                                            '<div class="transfer-detail">' +
                                            '<div class="detail-label">' + detail.label + ':</div>' +
                                            '<div class="detail-value">' + detail.value + '</div>' +
                                            '</div>'
                                        );
                                    }
                                });
                                
                                if (transfer.passengers) {
                                    transferItem.append(
                                        '<div class="transfer-detail">' +
                                        '<div class="detail-label">Yolcular:</div>' +
                                        '<div class="detail-value">' + transfer.passengers + '</div>' +
                                        '</div>'
                                    );
                                }
                                
                                var deleteBtn = $('<button class="btn action-btn delete"><i class="fas fa-trash-alt"></i> Sil</button>');
                                deleteBtn.data('id', transfer.id);
                                deleteBtn.click(function() {
                                    if (confirm('Bu voucher\'ı silmek istediğinizden emin misiniz?')) {
                                        deleteVoucher($(this).data('id'));
                                    }
                                });
                                
                                transferItem.append(deleteBtn);
                                transfersList.append(transferItem);
                            });
                        }
                        
                        // Modalı göster
                        var transferModal = new bootstrap.Modal(document.getElementById('transferModal'));
                        transferModal.show();
                    }
                });
            }
            
            function deleteVoucher(id) {
                $.ajax({
                    type: 'POST',
                    url: '',
                    data: { delete_voucher: id },
                    success: function() {
                        location.reload();
                    }
                });
            }
            
            function formatDate(dateString) {
                var date = new Date(dateString);
                return date.toLocaleDateString('tr-TR', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }
        });
    </script>
</body>
</body>
</html>