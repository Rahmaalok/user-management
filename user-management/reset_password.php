<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

if (!isset($_GET['token'])) {
    die("Token tidak valid!");
}

$token = $_GET['token'];
$error = '';
$success = '';

// Cek token
$stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ?");
$stmt->execute([$token]);

if ($stmt->rowCount() === 0) {
    $error = "Token tidak valid atau sudah kadaluarsa!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$error) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Update password dan hapus token
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
        
        if ($stmt->execute([$hashedPassword, $token])) {
            $success = "Password berhasil direset! Silakan login dengan password baru.";
        } else {
            $error = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Manajemen Gudang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container login-container">
        <div class="form-container">
            <h2 class="text-center">Reset Password</h2>
            
            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if (!$success && !$error): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Password Baru:</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label>Konfirmasi Password Baru:</label>
                    <input type="password" name="confirm_password" required minlength="6">
                </div>
                
                <button type="submit" style="width: 100%;">Reset Password</button>
            </form>
            <?php else: ?>
                <a href="login.php" class="btn" style="width: 100%; text-align: center; display: block;">Login Sekarang</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>