<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $full_name = sanitize($_POST['full_name']);
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if (empty($email) || empty($password) || empty($full_name) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek apakah email sudah terdaftar
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Generate activation token
            $activationToken = bin2hex(random_bytes(32));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Simpan user ke database
            $stmt = $pdo->prepare("INSERT INTO users (email, password, full_name, activation_token) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$email, $hashedPassword, $full_name, $activationToken])) {
                // Kirim email aktivasi
                sendActivationEmail($email, $activationToken);
                
                $success = "Registrasi berhasil! Silakan cek email Anda untuk aktivasi akun. (Lihat folder 'emails' untuk simulasi email)";
            } else {
                $error = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Manajemen Gudang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container login-container">
        <div class="form-container">
            <h2 class="text-center">Registrasi Admin Gudang</h2>
            
            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nama Lengkap:</label>
                    <input type="text" name="full_name" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label>Konfirmasi Password:</label>
                    <input type="password" name="confirm_password" required minlength="6">
                </div>
                
                <button type="submit" style="width: 100%;">Daftar</button>
            </form>
            
            <p class="text-center mt-3">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>
        </div>
    </div>
</body>
</html>