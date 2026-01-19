<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Gerekli dosyaları dahil et
require_once 'database.php';
require_once 'helpers.php';

// Makbuz silme işlemini yönet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_receipt_id'])) {
    $receipt_id_to_delete = intval($_POST['delete_receipt_id']);
    // Yönlendirme için şirket kimliğini al
    $company_id_redirect = isset($_GET['company']) ? intval($_GET['company']) : 0;

    if ($receipt_id_to_delete > 0) {
        $sql = "DELETE FROM receipts WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$receipt_id_to_delete]);
    }

    // Formun yeniden gönderilmesini önlemek için aynı sayfaya yönlendir
    header("Location: dashboard.php?company=" . $company_id_redirect);
    exit;
}

// Şirket kimliğini al
$companyId = isset($_GET['company']) ? intval($_GET['company']) : 0;

if ($companyId) {
    // Tek bir şirketin makbuzlarını görüntüleme mantığı
    $sql_receipts = "SELECT r.id, r.receipt_date, r.subject, r.received_by, p.company 
                     FROM receipts r 
                     JOIN partners p ON r.partner_id = p.id 
                     WHERE r.partner_id = ? 
                     ORDER BY r.receipt_date DESC, r.id DESC";
    $stmt_receipts = $pdo->prepare($sql_receipts);
    $stmt_receipts->execute([$companyId]);
    $receipts = $stmt_receipts->fetchAll(PDO::FETCH_ASSOC);

    // Tabloda göstermek için bu makbuzlara ait tüm ödemeleri çek
    $sql_payments = "SELECT rp.receipt_id, rp.amount, rp.currency, rp.money_provider
                     FROM receipt_payments rp
                     JOIN receipts r ON rp.receipt_id = r.id
                     WHERE r.partner_id = ?";
    $stmt_payments = $pdo->prepare($sql_payments);
    $stmt_payments->execute([$companyId]);
    $all_payments = $stmt_payments->fetchAll(PDO::FETCH_ASSOC);

    // Kolay erişim için ödemeleri makbuz kimliğine göre grupla
    $payments_by_receipt = [];
    foreach ($all_payments as $payment) {
        $payments_by_receipt[$payment['receipt_id']][] = $payment;
    }

    $companyName = $receipts ? $receipts[0]['company'] : 'Şirket Bulunamadı';

    // Bu şirket için receipt_payments tablosundan toplamları hesapla
    $sql_totals = "SELECT rp.currency, SUM(rp.amount) as total_sum
                   FROM receipt_payments rp
                   JOIN receipts r ON rp.receipt_id = r.id
                   WHERE r.partner_id = ?
                   GROUP BY rp.currency";
    $stmt_totals = $pdo->prepare($sql_totals);
    $stmt_totals->execute([$companyId]);
    $totals_data = $stmt_totals->fetchAll(PDO::FETCH_ASSOC);

    $currency_totals = [];
    foreach($totals_data as $row) {
        $currency_totals[$row['currency']] = $row['total_sum'];
    }
    $receiptCount = count($receipts);

} else {
    // Ana panel görünümü mantığı (tüm şirketler)
    // Adım 1: Tüm şirketleri ve toplam makbuz sayılarını al.
    $sql_companies = "SELECT p.id, p.company, 
                          (SELECT COUNT(*) FROM receipts r WHERE r.partner_id = p.id) AS receipt_count
                      FROM partners p
                      ORDER BY p.company";
    $all_companies = $pdo->query($sql_companies)->fetchAll(PDO::FETCH_ASSOC);

    // Adım 2: Şirket ve para birimine göre gruplandırılmış tüm ödeme toplamlarını al.
    $sql_totals = "SELECT r.partner_id, rp.currency, SUM(rp.amount) as total_sum
                   FROM receipt_payments rp
                   JOIN receipts r ON rp.receipt_id = r.id
                   GROUP BY r.partner_id, rp.currency";
    $all_totals = $pdo->query($sql_totals)->fetchAll(PDO::FETCH_ASSOC);

    // Adım 3: Kolay erişim için toplamları şirkete göre düzenle.
    $totals_by_company = [];
    foreach ($all_totals as $total_row) {
        $totals_by_company[$total_row['partner_id']][$total_row['currency']] = $total_row['total_sum'];
    }

    // Adım 4: Verileri görüntüleme için son bir yapıda birleştir.
    $companies = [];
    foreach ($all_companies as $company_row) {
        $id = $company_row['id'];
        $companies[$id] = [
            'id' => $id,
            'company' => $company_row['company'],
            'receipt_count' => $company_row['receipt_count'],
            'totals' => $totals_by_company[$id] ?? []
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $companyId ? htmlspecialchars($companyName) : 'Makbuz Paneli' ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-gold: #c5a47e; /* Rafine, daha az parlak bir altın rengi */
            --gradient-start: #e6c9a7;
            --gradient-end: #c5a47e;
            --success-color: #28a745;
            --light-gray: #fdfcf9; /* Daha sıcak bir kırık beyaz */
            --border-color: #e9ecef;
            --card-shadow: 0 4px 8px rgba(0, 0, 0, 0.04);
            --card-hover-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            --border-radius: .5rem;
            --font-family-header: 'Playfair Display', serif;
            --font-family-sans-serif: 'Inter', sans-serif;
        }

        /* --- Genel & Düzen --- */
        body {
            background-color: var(--light-gray);
            font-family: var(--font-family-sans-serif);
            color: #333;
        }
        h1, .h1, h2, .h2, .page-header .h2, .stat-card-value, .navbar-brand, .company-card .card-title {
            font-family: var(--font-family-header);
        }
        .text-gold {
            color: var(--primary-gold) !important;
        }
        .main-container {
             animation: fadeInUp .5s ease-out forwards;
        }
        .page-header {
            animation: fadeInUp .5s ease-out .1s forwards;
            opacity: 0;
        }

        /* --- Gezinme Çubuğu --- */
        .navbar {
            background-color: #fff;
            border-bottom: 1px solid var(--border-color);
            box-shadow: none;
        }

        /* --- Düğmeler --- */
        .btn-gradient {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(0,0,0,.1);
            transition: opacity .2s;
        }
        .btn-gradient:hover {
            opacity: 0.9;
        }

        .btn-outline-gold {
            color: var(--primary-gold);
            border-color: var(--primary-gold);
        }

        .btn-outline-gold:hover {
            color: #fff;
            background-color: var(--primary-gold);
            border-color: var(--primary-gold);
        }

        /* --- Kartlar --- */
        .card {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            opacity: 0;
            animation: fadeInUp .5s ease-out forwards;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-hover-shadow);
        }
        .card-header {
            background-color: #fff;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
        }

        /* --- Şirket Kartları (Panel) --- */
        .company-card a {
            text-decoration: none;
            color: inherit;
        }
        .company-card .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .company-card .card-title {
            font-size: 1.25rem; /* Playfair için daha büyük */
            font-weight: 700;
            margin-top: 1rem;
            color: var(--primary-gold);
        }
        .company-card-stats {
            display: flex;
            justify-content: space-around;
            width: 100%;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .company-card-stats strong {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .avatar-initials {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
            font-family: var(--font-family-header);
        }
        
        /* --- İstatistik Kartları (Detay Görünümü) --- */
        .stat-card {
            animation-delay: .2s;
        }
        .stat-card .card-body {
            display: flex;
            align-items: center;
        }
        .stat-card-icon {
            font-size: 1.75rem;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-right: 1rem;
            background-color: #fef6ec; /* Açık altın arkaplan */
        }
        .stat-card-icon.icon-receipts { color: var(--primary-gold); }
        .stat-card-icon.icon-subtotal { color: #fd7e14; }
        .stat-card-icon.icon-total { color: var(--success-color); }
        .stat-card-title {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        .stat-card-value {
            font-size: 1.75rem; /* Playfair için daha büyük */
            font-weight: 700;
        }
        
        /* --- Tablo --- */
        .table-container-card {
             animation-delay: .3s;
        }
        .table thead th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: none;
            background-color: #f8f9fa;
        }
        .table td {
            vertical-align: middle;
        }

        /* --- Boş Durum --- */
        .empty-state {
            text-align: center;
            padding: 3rem;
            opacity: 0.7;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        /* --- Alt Bilgi --- */
        footer {
            border-top: 1px solid var(--border-color);
            padding: 1.5rem 0;
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 3rem;
            background: #fff;
        }
        
        /* --- Animasyonlar --- */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-file-invoice-dollar text-gold"></i>
            Makbuzlar
        </a>
        <a href="create_receipt.php" class="btn btn-gradient ml-auto">
            <i class="fas fa-plus-circle mr-1"></i> Yeni Makbuz
        </a>
    </div>
</nav>

<div class="container main-container">
    <?php if ($companyId): ?>
        <!-- ======================= -->
        <!-- Tek Şirket Görünümü     -->
        <!-- ======================= -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 mb-0"><?= htmlspecialchars($companyName) ?></h1>
            <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Panele Geri Dön
            </a>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <div class="stat-card-icon icon-receipts"><i class="fas fa-file-alt"></i></div>
                        <div>
                            <div class="stat-card-title">Toplam Makbuz</div>
                            <div class="stat-card-value"><?= $receiptCount ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php foreach ($currency_totals as $currency => $total): ?>
                <div class="col-md-6">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="stat-card-icon icon-total"><i class="fas fa-wallet"></i></div>
                            <div>
                                <div class="stat-card-title">Toplam (<?= htmlspecialchars($currency) ?>)</div>
                                <div class="stat-card-value"><?= get_currency_symbol($currency) . number_format($total, 2) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Makbuz Tablosu -->
        <div class="card table-container-card">
            <div class="card-header">
                Makbuz Geçmişi
            </div>
            <?php if (empty($receipts)): ?>
                <div class="card-body">
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <h4>Hiç Makbuz Bulunamadı</h4>
                        <p class="text-muted">Bu şirket için henüz kaydedilmiş bir makbuz bulunmuyor.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tarih</th>
                                <th>Teslim Alan</th>
                                <th>Konu</th>
                                <th>Ödeyen</th>
                                <th class="text-right">Ödemeler</th>
                                <th class="text-center">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($receipts as $i => $row): ?>
                            <tr style="animation: fadeInUp .5s ease-out <?= .05 * $i ?>s forwards; opacity: 0;">
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= date('d/m/y', strtotime($row['receipt_date'])) ?></td>
                                <td><?= htmlspecialchars($row['received_by'] ?: 'Yok') ?></td>
                                <td><?= htmlspecialchars($row['subject']) ?></td>
                                <td>
                                    <?php 
                                    $receipt_payments = $payments_by_receipt[$row['id']] ?? [];
                                    if (!empty($receipt_payments)) {
                                        echo htmlspecialchars($receipt_payments[0]['money_provider']);
                                    } else {
                                        echo 'Yok';
                                    }
                                    ?>
                                </td>
                                <td class="text-right font-weight-bold" style="min-width: 150px;">
                                    <?php if (!empty($receipt_payments)): ?>
                                        <?php foreach ($receipt_payments as $payment): ?>
                                            <div><?= get_currency_symbol($payment['currency']) . number_format($payment['amount'], 2) ?></div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        Yok
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-outline-gold" href="view_receipt.php?id=<?= $row['id'] ?>" target="_blank" title="Makbuzu Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="dashboard.php?company=<?= $companyId ?>" method="POST" class="d-inline" onsubmit="return confirm('#<?= $row['id'] ?> numaralı makbuzu silmek istediğinizden emin misiniz?');">
                                        <input type="hidden" name="delete_receipt_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Makbuzu Sil">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- ======================= -->
        <!-- Tüm Şirketler Görünümü  -->
        <!-- ======================= -->
        <div class="page-header mb-4">
            <h1 class="h2">Şirket Paneli</h1>
            <p class="text-muted">Detaylı makbuz geçmişini görüntülemek için bir şirket seçin.</p>
        </div>
        <div class="row">
            <?php foreach ($companies as $i => $c): ?>
            <div class="col-md-6 col-lg-4 mb-4 company-card" style="animation-delay: <?= .1 * $i ?>s;">
                <a href="dashboard.php?company=<?= $c['id'] ?>">
                    <div class="card h-100">
                        <div class="card-body p-4">
                            <?= generate_initials_avatar($c['company']) ?>
                            <h5 class="card-title"><?= htmlspecialchars($c['company']) ?></h5>
                            <div class="company-card-stats border-top pt-3">
                                <div>
                                    Makbuzlar
                                    <strong><?= $c['receipt_count'] ?></strong>
                                </div>
                                <div class="w-100 border-top mt-2 pt-2">
                                    Toplam Değer
                                    <?php if (empty($c['totals'])): ?>
                                        <strong>Makbuz yok</strong>
                                    <?php else: ?>
                                        <?php foreach ($c['totals'] as $currency => $total): ?>
                                            <strong class="d-block"><?= get_currency_symbol($currency) . number_format($total, 2) ?></strong>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<footer class="mt-5">
    © <?= date('Y') ?> Cyntour. Tüm hakları saklıdır.
</footer>

</body>
</html>