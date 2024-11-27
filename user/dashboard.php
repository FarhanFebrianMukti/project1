<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

// Cek login
requireLogin();

// Ambil data user
$userData = getUserData($db, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GOR Pandu Cendikia</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Menu User</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="bookings.php">
                    <i class="fas fa-calendar"></i> Booking Saya
                </a>
                <a href="profile.php">
                    <i class="fas fa-user"></i> Profil
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="content-header">
                <h2>Dashboard</h2>
                <div class="user-info">
                    <span>Selamat datang, <?php echo htmlspecialchars($userData['username']); ?></span>
                </div>
            </header>

            <div class="content-body">
                <div class="grid grid-cols-3 gap-lg">
                    <!-- Booking Stats -->
                    <div class="card">
                        <div class="card-body">
                            <h3>Booking Aktif</h3>
                            <p class="stats">5</p>
                        </div>
                    </div>
                    
                    <!-- Points/Rewards -->
                    <div class="card">
                        <div class="card-body">
                            <h3>Poin Reward</h3>
                            <p class="stats">150</p>
                        </div>
                    </div>
                    
                    <!-- Member Status -->
                    <div class="card">
                        <div class="card-body">
                            <h3>Status Member</h3>
                            <p class="stats">Regular</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="card mt-xl">
                    <div class="card-header">
                        <h3>Booking Terakhir</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Lapangan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data booking akan ditampilkan di sini -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 