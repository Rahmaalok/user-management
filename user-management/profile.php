<?php
require_once 'includes/auth.php';

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$error = '';
$success = '';

// Update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize($_POST['full_name']);
    
    if (empty($full_name)) {
        $error = "Nama lengkap harus diisi!";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
        if ($stmt->execute([$full_name, $_SESSION['user_id']])) {
            $_SESSION['user_name'] = $full_name;
            $success = "Profil berhasil diperbarui!";
        } else {
            $error = "Terjadi kesalahan saat memperbarui profil.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Sistem Manajemen Gudang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="header">
        <h2>Profil Pengguna</h2>
        <div class="user-info">
            <a href="dashboard.php">Dashboard</a> | 
            <a href="products.php">Kelola Produk</a> | 
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="breadcrumb">
            <a href="dashboard.php">Dashboard</a> &raquo; Profil
        </div>
        
        <h3>Informasi Profil</h3>
        
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <div class="profile-info">
            <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Status Akun:</strong> 
                <span style="color: <?= $user['is_active'] ? '#28a745' : '#dc3545' ?>; font-weight: bold;">
                    <?= $user['is_active'] ? 'AKTIF' : 'BELUM AKTIF' ?>
                </span>
            </p>
            <p><strong>Tanggal Daftar:</strong> <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></p>
            <p><strong>Terakhir Update:</strong> <?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?></p>
        </div>
        
        <h3>Edit Profil</h3>
        
        <form method="POST">
            <input type="hidden" name="update_profile" value="1">
            
            <div class="form-group">
                <label>Nama Lengkap:</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background: #f8f9fa;">
                <small style="color: #6c757d;">Email tidak dapat diubah</small>
            </div>
            
            <button type="submit">Update Profil</button>
        </form>
        
        <div class="form-actions" style="margin-top: 30px;">
            <a href="change_password.php" class="btn">Ubah Password</a>
            <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>