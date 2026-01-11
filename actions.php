<?php
/**
 * Action Handlers
 * Enhanced Delivery Pro System v2.0
 * This file contains all business logic for admin, driver, and customer actions
 */

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

        // Delete User
        if (isset($_GET['delete_user'])) {
            $user_id = (int)$_GET['delete_user'];
            $conn->prepare("DELETE FROM users1 WHERE id=? AND id!=?")->execute([$user_id, $uid]);
            setFlash('success', $t['success_user_deleted'] ?? 'User deleted');
            header("Location: index.php");
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
        // Check phone verification
        if (!isPhoneVerified($u)) {
            setFlash('error', $t['add_phone_first'] ?? 'Please add your phone number first');
            header("Location: index.php?settings=1");
            exit();
        }

        $details = mb_convert_encoding(trim($_POST['details']), 'UTF-8', 'UTF-8');
        $address = mb_convert_encoding(trim($_POST['address']), 'UTF-8', 'UTF-8');

        if ($details && $address) {
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO orders1 (client_id, customer_name, details, address, status, delivery_code, points_cost) VALUES (?, ?, ?, ?, 'pending', ?, ?)");
            $stmt->execute([$uid, $u['username'], $details, $address, $otp, $points_cost_per_order]);
            setFlash('success', $t['success_add']);
            header("Location: index.php");
            exit();
        }
    }

    // Customer Cancel Order
    if (isset($_GET['customer_cancel']) && $u['role'] == 'customer') {
        $order_id = (int)$_GET['customer_cancel'];

        // Only allow cancellation of pending orders by the owner
        $stmt = $conn->prepare("SELECT id FROM orders1 WHERE id=? AND (client_id=? OR customer_name=?) AND status='pending'");
        $stmt->execute([$order_id, $uid, $u['username']]);

        if ($stmt->rowCount() > 0) {
            $conn->prepare("UPDATE orders1 SET status='cancelled', cancelled_at=NOW(), cancel_reason='Cancelled by customer' WHERE id=?")
                 ->execute([$order_id]);
            setFlash('success', $t['success_order_cancelled'] ?? 'Order cancelled');
        } else {
            setFlash('error', $t['err_general'] ?? 'Cannot cancel this order');
        }
        header("Location: index.php");
        exit();
    }

    // ==========================================
    // DRIVER ACTIONS
    // ==========================================
    if (isset($_POST['accept_order']) && $u['role'] == 'driver') {
        $oid = (int)$_POST['oid'];

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
                    // Update order
                    $upd = $conn->prepare("UPDATE orders1 SET status='accepted', driver_id=?, accepted_at=NOW(), points_cost=? WHERE id=?");
                    $upd->execute([$uid, $points_cost_per_order, $oid]);

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
