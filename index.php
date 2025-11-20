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

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fa-solid fa-box"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $total_products ?></div>
            <div class="stat-label">Total Produk</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fa-solid fa-truck"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $total_suppliers ?></div>
            <div class="stat-label">Supplier</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value" style="color: <?= $low_stock > 0 ? 'var(--danger-color)' : 'inherit' ?>"><?= $low_stock ?></div>
            <div class="stat-label">Stok Menipis</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fa-solid fa-right-left"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value"><?= $total_transactions ?></div>
            <div class="stat-label">Total Transaksi</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-arrow-trend-down" style="color: var(--danger-color); margin-right: 8px;"></i> Stok Terendah</h3>
            <a href="products.php" class="btn btn-sm" style="background-color: var(--background-color); color: var(--text-primary); border: 1px solid var(--border-color);">Lihat Semua</a>
        </div>
        <div class="table-responsive">
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
                        <td>
                            <span class="badge" style="background-color: <?= $row['stock'] < 10 ? '#fef2f2' : '#ecfdf5' ?>; color: <?= $row['stock'] < 10 ? 'var(--danger-color)' : 'var(--success-color)' ?>">
                                <?= $row['stock'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary-color); margin-right: 8px;"></i> Transaksi Terakhir</h3>
            <a href="history.php" class="btn btn-sm" style="background-color: var(--background-color); color: var(--text-primary); border: 1px solid var(--border-color);">Lihat Semua</a>
        </div>
        <div class="table-responsive">
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
                            <?php if ($row['type'] == 'in'): ?>
                                <span class="badge" style="background-color: #ecfdf5; color: var(--success-color);"><i class="fa-solid fa-arrow-down"></i> Masuk</span>
                            <?php else: ?>
                                <span class="badge" style="background-color: #fef2f2; color: var(--danger-color);"><i class="fa-solid fa-arrow-up"></i> Keluar</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= $row['qty'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
