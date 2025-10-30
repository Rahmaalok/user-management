<?php
require_once 'includes/functions.php';

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE activation_token = ? AND is_active = 0");
    $stmt->execute([$token]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch();
        
        // Aktifkan akun
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?");
        if ($stmt->execute([$user['id']])) {
            $message = "Akun berhasil diaktivasi! Silakan login.";
            $type = "success";
        } else {
            $message = "Terjadi kesalahan saat mengaktivasi akun.";
            $type = "error";
        }
    } else {
        $message = "Token tidak valid atau akun sudah diaktivasi.";
        $type = "error";
    }
} else {
    $message = "Token tidak ditemukan.";
    $type = "error";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun - Sistem Manajemen Gudang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container login-container">
        <div class="form-container text-center">
            <h2>Aktivasi Akun</h2>
            <div class="alert <?= $type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
            <a href="login.php" class="btn" style="display: inline-block; margin-top: 20px;">Login Sekarang</a>
        </div>
    </div>
</body>
</html>