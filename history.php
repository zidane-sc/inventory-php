<?php
$page_title = 'Histori Barang';
require_once 'header.php';
requireLogin();

// Filter
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$type = $_GET['type'] ?? 'all';

$query = "SELECT t.*, p.name as product_name, p.brand 
          FROM transactions t 
          JOIN products p ON t.product_id = p.id 
          WHERE t.date BETWEEN :start AND :end";

if ($type !== 'all') {
    $query .= " AND t.type = :type";
}

$query .= " ORDER BY t.date DESC, t.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bindValue(':start', $start_date, SQLITE3_TEXT);
$stmt->bindValue(':end', $end_date, SQLITE3_TEXT);
if ($type !== 'all') {
    $stmt->bindValue(':type', $type, SQLITE3_TEXT);
}
$results = $stmt->execute();
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Histori</h3>
    </div>
    <form method="GET" style="display: grid; grid-template-columns: auto auto auto auto; gap: 10px; align-items: end;">
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
                <option value="in" <?= $type == 'in' ? 'selected' : '' ?>>Barang Masuk</option>
                <option value="out" <?= $type == 'out' ? 'selected' : '' ?>>Barang Keluar</option>
            </select>
        </div>
        <button type="submit">Tampilkan</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Histori Transaksi</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>No. Ref</th>
                <th>Barang</th>
                <th>Qty</th>
                <th>Lokasi/Tujuan</th>
                <th>Ket</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $results->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?= $row['date'] ?></td>
                <td>
                    <span class="badge" style="background-color: <?= $row['type'] == 'in' ? 'var(--success-color)' : 'var(--danger-color)' ?>; color: white; padding: 4px 8px; border-radius: 4px;">
                        <?= $row['type'] == 'in' ? 'Masuk' : 'Keluar' ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($row['reference_no']) ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?> (<?= htmlspecialchars($row['brand']) ?>)</td>
                <td><?= $row['qty'] ?></td>
                <td><?= htmlspecialchars($row['location_destination']) ?></td>
                <td><?= htmlspecialchars($row['notes']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
