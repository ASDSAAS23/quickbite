<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

require_admin();

$sql = "SELECT orders.*, users.full_name, users.email
        FROM orders
        INNER JOIN users ON orders.user_id = users.id
        ORDER BY orders.created_at DESC";
$result = $conn->query($sql);

include '../includes/header.php';
?>

<section class="order-history-section">
    <div class="container">
        <h1 class="section-title animate-fade-up">Manage Orders</h1>
        <p class="section-subtitle animate-fade-up">View orders and food items ordered by customers. Update order processing status.</p>

        <div class="admin-top-actions animate-fade-up">
            <a href="<?php echo qb_url('admin/dashboard.php'); ?>" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
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
                        <!-- Customer Info -->
                        <div class="order-section">
                            <h4 class="order-section-title">Customer</h4>
                            <p class="order-info-text"><strong><?php echo h($order['full_name']); ?></strong></p>
                            <p class="order-info-text"><?php echo h($order['email']); ?></p>
                        </div>

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
                        <?php if (!empty($order['delivery_address'])): ?>
                            <div class="order-section">
                                <h4 class="order-section-title">Delivery Address</h4>
                                <p class="order-info-text"><?php echo h($order['delivery_address']); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Special Request -->
                        <?php if (!empty($order['special_request'])): ?>
                            <div class="order-section">
                                <h4 class="order-section-title">Special Request</h4>
                                <p class="order-info-text"><?php echo h($order['special_request']); ?></p>
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

                        <!-- Status Update Form -->
                        <div class="order-section">
                            <h4 class="order-section-title">Update Status</h4>
                            <form action="<?php echo qb_url('admin/update_order.php'); ?>" method="POST" class="order-update-form">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <div class="form-group">
                                    <select name="status" required class="form-control">
                                        <option value="Pending" <?php echo ($order['order_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Confirmed" <?php echo ($order['order_status'] === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="Preparing" <?php echo ($order['order_status'] === 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                                        <option value="Delivered" <?php echo ($order['order_status'] === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo ($order['order_status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Update Order Status</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state-box animate-fade-up">
                <h3>No Orders Found</h3>
                <p>There are currently no orders to manage.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
