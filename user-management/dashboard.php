<?php
require_once 'includes/auth.php';

// Ambil statistik
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE created_by = ?");
$stmt->execute([$_SESSION['user_id']]);
$totalProducts = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE created_by = ? AND stock > 0");
$stmt->execute([$_SESSION['user_id']]);
$productsInStock = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE created_by = ? AND stock = 0");
$stmt->execute([$_SESSION['user_id']]);
$productsOutOfStock = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Manajemen Gudang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="header">
        <h2>Dashboard Admin Gudang</h2>
        <div class="user-info">
            <span>Halo, <?= htmlspecialchars($_SESSION['user_name']) ?></span> | 
            <a href="profile.php">Profil</a> | 
            <a href="products.php">Kelola Produk</a> | 
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="breadcrumb">
            <a href="dashboard.php">Dashboard</a>
        </div>
        
        <h3>Selamat datang di Sistem Manajemen Gudang</h3>
        <p>Silakan gunakan menu di atas untuk mengelola data produk dan profil Anda.</p>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h4>Total Produk</h4>
                <p><?= $totalProducts ?></p>
            </div>
            <div class="stat-card">
                <h4>Produk Tersedia</h4>
                <p><?= $productsInStock ?></p>
            </div>
            <div class="stat-card">
                <h4>Produk Habis</h4>
                <p><?= $productsOutOfStock ?></p>
            </div>
        </div>
        
        <div class="quick-actions">
            <h4>Quick Actions:</h4>
            <div class="form-actions">
                <a href="products.php" class="btn">Kelola Produk</a>
                <a href="profile.php" class="btn btn-secondary">Edit Profil</a>
                <a href="change_password.php" class="btn">Ubah Password</a>
            </div>
        </div>
    </div>
</body>
</html>