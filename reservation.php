<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = is_logged_in() ? (int) $_SESSION['user_id'] : null;
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $reservationDate = trim($_POST['reservation_date'] ?? '');
    $reservationTime = trim($_POST['reservation_time'] ?? '');
    $guests = (int) ($_POST['guests'] ?? 0);
    $eventType = trim($_POST['event_type'] ?? '');
    $specialRequest = trim($_POST['special_request'] ?? '');

    if ($eventType !== '') {
        $specialRequest = "Event: $eventType" . ($specialRequest !== '' ? " | " . $specialRequest : '');
    }

    if ($fullName === '' || $email === '' || $phone === '' || $reservationDate === '' || $reservationTime === '' || $guests <= 0) {
        $message = 'Please complete all required fields.';
        $messageType = 'error';
    } else {
        $sql = "INSERT INTO reservations (user_id, full_name, email, phone, reservation_date, reservation_time, guests, special_request, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssds", $userId, $fullName, $email, $phone, $reservationDate, $reservationTime, $guests, $specialRequest);

        if ($stmt->execute()) {
            $message = 'Reservation submitted successfully! You will receive confirmation once the admin reviews it.';
            $messageType = 'success';
        } else {
            $message = 'Unable to submit reservation right now.';
            $messageType = 'error';
        }
    }
}

$defaultName = is_logged_in() ? ($_SESSION['full_name'] ?? '') : '';
$defaultEmail = is_logged_in() ? ($_SESSION['email'] ?? '') : '';

// Fetch user's reservation history
$reservations = [];
if (is_logged_in()) {
    $userId = (int) $_SESSION['user_id'];
    $resStmt = $conn->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $resStmt->bind_param("i", $userId);
    $resStmt->execute();
    $resResult = $resStmt->get_result();
    while ($r = $resResult->fetch_assoc()) {
        $reservations[] = $r;
    }
}

include 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <div class="page-banner-inner animate-fade-up">
            <h1 class="section-title">Reserve a Table</h1>
            <p class="section-subtitle" style="margin-bottom:0;">Pick your preferred date, time, and party size. Add any special requests for the kitchen or service team.</p>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <?php if ($message !== ''): ?>
            <div class="alert <?php echo $messageType; ?>"><?php echo h($message); ?></div>
        <?php endif; ?>

        <div class="reservation-layout">
            <!-- Form Column -->
            <div class="checkout-form-box animate-fade-up">
                <h2 style="font-family:'Poppins',sans-serif; margin-bottom:6px;">Reservation Details</h2>
                <p style="color:var(--muted); margin-bottom:22px; font-size:0.92rem;">Fill in your details below. All fields marked are required.</p>

                <form method="POST" class="checkout-form">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="<?php echo h($defaultName); ?>" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo h($defaultEmail); ?>" placeholder="you@example.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="+234 800 000 0000" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Reservation Date</label>
                            <input type="date" name="reservation_date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Reservation Time</label>
                            <input type="time" name="reservation_time" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Number of Guests</label>
                        <input type="number" name="guests" min="1" max="50" placeholder="e.g. 4" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Event Type <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                            <select name="event_type">
                                <option value="">None / Regular Dining</option>
                                <option value="Birthday">Birthday</option>
                                <option value="Anniversary">Anniversary</option>
                                <option value="Business Meeting">Business Meeting</option>
                                <option value="Date Night">Date Night</option>
                                <option value="Other Celebration">Other Celebration</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Special Request <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                            <textarea name="special_request" rows="2" placeholder="e.g. Window seat, dietary restrictions..."></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding:16px;">Submit Reservation</button>
                </form>
            </div>

            <!-- Info Sidebar -->
            <div class="reservation-info-sidebar animate-fade-up">
                <div class="reservation-info-card">
                    <h3>How It Works</h3>
                    <div class="res-step">
                        <div class="res-step-num">1</div>
                        <div>
                            <strong>Fill the Form</strong>
                            <p>Enter your details, date, time, and party size.</p>
                        </div>
                    </div>
                    <div class="res-step">
                        <div class="res-step-num">2</div>
                        <div>
                            <strong>Admin Reviews</strong>
                            <p>Our team checks availability and confirms your booking.</p>
                        </div>
                    </div>
                    <div class="res-step">
                        <div class="res-step-num">3</div>
                        <div>
                            <strong>Confirmation</strong>
                            <p>Your reservation status updates to Confirmed in your history.</p>
                        </div>
                    </div>
                </div>

                <div class="reservation-info-card">
                    <h3>Opening Hours</h3>
                    <div class="hours-row"><span>Mon - Fri</span><strong>9:00 AM - 10:00 PM</strong></div>
                    <div class="hours-row"><span>Saturday</span><strong>10:00 AM - 11:00 PM</strong></div>
                    <div class="hours-row"><span>Sunday</span><strong>12:00 PM - 9:00 PM</strong></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($reservations)): ?>
<section style="padding-top:20px;">
    <div class="container">
        <h2 class="section-title animate-fade-up">Your Reservations</h2>
        <p class="section-subtitle animate-fade-up">Track the status of your recent table bookings.</p>

        <div class="reservation-history-wrapper">
            <?php foreach ($reservations as $res): ?>
                <?php $statusClass = strtolower(str_replace(' ', '-', $res['status'])); ?>
                <div class="reservation-card animate-fade-up">
                    <div class="reservation-card-header">
                        <span class="reservation-id">#<?php echo $res['id']; ?></span>
                        <span class="status-badge status-<?php echo $statusClass; ?>"><?php echo h($res['status']); ?></span>
                    </div>
                    <div class="reservation-card-body">
                        <div class="reservation-detail">
                            <span class="detail-label">Date</span>
                            <span class="detail-value"><?php echo date('M d, Y', strtotime($res['reservation_date'])); ?></span>
                        </div>
                        <div class="reservation-detail">
                            <span class="detail-label">Time</span>
                            <span class="detail-value"><?php echo date('h:i A', strtotime($res['reservation_time'])); ?></span>
                        </div>
                        <div class="reservation-detail">
                            <span class="detail-label">Guests</span>
                            <span class="detail-value"><?php echo (int) $res['guests']; ?> people</span>
                        </div>
                        <div class="reservation-detail">
                            <span class="detail-label">Submitted</span>
                            <span class="detail-value"><?php echo date('M d, Y', strtotime($res['created_at'])); ?></span>
                        </div>
                        <?php if (!empty($res['special_request'])): ?>
                            <div class="reservation-detail full-width">
                                <span class="detail-label">Special Request</span>
                                <span class="detail-value"><?php echo h($res['special_request']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
