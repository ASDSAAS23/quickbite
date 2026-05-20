<?php
require_once 'includes/functions.php';
require_login();
include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div class="page-banner-inner animate-fade-up">
            <h1 class="section-title">My Profile</h1>
            <p class="section-subtitle" style="margin-bottom:0;">Your account details and session information.</p>
        </div>
    </div>
</section>

<section>
    <div class="container info-grid">
        <div class="info-card animate-fade-up stagger-1">
            <h3>Full Name</h3>
            <p><?php echo h($_SESSION['full_name'] ?? ''); ?></p>
        </div>
        <div class="info-card animate-fade-up stagger-2">
            <h3>Email Address</h3>
            <p><?php echo h($_SESSION['email'] ?? ''); ?></p>
        </div>
        <div class="info-card animate-fade-up stagger-3">
            <h3>Role</h3>
            <p style="text-transform: capitalize;"><?php echo h($_SESSION['role'] ?? 'user'); ?></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
