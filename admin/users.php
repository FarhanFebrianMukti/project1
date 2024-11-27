<?php 
$active_page = 'users';
require_once 'includes/admin_header.php';

// Handle Actions (Delete, Status Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                if (isset($_POST['user_id'])) {
                    try {
                        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
                        $stmt->execute([$_POST['user_id']]);
                        $_SESSION['success'] = "User berhasil dihapus";
                    } catch (PDOException $e) {
                        $_SESSION['error'] = "Gagal menghapus user";
                    }
                }
                break;
                
            case 'status':
                if (isset($_POST['user_id']) && isset($_POST['status'])) {
                    try {
                        $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ? AND role != 'admin'");
                        $stmt->execute([$_POST['status'], $_POST['user_id']]);
                        $_SESSION['success'] = "Status user berhasil diupdate";
                    } catch (PDOException $e) {
                        $_SESSION['error'] = "Gagal mengupdate status";
                    }
                }
                break;
        }
        header('Location: users.php');
        exit();
    }
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchWhere = '';
$params = [];

if ($search) {
    $searchWhere = "WHERE (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
    $searchParam = "%$search%";
    $params = [$searchParam, $searchParam, $searchParam];
}

// Get total users for pagination
$stmt = $db->prepare("SELECT COUNT(*) FROM users $searchWhere");
$stmt->execute($params);
$totalUsers = $stmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

// Get users
$query = "SELECT * FROM users $searchWhere ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params = array_merge($params, [$limit, $offset]);
$stmt = $db->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<div class="dashboard-container">
    <?php require_once 'includes/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="content-header">
            <h2>Manajemen User</h2>
            <div class="header-actions">
                <a href="add_user.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah User
                </a>
            </div>
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
                    <!-- Search Form -->
                    <form action="" method="GET" class="search-form">
                        <div class="form-group">
                            <input type="text" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Cari username, email, atau nama..."
                                   class="form-control">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Nama Lengkap</th>
                                    <th>Role</th>
                                    <th>Status</th>
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
                                        <span class="badge badge-<?php echo $user['role'] == 'admin' ? 'primary' : 'secondary'; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="status">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <select name="status" class="form-control-sm" 
                                                    onchange="this.form.submit()"
                                                    <?php echo $user['role'] == 'admin' ? 'disabled' : ''; ?>>
                                                <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>
                                                    Active
                                                </option>
                                                <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>
                                                    Inactive
                                                </option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-secondary"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user['role'] != 'admin'): ?>
                                            <form method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
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

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>" 
                               class="btn btn-sm btn-secondary">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                               class="btn btn-sm <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>" 
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