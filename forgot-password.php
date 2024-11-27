<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (empty($email)) {
        $error = 'Email harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } else {
        try {
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
                $stmt->execute([$token, $expires, $email]);
                
                // TODO: Kirim email reset password
                // Untuk sementara tampilkan link reset
                $success = "Link reset password telah dikirim ke email Anda";
                
            } else {
                $error = "Email tidak terdaftar";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - GOR Pandu Cendikia</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-body">
                <h2>Lupa Password</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                        <small class="form-text text-muted">
                            Masukkan email yang terdaftar untuk menerima link reset password
                        </small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                        <a href="login.php" class="btn btn-secondary">Kembali ke Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 