<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header("Location: " . (is_admin() ? qb_url('admin/dashboard.php') : qb_url('dashboard.php')));
    exit();
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } else {
        $sql = "SELECT id, full_name, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            $message = "No account found with that email.";
            $messageType = "error";
        } else {
            $user = $result->fetch_assoc();

            if (!password_verify($password, $user['password'])) {
                $message = "Incorrect password.";
                $messageType = "error";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: " . qb_url('admin/dashboard.php'));
                } else {
                    header("Location: " . qb_url('dashboard.php'));
                }
                exit();
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
            <h1>Welcome Back</h1>
            <p class="auth-subtext">Login to continue ordering meals or managing the restaurant.</p>

            <?php if ($message !== ''): ?>
                <div class="alert <?php echo $messageType; ?>"><?php echo h($message); ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Login</button>
            </form>

            <p class="auth-switch">Don't have an account? <a href="<?php echo qb_url('register.php'); ?>">Create Account</a></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
