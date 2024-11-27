<?php 
$active_page = 'dashboard';
require_once 'includes/admin_header.php';
?>

<div class="dashboard-container">
    <?php require_once 'includes/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <h2>Dashboard Admin</h2>
            <div class="header-actions">
                <span class="user-info">
                    Admin: <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
            </div>
        </header>

        <div class="content-body">
            <!-- Statistik Overview -->
            <div class="grid grid-cols-4 gap-lg md:grid-cols-2 sm:grid-cols-1">
                <div class="card">
                    <div class="card-body">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-details">
                                <h3>Total Booking</h3>
                                <?php
                                $stmt = $db->query("SELECT COUNT(*) FROM bookings");
                                $totalBookings = $stmt->fetchColumn();
                                ?>
                                <p class="stats"><?php echo $totalBookings; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-details">
                                <h3>Total Users</h3>
                                <?php
                                $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
                                $totalUsers = $stmt->fetchColumn();
                                ?>
                                <p class="stats"><?php echo $totalUsers; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-details">
                                <h3>Booking Hari Ini</h3>
                                <?php
                                $stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE DATE(booking_date) = CURDATE()");
                                $stmt->execute();
                                $todayBookings = $stmt->fetchColumn();
                                ?>
                                <p class="stats"><?php echo $todayBookings; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="stat-details">
                                <h3>Pendapatan Bulan Ini</h3>
                                <?php
                                $stmt = $db->prepare("SELECT SUM(total_price) FROM bookings WHERE MONTH(booking_date) = MONTH(CURRENT_DATE())");
                                $stmt->execute();
                                $monthlyIncome = $stmt->fetchColumn() ?: 0;
                                ?>
                                <p class="stats">Rp <?php echo number_format($monthlyIncome, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card mt-xl">
                <div class="card-header">
                    <h3>Booking Terbaru</h3>
                    <a href="bookings.php" class="btn btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Lapangan</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $db->query("
                                    SELECT b.*, u.username 
                                    FROM bookings b 
                                    JOIN users u ON b.user_id = u.id 
                                    ORDER BY b.booking_date DESC 
                                    LIMIT 5
                                ");
                                while ($booking = $stmt->fetch()) {
                                    ?>
                                    <tr>
                                        <td>#<?php echo $booking['id']; ?></td>
                                        <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                                        <td><?php echo $booking['time_slot']; ?></td>
                                        <td>Lapangan <?php echo $booking['court_number']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $booking['status']; ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td>Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once 'includes/admin_footer.php'; ?> 