<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

require_admin();

if (isset($_POST['order_id'], $_POST['status'])) {
    $orderId = (int) $_POST['order_id'];
    $status = trim($_POST['status']);

    $allowed = ['Pending', 'Confirmed', 'Preparing', 'Delivered', 'Cancelled'];

    if (in_array($status, $allowed, true)) {
        $sql = "UPDATE orders SET order_status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
    }
}

header("Location: " . qb_url('admin/orders.php'));
exit();
