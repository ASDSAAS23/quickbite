<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header("Location: " . qb_url('menu.php'));
    exit();
}

$itemId = (int) $_GET['id'];
$sql = "SELECT menu_items.*, categories.category_name
        FROM menu_items
        INNER JOIN categories ON menu_items.category_id = categories.id
        WHERE menu_items.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: " . qb_url('menu.php'));
    exit();
}

$item = $result->fetch_assoc();
include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div class="page-banner-inner animate-fade-up">
            <h1 class="section-title">Meal Details</h1>
            <p class="section-subtitle" style="margin-bottom:0;">View full meal details before adding it to your cart.</p>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="food-details-wrapper animate-fade-up">
            <div class="food-details-image">
                <img src="<?php echo qb_url('assets/images/foods/' . $item['image']); ?>" alt="<?php echo h($item['item_name']); ?>">
            </div>

            <div class="food-details-content">
                <span class="food-tag"><?php echo h($item['category_name']); ?></span>
                <h2><?php echo h($item['item_name']); ?></h2>
                <p class="food-price">&#8358;<?php echo number_format((float) $item['price'], 2); ?></p>
                <p class="food-description"><?php echo h($item['description']); ?></p>

                <div class="food-meta">
                    <p><strong>Category:</strong> <?php echo h($item['category_name']); ?></p>
                    <p><strong>Status:</strong> <?php echo h($item['availability_status']); ?></p>
                    <p><strong>Estimated Delivery:</strong> 20 - 30 minutes</p>
                </div>

                <div class="food-buttons">
                    <a href="<?php echo qb_url('add_to_cart.php?id=' . $item['id']); ?>" class="btn btn-primary">Add to Cart</a>
                    <a href="<?php echo qb_url('menu.php'); ?>" class="btn btn-light">Back to Menu</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
