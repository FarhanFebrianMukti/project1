<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Semua field harus diisi';
    } else {
        try {
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect berdasarkan role
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                $error = 'Email atau password salah';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GOR Pandu Cendikia</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-body">
                <h2>Login</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox">
                            <input type="checkbox" name="remember"> Ingat saya
                        </label>
                    </div>
                    
                    <div class="card-actions">
                        <button type="submit" class="btn btn-primary w-full">Login</button>
                    </div>
                    
                    <div class="form-footer">
                        <p>Belum punya akun? <a href="register.php">Daftar</a></p>
                        <p><a href="forgot-password.php">Lupa password?</a></p>
                        <p><a href="index.php">Kembali ke Beranda</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 