<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

require_admin();

// Only today's orders and revenue (resets every 24hrs)
$today = date('Y-m-d');
$orderSql = "SELECT COUNT(*) AS total_orders FROM orders WHERE DATE(created_at) = '$today'";
$orderResult = $conn->query($orderSql);
$totalOrders = (int) ($orderResult->fetch_assoc()['total_orders'] ?? 0);

$revenueSql = "SELECT COALESCE(SUM(total_amount), 0) AS total_revenue FROM orders WHERE DATE(created_at) = '$today'";
$revenueResult = $conn->query($revenueSql);
$totalRevenue = (float) ($revenueResult->fetch_assoc()['total_revenue'] ?? 0);

$userSql = "SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'";
$userResult = $conn->query($userSql);
$totalUsers = (int) ($userResult->fetch_assoc()['total_users'] ?? 0);

$pendingSql = "SELECT COUNT(*) AS pending_orders FROM orders WHERE order_status = 'Pending'";
$pendingResult = $conn->query($pendingSql);
$pendingOrders = (int) ($pendingResult->fetch_assoc()['pending_orders'] ?? 0);

// Reservations reset (only show upcoming or current)
$reservationSql = "SELECT COUNT(*) AS total_reservations FROM reservations WHERE reservation_date >= '$today'";
$reservationResult = $conn->query($reservationSql);
$totalReservations = (int) ($reservationResult->fetch_assoc()['total_reservations'] ?? 0);

$latestOrders = $conn->query("SELECT orders.id, users.full_name, orders.total_amount, orders.order_status, orders.created_at 
                              FROM orders 
                              INNER JOIN users ON orders.user_id = users.id
                              ORDER BY orders.created_at DESC LIMIT 5");

$latestReservations = $conn->query("SELECT full_name, guests, reservation_date, reservation_time, status
                                    FROM reservations
                                    WHERE reservation_date >= '$today'
                                    ORDER BY created_at DESC LIMIT 5");

include '../includes/header.php';
?>

<section class="admin-dashboard">
    <div class="container">
        <h1 class="section-title">Admin Dashboard</h1>
        <p class="section-subtitle">Monitor system activity, orders, menu growth, and reservation workflow from one screen.</p>

        <div class="admin-top-actions">
            <a href="<?php echo qb_url('admin/orders.php'); ?>" class="btn btn-primary">Manage Orders</a>
            <a href="<?php echo qb_url('admin/menu.php'); ?>" class="btn btn-light">Manage Menu</a>
            <a href="<?php echo qb_url('admin/add-menu.php'); ?>" class="btn btn-light">Add New Dish</a>
            <a href="<?php echo qb_url('admin/reservations.php'); ?>" class="btn btn-light">Manage Reservations</a>
        </div>

        <div class="dashboard-cards">
            <div class="dashboard-card">
                <h3>Total Orders</h3>
                <p><?php echo $totalOrders; ?></p>
                <small style="color:var(--muted)">Today only</small>
            </div>
            <div class="dashboard-card">
                <h3>Total Revenue</h3>
                <p>₦<?php echo number_format($totalRevenue, 2); ?></p>
                <small style="color:var(--muted)">Today only</small>
            </div>
            <div class="dashboard-card">
                <h3>Total Users</h3>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Pending Orders</h3>
                <p><?php echo $pendingOrders; ?></p>
            </div>
            <div class="dashboard-card">
                <h3>Reservations</h3>
                <p><?php echo $totalReservations; ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="admin-panel-card">
                <h3 style="font-family:'Poppins',sans-serif; margin-bottom:14px;">Recent Orders</h3>
                <div class="order-history-table-wrapper" style="padding:0; box-shadow:none;">
                    <table class="order-history-table" style="min-width:0;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($latestOrders && $latestOrders->num_rows > 0): ?>
                                <?php while ($order = $latestOrders->fetch_assoc()): ?>
                                    <?php $statusClass = strtolower(str_replace(' ', '-', $order['order_status'])); ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo h($order['full_name']); ?></td>
                                        <td>₦<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                                        <td><span class="status-badge status-<?php echo $statusClass; ?>"><?php echo h($order['order_status']); ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4">No orders yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-panel-card">
                <h3 style="font-family:'Poppins',sans-serif; margin-bottom:14px;">Recent Reservations</h3>
                <div class="order-history-table-wrapper" style="padding:0; box-shadow:none;">
                    <table class="order-history-table" style="min-width:0;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Guests</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($latestReservations && $latestReservations->num_rows > 0): ?>
                                <?php while ($reservation = $latestReservations->fetch_assoc()): ?>
                                    <?php $statusClass = strtolower($reservation['status']); ?>
                                    <tr>
                                        <td><?php echo h($reservation['full_name']); ?></td>
                                        <td><?php echo (int) $reservation['guests']; ?></td>
                                        <td><?php echo h($reservation['reservation_date']); ?></td>
                                        <td><span class="status-badge status-<?php echo $statusClass; ?>"><?php echo h($reservation['status']); ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4">No reservations yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
