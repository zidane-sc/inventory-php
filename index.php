<?php
$page_title = 'Dashboard';
require_once 'header.php';
requireLogin();

// Statistics
$total_products = $db->querySingle("SELECT COUNT(*) FROM products");
$total_suppliers = $db->querySingle("SELECT COUNT(*) FROM suppliers");
$low_stock = $db->querySingle("SELECT COUNT(*) FROM products WHERE stock < 10");
$total_transactions = $db->querySingle("SELECT COUNT(*) FROM transactions");

// Recent Transactions
$recent_tx = $db->query("SELECT t.*, p.name as product_name 
                         FROM transactions t 
                         JOIN products p ON t.product_id = p.id 
                         ORDER BY t.date DESC, t.created_at DESC LIMIT 5");

// Stock Overview
$stocks = $db->query("SELECT * FROM products ORDER BY stock ASC LIMIT 10");
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h3>
    </div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $total_products ?></div>
            <div class="stat-label">Total Produk</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $total_suppliers ?></div>
            <div class="stat-label">Supplier</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: <?= $low_stock > 0 ? 'var(--danger-color)' : 'var(--success-color)' ?>"><?= $low_stock ?></div>
            <div class="stat-label">Stok Menipis (< 10)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $total_transactions ?></div>
            <div class="stat-label">Total Transaksi</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Stok Barang (Top 10 Terendah)</h3>
            <a href="products.php" class="btn btn-sm">Lihat Semua</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Lokasi</th>
                    <th>Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stocks->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td style="font-weight: bold; color: <?= $row['stock'] < 10 ? 'var(--danger-color)' : 'var(--success-color)' ?>">
                        <?= $row['stock'] ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Transaksi Terakhir</h3>
            <a href="history.php" class="btn btn-sm">Lihat Semua</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Tgl</th>
                    <th>Tipe</th>
                    <th>Barang</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $recent_tx->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><?= date('d/m', strtotime($row['date'])) ?></td>
                    <td>
                        <span class="badge" style="background-color: <?= $row['type'] == 'in' ? 'var(--success-color)' : 'var(--danger-color)' ?>; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.8em;">
                            <?= $row['type'] == 'in' ? 'Masuk' : 'Keluar' ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= $row['qty'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>
