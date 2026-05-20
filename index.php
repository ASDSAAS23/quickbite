<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
if (is_logged_in()) {
    header('Location: ' . (is_admin() ? qb_url('admin/dashboard.php') : qb_url('dashboard.php')));
    exit();
}

// Fetch featured meals for slider
$featuredResult = $conn->query("SELECT menu_items.*, categories.category_name FROM menu_items INNER JOIN categories ON menu_items.category_id = categories.id WHERE menu_items.availability_status = 'Available' ORDER BY menu_items.id DESC LIMIT 10");

// Store them in an array to output them multiple times for the marquee
$featuredMeals = [];
if ($featuredResult && $featuredResult->num_rows > 0) {
    while ($feat = $featuredResult->fetch_assoc()) {
        $featuredMeals[] = $feat;
    }
}

include 'includes/header.php';
?>

<style>
/* Glovo-inspired Hero */
.glovo-hero {
    background-color: #FFC244; /* Glovo Yellow */
    padding: 120px 0 100px;
    border-radius: 0 0 60px 60px;
    position: relative;
    overflow: hidden;
}
.glovo-hero-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    z-index: 2;
}
.glovo-hero h1 {
    font-size: 4rem;
    font-weight: 800;
    color: #1A1A1A;
    margin-bottom: 25px;
    max-width: 800px;
    line-height: 1.1;
    font-family: 'Poppins', sans-serif;
    letter-spacing: -1px;
}
.glovo-hero p {
    font-size: 1.3rem;
    color: #333;
    margin-bottom: 45px;
    max-width: 600px;
    font-weight: 500;
}
.glovo-search-bar {
    display: flex;
    background: #FFF;
    border-radius: 999px;
    padding: 8px 8px 8px 30px;
    width: 100%;
    max-width: 700px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    align-items: center;
}
.glovo-search-bar input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 1.1rem;
    font-family: 'Roboto', sans-serif;
    color: #1A1A1A;
}
.glovo-search-bar .btn {
    border-radius: 999px;
    padding: 16px 40px;
    background: var(--primary); /* Now Orange */
    color: #FFF;
    font-weight: 700;
    font-size: 1.15rem;
    border: none;
    cursor: pointer;
    transition: background 0.2s ease;
}
.glovo-search-bar .btn:hover {
    background: #00876E;
}
/* Abstract food elements floating in hero */
.floating-food {
    position: absolute;
    width: 120px;
    height: 120px;
    background-size: contain;
    background-repeat: no-repeat;
    opacity: 0.9;
    z-index: 1;
    animation: float 6s ease-in-out infinite;
}
@keyframes float {
    0% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
    100% { transform: translateY(0px) rotate(0deg); }
}

/* Ticker Styles */
.ticker-section {
    padding: 80px 0;
    background: #FFF;
    overflow: hidden;
}
.ticker-section h2 {
    text-align: center;
    font-size: 2.2rem;
    font-weight: 800;
    color: #1A1A1A;
    margin-bottom: 40px;
    font-family: 'Poppins', sans-serif;
}
.ticker-container {
    width: 100%;
    overflow: hidden;
    position: relative;
    padding: 10px 0 30px;
}
.ticker-track {
    display: flex;
    width: max-content;
    animation: newsTicker 30s linear infinite;
    gap: 24px;
    padding-left: 24px;
}
.ticker-track:hover {
    animation-play-state: paused;
}
@keyframes newsTicker {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.ticker-card {
    width: 280px;
    flex-shrink: 0;
    background: #FFF;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #F1F5F9;
    display: flex;
    flex-direction: column;
}
.ticker-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
}
.ticker-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}
.ticker-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.ticker-category {
    font-size: 0.85rem;
    color: var(--accent); /* Now Green */
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}
.ticker-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.2rem;
    color: #1A1A1A;
    margin-bottom: 15px;
    line-height: 1.3;
    flex: 1;
}
.ticker-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}
.ticker-price {
    font-weight: 800;
    color: #1A1A1A;
    font-size: 1.3rem;
}

/* Explore Promo Box (Glovo style) */
.explore-section {
    padding: 60px 0 100px;
    background: #FFF;
}
.glovo-promo-box {
    background: var(--primary); /* Now Orange */
    border-radius: 40px;
    padding: 70px 40px;
    text-align: center;
    color: #FFF;
    position: relative;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,160,130,0.25);
    max-width: 1000px;
    margin: 0 auto;
}
.glovo-promo-box h2 {
    font-size: 3rem;
    font-family: 'Poppins', sans-serif;
    font-weight: 800;
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
    letter-spacing: -1px;
}
.glovo-promo-box p {
    font-size: 1.25rem;
    font-weight: 500;
    max-width: 650px;
    margin: 0 auto 40px;
    position: relative;
    z-index: 2;
    opacity: 0.95;
    line-height: 1.6;
}
.glovo-promo-actions {
    display: flex;
    justify-content: center;
    gap: 20px;
    position: relative;
    z-index: 2;
}
.glovo-promo-actions .btn-yellow {
    background: #FFC244;
    color: #1A1A1A;
    text-decoration: none;
    padding: 16px 40px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 1.15rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 10px 20px rgba(255,194,68,0.3);
}
.glovo-promo-actions .btn-yellow:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 30px rgba(255,194,68,0.4);
}
.glovo-promo-actions .btn-white {
    background: #FFF;
    color: var(--primary); /* Now Orange */
    text-decoration: none;
    padding: 16px 40px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 1.15rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.glovo-promo-actions .btn-white:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}
/* Background circles for promo box */
.promo-circle-1 {
    position: absolute;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    top: -150px;
    left: -100px;
    z-index: 1;
}
.promo-circle-2 {
    position: absolute;
    width: 500px;
    height: 500px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    bottom: -250px;
    right: -150px;
    z-index: 1;
}

@media (max-width: 768px) {
    .glovo-hero {
        padding: 80px 0 60px;
        border-radius: 0 0 40px 40px;
    }
    .glovo-hero h1 {
        font-size: 2.5rem;
    }
    .glovo-search-bar {
        flex-direction: column;
        border-radius: 20px;
        padding: 15px;
    }
    .glovo-search-bar input {
        width: 100%;
        margin-bottom: 10px;
    }
    .glovo-search-bar .btn {
        width: 100%;
    }
    .glovo-promo-box h2 {
        font-size: 2.2rem;
    }
    .glovo-promo-actions {
        flex-direction: column;
    }
}
</style>

<section class="glovo-hero">
    <!-- Abstract food elements (pizza, burger, drink) -->
    <div class="floating-food" style="top: 15%; left: 10%; background-image: url('https://cdn-icons-png.flaticon.com/512/3014/3014491.png'); transform: rotate(-15deg);"></div>
    <div class="floating-food" style="top: 25%; right: 10%; background-image: url('https://cdn-icons-png.flaticon.com/512/3014/3014532.png'); transform: rotate(15deg); animation-delay: 1.5s;"></div>
    <div class="floating-food" style="bottom: -30px; left: 25%; background-image: url('https://cdn-icons-png.flaticon.com/512/3014/3014421.png'); width: 180px; height: 180px; opacity: 0.6; animation-delay: 3s;"></div>
    
    <div class="container glovo-hero-content">
        <h1>What are you craving today?</h1>
        <p>Get your favorite meals, snacks, and drinks delivered fast directly to your door.</p>
        
        <form class="glovo-search-bar" action="<?php echo qb_url('menu.php'); ?>" method="GET">
            <input type="text" name="search" placeholder="Search for burgers, pizza, chicken...">
            <button type="submit" class="btn">Find Food</button>
        </form>
    </div>
</section>

<?php if (!empty($featuredMeals)): ?>
<section class="ticker-section">
    <div class="container">
        <h2>Irresistible Featured Meals 😋</h2>
    </div>
    <div class="ticker-container">
        <div class="ticker-track">
            <?php 
            // Render the meals twice to create a seamless marquee loop
            $loopMeals = array_merge($featuredMeals, $featuredMeals);
            foreach ($loopMeals as $feat): 
            ?>
                <div class="ticker-card">
                    <img src="<?php echo qb_url('assets/images/foods/' . $feat['image']); ?>" alt="<?php echo h($feat['item_name']); ?>">
                    <div class="ticker-content">
                        <div class="ticker-category"><?php echo h($feat['category_name']); ?></div>
                        <h3 class="ticker-title"><?php echo h($feat['item_name']); ?></h3>
                        <div class="ticker-bottom">
                            <span class="ticker-price">&#8358;<?php echo number_format((float) $feat['price'], 2); ?></span>
                            <a href="<?php echo qb_url('food-details.php?id=' . $feat['id']); ?>" class="btn" style="background: var(--primary); color: #FFF; padding: 8px 20px; border-radius: 999px; text-decoration: none; font-weight: 700; font-size: 0.9rem;">Order</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="explore-section">
    <div class="container">
        <div class="glovo-promo-box">
            <div class="promo-circle-1"></div>
            <div class="promo-circle-2"></div>
            <h2>Ready to feast? 🍽️</h2>
            <p>Dive into our full menu, grab the hottest meals in town, or book a table for the ultimate dining experience.</p>
            <div class="glovo-promo-actions">
                <a href="<?php echo qb_url('menu.php'); ?>" class="btn-yellow">Browse Menu</a>
                <a href="<?php echo qb_url('register.php'); ?>" class="btn-white">Join Now</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
