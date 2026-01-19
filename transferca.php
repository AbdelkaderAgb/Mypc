<?php
// Veritabanı bağlantı parametreleri
$host = 'localhost';
$username = "cyntzsrb_cyn";
$password = "Qj!d$}Zh,-~m";
$database = 'cyntzsrb_cyn';

// Değişkenleri başlat
$transfers = [];
$selectedMonth = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));
$error_message = "";

// Veritabanına bağlan
try {
    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        throw new Exception("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
    }

    // Seçilen ay için tüm transferleri getir
    $sql = "SELECT * FROM transfer_vouchers WHERE
           (MONTH(pickup_date) = ? AND YEAR(pickup_date) = ?) OR
           (MONTH(return_date) = ? AND YEAR(return_date) = ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $selectedMonth, $selectedYear, $selectedMonth, $selectedYear);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Alış tarihi transferlerini ekle
        $pickupDate = date('Y-m-d', strtotime($row['pickup_date']));
        if (!isset($transfers[$pickupDate])) {
            $transfers[$pickupDate] = [];
        }
        $row['trip_type'] = 'Alış';
        $transfers[$pickupDate][] = $row;

        // Dönüş tarihi transferlerini varsa ekle
        if (!empty($row['return_date'])) {
            $returnDate = date('Y-m-d', strtotime($row['return_date']));
            if (!isset($transfers[$returnDate])) {
                $transfers[$returnDate] = [];
            }
            $returnTrip = $row;
            $returnTrip['trip_type'] = 'Dönüş';
            $transfers[$returnDate][] = $returnTrip;
        }
    }

    $conn->close();
} catch (Exception $e) {
    $error_message = $e->getMessage();
}

// Ay içindeki gün sayısını getirme fonksiyonu
function getDaysInMonth($month, $year) {
    return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}

// Ayın ilk gününü getirme fonksiyonu (0 = Pazar, 6 = Cumartesi)
function getFirstDayOfMonth($month, $year) {
    return date('w', strtotime("$year-$month-01"));
}

// Ay adını getirme fonksiyonu
function getMonthName($month) {
    $months = [
        1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan', 5 => 'Mayıs', 6 => 'Haziran',
        7 => 'Temmuz', 8 => 'Ağustos', 9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
    ];
    return $months[$month];
}


// Takvimi oluştur
$daysInMonth = getDaysInMonth($selectedMonth, $selectedYear);
$firstDayOfMonth = getFirstDayOfMonth($selectedMonth, $selectedYear);
$monthName = getMonthName($selectedMonth);

// Önceki ve sonraki ay bağlantıları
$prevMonth = $selectedMonth - 1;
$prevYear = $selectedYear;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $selectedMonth + 1;
$nextYear = $selectedYear;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Takvimi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            background: linear-gradient(to right, var(--primary-dark), var(--primary));
            margin-left: -1rem;
            margin-right: -1rem;
            padding: 1.5rem 2rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .dashboard-title {
            margin: 0;
            color: white;
            font-weight: 700;
            font-size: 1.8rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .company-logo {
            height: 60px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
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
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
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
            color: white;
        }

        .calendar-controls {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
            padding: 1.5rem;
            background-color: var(--gray-100);
            border-radius: 0;
        }

        .control-btn {
            background-color: white;
            color: var(--primary);
            border: 1px solid var(--primary);
            border-radius: 6px;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .control-btn:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .month-display {
            font-size: 1.4rem;
            font-weight: 600;
            text-align: center;
            margin: 0;
            color: var(--primary-dark);
            padding: 0.7rem 1.5rem;
            background-color: var(--gray-100);
            border-radius: var(--border-radius);
            min-width: 220px;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            padding: 1.5rem;
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
            letter-spacing: 0.5px;
        }

        .calendar-day {
            min-height: 100px;
            border-radius: 8px;
            background-color: white;
            border: 1px solid var(--gray-300);
            padding: 0.5rem;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            position: relative;
            cursor: pointer;
        }

        .calendar-day:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .calendar-day.has-transfers {
            border-left: 4px solid var(--primary);
        }

        .calendar-day.empty {
            background-color: var(--gray-200);
            min-height: 80px;
            cursor: default;
        }

        .calendar-day.empty:hover {
            transform: none;
            box-shadow: none;
        }

        .day-number {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .transfer-count {
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            background-color: var(--primary);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 0.8rem;
            margin: 0 auto;
        }

        .today-marker {
            background-color: var(--primary-light);
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .transfers-list {
            padding: 0 1rem;
        }

        .transfers-list h2 {
            color: var(--primary);
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 0.5rem;
            margin-top: 2rem;
            font-size: 1.3rem;
        }

        .transfer-item {
            background-color: white;
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: var(--transition);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }

        .transfer-item:hover {
            transform: translateX(3px);
            box-shadow: var(--card-shadow);
        }

        .transfer-item p {
            margin: 0.5rem 0;
        }

        .transfer-item .btn-details {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .transfer-item .btn-details:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .app-footer {
            text-align: center;
            padding: 1.5rem 0;
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-top: 2rem;
            border-top: 1px solid var(--gray-300);
        }

        .alert-danger {
            background-color: var(--danger);
            color: white;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Modal stilleri */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 700px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.3s;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            padding: 1.25rem 1.5rem;
            border-top-left-radius: var(--border-radius);
            border-top-right-radius: var(--border-radius);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            color: white;
            margin: 0;
            font-size: 1.3rem;
        }

        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            background-color: var(--gray-100);
            border-bottom-left-radius: var(--border-radius);
            border-bottom-right-radius: var(--border-radius);
            text-align: right;
        }

        .close-btn {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            border: none;
            background: transparent;
            padding: 0;
        }

        .close-btn:hover {
            color: var(--gray-300);
        }

        .transfer-detail-section {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-300);
            padding-bottom: 1rem;
        }

        .transfer-detail-section:last-child {
            border-bottom: none;
        }

        .transfer-detail-section h3 {
            color: var(--primary);
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            margin-bottom: 0.75rem;
        }

        .detail-label {
            display: block;
            font-size: 0.85rem;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-weight: 500;
            color: var(--dark);
        }

        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            margin-left: 0.5rem;
        }

        .badge-primary {
            color: #fff;
            background-color: var(--primary);
        }

        .badge-pickup {
            color: #fff;
            background-color: var(--secondary);
        }

        .badge-return {
            color: #fff;
            background-color: var(--warning);
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .calendar-grid {
                gap: 5px;
                padding: 1rem;
            }

            .calendar-day {
                min-height: 80px;
            }

            .transfer-item {
                grid-template-columns: 1fr;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        /* Araç ipucu stilleri */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 200px;
            background-color: var(--dark);
            color: white;
            text-align: center;
            border-radius: 6px;
            padding: 0.5rem;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.8rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: var(--dark) transparent transparent transparent;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* Yükleme animasyonu */
        .loader {
            border: 3px solid var(--gray-300);
            border-radius: 50%;
            border-top: 3px solid var(--primary);
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="app-container">
        <header class="dashboard-header">
            <h1 class="dashboard-title"><i class="fas fa-calendar-alt"></i> Transfer Takvimi</h1>
            <img src="logo.png" alt="Şirket Logosu" class="company-logo">
        </header>

        <?php if ($error_message): ?>
            <div class="alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="app-card">
            <div class="calendar-controls">
                <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="control-btn">
                    <i class="fas fa-chevron-left"></i> Önceki Ay
                </a>
                <span class="month-display"><?php echo $monthName . ' ' . $selectedYear; ?></span>
                <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="control-btn">
                    Sonraki Ay <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            <div class="calendar-grid">
                <div class="weekday-header">Paz</div>
                <div class="weekday-header">Pzt</div>
                <div class="weekday-header">Sal</div>
                <div class="weekday-header">Çar</div>
                <div class="weekday-header">Per</div>
                <div class="weekday-header">Cum</div>
                <div class="weekday-header">Cmt</div>
                <?php
                // Ayın ilk gününden önceki günler için boş hücreler ekle
                for ($i = 0; $i < $firstDayOfMonth; $i++) {
                    echo '<div class="calendar-day empty"></div>';
                }
                // Ayın günlerini ekle
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $dateStr = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $day);
                    $hasTransfers = isset($transfers[$dateStr]);
                    $isToday = ($dateStr === date('Y-m-d'));
                    $class = 'calendar-day';
                    if ($hasTransfers) {
                        $class .= ' has-transfers';
                    }
                    if ($isToday) {
                        $class .= ' today';
                    }

                    echo '<div class="' . $class . '" data-date="' . $dateStr . '" onclick="showDayTransfers(\'' . $dateStr . '\')">';

                    if ($isToday) {
                        echo '<div class="today-marker">' . $day . '</div>';
                    } else {
                        echo '<span class="day-number">' . $day . '</span>';
                    }

                    if ($hasTransfers) {
                        $count = count($transfers[$dateStr]);
                        echo '<span class="transfer-count" title="' . $count . ' transfer(ler)">' . $count . '</span>';

                        // Gün hücresinin içinde transferlerin mini özetini ekle
                        echo '<div class="day-transfers-preview">';
                        foreach (array_slice($transfers[$dateStr], 0, 2) as $transfer) {
                            $badgeClass = $transfer['trip_type'] === 'Alış' ? 'badge-pickup' : 'badge-return';
                            echo '<div class="transfer-preview-item"><span class="badge mini ' . $badgeClass . '">' . $transfer['trip_type'] . '</span> ' . $transfer['pickup_time'] . '</div>';
                        }
                        if (count($transfers[$dateStr]) > 2) {
                            echo '<div class="transfer-preview-more">+' . (count($transfers[$dateStr]) - 2) . '</div>';
                        }
                        echo '</div>';
                    }

                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- Gün Transferleri Modali -->
        <div id="dayTransfersModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-calendar-day"></i> <span id="dayTransfersDate"></span></h2>
                    <button class="close-btn" onclick="closeDayModal()">×</button>
                </div>
                <div class="modal-body" id="dayTransfersList">
                    <!-- Gün transferleri buraya yüklenecek -->
                </div>
                <div class="modal-footer">
                    <button class="control-btn" onclick="closeDayModal()">Kapat</button>
                </div>
            </div>
        </div>

        <!-- Transfer Detayları Modali -->
        <div id="transferModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-exchange-alt"></i> Transfer Detayları</h2>
                    <button class="close-btn" onclick="closeModal()">×</button>
                </div>
                <div class="modal-body" id="transferDetails">
                    <!-- Transfer detayları buraya yüklenecek -->
                </div>
                <div class="modal-footer">
                    <button class="control-btn" onclick="closeModal()">Kapat</button>
                </div>
            </div>
        </div>

        <footer class="app-footer">
            <p>© <?php echo date("Y"); ?> Tüm Hakları Saklıdır.</p>
        </footer>
    </div>

    <script>
        // Fetch transfer details and then show modal
        async function fetchTransferDetails(voucherNo) {
            const transfers = <?php echo json_encode($transfers); ?>;
            let selectedTransfer = null;

            for (const date in transfers) {
                for (const transfer of transfers[date]) {
                    if (transfer.voucher_no === voucherNo) {
                        selectedTransfer = transfer;
                        break;
                    }
                }
                if (selectedTransfer) {
                    break;
                }
            }

            if (selectedTransfer) {
                showTransferDetails(selectedTransfer);
            } else {
                alert("Transfer details not found.");
            }
        }


        // Modalda transfer detaylarını göster
        function showTransferDetails(transfer) {
            const modal = document.getElementById('transferModal');
            const detailsContainer = document.getElementById('transferDetails');

            let tripType = transfer.trip_type;
            let badgeClass = tripType === 'Alış' ? 'badge-pickup' : 'badge-return';

            let html = `
                <div class="transfer-detail-section">
                    <h3><i class="fas fa-ticket-alt"></i> Voucher Bilgileri</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Seyahat Türü</span>
                            <span class="detail-value">${transfer.trip_type} <span class="badge ${badgeClass}">${transfer.trip_type}</span></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Voucher Numarası</span>
                            <span class="detail-value">${transfer.voucher_no}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Şirket</span>
                            <span class="detail-value">${transfer.company_name}</span>
                        </div>
                    </div>
                </div>

                <div class="transfer-detail-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Lokasyon Bilgileri</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Alış Lokasyonu</span>
                            <span class="detail-value">${transfer.pickup_location}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Bırakma Lokasyonu</span>
                            <span class="detail-value">${transfer.dropoff_location}</span>
                        </div>
                    </div>
                </div>

                <div class="transfer-detail-section">
                    <h3><i class="fas fa-clock"></i> Planlama Bilgileri</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Alış Tarihi</span>
                            <span class="detail-value">${formatDate(transfer.pickup_date)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Alış Saati</span>
                            <span class="detail-value">${transfer.pickup_time}</span>
                        </div>`;

            if (transfer.return_date) {
                html += `
                        <div class="detail-item">
                            <span class="detail-label">Dönüş Tarihi</span>
                            <span class="detail-value">${formatDate(transfer.return_date)}</span>
                        </div>`;
            }

            html += `
                    </div>
                </div>`;

            detailsContainer.innerHTML = html;
            modal.style.display = 'flex';
        }

        // Belirli bir gün için transferleri göster - Geliştirilmiş versiyon
        function showDayTransfers(date) {
            const transfers = <?php echo json_encode($transfers); ?>;
            const dayTransfers = transfers[date] || [];

            const modal = document.getElementById('dayTransfersModal');
            const dateDisplay = document.getElementById('dayTransfersDate');
            const listContainer = document.getElementById('dayTransfersList');

            // Tarihi görüntülemek için biçimlendir
            const formattedDate = formatDate(date);
            dateDisplay.textContent = formattedDate;

            if (dayTransfers.length === 0) {
                listContainer.innerHTML = '<p>Bu gün için planlanmış transfer yok.</p>';
            } else {
                // Üst kısma özet ekle
                let html = `
                    <div class="transfers-summary">
                        <div class="summary-total">${dayTransfers.length} planlanmış transfer</div>
                        <div class="summary-types">
                            <span class="summary-pickup">${dayTransfers.filter(t => t.trip_type === 'Alış').length} alışlar</span>
                            <span class="summary-return">${dayTransfers.filter(t => t.trip_type === 'Dönüş').length} dönüşler</span>
                        </div>
                    </div>
                    <div class="transfers-list">
                `;

                // Transferleri saate göre sırala
                const sortedTransfers = [...dayTransfers].sort((a, b) => {
                    return a.pickup_time.localeCompare(b.pickup_time);
                });

                // Daha iyi organizasyon için transferleri saate göre gruplandır
                const transfersByTime = {};
                sortedTransfers.forEach(transfer => {
                    if (!transfersByTime[transfer.pickup_time]) {
                        transfersByTime[transfer.pickup_time] = [];
                    }
                    transfersByTime[transfer.pickup_time].push(transfer);
                });

                // Saat gruplarını oluştur
                Object.keys(transfersByTime).sort().forEach(time => {
                    html += `<div class="time-group"><div class="time-label">${time}</div>`;

                    transfersByTime[time].forEach(transfer => {
                        let tripType = transfer.trip_type;
                        let badgeClass = tripType === 'Alış' ? 'badge-pickup' : 'badge-return';

                        html += `
                            <div class="transfer-item">
                                <div class="transfer-header">
                                    <span class="badge ${badgeClass}">${transfer.trip_type}</span>
                                    <button class="btn-details" onclick="fetchTransferDetails('${transfer.voucher_no}')">
                                        <i class="fas fa-info-circle"></i> Detaylar
                                    </button>
                                </div>
                                <div class="transfer-info">
                                    <div class="transfer-company">${transfer.company_name}</div>
                                    <div class="transfer-route">
                                        <span class="transfer-location">${transfer.pickup_location}</span>
                                        <i class="fas fa-long-arrow-alt-right"></i>
                                        <span class="transfer-location">${transfer.dropoff_location}</span>
                                    </div>
                                    <div class="transfer-voucher">Voucher: ${transfer.voucher_no}</div>
                                </div>
                            </div>
                        `;
                    });

                    html += `</div>`;
                });

                html += '</div>'; // Close transfers-list
                listContainer.innerHTML = html;
            }

            modal.style.display = 'flex';
        }

        // Modali kapat
        function closeModal() {
            document.getElementById('transferModal').style.display = 'none';
        }

        // Gün transferleri modalını kapat
        function closeDayModal() {
            document.getElementById('dayTransfersModal').style.display = 'none';
        }

        // Tarih biçimlendirme yardımcı fonksiyonu
        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('tr-TR', options);
        }

        // Dışarıya tıklayınca modalleri kapat
        window.onclick = function(event) {
            const transferModal = document.getElementById('transferModal');
            const dayModal = document.getElementById('dayTransfersModal');

            if (event.target === transferModal) {
                transferModal.style.display = 'none';
            } else if (event.target === dayModal) {
                dayModal.style.display = 'none';
            }
        }

        // Modaller için escape tuşu işleyicisi ekle
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
                closeDayModal();
            }
        });
    </script>

    <style>
        /* Orijinal stiller */
        .calendar-day {
            position: relative;
            min-height: 100px;
            border: 1px solid #ddd;
            padding: 5px;
            background-color: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .calendar-day:hover {
            background-color: #f8f9fa;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .day-number {
            position: absolute;
            top: 5px;
            right: 5px;
            font-weight: bold;
        }

        .today-marker {
            position: absolute;
            top: 2px;
            right: 5px;
            font-weight: bold;
            color: #007bff;
            padding: 2px;
            border-radius: 50%;
            background-color: rgba(0,123,255,0.1);
        }

        .transfer-count {
            position: absolute;
            top: 5px;
            left: 5px;
            font-size: 0.8em;
            background-color: #007bff;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .day-transfers-preview {
            position: absolute;
            top: 30px;
            left: 0;
            right: 0;
            padding: 0 5px;
        }

        .transfer-preview-item {
            font-size: 0.7em;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .badge.mini {
            font-size: 0.6em;
            padding: 1px 3px;
        }

        .transfer-preview-more {
            font-size: 0.7em;
            color: #6c757d;
            text-align: center;
            margin-top: 2px;
        }

        .badge-pickup {
            background-color: #28a745;
        }

        .badge-return {
            background-color: #dc3545;
        }

        .transfer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .transfer-time {
            font-weight: bold;
        }

        .transfer-info {
            padding-left: 5px;
            border-left: 3px solid #ddd;
        }

        .transfer-company {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .transfer-route {
            display: flex;
            align-items: center;
            font-size: 0.9em;
            margin-bottom: 3px;
        }

        .transfer-route i {
            margin: 0 5px;
            color: #6c757d;
        }

        .transfer-voucher {
            font-size: 0.8em;
            color: #6c757d;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.8em;
            color: #6c757d;
        }

        .detail-value {
            font-weight: bold;
        }

        .transfer-detail-section {
            margin-bottom: 20px;
        }

        .transfer-detail-section h3 {
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        /* Tüm Cihazlar için Duyarlı Tasarım */
        @media screen and (max-width: 768px) {
            /* Takvim Düzeni İyileştirmeleri */
            .calendar-grid {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 2px;
            }

            .calendar-day {
                min-height: 60px;
                font-size: 0.9em;
                padding: 2px;
            }

            .today-marker, .day-number {
                top: 2px;
                right: 2px;
                font-size: 0.9em;
            }

            .transfer-count {
                font-size: 0.7em;
                padding: 1px 4px;
            }

            .day-transfers-preview {
                top: 20px;
            }

            .transfer-preview-item {
                font-size: 0.6em;
                margin-bottom: 2px;
            }

            /* Modal İyileştirmeleri */
            .modal-content {
                width: 95%;
                max-width: 400px;
                margin: 10px auto;
            }

            .modal-header h2 {
                font-size: 1.2em;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .transfer-item {
                padding: 8px;
            }
        }

        /* Ekstra Küçük Cihazlar */
        @media screen and (max-width: 480px) {
            .calendar-controls {
                flex-direction: column;
                align-items: center;
            }

            .control-btn {
                margin: 5px 0;
                font-size: 0.8em;
            }

            .calendar-day {
                min-height: 50px;
            }

            .transfer-preview-item {
                display: none;
            }

            .transfer-count {
                left: 50%;
                transform: translateX(-50%);
                top: 50%;
                margin-top: -10px;
            }

            .day-number, .today-marker {
                left: 2px;
                right: auto;
            }

            .transfer-route {
                flex-direction: column;
                align-items: flex-start;
            }

            .transfer-route i {
                transform: rotate(90deg);
                margin: 2px 0;
            }
        }

        /* Küçük Ekranlarda Modalın Kaydırılabilir Olmasını Sağla */
        .modal {
            overflow-y: auto;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Küçük Ekranlarda Ay Görüntüsünü Düzelt */
        .month-display {
            text-align: center;
            padding: 5px 0;
        }

        /* Genel Duyarlılığı İyileştir */
        .app-container {
            max-width: 100%;
            padding: 10px;
        }

        .app-card {
            padding: 10px;
        }

        /* Transfer Görüntüsünü Daha Net Hale Getir */
        .transfer-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        /* Rozet Görünürlüğünü İyileştir */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            color: white;
            font-size: 0.8em;
            font-weight: bold;
        }

        /* Çoklu Transfer Görüntüsü için Stiller */
        .transfers-summary {
            background-color: #f0f8ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .summary-total {
            font-size: 1.1em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-types {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .summary-pickup {
            color: #28a745;
            font-weight: bold;
        }

        .summary-return {
            color: #dc3545;
            font-weight: bold;
        }

        .time-group {
            margin-bottom: 15px;
            border-left: 3px solid #007bff;
            padding-left: 10px;
        }

        .time-label {
            background-color: #007bff;
            color: white;
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .transfers-list {
            max-height: 60vh;
            overflow-y: auto;
            padding-right: 5px;
        }

        /* Mobil cihazlarda yüklemeyi iyileştir */
        @media screen and (max-width: 480px) {
            .time-group {
                padding-left: 5px;
            }

            .transfer-item {
                padding: 8px;
            }

            .summary-types {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</body>

</html>