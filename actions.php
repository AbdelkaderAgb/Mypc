<?php
/**
 * Action Handlers
 * This file contains all business logic for admin, driver, and customer actions
 */

// Only process actions if user is logged in
if (isset($_SESSION['user'])) {

    // ==========================================
    // ADMIN ACTIONS
    // ==========================================
    if($u['role'] == 'admin') {

        // Add User
        if(isset($_POST['admin_add_user'])) {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $role = $_POST['role'];
            $points = (int)$_POST['points'];

            try {
                $stmt = $conn->prepare("INSERT INTO users1 (username, password, role, points, status) VALUES (?, ?, ?, ?, 'active')");
                $stmt->execute([$username, $password, $role, $points]);
                setFlash('success', 'User added successfully');
            } catch(PDOException $e) {
                setFlash('error', 'Username already exists');
            }
            header("Location: index.php");
            exit();
        }

        // Edit User
        if(isset($_POST['admin_edit_user'])) {
            $user_id = (int)$_POST['user_id'];
            $password = trim($_POST['password']);
            $role = $_POST['role'];
            $points = (int)$_POST['points'];

            if(!empty($password)) {
                $conn->prepare("UPDATE users1 SET password=?, role=?, points=? WHERE id=?")
                     ->execute([$password, $role, $points, $user_id]);
            } else {
                $conn->prepare("UPDATE users1 SET role=?, points=? WHERE id=?")
                     ->execute([$role, $points, $user_id]);
            }
            setFlash('success', 'User updated successfully');
            header("Location: index.php");
            exit();
        }

        // Ban/Unban User
        if(isset($_GET['toggle_ban'])) {
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
        if(isset($_GET['delete_user'])) {
            $user_id = (int)$_GET['delete_user'];
            $conn->prepare("DELETE FROM users1 WHERE id=? AND id!=?")->execute([$user_id, $uid]);
            setFlash('success', 'User deleted');
            header("Location: index.php");
            exit();
        }

        // Recharge Points
        if (isset($_POST['recharge'])) {
            $amt = (int)$_POST['amount'];
            $did = (int)$_POST['driver_id'];
            if ($amt > 0 && $did > 0) {
                $conn->prepare("UPDATE users1 SET points = points + ? WHERE id=?")->execute([$amt, $did]);
                setFlash('success', "Points added successfully.");
                header("Location: index.php");
                exit();
            }
        }

        // Add Order (Admin)
        if(isset($_POST['admin_add_order'])) {
            $customer_name = trim($_POST['customer_name']);
            $details = mb_convert_encoding(trim($_POST['details']), 'UTF-8', 'UTF-8');
            $address = mb_convert_encoding(trim($_POST['address']), 'UTF-8', 'UTF-8');
            $status = $_POST['status'];
            $driver_id = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : NULL;
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO orders1 (customer_name, details, address, status, driver_id, delivery_code) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customer_name, $details, $address, $status, $driver_id, $otp]);
            setFlash('success', 'Order added successfully');
            header("Location: index.php");
            exit();
        }

        // Edit Order (Admin)
        if(isset($_POST['admin_edit_order'])) {
            $order_id = (int)$_POST['order_id'];
            $customer_name = trim($_POST['customer_name']);
            $details = mb_convert_encoding(trim($_POST['details']), 'UTF-8', 'UTF-8');
            $address = mb_convert_encoding(trim($_POST['address']), 'UTF-8', 'UTF-8');
            $status = $_POST['status'];
            $driver_id = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : NULL;

            $conn->prepare("UPDATE orders1 SET customer_name=?, details=?, address=?, status=?, driver_id=? WHERE id=?")
                 ->execute([$customer_name, $details, $address, $status, $driver_id, $order_id]);
            setFlash('success', 'Order updated successfully');
            header("Location: index.php");
            exit();
        }

        // Cancel Order
        if(isset($_GET['cancel_order'])) {
            $order_id = (int)$_GET['cancel_order'];
            $conn->prepare("UPDATE orders1 SET status='cancelled' WHERE id=?")->execute([$order_id]);
            setFlash('success', 'Order cancelled');
            header("Location: index.php");
            exit();
        }

        // Delete Order
        if(isset($_GET['delete_order'])) {
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
        $details = mb_convert_encoding(trim($_POST['details']), 'UTF-8', 'UTF-8');
        $address = mb_convert_encoding(trim($_POST['address']), 'UTF-8', 'UTF-8');

        if($details && $address) {
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO orders1 (customer_name, details, address, status, delivery_code) VALUES (?, ?, ?, 'pending', ?)");
            $stmt->execute([$u['username'], $details, $address, $otp]);
            setFlash('success', $t['success_add']);
            header("Location: index.php");
            exit();
        }
    }

    // ==========================================
    // DRIVER ACTIONS
    // ==========================================
    if (isset($_POST['accept_order']) && $u['role'] == 'driver') {
        $oid = (int)$_POST['oid'];

        if ($u['points'] < $points_cost_per_order) {
            setFlash('error', $t['err_low_bal']);
        } else {
            try {
                $conn->beginTransaction();

                $chk = $conn->prepare("SELECT id FROM orders1 WHERE id=? AND status='pending' FOR UPDATE");
                $chk->execute([$oid]);

                if ($chk->rowCount() > 0) {
                    $upd = $conn->prepare("UPDATE orders1 SET status='accepted', driver_id=? WHERE id=?");
                    $upd->execute([$uid, $oid]);

                    $deduct = $conn->prepare("UPDATE users1 SET points = points - ? WHERE id=?");
                    $deduct->execute([$points_cost_per_order, $uid]);

                    $conn->commit();
                    setFlash('success', $t['success_acc']);
                } else {
                    $conn->rollBack();
                    setFlash('error', "Order already taken");
                }
            } catch (Exception $e) {
                $conn->rollBack();
                setFlash('error', "System Error");
            }
        }
        header("Location: index.php");
        exit();
    }

    if (isset($_POST['finish_job']) && $u['role'] == 'driver') {
        $oid = (int)$_POST['oid'];
        $pin = str_pad(trim($_POST['pin']), 4, '0', STR_PAD_LEFT);

        $chk = $conn->prepare("SELECT delivery_code FROM orders1 WHERE id=? AND driver_id=? AND status='accepted'");
        $chk->execute([$oid, $uid]);
        $order = $chk->fetch();

        if ($order && $order['delivery_code'] === $pin) {
            $conn->prepare("UPDATE orders1 SET status='delivered' WHERE id=?")->execute([$oid]);
            setFlash('success', $t['success_fin']);
        } else {
            setFlash('error', $t['err_pin']);
        }
        header("Location: index.php");
        exit();
    }
}
?>
