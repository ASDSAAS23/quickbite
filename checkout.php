<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_login();

$userId = (int) $_SESSION['user_id'];

$sql = "SELECT cart.quantity, menu_items.id AS menu_item_id, menu_items.item_name, menu_items.price
        FROM cart
        INNER JOIN menu_items ON cart.menu_item_id = menu_items.id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$subtotal = 0;

while ($row = $result->fetch_assoc()) {
    $row['row_total'] = (float) $row['price'] * (int) $row['quantity'];
    $subtotal += $row['row_total'];
    $cartItems[] = $row;
}

if (!$cartItems) {
    header("Location: " . qb_url('cart.php'));
    exit();
}

$deliveryFee = 1500;

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deliveryMethod = trim($_POST['delivery_method'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? '');
    $deliveryAddress = trim($_POST['delivery_address'] ?? '');
    $specialRequest = trim($_POST['special_request'] ?? '');

    $finalDeliveryFee = ($deliveryMethod === 'Delivery') ? $deliveryFee : 0;
    $total = $subtotal + $finalDeliveryFee;

    if ($deliveryMethod === '' || $paymentMethod === '') {
        $message = 'Please complete all required fields.';
        $messageType = 'error';
    } elseif ($deliveryMethod === 'Delivery' && $deliveryAddress === '') {
        $message = 'Please enter a delivery address.';
        $messageType = 'error';
    } else {
        $orderSql = "INSERT INTO orders (user_id, total_amount, delivery_method, payment_method, order_status, delivery_address, special_request)
                     VALUES (?, ?, ?, ?, 'Pending', ?, ?)";
        $orderStmt = $conn->prepare($orderSql);
        $orderStmt->bind_param("idssss", $userId, $total, $deliveryMethod, $paymentMethod, $deliveryAddress, $specialRequest);

        if ($orderStmt->execute()) {
            $orderId = $conn->insert_id;

            foreach ($cartItems as $item) {
                $itemSql = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
                $itemStmt = $conn->prepare($itemSql);
                $itemStmt->bind_param("iiid", $orderId, $item['menu_item_id'], $item['quantity'], $item['price']);
                $itemStmt->execute();
            }

            $paymentSql = "INSERT INTO payments (order_id, payment_method, amount, payment_status) VALUES (?, ?, ?, 'Pending')";
            $paymentStmt = $conn->prepare($paymentSql);
            $paymentStmt->bind_param("isd", $orderId, $paymentMethod, $total);
            $paymentStmt->execute();

            $clearSql = "DELETE FROM cart WHERE user_id = ?";
            $clearStmt = $conn->prepare($clearSql);
            $clearStmt->bind_param("i", $userId);
            $clearStmt->execute();

            if ($paymentMethod === 'Demo Card Payment') {
                header("Location: " . qb_url('pay-card.php?order_id=' . $orderId));
            } elseif ($paymentMethod === 'Bank Transfer') {
                header("Location: " . qb_url('pay-transfer.php?order_id=' . $orderId));
            } else {
                header("Location: " . qb_url('order-success.php?order_id=' . $orderId));
            }
            exit();
            $message = 'Unable to place order right now.';
            $messageType = 'error';
        }
    }
}

include 'includes/header.php';
?>

<section class="checkout-section">
    <div class="container">
        <h1 class="section-title animate-fade-up">Checkout</h1>
        <p class="section-subtitle animate-fade-up">Complete your order details and place your order.</p>

        <?php if ($message !== ''): ?>
            <div class="alert <?php echo $messageType; ?>"><?php echo h($message); ?></div>
        <?php endif; ?>

        <div class="checkout-wrapper">
            <div class="checkout-form-box animate-fade-up">
                <form method="POST" class="checkout-form" id="checkoutForm">
                    <div class="form-group">
                        <label>Delivery Method</label>
                        <div class="toggle-slider-wrap">
                            <input type="radio" name="delivery_method" value="Delivery" id="methodDelivery" checked class="toggle-slider-input">
                            <input type="radio" name="delivery_method" value="Pickup" id="methodPickup" class="toggle-slider-input">
                            <div class="toggle-slider" id="toggleSlider">
                                <div class="toggle-slider-bg"></div>
                                <label for="methodDelivery" class="toggle-slider-option active" data-option="delivery">
                                    Delivery (+&#8358;<?php echo number_format($deliveryFee, 0); ?>)
                                </label>
                                <label for="methodPickup" class="toggle-slider-option" data-option="pickup">
                                    Pickup (Free)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" data-address-group>
                        <label>Delivery Address</label>
                        <textarea name="delivery_address" rows="3" placeholder="Enter your full delivery address"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="Demo Card Payment">Demo Card Payment</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Pay on Pickup">Pay on Pickup (or Delivery)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Special Request <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                        <textarea name="special_request" rows="3" placeholder="e.g. No onions, extra sauce, allergies, delivery instructions..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Place Order</button>
                </form>
            </div>

            <div class="checkout-summary-box animate-fade-up">
                <h2 style="font-family:'Poppins',sans-serif; margin-bottom:18px;">Order Summary</h2>

                <?php foreach ($cartItems as $item): ?>
                    <div class="checkout-item-row">
                        <div>
                            <strong><?php echo h($item['item_name']); ?></strong>
                            <p>Qty: <?php echo (int) $item['quantity']; ?></p>
                        </div>
                        <span>&#8358;<?php echo number_format((float) $item['row_total'], 2); ?></span>
                    </div>
                <?php endforeach; ?>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>&#8358;<?php echo number_format((float) $subtotal, 2); ?></span>
                </div>

                <div class="summary-row" id="deliveryFeeRow">
                    <span>Delivery Fee</span>
                    <span id="deliveryFeeAmount">&#8358;<?php echo number_format((float) $deliveryFee, 2); ?></span>
                </div>

                <div class="summary-row total-row">
                    <span>Total</span>
                    <span id="totalAmount">&#8358;<?php echo number_format((float) ($subtotal + $deliveryFee), 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const deliveryRadios = document.querySelectorAll('input[name="delivery_method"]');
    const feeRow = document.getElementById('deliveryFeeRow');
    const feeAmount = document.getElementById('deliveryFeeAmount');
    const totalAmount = document.getElementById('totalAmount');
    const toggleSlider = document.getElementById('toggleSlider');
    const options = document.querySelectorAll('.toggle-slider-option');
    const subtotal = <?php echo $subtotal; ?>;
    const deliveryFee = <?php echo $deliveryFee; ?>;

    const updateSummary = () => {
        const selected = document.querySelector('input[name="delivery_method"]:checked');
        if (!selected) return;

        const isDelivery = selected.value === 'Delivery';
        const fee = isDelivery ? deliveryFee : 0;
        const total = subtotal + fee;

        feeAmount.textContent = '\u20A6' + fee.toLocaleString('en-NG', {minimumFractionDigits: 2});
        totalAmount.textContent = '\u20A6' + total.toLocaleString('en-NG', {minimumFractionDigits: 2});

        if (!isDelivery) {
            feeRow.style.opacity = '0.4';
            feeRow.style.textDecoration = 'line-through';
        } else {
            feeRow.style.opacity = '1';
            feeRow.style.textDecoration = 'none';
        }

        // Toggle slider animation
        if (toggleSlider) {
            toggleSlider.classList.toggle('pickup', !isDelivery);
            options.forEach(opt => {
                opt.classList.toggle('active', 
                    (isDelivery && opt.dataset.option === 'delivery') ||
                    (!isDelivery && opt.dataset.option === 'pickup')
                );
            });
        }
    };

    deliveryRadios.forEach(r => r.addEventListener('change', updateSummary));
    updateSummary();
});
</script>

<?php include 'includes/footer.php'; ?>
