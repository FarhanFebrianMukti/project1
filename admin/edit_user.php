<?php 
$active_page = 'users';
require_once 'includes/admin_header.php';

// Cek ID user
if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit();
}

$user_id = $_GET['id'];

// Ambil data user
try {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        header('Location: users.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: users.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];
    $password = trim($_POST['password']); // Optional
    
    // Validasi
    if (empty($username) || empty($email) || empty($full_name)) {
        $error = "Field yang bertanda * wajib diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid";
    } else {
        try {
            // Cek username dan email (kecuali milik user ini)
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$username, $email, $user_id]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Username atau email sudah digunakan";
            } else {
                // Update user
                if (!empty($password)) {
                    // Update dengan password baru
                    $stmt = $db->prepare("
                        UPDATE users 
                        SET username = ?, email = ?, password = ?, full_name = ?, 
                            phone = ?, role = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $role, $user_id]);
                } else {
                    // Update tanpa password
                    $stmt = $db->prepare("
                        UPDATE users 
                        SET username = ?, email = ?, full_name = ?, 
                            phone = ?, role = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$username, $email, $full_name, $phone, $role, $user_id]);
                }
                
                $success = "Data user berhasil diupdate";
                
                // Refresh data user
                $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
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
            <h2>Edit User</h2>
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
                                   value="<?php echo htmlspecialchars($user['username']); ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" id="password" name="password" class="form-control">
                            <small class="form-text text-muted">
                                Minimal 8 karakter, kombinasi huruf dan angka
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="full_name">Nama Lengkap *</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="phone">No. Telepon</label>
                            <input type="tel" id="phone" name="phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="role">Role *</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>
                                    User
                                </option>
                                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>
                                    Admin
                                </option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
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