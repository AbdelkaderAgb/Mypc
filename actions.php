<?php
/**
 * Action Handlers
 * Enhanced Delivery Pro System v2.0
 * This file contains all business logic for admin, driver, and customer actions
 */

// ==========================================
// AUTO-EXPIRE OLD PENDING ORDERS (3 hours)
// ==========================================
try {
    $conn->exec("UPDATE orders1 SET status='cancelled', cancelled_at=NOW(), cancel_reason='Auto-expired: No driver accepted within 3 hours' WHERE status='pending' AND created_at < DATE_SUB(NOW(), INTERVAL {$order_expiry_hours} HOUR)");
} catch (Exception $e) {
    // Silently fail - table might not exist yet
}

// Only process actions if user is logged in
if (isset($_SESSION['user'])) {

    // ==========================================
    // PROFILE UPDATE (All Users)
    // ==========================================
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $address = trim($_POST['profile_address']);
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_new_password = trim($_POST['confirm_new_password'] ?? '');

        // Handle avatar upload
        $avatar_sql = '';
        $avatar_params = [];
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $result = uploadAvatar($_FILES['avatar'], $uid);
            if ($result['success']) {
                $avatar_sql = ', avatar_url=?';
                $avatar_params[] = $result['path'];
            } else {
                setFlash('warning', $result['error']);
            }
        }

        // Check if phone is being added/changed - auto verify (no OTP)
        $phone_verified = 0;
        if (!empty($phone)) {
            $phone_verified = 1;
        }

        // Update profile info
        $sql = "UPDATE users1 SET full_name=?, phone=?, phone_verified=?, email=?, address=?" . $avatar_sql . " WHERE id=?";
        $params = array_merge([$full_name, $phone, $phone_verified, $email, $address], $avatar_params, [$uid]);
        $conn->prepare($sql)->execute($params);

        // Update password if provided
        if (!empty($new_password)) {
            if (strlen($new_password) < 4) {
                setFlash('error', $t['err_password_short'] ?? 'Password must be at least 4 characters');
                header("Location: index.php?settings=1");
                exit();
            } elseif ($new_password !== $confirm_new_password) {
                setFlash('error', $t['err_password_mismatch'] ?? 'Passwords do not match');
                header("Location: index.php?settings=1");
                exit();
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $conn->prepare("UPDATE users1 SET password=? WHERE id=?")->execute([$hashed_password, $uid]);
            }
        }

        // Refresh session with updated user data
        $stmt = $conn->prepare("SELECT * FROM users1 WHERE id=?");
        $stmt->execute([$uid]);
        $_SESSION['user'] = $stmt->fetch();

        setFlash('success', $t['success_profile'] ?? 'Profile updated successfully');
        header("Location: index.php?settings=1");
        exit();
    }

    // ==========================================
    // DRIVER ONLINE/OFFLINE TOGGLE
    // ==========================================
    if (isset($_POST['toggle_online']) && $u['role'] == 'driver') {
        $new_status = $u['is_online'] ? 0 : 1;

        // Check phone verification before going online
        if ($new_status == 1 && !isPhoneVerified($u)) {
            setFlash('error', $t['add_phone_first'] ?? 'Please add your phone number first');
            header("Location: index.php?settings=1");
            exit();
        }

        $conn->prepare("UPDATE users1 SET is_online=? WHERE id=?")->execute([$new_status, $uid]);

        // Refresh session
        $_SESSION['user']['is_online'] = $new_status;

        setFlash('success', $new_status ? ($t['you_are_online'] ?? 'You are now online') : ($t['you_are_offline'] ?? 'You are now offline'));
        header("Location: index.php");
        exit();
    }

    // ==========================================
    // ADMIN ACTIONS
    // ==========================================
    if ($u['role'] == 'admin') {

        // Add User (with hashed password and serial number)
        if (isset($_POST['admin_add_user'])) {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $role = $_POST['role'];
            $points = (int)$_POST['points'];

            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $serial_no = generateSerialNumber($conn, $role);

                $stmt = $conn->prepare("INSERT INTO users1 (serial_no, username, password, role, points, status, phone_verified) VALUES (?, ?, ?, ?, ?, 'active', 1)");
                $stmt->execute([$serial_no, $username, $hashed_password, $role, $points]);
                setFlash('success', $t['success_user_added'] ?? 'User added successfully');
            } catch (PDOException $e) {
                setFlash('error', $t['err_username_exists'] ?? 'Username already exists');
            }
            header("Location: index.php");
            exit();
        }

        // Edit User
        if (isset($_POST['admin_edit_user'])) {
            $user_id = (int)$_POST['user_id'];
            $password = trim($_POST['password']);
            $role = $_POST['role'];
            $points = (int)$_POST['points'];

            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $conn->prepare("UPDATE users1 SET password=?, role=?, points=? WHERE id=?")
                     ->execute([$hashed_password, $role, $points, $user_id]);
            } else {
                $conn->prepare("UPDATE users1 SET role=?, points=? WHERE id=?")
                     ->execute([$role, $points, $user_id]);
            }
            setFlash('success', $t['success_user_updated'] ?? 'User updated successfully');
            header("Location: index.php");
            exit();
        }

        // Ban/Unban User
        if (isset($_GET['toggle_ban'])) {
            $user_id = (int)$_GET['toggle_ban'];
            $stmt = $conn->prepare("SELECT status FROM users1 WHERE id=?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            $new_status = ($user['status'] == 'active') ? 'banned' : 'active';
            $conn->prepare("UPDATE users1 SET status=? WHERE id=?")->execute([$new_status, $user_id]);
            setFlash('success', 'User status updated');
            header("Location: index.php");
            exit();
        }

        // Toggle Driver Verification
        if (isset($_GET['toggle_verify'])) {
            $driver_id = (int)$_GET['toggle_verify'];
            $stmt = $conn->prepare("SELECT is_verified, role FROM users1 WHERE id=?");
            $stmt->execute([$driver_id]);
            $driver = $stmt->fetch();

            if ($driver && $driver['role'] == 'driver') {
                $new_verified = $driver['is_verified'] ? 0 : 1;
                $conn->prepare("UPDATE users1 SET is_verified=? WHERE id=?")->execute([$new_verified, $driver_id]);

                if ($new_verified) {
                    setFlash('success', $t['driver_verified_success'] ?? 'Driver verified successfully! They can now accept orders.');
                } else {
                    setFlash('warning', $t['driver_unverified'] ?? 'Driver verification removed.');
                }
            }
            header("Location: index.php#drivers");
            exit();
        }

        // Delete User
        if (isset($_GET['delete_user'])) {
            $user_id = (int)$_GET['delete_user'];
            $conn->prepare("DELETE FROM users1 WHERE id=? AND id!=?")->execute([$user_id, $uid]);
            setFlash('success', $t['success_user_deleted'] ?? 'User deleted');
            header("Location: index.php");
            exit();
        }

        // Save Promo Code (Create/Update)
        if (isset($_POST['save_promo_code'])) {
            $promo_id = !empty($_POST['promo_id']) ? (int)$_POST['promo_id'] : null;
            $code = strtoupper(trim($_POST['promo_code']));
            $discount_type = $_POST['discount_type'];
            $discount_value = floatval($_POST['discount_value']);
            $max_uses = !empty($_POST['max_uses']) ? (int)$_POST['max_uses'] : null;
            $valid_from = !empty($_POST['valid_from']) ? $_POST['valid_from'] : null;
            $valid_until = !empty($_POST['valid_until']) ? $_POST['valid_until'] : null;
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            // Validate
            if (empty($code) || !preg_match('/^[A-Z0-9]+$/', $code)) {
                setFlash('error', $t['invalid_promo_code'] ?? 'Invalid promo code. Use uppercase letters and numbers only.');
                header("Location: index.php#promo-codes");
                exit();
            }

            if ($discount_value <= 0) {
                setFlash('error', $t['invalid_discount'] ?? 'Discount value must be greater than 0.');
                header("Location: index.php#promo-codes");
                exit();
            }

            if ($discount_type == 'percentage' && $discount_value > 100) {
                setFlash('error', $t['invalid_percentage'] ?? 'Percentage cannot exceed 100%.');
                header("Location: index.php#promo-codes");
                exit();
            }

            // Check if code already exists (for different promo)
            $check = $conn->prepare("SELECT id FROM promo_codes WHERE code=? AND id!=?");
            $check->execute([$code, $promo_id ?? 0]);
            if ($check->rowCount() > 0) {
                setFlash('error', $t['promo_code_exists'] ?? 'This promo code already exists.');
                header("Location: index.php#promo-codes");
                exit();
            }

            if ($promo_id) {
                // Update existing
                $stmt = $conn->prepare("UPDATE promo_codes SET code=?, discount_type=?, discount_value=?, max_uses=?, valid_from=?, valid_until=?, is_active=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
                $stmt->execute([$code, $discount_type, $discount_value, $max_uses, $valid_from, $valid_until, $is_active, $promo_id]);
                setFlash('success', $t['promo_updated'] ?? 'Promo code updated successfully!');
            } else {
                // Create new
                $stmt = $conn->prepare("INSERT INTO promo_codes (code, discount_type, discount_value, max_uses, valid_from, valid_until, is_active, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$code, $discount_type, $discount_value, $max_uses, $valid_from, $valid_until, $is_active, $uid]);
                setFlash('success', $t['promo_created'] ?? 'Promo code created successfully!');
            }

            header("Location: index.php#promo-codes");
            exit();
        }

        // Toggle Promo Code Active Status
        if (isset($_GET['toggle_promo'])) {
            $promo_id = (int)$_GET['toggle_promo'];
            $stmt = $conn->prepare("SELECT is_active FROM promo_codes WHERE id=?");
            $stmt->execute([$promo_id]);
            $promo = $stmt->fetch();

            if ($promo) {
                $new_status = $promo['is_active'] ? 0 : 1;
                $conn->prepare("UPDATE promo_codes SET is_active=? WHERE id=?")->execute([$new_status, $promo_id]);
                setFlash('success', $new_status ? ($t['promo_activated'] ?? 'Promo code activated!') : ($t['promo_deactivated'] ?? 'Promo code deactivated!'));
            }
            header("Location: index.php#promo-codes");
            exit();
        }

        // Delete Promo Code
        if (isset($_GET['delete_promo'])) {
            $promo_id = (int)$_GET['delete_promo'];
            $conn->prepare("DELETE FROM promo_codes WHERE id=?")->execute([$promo_id]);
            setFlash('success', $t['promo_deleted'] ?? 'Promo code deleted successfully!');
            header("Location: index.php#promo-codes");
            exit();
        }

        // Recharge Points
        if (isset($_POST['recharge'])) {
            $amt = (int)$_POST['amount'];
            $did = (int)$_POST['driver_id'];
            if ($amt > 0 && $did > 0) {
                $conn->prepare("UPDATE users1 SET points = points + ? WHERE id=?")->execute([$amt, $did]);
                setFlash('success', $t['success_points_added'] ?? "Points added successfully.");
                header("Location: index.php");
                exit();
            }
        }

        // Bulk Recharge Drivers
        if (isset($_POST['bulk_recharge_drivers'])) {
            $amt = (int)$_POST['bulk_amount'];
            $driver_ids = $_POST['driver_ids'];

            if ($amt > 0 && !empty($driver_ids)) {
                $ids = explode(',', $driver_ids);
                $ids = array_map('intval', $ids);
                $ids = array_filter($ids, function($id) { return $id > 0; });

                if (count($ids) > 0) {
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $stmt = $conn->prepare("UPDATE users1 SET points = points + ? WHERE id IN ($placeholders) AND role='driver'");
                    $params = array_merge([$amt], $ids);
                    $stmt->execute($params);

                    $count = $stmt->rowCount();
                    setFlash('success', ($t['bulk_recharge_success'] ?? "Successfully recharged %d drivers with %d points.") . " " . sprintf("%d drivers recharged with %d points each.", $count, $amt));
                    header("Location: index.php");
                    exit();
                }
            }
        }

        // Add Order (Admin)
        if (isset($_POST['admin_add_order'])) {
            $customer_name = trim($_POST['customer_name']);
            $details = mb_convert_encoding(trim($_POST['details']), 'UTF-8', 'UTF-8');
            $address = mb_convert_encoding(trim($_POST['address']), 'UTF-8', 'UTF-8');
            $status = $_POST['status'];
            $driver_id = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : NULL;
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO orders1 (customer_name, details, address, status, driver_id, delivery_code, points_cost) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customer_name, $details, $address, $status, $driver_id, $otp, $points_cost_per_order]);
            setFlash('success', 'Order added successfully');
            header("Location: index.php");
            exit();
        }

        // Edit Order (Admin)
        if (isset($_POST['admin_edit_order'])) {
            $order_id = (int)$_POST['order_id'];
            $customer_name = trim($_POST['customer_name']);
            $details = mb_convert_encoding(trim($_POST['details']), 'UTF-8', 'UTF-8');
            $address = mb_convert_encoding(trim($_POST['address']), 'UTF-8', 'UTF-8');
            $status = $_POST['status'];
            $driver_id = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : NULL;

            // Update timestamps based on status
            $timestamp_sql = '';
            if ($status == 'accepted') $timestamp_sql = ', accepted_at=NOW()';
            if ($status == 'picked_up') $timestamp_sql = ', picked_at=NOW()';
            if ($status == 'delivered') $timestamp_sql = ', delivered_at=NOW()';
            if ($status == 'cancelled') $timestamp_sql = ', cancelled_at=NOW()';

            $conn->prepare("UPDATE orders1 SET customer_name=?, details=?, address=?, status=?, driver_id=?" . $timestamp_sql . " WHERE id=?")
                 ->execute([$customer_name, $details, $address, $status, $driver_id, $order_id]);
            setFlash('success', 'Order updated successfully');
            header("Location: index.php");
            exit();
        }

        // Cancel Order
        if (isset($_GET['cancel_order'])) {
            $order_id = (int)$_GET['cancel_order'];
            $conn->prepare("UPDATE orders1 SET status='cancelled', cancelled_at=NOW() WHERE id=?")->execute([$order_id]);
            setFlash('success', $t['success_order_cancelled'] ?? 'Order cancelled');
            header("Location: index.php");
            exit();
        }

        // Delete Order
        if (isset($_GET['delete_order'])) {
            $order_id = (int)$_GET['delete_order'];
            $conn->prepare("DELETE FROM orders1 WHERE id=?")->execute([$order_id]);
            setFlash('success', 'Order deleted');
            header("Location: index.php");
            exit();
        }
    }

    // ==========================================
    // CUSTOMER ACTIONS
    // ==========================================
    if (isset($_POST['add_order']) && $u['role'] == 'customer') {
        // Get form data
        $details = mb_convert_encoding(trim($_POST['details']), 'UTF-8', 'UTF-8');
        $address = mb_convert_encoding(trim($_POST['address'] ?? $u['address'] ?? ''), 'UTF-8', 'UTF-8');
        $client_phone = preg_replace('/[^0-9]/', '', $_POST['client_phone'] ?? '');

        // Validate order details
        if (empty($details) || strlen($details) < 3) {
            setFlash('error', $t['details_required'] ?? 'Please provide order details (minimum 3 characters)');
            header("Location: index.php");
            exit();
        }

        // GPS pickup coordinates
        $pickup_lat = !empty($_POST['pickup_lat']) ? floatval($_POST['pickup_lat']) : null;
        $pickup_lng = !empty($_POST['pickup_lng']) ? floatval($_POST['pickup_lng']) : null;

        // Validate GPS location is provided
        if (!$pickup_lat || !$pickup_lng) {
            setFlash('error', $t['gps_required'] ?? 'Please set your GPS location for pickup');
            header("Location: index.php");
            exit();
        }

        // Validate GPS coordinates are within valid ranges
        if ($pickup_lat < -90 || $pickup_lat > 90 || $pickup_lng < -180 || $pickup_lng > 180) {
            setFlash('error', $t['invalid_gps'] ?? 'Invalid GPS coordinates. Please try again.');
            header("Location: index.php");
            exit();
        }

        // Validate phone number (Mauritanian format: 8 digits)
        if (!empty($client_phone) && strlen($client_phone) != 8) {
            setFlash('error', $t['invalid_phone'] ?? 'Phone number must be exactly 8 digits');
            header("Location: index.php");
            exit();
        }

        // Update user phone if provided and not already set
        if ($client_phone && empty($u['phone'])) {
            $stmt = $conn->prepare("UPDATE users1 SET phone = ?, phone_verified = 1 WHERE id = ?");
            $stmt->execute([$client_phone, $uid]);
            $_SESSION['user']['phone'] = $client_phone;
            $_SESSION['user']['phone_verified'] = 1;
            $u = $_SESSION['user'];
        }

        // Check phone verification (either already verified or just provided)
        if (empty($u['phone']) && empty($client_phone)) {
            setFlash('error', $t['add_phone_first'] ?? 'Please add your phone number first');
            header("Location: index.php?settings=1");
            exit();
        }

        // Validate and process promo code if provided
        $promo_code = !empty($_POST['promo_code']) ? strtoupper(trim($_POST['promo_code'])) : null;
        $discount_amount = 0;
        $promo_id = null;

        if ($promo_code) {
            $stmt = $conn->prepare("SELECT * FROM promo_codes WHERE code = ? AND is_active = 1");
            $stmt->execute([$promo_code]);
            $promo = $stmt->fetch();

            if ($promo) {
                $now = time();
                $is_valid = true;

                // Check expiry
                if ($promo['valid_from'] && strtotime($promo['valid_from']) > $now) {
                    $is_valid = false;
                }
                if ($promo['valid_until'] && strtotime($promo['valid_until']) < $now) {
                    $is_valid = false;
                }

                // Check max uses
                if ($promo['max_uses'] && $promo['used_count'] >= $promo['max_uses']) {
                    $is_valid = false;
                }

                // Check if user already used it
                $check = $conn->prepare("SELECT id FROM promo_code_uses WHERE promo_code_id = ? AND user_id = ?");
                $check->execute([$promo['id'], $uid]);
                if ($check->rowCount() > 0) {
                    $is_valid = false;
                }

                if ($is_valid) {
                    // Calculate discount (we'll assume base order cost for percentage calculation)
                    // For fixed, just use the value
                    if ($promo['discount_type'] == 'fixed') {
                        $discount_amount = $promo['discount_value'];
                    } else {
                        // For percentage, calculate based on points_cost_per_order or fixed base price
                        // Assuming 1 point = 1 MRU for simplicity
                        $base_price = $points_cost_per_order; // or use a fixed value
                        $discount_amount = ($base_price * $promo['discount_value']) / 100;
                    }

                    $promo_id = $promo['id'];
                }
            }
        }

        if ($details) {
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO orders1 (client_id, customer_name, details, address, client_phone, pickup_lat, pickup_lng, status, delivery_code, points_cost, promo_code, discount_amount) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)");
            $stmt->execute([
                $uid,
                $u['username'],
                $details,
                $address ?: 'GPS Location',
                $client_phone ?: $u['phone'],
                $pickup_lat,
                $pickup_lng,
                $otp,
                $points_cost_per_order,
                $promo_code,
                $discount_amount
            ]);

            $order_id = $conn->lastInsertId();

            // If promo code was used, update usage tracking
            if ($promo_id) {
                // Increment usage count
                $conn->prepare("UPDATE promo_codes SET used_count = used_count + 1 WHERE id = ?")
                    ->execute([$promo_id]);

                // Track user usage
                $conn->prepare("INSERT INTO promo_code_uses (promo_code_id, user_id, order_id, discount_amount) VALUES (?, ?, ?, ?)")
                    ->execute([$promo_id, $uid, $order_id, $discount_amount]);

                setFlash('success', ($t['success_add_with_promo'] ?? 'Order created! Discount applied: %s MRU') . ' ' . number_format($discount_amount, 2) . ' MRU');
            } else {
                setFlash('success', $t['success_add']);
            }

            header("Location: index.php");
            exit();
        }
    }

    // Customer Cancel Order (pending or accepted - before pickup)
    if (isset($_GET['customer_cancel']) && $u['role'] == 'customer') {
        $order_id = (int)$_GET['customer_cancel'];

        // Allow cancellation of pending or accepted orders (before pickup)
        $stmt = $conn->prepare("SELECT id, status, driver_id, points_cost FROM orders1 WHERE id=? AND (client_id=? OR customer_name=?) AND status IN ('pending', 'accepted')");
        $stmt->execute([$order_id, $uid, $u['username']]);
        $order = $stmt->fetch();

        if ($order) {
            // If order was accepted, refund points to driver
            if ($order['status'] == 'accepted' && $order['driver_id'] && $order['points_cost']) {
                $conn->prepare("UPDATE users1 SET points = points + ? WHERE id=?")
                     ->execute([$order['points_cost'], $order['driver_id']]);
            }
            $conn->prepare("UPDATE orders1 SET status='cancelled', cancelled_at=NOW(), cancel_reason='Cancelled by customer' WHERE id=?")
                 ->execute([$order_id]);
            setFlash('success', $t['success_order_cancelled'] ?? 'Order cancelled');
        } else {
            setFlash('error', $t['err_cannot_cancel'] ?? 'Cannot cancel this order (already picked up)');
        }
        header("Location: index.php");
        exit();
    }

    // Driver Cancel Order (accepted orders only - refund points)
    if (isset($_GET['driver_cancel']) && $u['role'] == 'driver') {
        $order_id = (int)$_GET['driver_cancel'];

        $stmt = $conn->prepare("SELECT id, points_cost FROM orders1 WHERE id=? AND driver_id=? AND status='accepted'");
        $stmt->execute([$order_id, $uid]);
        $order = $stmt->fetch();

        if ($order) {
            // Refund points to driver
            if ($order['points_cost']) {
                $conn->prepare("UPDATE users1 SET points = points + ? WHERE id=?")
                     ->execute([$order['points_cost'], $uid]);
                $_SESSION['user']['points'] += $order['points_cost'];
            }
            // Reset order to pending (keep original points_cost for audit trail)
            $conn->prepare("UPDATE orders1 SET status='pending', driver_id=NULL, accepted_at=NULL, cancel_reason='Driver cancelled' WHERE id=?")
                 ->execute([$order_id]);
            setFlash('success', $t['order_released'] ?? 'Order released. Points refunded.');
        } else {
            setFlash('error', $t['err_cannot_cancel'] ?? 'Cannot cancel this order');
        }
        header("Location: index.php");
        exit();
    }

    // ==========================================
    // DRIVER ACTIONS
    // ==========================================
    if (isset($_POST['accept_order']) && $u['role'] == 'driver') {
        $oid = (int)$_POST['oid'];

        // Check driver verification by admin
        if (empty($u['is_verified'])) {
            setFlash('error', $t['driver_not_verified'] ?? 'Your account must be verified by admin before accepting orders');
            header("Location: index.php");
            exit();
        }

        // Check phone verification
        if (!isPhoneVerified($u)) {
            setFlash('error', $t['add_phone_first'] ?? 'Please add your phone number first');
            header("Location: index.php?settings=1");
            exit();
        }

        // Check if driver has too many active orders
        $activeOrders = countActiveOrders($conn, $uid);
        if ($activeOrders >= $driver_max_active_orders) {
            setFlash('error', $t['max_orders_reached'] ?? 'You have reached the maximum number of active orders');
            header("Location: index.php");
            exit();
        }

        if ($u['points'] < $points_cost_per_order) {
            setFlash('error', $t['err_low_bal']);
        } else {
            try {
                $conn->beginTransaction();

                // Lock the order row for update (race condition prevention)
                $chk = $conn->prepare("SELECT id, status FROM orders1 WHERE id=? AND status='pending' FOR UPDATE");
                $chk->execute([$oid]);
                $order = $chk->fetch();

                if ($order) {
                    // Get order pickup location and driver location to calculate distance
                    $orderInfo = $conn->prepare("SELECT pickup_lat, pickup_lng FROM orders1 WHERE id=?");
                    $orderInfo->execute([$oid]);
                    $orderLoc = $orderInfo->fetch();

                    $driverInfo = $conn->prepare("SELECT last_lat, last_lng FROM users1 WHERE id=?");
                    $driverInfo->execute([$uid]);
                    $driverLoc = $driverInfo->fetch();

                    // Calculate distance if both locations are available
                    $distance_km = null;
                    if ($orderLoc && $driverLoc && !empty($orderLoc['pickup_lat']) && !empty($driverLoc['last_lat'])) {
                        $lat1 = deg2rad($driverLoc['last_lat']);
                        $lon1 = deg2rad($driverLoc['last_lng']);
                        $lat2 = deg2rad($orderLoc['pickup_lat']);
                        $lon2 = deg2rad($orderLoc['pickup_lng']);

                        $dlat = $lat2 - $lat1;
                        $dlon = $lon2 - $lon1;
                        $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
                        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                        $distance_km = round(6371 * $c, 2); // Earth radius in km
                    }

                    // Update order with distance
                    $upd = $conn->prepare("UPDATE orders1 SET status='accepted', driver_id=?, accepted_at=NOW(), points_cost=?, distance_km=? WHERE id=?");
                    $upd->execute([$uid, $points_cost_per_order, $distance_km, $oid]);

                    // Deduct points from driver
                    $deduct = $conn->prepare("UPDATE users1 SET points = points - ?, total_orders = total_orders + 1 WHERE id=?");
                    $deduct->execute([$points_cost_per_order, $uid]);

                    // Update session points
                    $_SESSION['user']['points'] -= $points_cost_per_order;

                    $conn->commit();
                    setFlash('success', $t['success_acc']);
                } else {
                    $conn->rollBack();
                    setFlash('error', $t['err_order_taken'] ?? "Order already taken by another driver");
                }
            } catch (Exception $e) {
                $conn->rollBack();
                setFlash('error', $t['err_general'] ?? "System Error");
            }
        }
        header("Location: index.php");
        exit();
    }

    // Driver marks package as picked up
    if (isset($_POST['pickup_order']) && $u['role'] == 'driver') {
        $oid = (int)$_POST['oid'];

        $stmt = $conn->prepare("UPDATE orders1 SET status='picked_up', picked_at=NOW() WHERE id=? AND driver_id=? AND status='accepted'");
        $stmt->execute([$oid, $uid]);

        if ($stmt->rowCount() > 0) {
            setFlash('success', $t['package_picked'] ?? 'Package picked up');
        }
        header("Location: index.php");
        exit();
    }

    // Driver finishes delivery
    if (isset($_POST['finish_job']) && $u['role'] == 'driver') {
        $oid = (int)$_POST['oid'];
        $pin = str_pad(trim($_POST['pin']), 4, '0', STR_PAD_LEFT);

        $chk = $conn->prepare("SELECT delivery_code, points_cost FROM orders1 WHERE id=? AND driver_id=? AND status IN ('accepted', 'picked_up')");
        $chk->execute([$oid, $uid]);
        $order = $chk->fetch();

        if ($order && $order['delivery_code'] === $pin) {
            $conn->prepare("UPDATE orders1 SET status='delivered', delivered_at=NOW() WHERE id=?")->execute([$oid]);

            // Update driver stats
            $conn->prepare("UPDATE users1 SET total_earnings = total_earnings + ? WHERE id=?")
                 ->execute([$order['points_cost'], $uid]);

            setFlash('success', $t['success_fin']);
        } else {
            setFlash('error', $t['err_pin']);
        }
        header("Location: index.php");
        exit();
    }

    // ==========================================
    // RATING SYSTEM
    // ==========================================
    if (isset($_POST['submit_rating'])) {
        $order_id = (int)$_POST['order_id'];
        $score = min(5, max(1, (int)$_POST['score']));
        $comment = trim($_POST['comment'] ?? '');

        // Get order to find who to rate
        $stmt = $conn->prepare("SELECT driver_id, client_id, customer_name FROM orders1 WHERE id=? AND status='delivered'");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();

        if ($order) {
            // Determine who is rating whom
            if ($u['role'] == 'customer') {
                $ratee_id = $order['driver_id'];
            } else {
                $ratee_id = $order['client_id'];
            }

            if ($ratee_id) {
                // Check if already rated
                $check = $conn->prepare("SELECT id FROM ratings WHERE order_id=? AND rater_id=?");
                $check->execute([$order_id, $uid]);

                if ($check->rowCount() == 0) {
                    $conn->prepare("INSERT INTO ratings (order_id, rater_id, ratee_id, score, comment) VALUES (?, ?, ?, ?, ?)")
                         ->execute([$order_id, $uid, $ratee_id, $score, $comment]);

                    // Update average rating
                    $avg = $conn->prepare("SELECT AVG(score) FROM ratings WHERE ratee_id=?");
                    $avg->execute([$ratee_id]);
                    $newRating = round($avg->fetchColumn(), 2);

                    $conn->prepare("UPDATE users1 SET rating=? WHERE id=?")->execute([$newRating, $ratee_id]);

                    setFlash('success', $t['thanks_for_rating'] ?? 'Thank you for your rating!');
                }
            }
        }
        header("Location: index.php");
        exit();
    }
}
?>
