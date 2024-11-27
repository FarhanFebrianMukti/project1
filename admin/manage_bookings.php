<?php 
include 'includes/header.php';
include 'includes/sidebar.php';
require_once '../config/database.php';

// Handle Status Update
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];
    $stmt = $db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $booking_id]);
}

// Fetch Bookings with User Info
$stmt = $db->query("
    SELECT b.*, u.username, u.full_name 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.booking_date DESC, b.start_time DESC
");
$bookings = $stmt->fetchAll();
?>

<div class="admin-content">
    <div class="content-header">
        <h2>Kelola Booking</h2>
        <div class="filter-controls">
            <select id="statusFilter">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <input type="date" id="dateFilter">
        </div>
    </div>

    <div class="bookings-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                <tr data-status="<?php echo $booking['status']; ?>" 
                    data-date="<?php echo $booking['booking_date']; ?>">
                    <td><?php echo $booking['id']; ?></td>
                    <td>
                        <div class="user-info">
                            <span class="username"><?php echo htmlspecialchars($booking['username']); ?></span>
                            <span class="fullname"><?php echo htmlspecialchars($booking['full_name']); ?></span>
                        </div>
                    </td>
                    <td>Lapangan <?php echo $booking['court_number']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                    <td>
                        <?php echo date('H:i', strtotime($booking['start_time'])) . ' - ' . 
                                 date('H:i', strtotime($booking['end_time'])); ?>
                    </td>
                    <td>
                        <form method="POST" class="status-form">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <select name="status" onchange="this.form.submit()" 
                                    class="status-select <?php echo $booking['status']; ?>">
                                <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>
                                    Pending
                                </option>
                                <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>
                                    Confirmed
                                </option>
                                <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>
                                    Cancelled
                                </option>
                            </select>
                            <input type="hidden" name="update_status">
                        </form>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-view" onclick="viewBookingDetails(<?php echo $booking['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-delete" onclick="deleteBooking(<?php echo $booking['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Booking -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Detail Booking</h2>
        <div id="bookingDetails"></div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 