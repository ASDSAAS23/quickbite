<?php
require_once 'includes/functions.php';
include 'includes/header.php';

$orderId = (int) ($_GET['order_id'] ?? 0);
?>

<section class="success-section">
    <div class="container">
        <div class="success-box cart-summary animate-scale" style="position:static;">
            <h1 class="section-title">Order Placed Successfully</h1>
            <p class="section-subtitle">Your order has been created and is now awaiting processing.</p>
            <p><strong>Order ID:</strong> #<?php echo $orderId; ?></p>

            <div class="success-buttons">
                <a href="<?php echo qb_url('order-history.php'); ?>" class="btn btn-light">View Orders</a>
                <a href="<?php echo qb_url('menu.php'); ?>" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
