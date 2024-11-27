<?php 
include 'includes/header.php';
include 'includes/sidebar.php';
require_once '../config/database.php';

// Handle Delete User
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
}

// Handle Update Role
if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$new_role, $user_id]);
}

// Fetch Users
$stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="admin-content">
    <div class="content-header">
        <h2>Kelola Users</h2>
        <button class="btn-primary" onclick="showAddUserModal()">
            <i class="fas fa-plus"></i> Tambah User
        </button>
    </div>

    <div class="users-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Nama Lengkap</th>
                    <th>Role</th>
                    <th>Tgl Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td>
                        <form method="POST" class="role-form">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <select name="role" onchange="this.form.submit()" 
                                    <?php echo $user['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <input type="hidden" name="update_role">
                        </form>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-edit" onclick="editUser(<?php echo $user['id']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" class="delete-form" onsubmit="return confirm('Yakin hapus user ini?')">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit User -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalTitle">Tambah User Baru</h2>
        <form id="userForm" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <small>Kosongkan jika tidak ingin mengubah password</small>
            </div>
            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Simpan</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 