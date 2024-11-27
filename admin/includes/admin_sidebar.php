<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Admin Panel</h3>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" <?php echo ($active_page === 'dashboard') ? 'class="active"' : ''; ?>>
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="bookings.php" <?php echo ($active_page === 'bookings') ? 'class="active"' : ''; ?>>
            <i class="fas fa-calendar-alt"></i> Booking
        </a>
        <a href="users.php" <?php echo ($active_page === 'users') ? 'class="active"' : ''; ?>>
            <i class="fas fa-users"></i> Users
        </a>
        <a href="settings.php" <?php echo ($active_page === 'settings') ? 'class="active"' : ''; ?>>
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</aside> 