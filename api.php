<?php
/**
 * API Endpoints for AJAX calls
 * Handles location tracking, order updates, and real-time notifications
 */

header('Content-Type: application/json; charset=utf-8');

// Include configuration (starts session)
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$user = $_SESSION['user'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    // ==========================================
    // CHECK FOR NEW ORDERS (Real-time polling)
    // ==========================================
    case 'check_orders':
        $last_check = isset($_GET['last_check']) ? (int)$_GET['last_check'] : 0;

        if ($user['role'] == 'driver') {
            // For drivers: check for new pending orders
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders1 WHERE status = 'pending' AND UNIX_TIMESTAMP(created_at) > ?");
            $stmt->execute([$last_check]);
            $result = $stmt->fetch();

            // Get pending orders count
            $pending = $conn->query("SELECT COUNT(*) FROM orders1 WHERE status = 'pending'")->fetchColumn();

            echo json_encode([
                'success' => true,
                'new_orders' => (int)$result['count'],
                'pending_count' => (int)$pending,
                'timestamp' => time(),
                'should_notify' => $result['count'] > 0 && $last_check > 0
            ]);

        } elseif ($user['role'] == 'customer') {
            // For customers: check for order status changes
            $stmt = $conn->prepare("SELECT id, status, driver_id, UNIX_TIMESTAMP(updated_at) as updated_ts FROM orders1 WHERE (customer_name = ? OR client_id = ?) AND UNIX_TIMESTAMP(updated_at) > ? ORDER BY updated_at DESC LIMIT 5");
            $stmt->execute([$user['username'], $user['id'], $last_check]);
            $changed_orders = $stmt->fetchAll();

            $notifications = [];
            foreach ($changed_orders as $order) {
                $notifications[] = [
                    'order_id' => $order['id'],
                    'status' => $order['status'],
                    'driver_id' => $order['driver_id']
                ];
            }

            echo json_encode([
                'success' => true,
                'changed_orders' => $notifications,
                'timestamp' => time(),
                'should_notify' => count($notifications) > 0 && $last_check > 0
            ]);

        } else {
            echo json_encode([
                'success' => true,
                'timestamp' => time()
            ]);
        }
        break;

    // ==========================================
    // UPDATE DRIVER LOCATION
    // ==========================================
    case 'update_location':
        if ($user['role'] !== 'driver') {
            echo json_encode(['success' => false, 'error' => 'Not a driver']);
            exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $lat = floatval($input['lat'] ?? 0);
        $lng = floatval($input['lng'] ?? 0);

        if ($lat && $lng) {
            try {
                $stmt = $conn->prepare("UPDATE users1 SET last_lat = ?, last_lng = ?, last_location_update = NOW() WHERE id = ?");
                $stmt->execute([$lat, $lng, $user['id']]);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Database error']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid coordinates']);
        }
        break;

    // ==========================================
    // GET DRIVER LOCATION (for tracking)
    // ==========================================
    case 'get_driver_location':
        $driverId = (int)($_GET['driver_id'] ?? 0);

        if (!$driverId) {
            echo json_encode(['success' => false, 'error' => 'Driver ID required']);
            exit();
        }

        try {
            $stmt = $conn->prepare("SELECT last_lat, last_lng, last_location_update FROM users1 WHERE id = ? AND role = 'driver'");
            $stmt->execute([$driverId]);
            $driver = $stmt->fetch();

            if ($driver && $driver['last_lat'] && $driver['last_lng']) {
                echo json_encode([
                    'success' => true,
                    'lat' => floatval($driver['last_lat']),
                    'lng' => floatval($driver['last_lng']),
                    'updated' => $driver['last_location_update']
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Location not available']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        break;

    // ==========================================
    // GET ORDER DETAILS
    // ==========================================
    case 'get_order':
        $orderId = (int)($_GET['order_id'] ?? 0);

        if (!$orderId) {
            echo json_encode(['success' => false, 'error' => 'Order ID required']);
            exit();
        }

        try {
            $stmt = $conn->prepare("
                SELECT o.*, u.full_name as driver_name, u.phone as driver_phone, u.avatar_url as driver_avatar,
                       u.last_lat as driver_lat, u.last_lng as driver_lng, u.is_verified as driver_verified
                FROM orders1 o
                LEFT JOIN users1 u ON o.driver_id = u.id
                WHERE o.id = ?
            ");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                // Security check: only owner, driver, or admin can view
                if ($user['role'] === 'admin' ||
                    $order['driver_id'] == $user['id'] ||
                    $order['client_id'] == $user['id'] ||
                    $order['customer_name'] === $user['username']) {

                    echo json_encode(['success' => true, 'order' => $order]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Access denied']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Order not found']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        break;

    // ==========================================
    // CALCULATE DISTANCE BETWEEN TWO POINTS
    // ==========================================
    case 'calculate_distance':
        $lat1 = floatval($_GET['lat1'] ?? 0);
        $lng1 = floatval($_GET['lng1'] ?? 0);
        $lat2 = floatval($_GET['lat2'] ?? 0);
        $lng2 = floatval($_GET['lng2'] ?? 0);

        if ($lat1 && $lng1 && $lat2 && $lng2) {
            $distance = haversineDistanceAPI($lat1, $lng1, $lat2, $lng2);
            $time = ceil($distance / 30 * 60); // 30 km/h average

            echo json_encode([
                'success' => true,
                'distance' => round($distance, 2),
                'time' => $time,
                'unit' => 'km'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid coordinates']);
        }
        break;

    // ==========================================
    // GET USER POINTS
    // ==========================================
    case 'get_user_points':
        $stmt = $conn->prepare("SELECT points FROM users1 WHERE id = ?");
        $stmt->execute([$user['id']]);
        $result = $stmt->fetch();

        echo json_encode([
            'success' => true,
            'points' => (int)$result['points']
        ]);
        break;

    // ==========================================
    // GET USER STATS
    // ==========================================
    case 'get_stats':
        try {
            if ($user['role'] === 'driver') {
                $stats = getDriverStats($conn, $user['id']);
            } elseif ($user['role'] === 'customer') {
                $stats = getClientStats($conn, $user['id'], $user['username']);
            } else {
                // Admin stats
                $stats = [
                    'total_orders' => $conn->query("SELECT COUNT(*) FROM orders1")->fetchColumn(),
                    'pending_orders' => $conn->query("SELECT COUNT(*) FROM orders1 WHERE status='pending'")->fetchColumn(),
                    'total_drivers' => $conn->query("SELECT COUNT(*) FROM users1 WHERE role='driver'")->fetchColumn(),
                    'total_customers' => $conn->query("SELECT COUNT(*) FROM users1 WHERE role='customer'")->fetchColumn()
                ];
            }
            echo json_encode(['success' => true, 'stats' => $stats]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

/**
 * Calculate distance between two points using Haversine formula
 */
function haversineDistanceAPI($lat1, $lon1, $lat2, $lon2) {
    $R = 6371; // Earth's radius in kilometers

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    return $R * $c;
}
?>
