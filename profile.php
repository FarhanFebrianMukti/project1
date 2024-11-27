<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

// Cek login
requireLogin();

$error = '';
$success = '';

// Ambil data user
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $error = "Gagal mengambil data user";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    try {
        if (!empty($current_password)) {
            // Jika ingin ganti password
            if (!password_verify($current_password, $user['password'])) {
                $error = "Password saat ini salah";
            } elseif ($new_password !== $confirm_password) {
                $error = "Password baru tidak cocok";
            } else {
                $stmt = $db->prepare("
                    UPDATE users 
                    SET full_name = ?, phone = ?, password = ? 
                    WHERE id = ?
                ");
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt->execute([$full_name, $phone, $hashed_password, $_SESSION['user_id']]);
                $success = "Profil berhasil diupdate";
            }
        } else {
            // Update tanpa ganti password
            $stmt = $db->prepare("
                UPDATE users 
                SET full_name = ?, phone = ? 
                WHERE id = ?
            ");
            $stmt->execute([$full_name, $phone, $_SESSION['user_id']]);
            $success = "Profil berhasil diupdate";
        }
        
        // Refresh data user
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
    } catch (PDOException $e) {
        $error = "Gagal mengupdate profil";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - GOR Pandu Cendikia</title>
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
                <a href="dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="bookings.php">
                    <i class="fas fa-calendar"></i> Booking Saya
                </a>
                <a href="profile.php" class="active">
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
                <h2>Profil Saya</h2>
            </header>

            <div class="content-body">
                <div class="card">
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form method="POST" class="form">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" 
                                       disabled>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" 
                                       disabled>
                            </div>

                            <div class="form-group">
                                <label for="full_name">Nama Lengkap</label>
                                <input type="text" id="full_name" name="full_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="phone">No. Telepon</label>
                                <input type="tel" id="phone" name="phone" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>

                            <div class="form-section">
                                <h4>Ganti Password</h4>
                                <div class="form-group">
                                    <label for="current_password">Password Saat Ini</label>
                                    <input type="password" id="current_password" name="current_password" 
                                           class="form-control">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="new_password">Password Baru</label>
                                        <input type="password" id="new_password" name="new_password" 
                                               class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label for="confirm_password">Konfirmasi Password Baru</label>
                                        <input type="password" id="confirm_password" name="confirm_password" 
                                               class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Profil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 