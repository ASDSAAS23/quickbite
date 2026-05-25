<?php
$useSidebar = is_logged_in();
?>

<?php if ($useSidebar): ?>
<!-- Slim footer for sidebar pages -->
</main><!-- end .app-main -->
<div class="slim-footer">
    <div class="slim-footer-inner">
        <span>&copy; <?php echo date('Y'); ?> Seges Foods</span>
        <span class="slim-footer-sep">&middot;</span>
        <a href="<?php echo qb_url('menu.php'); ?>">Menu</a>
        <span class="slim-footer-sep">&middot;</span>

        <span>Logged in as <?php echo h($_SESSION['full_name'] ?? 'User'); ?></span>
    </div>
</div>

<?php else: ?>
<!-- Full footer for public pages -->
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-box">
            <h3>Seges Foods</h3>
            <p>Seges Foods is a full restaurant ordering and management system designed for fast ordering, real-time cart handling, and admin control.</p>
            <div class="footer-social">
                <a href="#" title="Twitter">X</a>
                <a href="#" title="Instagram">IG</a>
                <a href="#" title="Facebook">FB</a>
                <a href="#" title="LinkedIn">IN</a>
            </div>
        </div>

        <div class="footer-box">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo qb_url('index.php'); ?>">Home</a></li>
                <li><a href="<?php echo qb_url('menu.php'); ?>">Menu</a></li>

                <li><a href="<?php echo qb_url('about.php'); ?>">About Us</a></li>
                <li><a href="<?php echo qb_url('contact.php'); ?>">Contact</a></li>
            </ul>
        </div>

        <div class="footer-box">
            <h4>Contact</h4>
            <p>Email: hello@quickbite.test</p>
            <p>Phone: +234 800 111 2233</p>
            <p>Address: 12 Food Lane, Lagos, Nigeria</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Seges Foods. All rights reserved.</p>
    </div>
</footer>
<?php endif; ?>

<button class="scroll-to-top" id="scrollToTop" aria-label="Scroll to top">&uarr;</button>

<script src="<?php echo qb_url('assets/js/main.js'); ?>"></script>
</body>
</html>
<?php
// Chowdeck-style bottom nav for mobile only
?>
<div class="bottom-nav" id="bottomNav" style="display:none">
    <a href="<?php echo qb_url('index.php'); ?>" class="<?php if ($currentPage === 'index.php') echo 'active'; ?>">
        <span class="nav-icon">🏠</span>
        <span>Home</span>
    </a>
    <a href="<?php echo qb_url('menu.php'); ?>" class="<?php if ($currentPage === 'menu.php') echo 'active'; ?>">
        <span class="nav-icon">🍔</span>
        <span>Menu</span>
    </a>
    <a href="<?php echo qb_url('cart.php'); ?>" class="<?php if ($currentPage === 'cart.php') echo 'active'; ?>">
        <span class="nav-icon">🛒</span>
        <span>Cart</span>
    </a>
    <a href="<?php echo qb_url('order-history.php'); ?>" class="<?php if ($currentPage === 'order-history.php') echo 'active'; ?>">
        <span class="nav-icon">📦</span>
        <span>Orders</span>
    </a>
    <a href="<?php echo qb_url('profile.php'); ?>" class="<?php if ($currentPage === 'profile.php') echo 'active'; ?>">
        <span class="nav-icon">👤</span>
        <span>Me</span>
    </a>
</div>
<script>
// Show bottom nav only on mobile
if(window.innerWidth <= 576){
  document.getElementById('bottomNav').style.display = 'flex';
}
window.addEventListener('resize',function(){
  document.getElementById('bottomNav').style.display = (window.innerWidth <= 576) ? 'flex' : 'none';
});
</script>
