<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$currentPage = current_page_name();
$loggedIn = is_logged_in();
$adminUser = is_admin();
$cartCount = cart_item_count($conn);
$logoTarget = qb_url('index.php');

if ($loggedIn) {
    $logoTarget = $adminUser ? qb_url('admin/dashboard.php') : qb_url('dashboard.php');
}

// Sidebar pages = logged-in users only
$useSidebar = $loggedIn;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seges Foods - Fast. Fresh. Easy.</title>
    <meta name="description" content="Seges Foods — modern online food ordering, reservations, and restaurant management.">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="<?php echo qb_url('assets/css/style.css'); ?>">
</head>
<body class="<?php echo $useSidebar ? 'has-sidebar' : 'has-topbar'; ?>">

<?php if ($useSidebar): ?>
<!-- ====== MOBILE TOP BAR (sidebar pages) ====== -->
<div class="mobile-topbar">
    <a href="<?php echo $logoTarget; ?>" class="logo">
        <div class="logo-icon">S</div>
        <div class="logo-text"><h2>Seges Foods</h2></div>
    </a>
    <div class="mobile-topbar-actions">
        <?php if (!$adminUser): ?>
            <a href="<?php echo qb_url('cart.php'); ?>" class="cart-link-btn">
                Cart
                <?php if ($cartCount > 0): ?><span class="cart-badge"><?php echo $cartCount; ?></span><?php endif; ?>
            </a>
        <?php endif; ?>
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</div>

<!-- ====== DESKTOP SIDEBAR ====== -->
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <a href="<?php echo $logoTarget; ?>" class="logo">
            <div class="logo-icon">S</div>
            <div class="logo-text">
                <h2>Seges Foods</h2>
                <p>Fast. Fresh. Easy.</p>
            </div>
        </a>
    </div>

    <div class="sidebar-user">
        <div class="sidebar-user-avatar"><?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?></div>
        <div class="sidebar-user-info">
            <strong><?php echo h($_SESSION['full_name'] ?? 'User'); ?></strong>
            <span><?php echo $adminUser ? 'Administrator' : 'Customer'; ?></span>
        </div>
    </div>

    <div class="sidebar-label">Navigation</div>
    <ul class="sidebar-nav">
        <?php if ($adminUser): ?>
            <li><a href="<?php echo qb_url('admin/dashboard.php'); ?>" class="<?php echo ($currentPage === 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="<?php echo qb_url('admin/orders.php'); ?>" class="<?php echo ($currentPage === 'orders.php') ? 'active' : ''; ?>">Orders</a></li>
            <li><a href="<?php echo qb_url('admin/menu.php'); ?>" class="<?php echo ($currentPage === 'menu.php') ? 'active' : ''; ?>">Menu</a></li>
            <li><a href="<?php echo qb_url('admin/add-menu.php'); ?>" class="<?php echo ($currentPage === 'add-menu.php') ? 'active' : ''; ?>">Add Dish</a></li>
            <li><a href="<?php echo qb_url('admin/reservations.php'); ?>" class="<?php echo ($currentPage === 'reservations.php') ? 'active' : ''; ?>">Reservations</a></li>
            <li><a href="<?php echo qb_url('admin/reports.php'); ?>" class="<?php echo ($currentPage === 'reports.php') ? 'active' : ''; ?>">Monthly Reports</a></li>
        <?php else: ?>
            <li><a href="<?php echo qb_url('dashboard.php'); ?>" class="<?php echo ($currentPage === 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="<?php echo qb_url('menu.php'); ?>" class="<?php echo ($currentPage === 'menu.php') ? 'active' : ''; ?>">Menu</a></li>
            <li><a href="<?php echo qb_url('order-history.php'); ?>" class="<?php echo ($currentPage === 'order-history.php') ? 'active' : ''; ?>">Orders</a></li>
            <li><a href="<?php echo qb_url('cart.php'); ?>" class="<?php echo ($currentPage === 'cart.php') ? 'active' : ''; ?>">Cart<?php if ($cartCount > 0) echo ' <span class="sidebar-count">' . $cartCount . '</span>'; ?></a></li>
            <li><a href="<?php echo qb_url('reservation.php'); ?>" class="<?php echo ($currentPage === 'reservation.php') ? 'active' : ''; ?>">Reservation</a></li>
            <li><a href="<?php echo qb_url('profile.php'); ?>" class="<?php echo ($currentPage === 'profile.php') ? 'active' : ''; ?>">Profile</a></li>
        <?php endif; ?>
    </ul>

    <div class="sidebar-bottom">
        <a href="<?php echo qb_url('logout.php'); ?>" class="sidebar-bottom-btn logout">Logout</a>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<main class="app-main">

<?php else: ?>
<!-- ====== TOP NAVBAR (public pages) ====== -->
<header class="topbar-header">
    <div class="container topbar-container">
        <a href="<?php echo qb_url('index.php'); ?>" class="logo">
            <div class="logo-icon">S</div>
            <div class="logo-text">
                <h2>Seges Foods</h2>
                <p>Fast. Fresh. Easy.</p>
            </div>
        </a>

        <nav class="topbar-nav">
            <ul class="topbar-links">
                <li><a href="<?php echo qb_url('index.php'); ?>" class="<?php echo ($currentPage === 'index.php') ? 'active' : ''; ?>">Home</a></li>
                <li><a href="<?php echo qb_url('menu.php'); ?>" class="<?php echo ($currentPage === 'menu.php') ? 'active' : ''; ?>">Menu</a></li>
                <li><a href="<?php echo qb_url('about.php'); ?>" class="<?php echo ($currentPage === 'about.php') ? 'active' : ''; ?>">About</a></li>
                <li><a href="<?php echo qb_url('reservation.php'); ?>" class="<?php echo ($currentPage === 'reservation.php') ? 'active' : ''; ?>">Reservation</a></li>
                <li><a href="<?php echo qb_url('contact.php'); ?>" class="<?php echo ($currentPage === 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
            </ul>
        </nav>

        <div class="topbar-actions">
            <a href="<?php echo qb_url('login.php'); ?>" class="btn btn-light btn-sm">Login</a>
            <a href="<?php echo qb_url('register.php'); ?>" class="btn btn-primary btn-sm">Create Account</a>
        </div>

        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Mobile drawer for public pages -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="mobile-drawer" id="mobileDrawer">
    <button class="drawer-close" id="drawerClose">&times;</button>
    <ul class="drawer-links">
        <li><a href="<?php echo qb_url('index.php'); ?>">Home</a></li>
        <li><a href="<?php echo qb_url('menu.php'); ?>">Menu</a></li>
        <li><a href="<?php echo qb_url('about.php'); ?>">About</a></li>
        <li><a href="<?php echo qb_url('reservation.php'); ?>">Reservation</a></li>
        <li><a href="<?php echo qb_url('contact.php'); ?>">Contact</a></li>
    </ul>
    <div class="drawer-actions">
        <a href="<?php echo qb_url('login.php'); ?>" class="btn btn-light" style="width:100%;justify-content:center;">Login</a>
        <a href="<?php echo qb_url('register.php'); ?>" class="btn btn-primary" style="width:100%;justify-content:center;">Create Account</a>
    </div>
</div>

<?php endif; ?>
