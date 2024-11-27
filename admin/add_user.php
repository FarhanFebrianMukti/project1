<?php 
$active_page = 'users';
require_once 'includes/admin_header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];
    
    // Validasi
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = "Semua field wajib diisi";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid";
    } else {
        try {
            // Cek username dan email sudah ada atau belum
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Username atau email sudah terdaftar";
            } else {
                // Insert user baru
                $stmt = $db->prepare("
                    INSERT INTO users (username, email, password, full_name, phone, role, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())
                ");
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $role]);
                
                $success = "User berhasil ditambahkan";
                // Reset form
                $_POST = array();
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem";
        }
    }
}
?>

<div class="dashboard-container">
    <?php require_once 'includes/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <h2>Tambah User Baru</h2>
            <div class="header-actions">
                <a href="users.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
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
                            <label for="username">Username *</label>
                            <input type="text" id="username" name="username" class="form-control" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                   required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="password">Password *</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="confirm_password">Konfirmasi Password *</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="full_name">Nama Lengkap *</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" 
                                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="phone">No. Telepon</label>
                            <input type="tel" id="phone" name="phone" class="form-control" 
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="role">Role *</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="user" <?php echo (isset($_POST['role']) && $_POST['role'] == 'user') ? 'selected' : ''; ?>>
                                    User
                                </option>
                                <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>
                                    Admin
                                </option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="users.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once 'includes/admin_footer.php'; ?> 