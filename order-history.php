<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_login();

$userId = (int) $_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

include 'includes/header.php';
?>

<section class="order-history-section">
    <div class="container">
        <h1 class="section-title animate-fade-up">My Order History</h1>
        <p class="section-subtitle animate-fade-up">Track every order you have placed and view items ordered.</p>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($order = $result->fetch_assoc()): ?>
                <?php $statusClass = strtolower(str_replace(' ', '-', $order['order_status'])); ?>
                <div class="order-card animate-fade-up">
                    <div class="order-card-header">
                        <div class="order-card-title">
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p class="order-card-date"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="order-card-status">
                            <span class="status-badge status-<?php echo $statusClass; ?>"><?php echo h($order['order_status']); ?></span>
                        </div>
                    </div>

                    <div class="order-card-body">
                        <!-- Order Summary -->
                        <div class="order-section-row">
                            <div class="order-section">
                                <h4 class="order-section-title">Total Amount</h4>
                                <p class="order-info-amount">₦<?php echo number_format((float) $order['total_amount'], 2); ?></p>
                            </div>
                            <div class="order-section">
                                <h4 class="order-section-title">Delivery Method</h4>
                                <p class="order-info-text"><?php echo h($order['delivery_method']); ?></p>
                            </div>
                            <div class="order-section">
                                <h4 class="order-section-title">Payment Method</h4>
                                <p class="order-info-text"><?php echo h($order['payment_method']); ?></p>
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <?php if (!empty($order['delivery_address']) && $order['delivery_method'] === 'Delivery'): ?>
                            <div class="order-section">
                                <h4 class="order-section-title">Delivery Address</h4>
                                <p class="order-info-text"><?php echo h($order['delivery_address']); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Ordered Items -->
                        <div class="order-section">
                            <h4 class="order-section-title">Items Ordered</h4>
                            <div class="order-items-list">
                                <?php
                                $itemsSql = "SELECT oi.quantity, oi.price, mi.item_name 
                                           FROM order_items oi 
                                           JOIN menu_items mi ON oi.menu_item_id = mi.id 
                                           WHERE oi.order_id = " . (int)$order['id'];
                                $itemsResult = $conn->query($itemsSql);
                                
                                if ($itemsResult && $itemsResult->num_rows > 0):
                                    while ($item = $itemsResult->fetch_assoc()):
                                ?>
                                    <div class="order-item">
                                        <div class="order-item-name">
                                            <span><?php echo h($item['item_name']); ?></span>
                                            <span class="order-item-qty">× <?php echo $item['quantity']; ?></span>
                                        </div>
                                        <div class="order-item-price">₦<?php echo number_format((float) $item['price'] * $item['quantity'], 2); ?></div>
                                    </div>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <p class="order-info-text" style="color: var(--muted);">No items found</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state-box success-box animate-scale">
                <h2>No orders yet</h2>
                <p>You have not placed any orders yet.</p>
                <a href="<?php echo qb_url('menu.php'); ?>" class="btn btn-primary">Start Ordering</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<?php include 'includes/footer.php'; ?>
