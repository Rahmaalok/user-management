<?php
require_once 'includes/auth.php';

$error = '';
$success = '';
$edit_mode = false;
$current_product = null;

// EDIT PRODUCT - Tampilkan form edit
if (isset($_GET['edit'])) {
    $product_id = $_GET['edit'];
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND created_by = ?");
    $stmt->execute([$product_id, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        $current_product = $stmt->fetch();
        $edit_mode = true;
    } else {
        $error = "Produk tidak ditemukan atau Anda tidak memiliki akses!";
    }
}

// CANCEL EDIT
if (isset($_POST['cancel_edit'])) {
    $edit_mode = false;
    $current_product = null;
    redirect('products.php');
}

// ADD/UPDATE PRODUCT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product']) || isset($_POST['update_product'])) {
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
        
        // Validasi
        if (empty($name) || empty($price) || empty($stock)) {
            $error = "Nama, harga, dan stok harus diisi!";
        } elseif ($price <= 0) {
            $error = "Harga harus lebih dari 0!";
        } elseif ($stock < 0) {
            $error = "Stok tidak boleh negatif!";
        } else {
            if (isset($_POST['add_product'])) {
                // TAMBAH PRODUK BARU
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, created_by) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$name, $description, $price, $stock, $_SESSION['user_id']])) {
                    $success = "Produk berhasil ditambahkan!";
                    $_POST = array(); // Clear form
                } else {
                    $error = "Terjadi kesalahan saat menambah produk.";
                }
            } elseif (isset($_POST['update_product'])) {
                // UPDATE PRODUK YANG SUDAH ADA
                // Verifikasi kepemilikan produk
                $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND created_by = ?");
                $stmt->execute([$product_id, $_SESSION['user_id']]);
                
                if ($stmt->rowCount() > 0) {
                    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, updated_at = NOW() WHERE id = ? AND created_by = ?");
                    if ($stmt->execute([$name, $description, $price, $stock, $product_id, $_SESSION['user_id']])) {
                        $success = "Produk berhasil diperbarui!";
                        $edit_mode = false;
                        $current_product = null;
                    } else {
                        $error = "Terjadi kesalahan saat memperbarui produk.";
                    }
                } else {
                    $error = "Produk tidak ditemukan atau Anda tidak memiliki akses!";
                }
            }
        }
    }
}

// DELETE PRODUCT
if (isset($_GET['delete'])) {
    $productId = $_GET['delete'];
    
    // Verifikasi kepemilikan produk
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND created_by = ?");
    $stmt->execute([$productId, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND created_by = ?");
        if ($stmt->execute([$productId, $_SESSION['user_id']])) {
            $success = "Produk berhasil dihapus!";
        } else {
            $error = "Terjadi kesalahan saat menghapus produk.";
        }
    } else {
        $error = "Produk tidak ditemukan atau Anda tidak memiliki akses!";
    }
}

// VIEW PRODUCT DETAIL
if (isset($_GET['view'])) {
    $product_id = $_GET['view'];
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND created_by = ?");
    $stmt->execute([$product_id, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        $view_product = $stmt->fetch();
        $view_mode = true;
    } else {
        $error = "Produk tidak ditemukan atau Anda tidak memiliki akses!";
    }
}

// Ambil data produk untuk ditampilkan
$stmt = $pdo->prepare("SELECT * FROM products WHERE created_by = ? ORDER BY id DESC");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();

// Hitung statistik
$total_products = count($products);
$total_value = 0;
foreach ($products as $product) {
    $total_value += $product['price'] * $product['stock'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Sistem Manajemen Gudang</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .product-detail {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        
        .product-detail h4 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }
        
        .detail-value {
            flex: 1;
            color: #2c3e50;
        }
        
        .stock-low {
            color: #dc3545;
            font-weight: bold;
        }
        
        .stock-medium {
            color: #ffc107;
            font-weight: bold;
        }
        
        .stock-high {
            color: #28a745;
            font-weight: bold;
        }
        
        .search-filter {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card-small {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .stat-card-small h4 {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 8px;
        }
        
        .stat-card-small p {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Kelola Produk</h2>
        <div class="user-info">
            <a href="dashboard.php">Dashboard</a> | 
            <a href="profile.php">Profil</a> | 
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="breadcrumb">
            <a href="dashboard.php">Dashboard</a> &raquo; Kelola Produk
        </div>
        
        <!-- Tampilkan Detail Produk -->
        <?php if (isset($view_mode) && $view_mode): ?>
        <div class="product-detail">
            <h4>Detail Produk: <?= htmlspecialchars($view_product['name']) ?></h4>
            <div class="detail-row">
                <div class="detail-label">Nama Produk:</div>
                <div class="detail-value"><?= htmlspecialchars($view_product['name']) ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Deskripsi:</div>
                <div class="detail-value"><?= htmlspecialchars($view_product['description']) ?: '- Tidak ada deskripsi -' ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Harga:</div>
                <div class="detail-value">Rp <?= number_format($view_product['price'], 2, ',', '.') ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Stok:</div>
                <div class="detail-value">
                    <?php
                    $stock_class = '';
                    if ($view_product['stock'] == 0) {
                        $stock_class = 'stock-low';
                    } elseif ($view_product['stock'] <= 5) {
                        $stock_class = 'stock-medium';
                    } else {
                        $stock_class = 'stock-high';
                    }
                    ?>
                    <span class="<?= $stock_class ?>"><?= $view_product['stock'] ?> unit</span>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Nilai Inventory:</div>
                <div class="detail-value">Rp <?= number_format($view_product['price'] * $view_product['stock'], 2, ',', '.') ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Ditambahkan:</div>
                <div class="detail-value"><?= date('d/m/Y H:i', strtotime($view_product['created_at'])) ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Terakhir Update:</div>
                <div class="detail-value"><?= date('d/m/Y H:i', strtotime($view_product['updated_at'])) ?></div>
            </div>
            <div class="form-actions">
                <a href="products.php?edit=<?= $view_product['id'] ?>" class="btn">Edit Produk</a>
                <a href="products.php" class="btn btn-secondary">Kembali ke Daftar</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Notifikasi -->
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Statistik Cepat -->
        <div class="stats-cards">
            <div class="stat-card-small">
                <h4>Total Produk</h4>
                <p><?= $total_products ?></p>
            </div>
            <div class="stat-card-small">
                <h4>Total Nilai Inventory</h4>
                <p>Rp <?= number_format($total_value, 2, ',', '.') ?></p>
            </div>
            <div class="stat-card-small">
                <h4>Produk Tersedia</h4>
                <p><?= count(array_filter($products, function($p) { return $p['stock'] > 0; })) ?></p>
            </div>
            <div class="stat-card-small">
                <h4>Produk Habis</h4>
                <p><?= count(array_filter($products, function($p) { return $p['stock'] == 0; })) ?></p>
            </div>
        </div>

        <!-- Form Tambah/Edit Produk -->
        <h3><?= $edit_mode ? 'Edit Produk' : 'Tambah Produk Baru' ?></h3>
        
        <form method="POST" class="product-form">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="update_product" value="1">
                <input type="hidden" name="product_id" value="<?= $current_product['id'] ?>">
            <?php else: ?>
                <input type="hidden" name="add_product" value="1">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Nama Produk:</label>
                <input type="text" name="name" 
                       value="<?= $edit_mode ? htmlspecialchars($current_product['name']) : (isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '') ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label>Deskripsi:</label>
                <textarea name="description" rows="3"><?= $edit_mode ? htmlspecialchars($current_product['description']) : (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Harga (Rp):</label>
                <input type="number" name="price" step="0.01" min="0" 
                       value="<?= $edit_mode ? $current_product['price'] : (isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '') ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label>Stok:</label>
                <input type="number" name="stock" min="0" 
                       value="<?= $edit_mode ? $current_product['stock'] : (isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : '') ?>" 
                       required>
            </div>
            
            <div class="form-actions">
                <button type="submit">
                    <?= $edit_mode ? 'Update Produk' : 'Tambah Produk' ?>
                </button>
                
                <?php if ($edit_mode): ?>
                    <button type="submit" name="cancel_edit" class="btn btn-secondary">Batal Edit</button>
                <?php endif; ?>
                
                <a href="products.php" class="btn btn-secondary">Refresh</a>
            </div>
        </form>
        
        <!-- Daftar Produk -->
        <h3>Daftar Produk (<?= count($products) ?>)</h3>
        
        <?php if (count($products) > 0): ?>
        <div class="table-responsive">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $index => $product): 
                        $stock_class = '';
                        if ($product['stock'] == 0) {
                            $stock_class = 'stock-low';
                        } elseif ($product['stock'] <= 5) {
                            $stock_class = 'stock-medium';
                        } else {
                            $stock_class = 'stock-high';
                        }
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <strong><?= htmlspecialchars($product['name']) ?></strong>
                            <?php if ($product['stock'] == 0): ?>
                                <br><small style="color: #dc3545;">(Habis)</small>
                            <?php elseif ($product['stock'] <= 5): ?>
                                <br><small style="color: #ffc107;">(Stok Menipis)</small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($product['description']) ?: '-' ?></td>
                        <td>Rp <?= number_format($product['price'], 2, ',', '.') ?></td>
                        <td>
                            <span class="<?= $stock_class ?>"><?= $product['stock'] ?></span>
                        </td>
                        <td>Rp <?= number_format($product['price'] * $product['stock'], 2, ',', '.') ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="products.php?view=<?= $product['id'] ?>" class="btn btn-sm" title="Lihat Detail">👁️</a>
                                <a href="products.php?edit=<?= $product['id'] ?>" class="btn btn-sm" title="Edit">✏️</a>
                                <a href="products.php?delete=<?= $product['id'] ?>" 
                                   onclick="return confirm('Yakin hapus produk <?= htmlspecialchars($product['name']) ?>?')" 
                                   class="btn-danger btn-sm" title="Hapus">🗑️</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Ekspor Data -->
        <div class="form-actions" style="margin-top: 20px;">
            <a href="export_products.php" class="btn">📊 Ekspor ke Excel</a>
            <a href="print_products.php" target="_blank" class="btn">🖨️ Cetak Laporan</a>
        </div>

        <?php else: ?>
            <div class="alert info">
                <strong>Belum ada produk.</strong> Silakan tambah produk baru menggunakan form di atas.
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-hide alerts setelah 5 detik
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Confirm sebelum hapus
        function confirmDelete(productName) {
            return confirm(`Yakin hapus produk "${productName}"? Tindakan ini tidak dapat dibatalkan.`);
        }
    </script>
</body>
</html>