<?php 
include 'includes/header.php';
include 'includes/sidebar.php';
require_once '../config/database.php';

// Get monthly stats
$stmt = $db->query("
    SELECT 
        DATE_FORMAT(booking_date, '%Y-%m') as month,
        COUNT(*) as total_bookings,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
        COUNT(DISTINCT user_id) as unique_users
    FROM bookings 
    WHERE booking_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(booking_date, '%Y-%m')
    ORDER BY month DESC
");
$monthly_stats = $stmt->fetchAll();

// Get court usage stats
$stmt = $db->query("
    SELECT 
        court_number,
        COUNT(*) as total_bookings
    FROM bookings 
    WHERE status = 'confirmed'
    GROUP BY court_number
");
$court_stats = $stmt->fetchAll();
?>

<div class="admin-content">
    <div class="content-header">
        <h2>Laporan & Statistik</h2>
        <div class="date-range">
            <input type="month" id="startMonth">
            <span>sampai</span>
            <input type="month" id="endMonth">
            <button class="btn-primary" onclick="updateStats()">
                <i class="fas fa-sync"></i> Update
            </button>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card total-bookings">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3>Total Booking</h3>
                <p id="totalBookings">0</p>
            </div>
        </div>

        <div class="stat-card revenue">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>Pendapatan</h3>
                <p id="totalRevenue">Rp 0</p>
            </div>
        </div>

        <div class="stat-card users">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>User Aktif</h3>
                <p id="activeUsers">0</p>
            </div>
        </div>
    </div>

    <div class="charts-container">
        <div class="chart-card">
            <h3>Trend Booking Bulanan</h3>
            <canvas id="bookingTrend"></canvas>
        </div>

        <div class="chart-card">
            <h3>Penggunaan Lapangan</h3>
            <canvas id="courtUsage"></canvas>
        </div>
    </div>

    <div class="export-section">
        <h3>Export Laporan</h3>
        <div class="export-buttons">
            <button class="btn-export" onclick="exportPDF()">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <button class="btn-export" onclick="exportExcel()">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Initialize charts and stats
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    updateStats();
});

function initializeCharts() {
    // Booking Trend Chart
    const bookingTrendCtx = document.getElementById('bookingTrend').getContext('2d');
    new Chart(bookingTrendCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($monthly_stats, 'month')); ?>,
            datasets: [{
                label: 'Total Booking',
                data: <?php echo json_encode(array_column($monthly_stats, 'total_bookings')); ?>,
                borderColor: '#0E8388',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Court Usage Chart
    const courtUsageCtx = document.getElementById('courtUsage').getContext('2d');
    new Chart(courtUsageCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_map(function($item) { 
                return 'Lapangan ' . $item['court_number']; 
            }, $court_stats)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($court_stats, 'total_bookings')); ?>,
                backgroundColor: ['#2E4F4F', '#0E8388', '#CBE4DE']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?> 