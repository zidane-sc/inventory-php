<?php
$page_title = 'Riwayat Transaksi';
require_once 'header.php';
requireLogin();

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$type = isset($_GET['type']) ? $_GET['type'] : 'all';

$query = "SELECT t.*, p.name as product_name 
          FROM transactions t 
          JOIN products p ON t.product_id = p.id 
          WHERE t.date BETWEEN '$start_date' AND '$end_date'";

if ($type != 'all') {
    $query .= " AND t.type = '$type'";
}

$query .= " ORDER BY t.date DESC, t.created_at DESC";

$transactions = $db->query($query);
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-filter"></i> Filter Data</h3>
    </div>
    <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Dari Tanggal</label>
            <input type="date" name="start_date" value="<?= $start_date ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Sampai Tanggal</label>
            <input type="date" name="end_date" value="<?= $end_date ?>">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Tipe</label>
            <select name="type">
                <option value="all" <?= $type == 'all' ? 'selected' : '' ?>>Semua</option>
                <option value="in" <?= $type == 'in' ? 'selected' : '' ?>>Masuk</option>
                <option value="out" <?= $type == 'out' ? 'selected' : '' ?>>Keluar</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-search"></i> Tampilkan
        </button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Data Riwayat</h3>
        <a href="history.php" class="btn btn-sm" style="background-color: var(--background-color); color: var(--text-primary); border: 1px solid var(--border-color);">Reset Filter</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No. Ref</th>
                    <th>Tipe</th>
                    <th>Barang</th>
                    <th>Qty</th>
                    <th>Tujuan / Ket</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $transactions->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($row['date'])) ?></td>
                    <td><?= htmlspecialchars($row['ref_no']) ?></td>
                    <td>
                        <?php if ($row['type'] == 'in'): ?>
                            <span class="badge" style="background-color: #ecfdf5; color: var(--success-color);"><i class="fa-solid fa-arrow-down"></i> Masuk</span>
                        <?php else: ?>
                            <span class="badge" style="background-color: #fef2f2; color: var(--danger-color);"><i class="fa-solid fa-arrow-up"></i> Keluar</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= $row['qty'] ?></td>
                    <td>
                        <?php 
                        if ($row['type'] == 'out' && !empty($row['destination'])) {
                            echo '<strong>' . htmlspecialchars($row['destination']) . '</strong><br>';
                        }
                        echo htmlspecialchars($row['description']);
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>
