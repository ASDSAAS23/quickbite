<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header("Location: " . qb_url('index.php'));
    exit();
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if ($fullName === '' || $email === '' || $phone === '' || $password === '' || $confirmPassword === '') {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long.";
        $messageType = "error";
    } else {
        $checkSql = "SELECT id FROM users WHERE email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $message = "An account with this email already exists.";
            $messageType = "error";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertSql = "INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("ssss", $fullName, $email, $phone, $hashedPassword);

            if ($insertStmt->execute()) {
                $message = "Account created successfully. You can now log in.";
                $messageType = "success";
            } else {
                $message = "Unable to create account right now.";
                $messageType = "error";
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <h2>Seges Foods</h2>
            </div>
            <h1>Create Account</h1>
            <p class="auth-subtext">Register to start ordering meals, saving reservations, and tracking your orders.</p>

            <?php if ($message !== ''): ?>
                <div class="alert <?php echo $messageType; ?>"><?php echo h($message); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="+234 800 000 0000" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Min. 6 characters" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Re-enter password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Create Account</button>
            </form>

            <p class="auth-switch">Already have an account? <a href="<?php echo qb_url('login.php'); ?>">Login</a></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
