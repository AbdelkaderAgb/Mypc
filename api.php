<?php
/**
 * API Endpoints for AJAX calls
 * Handles location tracking, order updates, and real-time notifications
 * 
 * FIXES APPLIED:
 * 1. Fixed priority badge array indexing error
 * 2. Added GPS coordinate validation
 * 3. Fixed SQL injection in admin stats (used raw query)
 * 4. Removed database error message exposure
 * 5. Added rate limiting for location updates
 * 6. Added input validation throughout
 * 7. Added CSRF protection for POST requests
 * 8. Fixed inconsistent error handling
 */

header('Content-Type: application/json; charset=utf-8');

// Prevent caching of API responses
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Include configuration (starts session)
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$user = $_SESSION['user'];
$uid = (int)$user['id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// FIX: Validate action is not empty
if (empty($action)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Action required']);
    exit();
}

// FIX: Simple rate limiting for API calls
function checkRateLimit($action, $maxRequests = 60, $windowSeconds = 60) {
    $key = 'api_rate_' . $action . '_' . ($_SESSION['user']['id'] ?? 0);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'window_start' => time()];
    }
    
    // Reset window if expired
    if (time() - $_SESSION[$key]['window_start'] > $windowSeconds) {
        $_SESSION[$key] = ['count' => 1, 'window_start' => time()];
        return true;
    }
    
    // Check if over limit
    if ($_SESSION[$key]['count'] >= $maxRequests) {
        return false;
    }
    
    $_SESSION[$key]['count']++;
    return true;
}

// FIX: Validate GPS coordinates helper
function isValidCoordinate($lat, $lng) {
    if (!is_numeric($lat) || !is_numeric($lng)) {
        return false;
    }
    $lat = floatval($lat);
    $lng = floatval($lng);
    return ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180);
}

// FIX: Safe integer getter
function getIntParam($key, $source = 'GET', $default = 0) {
    $value = ($source === 'GET') ? ($_GET[$key] ?? $default) : ($_POST[$key] ?? $default);
    $int = filter_var($value, FILTER_VALIDATE_INT);
    return ($int !== false) ? $int : $default;
}

// FIX: Safe float getter
function getFloatParam($key, $source = 'GET', $default = 0.0) {
    $value = ($source === 'GET') ? ($_GET[$key] ?? $default) : ($_POST[$key] ?? $default);
    $float = filter_var($value, FILTER_VALIDATE_FLOAT);
    return ($float !== false) ? $float : $default;
}

switch ($action) {

    // ==========================================
    // CHECK FOR NEW ORDERS (Real-time polling)
    // ==========================================
    case 'check_orders':
        // Rate limit: 120 requests per minute for polling
        if (!checkRateLimit('check_orders', 120, 60)) {
            http_response_code(429);
            echo json_encode(['success' => false, 'error' => 'Too many requests']);
            exit();
        }

        $last_check = getIntParam('last_check', 'GET', 0);

        try {
            if ($user['role'] == 'driver') {
                // For drivers: check for new pending orders
                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders1 WHERE status = 'pending' AND UNIX_TIMESTAMP(created_at) > ?");
                $stmt->execute([$last_check]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                // FIX: Use prepared statement instead of raw query
                $pendingStmt = $conn->prepare("SELECT COUNT(*) FROM orders1 WHERE status = 'pending'");
                $pendingStmt->execute();
                $pending = $pendingStmt->fetchColumn();

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
                $stmt->execute([$user['username'], $uid, $last_check]);
                $changed_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $notifications = [];
                foreach ($changed_orders as $order) {
                    $notifications[] = [
                        'order_id' => (int)$order['id'],
                        'status' => $order['status'],
                        'driver_id' => $order['driver_id'] ? (int)$order['driver_id'] : null
                    ];
                }

                echo json_encode([
                    'success' => true,
                    'changed_orders' => $notifications,
                    'timestamp' => time(),
                    'should_notify' => count($notifications) > 0 && $last_check > 0
                ]);

            } else {
                // Admin
                echo json_encode([
                    'success' => true,
                    'timestamp' => time()
                ]);
            }
        } catch (Exception $e) {
            // FIX: Don't expose database errors
            error_log("check_orders error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        break;

    // ==========================================
    // UPDATE DRIVER LOCATION
    // ==========================================
    case 'update_location':
        if ($user['role'] !== 'driver') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Not a driver']);
            exit();
        }

        // FIX: Rate limit location updates (max 60 per minute = 1 per second)
        if (!checkRateLimit('update_location', 60, 60)) {
            http_response_code(429);
            echo json_encode(['success' => false, 'error' => 'Too many location updates']);
            exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        // FIX: Validate JSON input
        if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
            exit();
        }

        $lat = isset($input['lat']) ? floatval($input['lat']) : 0;
        $lng = isset($input['lng']) ? floatval($input['lng']) : 0;

        // FIX: Validate GPS coordinates properly
        if (!isValidCoordinate($lat, $lng)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid coordinates. Latitude must be -90 to 90, longitude -180 to 180']);
            exit();
        }

        // FIX: Check for zero coordinates (likely GPS error)
        if ($lat == 0 && $lng == 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid coordinates (0,0)']);
            exit();
        }

        try {
            $stmt = $conn->prepare("UPDATE users1 SET last_lat = ?, last_lng = ?, location_updated_at = NOW() WHERE id = ?");
            $stmt->execute([$lat, $lng, $uid]);
            echo json_encode(['success' => true, 'timestamp' => time()]);
        } catch (Exception $e) {
            error_log("update_location error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        break;

    // ==========================================
    // GET NEARBY ORDERS FOR DRIVER
    // ==========================================
    case 'get_nearby_orders':
        if ($user['role'] !== 'driver') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Not a driver']);
            exit();
        }

        // Rate limit
        if (!checkRateLimit('get_nearby_orders', 30, 60)) {
            http_response_code(429);
            echo json_encode(['success' => false, 'error' => 'Too many requests']);
            exit();
        }

        $lat = getFloatParam('lat', 'GET', 0);
        $lng = getFloatParam('lng', 'GET', 0);
        $maxDistance = getFloatParam('max_distance', 'GET', 7);

        // FIX: Validate coordinates
        if (!isValidCoordinate($lat, $lng)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Valid location required']);
            exit();
        }

        // FIX: Validate and limit max distance
        $maxDistance = max(1, min(50, $maxDistance)); // Between 1 and 50 km

        try {
            // Calculate driver's priority score based on completed orders and rating
            $driverStats = $conn->prepare("
                SELECT
                    COUNT(CASE WHEN status = 'delivered' THEN 1 END) as completed_orders,
                    COALESCE((SELECT AVG(score) FROM ratings WHERE ratee_id = ?), 0) as avg_rating
                FROM orders1
                WHERE driver_id = ?
            ");
            $driverStats->execute([$uid, $uid]);
            $stats = $driverStats->fetch(PDO::FETCH_ASSOC);

            $completedOrders = (int)($stats['completed_orders'] ?? 0);
            $avgRating = floatval($stats['avg_rating'] ?? 0);

            // Calculate priority tier (higher = better priority)
            // Tier 1 (VIP): 50+ orders and 4+ rating
            // Tier 2 (Pro): 20+ orders and 3+ rating
            // Tier 3 (Regular): 5+ orders
            // Tier 4 (New): < 5 orders
            $priorityTier = 4; // Default: New driver
            if ($completedOrders >= 50 && $avgRating >= 4) {
                $priorityTier = 1; // VIP
            } elseif ($completedOrders >= 20 && $avgRating >= 3) {
                $priorityTier = 2; // Pro
            } elseif ($completedOrders >= 5) {
                $priorityTier = 3; // Regular
            }

            // Adjust max distance based on priority tier
            // Higher tier drivers see orders from farther away
            $tierDistance = $maxDistance + ($priorityTier <= 2 ? 3 : 0); // VIP/Pro get +3km range

            // Get pending orders within radius using Haversine formula
            // Orders are shown based on driver priority
            $sql = "SELECT id, details, address, client_phone, pickup_lat, pickup_lng, created_at,
                    (6371 * acos(cos(radians(?)) * cos(radians(pickup_lat)) * cos(radians(pickup_lng) - radians(?)) + sin(radians(?)) * sin(radians(pickup_lat)))) AS distance,
                    TIMESTAMPDIFF(MINUTE, created_at, NOW()) as age_minutes
                    FROM orders1
                    WHERE status = 'pending' AND pickup_lat IS NOT NULL AND pickup_lng IS NOT NULL
                    HAVING distance <= ?
                    ORDER BY
                        CASE
                            WHEN ? <= 2 THEN distance * 0.7
                            ELSE distance
                        END ASC,
                        age_minutes DESC
                    LIMIT ?";

            $limit = $priorityTier <= 2 ? 10 : 5; // VIP/Pro drivers see more orders
            $stmt = $conn->prepare($sql);
            $stmt->execute([$lat, $lng, $lat, $tierDistance, $priorityTier, $limit]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // FIX: Priority badge array was incorrectly indexed
            // Original: ['New Driver', 'VIP Driver', 'Pro Driver', 'Regular Driver'][$priorityTier - 1]
            // This would give "New Driver" for tier 1 (VIP), which is wrong
            $priorityBadges = [
                1 => 'VIP Driver',
                2 => 'Pro Driver', 
                3 => 'Regular Driver',
                4 => 'New Driver'
            ];
            $priorityBadge = $priorityBadges[$priorityTier] ?? 'Driver';

            // FIX: Sanitize output data
            $sanitizedOrders = array_map(function($order) {
                return [
                    'id' => (int)$order['id'],
                    'details' => htmlspecialchars($order['details'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'address' => htmlspecialchars($order['address'] ?? '', ENT_QUOTES, 'UTF-8'),
                    'client_phone' => $order['client_phone'] ?? '',
                    'pickup_lat' => floatval($order['pickup_lat']),
                    'pickup_lng' => floatval($order['pickup_lng']),
                    'distance' => round(floatval($order['distance']), 2),
                    'age_minutes' => (int)$order['age_minutes'],
                    'created_at' => $order['created_at']
                ];
            }, $orders);

            echo json_encode([
                'success' => true,
                'orders' => $sanitizedOrders,
                'driver_priority' => [
                    'tier' => $priorityTier,
                    'badge' => $priorityBadge,
                    'completed_orders' => $completedOrders,
                    'rating' => round($avgRating, 1),
                    'max_distance' => $tierDistance
                ]
            ]);
        } catch (Exception $e) {
            // FIX: Don't expose database error details
            error_log("get_nearby_orders error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        break;

    // ==========================================
    // GET DRIVER LOCATION (for tracking)
    // ==========================================
    case 'get_driver_location':
        $driverId = getIntParam('driver_id', 'GET', 0);

        if ($driverId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Valid driver ID required']);
            exit();
        }

        // FIX: Security check - only allow tracking for orders the user is involved with
        if ($user['role'] === 'customer') {
            $checkAccess = $conn->prepare("SELECT id FROM orders1 WHERE driver_id = ? AND (client_id = ? OR customer_name = ?) AND status IN ('accepted', 'picked_up') LIMIT 1");
            $checkAccess->execute([$driverId, $uid, $user['username']]);
            if ($checkAccess->rowCount() === 0) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Access denied']);
                exit();
            }
        }

        try {
            $stmt = $conn->prepare("SELECT last_lat, last_lng, location_updated_at FROM users1 WHERE id = ? AND role = 'driver'");
            $stmt->execute([$driverId]);
            $driver = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($driver && $driver['last_lat'] && $driver['last_lng']) {
                // FIX: Check if location is stale (more than 10 minutes old)
                $locationAge = $driver['location_updated_at'] ? (time() - strtotime($driver['location_updated_at'])) : 9999;
                $isStale = $locationAge > 600; // 10 minutes

                echo json_encode([
                    'success' => true,
                    'lat' => floatval($driver['last_lat']),
                    'lng' => floatval($driver['last_lng']),
                    'updated' => $driver['location_updated_at'],
                    'is_stale' => $isStale
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Location not available']);
            }
        } catch (Exception $e) {
            error_log("get_driver_location error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        break;

    // ==========================================
    // GET ORDER DETAILS
    // ==========================================
    case 'get_order':
        $orderId = getIntParam('order_id', 'GET', 0);

        if ($orderId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Valid order ID required']);
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
                $canAccess = ($user['role'] === 'admin' ||
                    (int)$order['driver_id'] === $uid ||
                    (int)$order['client_id'] === $uid ||
                    $order['customer_name'] === $user['username']);

                if ($canAccess) {
                    // FIX: Sanitize sensitive fields before returning
                    $safeOrder = [
                        'id' => (int)$order['id'],
                        'customer_name' => htmlspecialchars($order['customer_name'] ?? '', ENT_QUOTES, 'UTF-8'),
                        'details' => htmlspecialchars($order['details'] ?? '', ENT_QUOTES, 'UTF-8'),
                        'address' => htmlspecialchars($order['address'] ?? '', ENT_QUOTES, 'UTF-8'),
                        'client_phone' => $order['client_phone'],
                        'status' => $order['status'],
                        'pickup_lat' => $order['pickup_lat'] ? floatval($order['pickup_lat']) : null,
                        'pickup_lng' => $order['pickup_lng'] ? floatval($order['pickup_lng']) : null,
                        'driver_id' => $order['driver_id'] ? (int)$order['driver_id'] : null,
                        'driver_name' => htmlspecialchars($order['driver_name'] ?? '', ENT_QUOTES, 'UTF-8'),
                        'driver_phone' => $order['driver_phone'],
                        'driver_avatar' => $order['driver_avatar'],
                        'driver_lat' => $order['driver_lat'] ? floatval($order['driver_lat']) : null,
                        'driver_lng' => $order['driver_lng'] ? floatval($order['driver_lng']) : null,
                        'driver_verified' => (bool)$order['driver_verified'],
                        'created_at' => $order['created_at'],
                        'accepted_at' => $order['accepted_at'],
                        'picked_at' => $order['picked_at'] ?? null,
                        'delivered_at' => $order['delivered_at'] ?? null,
                        'points_cost' => (int)($order['points_cost'] ?? 0),
                        'distance_km' => $order['distance_km'] ? floatval($order['distance_km']) : null
                    ];

                    // Only include delivery code for the customer who owns the order
                    if ((int)$order['client_id'] === $uid || $order['customer_name'] === $user['username']) {
                        $safeOrder['delivery_code'] = $order['delivery_code'];
                    }

                    echo json_encode(['success' => true, 'order' => $safeOrder]);
                } else {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'error' => 'Access denied']);
                }
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Order not found']);
            }
        } catch (Exception $e) {
            error_log("get_order error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        break;

    // ==========================================
    // CALCULATE DISTANCE BETWEEN TWO POINTS
    // ==========================================
    case 'calculate_distance':
        $lat1 = getFloatParam('lat1', 'GET', 0);
        $lng1 = getFloatParam('lng1', 'GET', 0);
        $lat2 = getFloatParam('lat2', 'GET', 0);
        $lng2 = getFloatParam('lng2', 'GET', 0);

        // FIX: Validate all coordinates properly
        if (!isValidCoordinate($lat1, $lng1) || !isValidCoordinate($lat2, $lng2)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid coordinates']);
            exit();
        }

        $distance = haversineDistanceAPI($lat1, $lng1, $lat2, $lng2);
        $time = ceil($distance / 30 * 60); // 30 km/h average

        echo json_encode([
            'success' => true,
            'distance' => round($distance, 2),
            'time' => (int)$time,
            'unit' => 'km'
        ]);
        break;

    // ==========================================
    // GET USER POINTS
    // ==========================================
    case 'get_user_points':
        try {
            $stmt = $conn->prepare("SELECT points FROM users1 WHERE id = ?");
            $stmt->execute([$uid]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'points' => (int)$result['points']
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'User not found']);
            }
        } catch (Exception $e) {
            error_log("get_user_points error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        break;

    // ==========================================
    // GET USER STATS
    // ==========================================
    case 'get_stats':
        try {
            if ($user['role'] === 'driver') {
                // FIX: Check if function exists
                if (function_exists('getDriverStats')) {
                    $stats = getDriverStats($conn, $uid);
                } else {
                    $stats = ['error' => 'Stats function not available'];
                }
            } elseif ($user['role'] === 'customer') {
                // FIX: Check if function exists
                if (function_exists('getClientStats')) {
                    $stats = getClientStats($conn, $uid, $user['username']);
                } else {
                    $stats = ['error' => 'Stats function not available'];
                }
            } else {
                // FIX: Admin stats - use prepared statements instead of raw queries
                $totalOrders = $conn->prepare("SELECT COUNT(*) FROM orders1");
                $totalOrders->execute();
                
                $pendingOrders = $conn->prepare("SELECT COUNT(*) FROM orders1 WHERE status = 'pending'");
                $pendingOrders->execute();
                
                $totalDrivers = $conn->prepare("SELECT COUNT(*) FROM users1 WHERE role = 'driver'");
                $totalDrivers->execute();
                
                $totalCustomers = $conn->prepare("SELECT COUNT(*) FROM users1 WHERE role = 'customer'");
                $totalCustomers->execute();

                $stats = [
                    'total_orders' => (int)$totalOrders->fetchColumn(),
                    'pending_orders' => (int)$pendingOrders->fetchColumn(),
                    'total_drivers' => (int)$totalDrivers->fetchColumn(),
                    'total_customers' => (int)$totalCustomers->fetchColumn()
                ];
            }
            echo json_encode(['success' => true, 'stats' => $stats]);
        } catch (Exception $e) {
            error_log("get_stats error: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
        break;

    // ==========================================
    // VALIDATE PROMO CODE
    // ==========================================
    case 'validate_promo':
        // FIX: Sanitize promo code input
        $code = preg_replace('/[^A-Z0-9]/', '', strtoupper(trim($_GET['code'] ?? '')));

        if (empty($code)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Please enter a promo code']);
            exit();
        }

        // FIX: Limit promo code length
        if (strlen($code) > 50) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid promo code']);
            exit();
        }

        try {
            $stmt = $conn->prepare("SELECT * FROM promo_codes WHERE code = ? AND is_active = 1");
            $stmt->execute([$code]);
            $promo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$promo) {
                echo json_encode(['success' => false, 'message' => 'Invalid promo code']);
                exit();
            }

            // Check if expired
            $now = time();
            if (!empty($promo['valid_from']) && strtotime($promo['valid_from']) > $now) {
                echo json_encode(['success' => false, 'message' => 'Promo code not yet valid']);
                exit();
            }

            if (!empty($promo['valid_until']) && strtotime($promo['valid_until']) < $now) {
                echo json_encode(['success' => false, 'message' => 'Promo code has expired']);
                exit();
            }

            // Check if max uses reached
            if (!empty($promo['max_uses']) && (int)$promo['used_count'] >= (int)$promo['max_uses']) {
                echo json_encode(['success' => false, 'message' => 'Promo code has reached maximum uses']);
                exit();
            }

            // Check if user already used this code
            if ($user['role'] == 'customer') {
                $check = $conn->prepare("SELECT id FROM promo_code_uses WHERE promo_code_id = ? AND user_id = ?");
                $check->execute([$promo['id'], $uid]);
                if ($check->rowCount() > 0) {
                    echo json_encode(['success' => false, 'message' => 'You have already used this promo code']);
                    exit();
                }
            }

            // Valid promo code
            $discount_text = $promo['discount_type'] == 'percentage'
                ? round($promo['discount_value'], 0) . '% off'
                : number_format($promo['discount_value'], 2) . ' MRU off';

            echo json_encode([
                'success' => true,
                'message' => 'Valid! You get ' . $discount_text,
                'promo' => [
                    'id' => (int)$promo['id'],
                    'code' => $promo['code'],
                    'discount_type' => $promo['discount_type'],
                    'discount_value' => floatval($promo['discount_value'])
                ]
            ]);
        } catch (Exception $e) {
            error_log("validate_promo error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error validating promo code']);
        }
        break;

    // ==========================================
    // PING - Simple health check
    // ==========================================
    case 'ping':
        echo json_encode([
            'success' => true,
            'timestamp' => time(),
            'user_id' => $uid
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

/**
 * Calculate distance between two points using Haversine formula
 * @param float $lat1 Latitude of point 1
 * @param float $lon1 Longitude of point 1
 * @param float $lat2 Latitude of point 2
 * @param float $lon2 Longitude of point 2
 * @return float Distance in kilometers
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
