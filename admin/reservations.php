<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

require_admin();

$today = date('Y-m-d');
$sql = "SELECT * FROM reservations WHERE reservation_date >= '$today' ORDER BY reservation_date ASC, reservation_time ASC";
$result = $conn->query($sql);

include '../includes/header.php';
?>

<section class="order-history-section">
    <div class="container">
        <h1 class="section-title">Manage Reservations</h1>
        <p class="section-subtitle">Approve or reject upcoming reservation requests. Past reservations are automatically archived.</p>

        <div class="admin-top-actions">
            <a href="<?php echo qb_url('admin/dashboard.php'); ?>" class="btn btn-light btn-sm">Back to Dashboard</a>
        </div>

        <div class="order-history-table-wrapper">
            <table class="order-history-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Date & Time</th>
                        <th>Guests</th>
                        <th>Special Request</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($reservation = $result->fetch_assoc()): ?>
                            <?php $statusClass = strtolower($reservation['status']); ?>
                            <tr>
                                <td><strong><?php echo h($reservation['full_name']); ?></strong></td>
                                <td>
                                    <small><?php echo h($reservation['email']); ?></small><br>
                                    <small><?php echo h($reservation['phone']); ?></small>
                                </td>
                                <td>
                                    <?php echo date('M d, Y', strtotime($reservation['reservation_date'])); ?><br>
                                    <small><?php echo date('h:i A', strtotime($reservation['reservation_time'])); ?></small>
                                </td>
                                <td><?php echo (int) $reservation['guests']; ?></td>
                                <td>
                                    <?php if (!empty($reservation['special_request'])): ?>
                                        <div class="special-request-text" style="font-size:0.85rem; max-width:200px;"><?php echo h($reservation['special_request']); ?></div>
                                    <?php else: ?>
                                        <span style="color:var(--muted);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-badge status-<?php echo $statusClass; ?>"><?php echo h($reservation['status']); ?></span></td>
                                <td>
                                    <form action="<?php echo qb_url('admin/update_reservation.php'); ?>" method="POST" class="inline-actions">
                                        <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                        <select name="status" required style="padding:4px; font-size:0.85rem;">
                                            <option value="Pending" <?php echo ($reservation['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Approved" <?php echo ($reservation['status'] === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                            <option value="Rejected" <?php echo ($reservation['status'] === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No upcoming reservations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
