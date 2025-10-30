<?php
require_once 'includes/auth.php';

// Set header untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="data_produk_' . date('Y-m-d') . '.xls"');

// Ambil data produk
$stmt = $pdo->prepare("SELECT * FROM products WHERE created_by = ? ORDER BY name");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Produk</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Data Produk - <?= $_SESSION['user_name'] ?></h2>
    <p>Tanggal Ekspor: <?= date('d/m/Y H:i') ?></p>
    
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
                <td><?= htmlspecialchars($product['description']) ?></td>
                <td>Rp <?= number_format($product['price'], 2, ',', '.') ?></td>
                <td><?= $product['stock'] ?></td>
                <td>Rp <?= number_format($product['price'] * $product['stock'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>