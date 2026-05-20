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
        <p class="section-subtitle animate-fade-up">Update order processing status from pending to delivery completion.</p>

        <div class="admin-top-actions animate-fade-up">
            <a href="<?php echo qb_url('admin/dashboard.php'); ?>" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>

        <div class="order-history-table-wrapper animate-fade-up">
            <table class="order-history-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Delivery</th>
                        <th>Payment</th>
                        <th>Special Request</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($order = $result->fetch_assoc()): ?>
                            <?php $statusClass = strtolower(str_replace(' ', '-', $order['order_status'])); ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td>
                                    <strong><?php echo h($order['full_name']); ?></strong>
                                    <br><small style="color:var(--muted);"><?php echo h($order['email']); ?></small>
                                </td>
                                <td>&#8358;<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                                <td>
                                    <?php echo h($order['delivery_method']); ?>
                                    <?php if (!empty($order['delivery_address'])): ?>
                                        <br><small style="color:var(--muted);"><?php echo h($order['delivery_address']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo h($order['payment_method']); ?></td>
                                <td>
                                    <?php if (!empty($order['special_request'])): ?>
                                        <span class="special-request-text"><?php echo h($order['special_request']); ?></span>
                                    <?php else: ?>
                                        <span style="color:var(--muted);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-badge status-<?php echo $statusClass; ?>"><?php echo h($order['order_status']); ?></span></td>
                                <td>
                                    <form action="<?php echo qb_url('admin/update_order.php'); ?>" method="POST" class="inline-actions">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" required>
                                            <option value="Pending" <?php echo ($order['order_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Confirmed" <?php echo ($order['order_status'] === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="Preparing" <?php echo ($order['order_status'] === 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                                            <option value="Delivered" <?php echo ($order['order_status'] === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="Cancelled" <?php echo ($order['order_status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
