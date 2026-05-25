<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_login();

if (is_admin()) {
    header('Location: ' . qb_url('admin/dashboard.php'));
    exit();
}

$userId = (int) $_SESSION['user_id'];

$orderCountStmt = $conn->prepare("SELECT COUNT(*) AS total_orders FROM orders WHERE user_id = ?");
$orderCountStmt->bind_param("i", $userId);
$orderCountStmt->execute();
$orderCount = (int) ($orderCountStmt->get_result()->fetch_assoc()['total_orders'] ?? 0);

$activeOrderStmt = $conn->prepare("SELECT COUNT(*) AS active_orders FROM orders WHERE user_id = ? AND order_status IN ('Pending','Confirmed','Preparing')");
$activeOrderStmt->bind_param("i", $userId);
$activeOrderStmt->execute();
$activeOrders = (int) ($activeOrderStmt->get_result()->fetch_assoc()['active_orders'] ?? 0);



$recentOrdersStmt = $conn->prepare("SELECT id, total_amount, order_status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$recentOrdersStmt->bind_param("i", $userId);
$recentOrdersStmt->execute();
$recentOrders = $recentOrdersStmt->get_result();

include 'includes/header.php';
?>

<section class="user-dashboard">
    <div class="container">
        <div class="dashboard-hero animate-fade-up">
            <div>
                <span class="dashboard-kicker">Welcome back</span>
                <h1 class="dashboard-title"><?php echo h($_SESSION['full_name'] ?? 'User'); ?></h1>
                <p class="dashboard-copy">This is your personal Seges Foods dashboard. From here you can order meals, monitor your orders, and keep track of account activity.</p>
            </div>
            <div class="dashboard-hero-actions">
                <a href="<?php echo qb_url('menu.php'); ?>" class="btn btn-primary">Order Meals</a>
                <a href="<?php echo qb_url('order-history.php'); ?>" class="btn btn-light">View Orders</a>
            </div>
        </div>

        <div class="dashboard-cards user-dashboard-cards">
            <div class="dashboard-card animate-fade-up stagger-1">
                <h3>Total Orders</h3>
                <p><?php echo $orderCount; ?></p>
            </div>
            <div class="dashboard-card animate-fade-up stagger-2">
                <h3>Active Orders</h3>
                <p><?php echo $activeOrders; ?></p>
            </div>

            <div class="dashboard-card animate-fade-up stagger-4">
                <h3>Cart Items</h3>
                <p><?php echo cart_item_count($conn); ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="admin-panel-card animate-fade-up">
                <h3 class="panel-title">Quick Actions</h3>
                <div class="quick-actions-grid">
                    <a href="<?php echo qb_url('menu.php'); ?>" class="quick-action-card">
                        <strong>Browse Menu</strong>
                        <span>Explore meals and add new items to cart.</span>
                    </a>
                    <a href="<?php echo qb_url('cart.php'); ?>" class="quick-action-card">
                        <strong>Open Cart</strong>
                        <span>Review quantities, totals, and proceed to checkout.</span>
                    </a>

                    <a href="<?php echo qb_url('profile.php'); ?>" class="quick-action-card">
                        <strong>My Profile</strong>
                        <span>See the account details currently stored in your session.</span>
                    </a>
                </div>
            </div>

            <div class="admin-panel-card animate-fade-up">
                <h3 class="panel-title">Recent Orders</h3>
                <div class="order-history-table-wrapper compact-table-wrap">
                    <table class="order-history-table compact-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentOrders->num_rows > 0): ?>
                                <?php while ($order = $recentOrders->fetch_assoc()): ?>
                                    <?php $statusClass = strtolower(str_replace(' ', '-', $order['order_status'])); ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td>&#8358;<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                                        <td><span class="status-badge status-<?php echo $statusClass; ?>"><?php echo h($order['order_status']); ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4">No orders yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
