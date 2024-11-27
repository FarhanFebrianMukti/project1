<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    
    try {
        $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, phone, role) VALUES (?, ?, ?, ?, ?, 'user')");
        $stmt->execute([$username, $email, $password, $full_name, $phone]);
        $success = 'Registrasi berhasil! Silakan login.';
    } catch(PDOException $e) {
        $error = 'Email atau username sudah terdaftar!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GOR Pandu Cendikia</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="register-container">
        <form class="register-form" method="POST">
            <h2>Daftar Akun</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
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
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label for="phone">No. Telepon</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            
            <button type="submit" class="btn-primary">Daftar</button>
            
            <div class="form-footer">
                <p>Sudah punya akun? <a href="login.php">Login</a></p>
                <p><a href="index.php">Kembali ke Beranda</a></p>
            </div>
        </form>
    </div>
</body>
</html> 