<?php
/**
 * API Endpoint for Real-Time Notifications
 * This file handles AJAX requests for live updates
 */

header('Content-Type: application/json; charset=utf-8');

// Include configuration (starts session)
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$user = $_SESSION['user'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'check_orders':
        // Get the last check timestamp
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
            $stmt = $conn->prepare("SELECT id, status, UNIX_TIMESTAMP(updated_at) as updated_ts FROM orders1 WHERE customer_name = ? AND UNIX_TIMESTAMP(updated_at) > ? ORDER BY updated_at DESC LIMIT 5");
            $stmt->execute([$user['username'], $last_check]);
            $changed_orders = $stmt->fetchAll();

            $notifications = [];
            foreach ($changed_orders as $order) {
                $notifications[] = [
                    'order_id' => $order['id'],
                    'status' => $order['status']
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

    case 'get_user_points':
        // Get current user points (for drivers)
        $stmt = $conn->prepare("SELECT points FROM users1 WHERE id = ?");
        $stmt->execute([$user['id']]);
        $result = $stmt->fetch();

        echo json_encode([
            'success' => true,
            'points' => (int)$result['points']
        ]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>
