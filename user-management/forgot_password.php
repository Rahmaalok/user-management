<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    
    if (empty($email)) {
        $error = "Email harus diisi!";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            $resetToken = bin2hex(random_bytes(32));
            
            // Simpan token reset
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ? WHERE id = ?");
            if ($stmt->execute([$resetToken, $user['id']])) {
                // Kirim email reset
                sendResetEmail($email, $resetToken);
                
                $success = "Link reset password telah dikirim ke email Anda! (Lihat folder 'emails' untuk simulasi email)";
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
        } else {
            $error = "Email tidak ditemukan atau akun belum diaktivasi!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sistem Manajemen Gudang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container login-container">
        <div class="form-container">
            <h2 class="text-center">Lupa Password</h2>
            
            <?php if ($error): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>
                
                <button type="submit" style="width: 100%;">Kirim Link Reset</button>
            </form>
            
            <p class="text-center mt-3">
                <a href="login.php">Kembali ke Login</a>
            </p>
        </div>
    </div>
</body>
</html>