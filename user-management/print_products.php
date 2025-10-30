<?php
require_once 'includes/auth.php';

// Ambil data produk
$stmt = $pdo->prepare("SELECT * FROM products WHERE created_by = ? ORDER BY name");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();

// Hitung total
$total_value = 0;
$total_stock = 0;
foreach ($products as $product) {
    $total_value += $product['price'] * $product['stock'];
    $total_stock += $product['stock'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produk</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; }
        .total-row { background-color: #e8f4fd; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Produk</h1>
        <p>Sistem Manajemen Gudang</p>
    </div>
    
    <div class="info">
        <p><strong>Nama Admin:</strong> <?= $_SESSION['user_name'] ?></p>
        <p><strong>Tanggal Cetak:</strong> <?= date('d/m/Y H:i') ?></p>
        <p><strong>Total Produk:</strong> <?= count($products) ?> item</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Deskripsi</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Nilai Inventory</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $index => $product): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['description']) ?: '-' ?></td>
                <td>Rp <?= number_format($product['price'], 2, ',', '.') ?></td>
                <td><?= $product['stock'] ?></td>
                <td>Rp <?= number_format($product['price'] * $product['stock'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="4"><strong>TOTAL</strong></td>
                <td><strong><?= $total_stock ?></strong></td>
                <td><strong>Rp <?= number_format($total_value, 2, ',', '.') ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: <?= $_SESSION['user_name'] ?></p>
        <p>Tanggal: <?= date('d/m/Y H:i') ?></p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="btn">🖨️ Cetak Laporan</button>
        <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
    </div>
</body>
</html>