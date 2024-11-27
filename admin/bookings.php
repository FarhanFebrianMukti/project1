<?php 
$active_page = 'bookings';
require_once 'includes/admin_header.php';

// Handle Actions (Confirm, Cancel, Delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['booking_id'])) {
        $booking_id = $_POST['booking_id'];
        
        try {
            switch ($_POST['action']) {
                case 'confirm':
                    $stmt = $db->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
                    $stmt->execute([$booking_id]);
                    $_SESSION['success'] = "Booking berhasil dikonfirmasi";
                    break;
                    
                case 'cancel':
                    $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
                    $stmt->execute([$booking_id]);
                    $_SESSION['success'] = "Booking berhasil dibatalkan";
                    break;
                    
                case 'delete':
                    $stmt = $db->prepare("DELETE FROM bookings WHERE id = ?");
                    $stmt->execute([$booking_id]);
                    $_SESSION['success'] = "Booking berhasil dihapus";
                    break;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal memproses booking";
        }
        
        header('Location: bookings.php');
        exit();
    }
}

// Filter dan Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filter
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$where = [];
$params = [];

if ($status) {
    $where[] = "b.status = ?";
    $params[] = $status;
}

if ($date_from) {
    $where[] = "DATE(b.booking_date) >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $where[] = "DATE(b.booking_date) <= ?";
    $params[] = $date_to;
}

if ($search) {
    $where[] = "(u.username LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Get total bookings for pagination
$stmt = $db->prepare("
    SELECT COUNT(*) 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    $whereClause
");
$stmt->execute($params);
$totalBookings = $stmt->fetchColumn();
$totalPages = ceil($totalBookings / $limit);

// Get bookings
$query = "
    SELECT b.*, u.username, u.full_name, u.email, u.phone
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    $whereClause 
    ORDER BY b.booking_date DESC, b.time_slot ASC 
    LIMIT ? OFFSET ?
";
$params = array_merge($params, [$limit, $offset]);
$stmt = $db->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>

<div class="dashboard-container">
    <?php require_once 'includes/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <h2>Manajemen Booking</h2>
        </header>

        <div class="content-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="filter-form mb-xl">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>
                                        Pending
                                    </option>
                                    <option value="confirmed" <?php echo $status === 'confirmed' ? 'selected' : ''; ?>>
                                        Confirmed
                                    </option>
                                    <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>
                                        Cancelled
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="date_from">Dari Tanggal</label>
                                <input type="date" id="date_from" name="date_from" class="form-control"
                                       value="<?php echo $date_from; ?>">
                            </div>

                            <div class="form-group">
                                <label for="date_to">Sampai Tanggal</label>
                                <input type="date" id="date_to" name="date_to" class="form-control"
                                       value="<?php echo $date_to; ?>">
                            </div>

                            <div class="form-group">
                                <label for="search">Cari</label>
                                <input type="text" id="search" name="search" class="form-control"
                                       placeholder="Cari username/nama/email..."
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="bookings.php" class="btn btn-secondary">
                                <i class="fas fa-sync"></i> Reset
                            </a>
                        </div>
                    </form>

                    <!-- Bookings Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Lapangan</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td>
                                        <div><?php echo htmlspecialchars($booking['full_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['email']); ?></small>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                                    <td><?php echo $booking['time_slot']; ?></td>
                                    <td>Lapangan <?php echo $booking['court_number']; ?></td>
                                    <td>Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $booking['status']; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if ($booking['status'] === 'pending'): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="confirm">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success" title="Konfirmasi">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>

                                            <?php if ($booking['status'] !== 'cancelled'): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="cancel">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-warning" title="Batalkan"
                                                        onclick="return confirm('Yakin ingin membatalkan booking ini?')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>

                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus"
                                                        onclick="return confirm('Yakin ingin menghapus booking ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>

                                <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data booking</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?>&status=<?php echo urlencode($status); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&search=<?php echo urlencode($search); ?>" 
                               class="btn btn-sm btn-secondary">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&search=<?php echo urlencode($search); ?>" 
                               class="btn btn-sm <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page+1; ?>&status=<?php echo urlencode($status); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&search=<?php echo urlencode($search); ?>" 
                               class="btn btn-sm btn-secondary">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once 'includes/admin_footer.php'; ?> 